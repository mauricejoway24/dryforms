<template>
    <b-modal id="selectEquipment" class="selectEquipment" title="Select Forms for this project" v-model="showModal"
             v-if="isLoaded">
        <div v-for="form in forms" :key="form.form_id" v-if="form.form_id !== 1 && form.form_id !== 12">
            <b-form-checkbox v-model="selelctedForms[form.form_id]" value=1 unchecked-value=0>
                {{ form.name }}
            </b-form-checkbox>
        </div>
        <div slot="modal-footer" class="w-100">
            <b-btn variant="primary" class="float-right" @click="saveForm()">Save</b-btn>
        </div>
    </b-modal>
    <loading v-else></loading>
</template>

<script type="text/babel">
    import {mapActions} from 'vuex'
    import Loading from '../layout/Loading'
    import apiProjectForms from '../../api/project_forms'
    import ErrorHandler from '../../mixins/error-handler'

    export default {
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        data() {
            return {
                showModal: true,
                selelctedForms: [],
                projectId: null,
                initiated: false
            }
        },
        created() {
            this.projectId = parseInt(this.$route.params.project_id)
        },
        methods: {
            ...mapActions([
                'fetchFormsOrder',
                'fetchProjectForm'
            ]),
            saveForm() {
                let isSel = false
                let projectForms = [{
                    form_id: 1
                }]
                for (var formID in this.selelctedForms) {
                    if (formID !== '1' && this.selelctedForms[formID] === '1') {
                        projectForms.push({
                            form_id: parseInt(formID)
                        })
                        isSel = true
                    }
                }

                if (!isSel) {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: 'Please select forms'
                    })
                    return
                }

                apiProjectForms.patch(this.projectId, {
                    project_forms: projectForms,
                    project_id: this.projectId
                }).then((res) => {
                    this.$router.push({
                        name: 'Form Call Report',
                        params: {
                            project_id: this.projectId,
                            form_id: 1
                        }
                    })
                })
            }
        },
        computed: {
            forms: function () {
                return this.$store.state.StandardForm.formsOrder
            },
            projectForms: function () {
                return this.$store.state.ProjectForm.projectForms
            },
            isLoaded: function () {
                if (this.forms.length && this.projectForms.length) {
                    if (!this.initiated) {
                        this.selelctedForms = []
                        this.projectForms.forEach(projectForm => {
                            this.selelctedForms[projectForm.form_id] = '1'
                        })
                        this.initiated = true
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
