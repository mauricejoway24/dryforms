<template>

    <b-modal id="selectForm-print" title="Select Forms for Print" v-model="showModal">
        <loader :is-running="isRunning"></loader>

        <b-form-checkbox-group id="forms" name="forms" v-model="selectedForms">
            <div v-for="form in forms" :key="form.form_id" v-if="form.form_id !== 1 && form.form_id !== 12">
                <b-form-checkbox :value="form.form_id">
                    {{ form.name }}
                </b-form-checkbox>
            </div>
        </b-form-checkbox-group>
        <hr>

        <b-form-checkbox v-model="dropboxUpload" value=1 unchecked-value=0 :disabled="company.dbx_token?false:true">
            Upload pdf to Dropbox?
        </b-form-checkbox>

        <div slot="modal-footer" class="w-100">
            <b-btn variant="primary" class="float-right" @click="printForm()">Print</b-btn>
        </div>
    </b-modal>
</template>

<script type="text/babel">
    import Loader from '../layout/Loader'
    import ErrorHandler from '../../mixins/error-handler'
    import apiProjectForms from '../../api/project_forms'
    import DataProcessMixin from '../../mixins/data-process'
    import Vue from 'vue'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: {
            Loader
        },
        data() {
            return {
                forms: [],
                showModal: true,
                selectedForms: [],
                projectId: null,
                dropboxUpload: 1,
                initiated: false
            }
        },
        created() {
            this.projectId = parseInt(this.$route.params.project_id)
            this.$nextTick(() => {
                this.init()
            })
        },
        methods: {
            init() {
                this.run()
                apiProjectForms.index({project_id: this.$route.params.project_id})
                    .then(response => {
                        this.forms = response.data
                        this.$emit('data-ready')
                    }).catch(response => {
                    this.$emit('data-failed')
                    this.handleErrorResponse(response)
                })
            },
            setCookie(name, value, hours) {
                var expires = ''
                if (hours) {
                    var date = new Date()
                    date.setTime(date.getTime() + (hours * 60 * 60 * 1000))
                    expires = '; expires=' + date.toUTCString()
                }
                document.cookie = name + '=' + (value || '') + expires + '; path=/'
            },
            printForm() {
                let projectForms = this.selectedForms
                projectForms.push(1)

                if (projectForms.length <= 1) {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: 'Please select forms'
                    })
                    return
                }

                let form = document.createElement('form')
                form.setAttribute('method', 'get')
                form.setAttribute('action', '/project/print/' + this.projectId)

                let tokenAttribute = document.createElement('input')
                tokenAttribute.setAttribute('type', 'hidden')
                tokenAttribute.setAttribute('name', 'token')
                tokenAttribute.setAttribute('value', Vue.localStorage.get('apiToken'))
                form.appendChild(tokenAttribute)

                let formsAttribute = document.createElement('input')
                formsAttribute.setAttribute('type', 'hidden')
                formsAttribute.setAttribute('name', 'forms')
                formsAttribute.setAttribute('value', projectForms)
                form.appendChild(formsAttribute)

                let hiddenFieldDropbox = document.createElement('input')
                hiddenFieldDropbox.setAttribute('type', 'hidden')
                hiddenFieldDropbox.setAttribute('name', 'dropbox_upload')
                hiddenFieldDropbox.setAttribute('value', this.dropboxUpload)
                form.appendChild(hiddenFieldDropbox)
                document.body.appendChild(form)
                this.setCookie('apiToken', localStorage.getItem('apiToken'), 1)
                form.submit()
            }
        },
        computed: {
            projectForms: function () {
                return this.$store.state.ProjectForm.projectForms
            },
            company: function () {
                return this.$store.state.User.company
            },
            isLoaded: function () {
                if (this.forms.length && this.projectForms.length && this.company.length !== 0) {
                    if (!this.initiated) {
                        this.selectedForms = []
                        this.projectForms.forEach(projectForm => {
                            this.selectedForms[projectForm.form_id] = '1'
                        })
                        this.initiated = true
                    }
                    if (!this.company.dbx_token) {
                        this.dropboxUpload = 0
                    }
                    return true
                } else return false
            }
        },
        watch: {
            showModal: function () {
                if (!this.showModal) {
                    this.$router.push({
                        name: 'Form Call Report',
                        params: {
                            project_id: this.projectId,
                            form_id: 1
                        }
                    })
                }
            }
        }
    }
</script>


<style type="text/css" lang="scss" rel="stylesheet/scss">
    .custom-control .custom-control-input:disabled ~ .custom-control-indicator {
        background-color: #d3dbe4 !important;
    }
</style>
