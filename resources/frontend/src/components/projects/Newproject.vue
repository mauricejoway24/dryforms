<template>
    <b-modal id="selectForm" :title="$route.meta.title" v-model="showModal" v-if="isLoaded">
        <div v-for="form in forms" :key="form.form_id" v-if="form.form_id !== 1 && form.form_id !== 12">
            <b-form-checkbox v-model="form.selected" value="1" unchecked-value="0">
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
    import apiProjects from '../../api/projects'
    import apiProjectForms from '../../api/project_forms'
    import apiProjectCallReports from '../../api/project_call_reports'
    import ErrorHandler from '../../mixins/error-handler'

    export default {
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        data() {
            return {
                showModal: true,
                selectedForms: []
            }
        },
        methods: {
            ...mapActions([
                'fetchFormsOrder',
                'fetchUser'
            ]),
            saveForm() {
                let isSel = false
                let projectForms = []
                this.forms.forEach((form) => {
                    if (form.selected === '1') {
                        isSel = true
                        projectForms.push({
                            form_id: form.form_id
                        })
                    }
                })
                if (!isSel) {
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: 'Please select forms'
                    })
                    return
                }
                apiProjects.store({
                    company_id: this.user.company_id,
                    owner_id: this.user.id,
                    status: 1
                }).then((response) => {
                    let projectId = response.data.project.id
                    let moment = this.$moment()
                    const apis = [
                        apiProjectForms.store({
                            project_forms: projectForms,
                            project_id: projectId,
                            company_id: this.user.company_id
                        }),
                        apiProjectCallReports.store({
                            project_id: projectId,
                            company_id: this.user.company_id,
                            date_contacted: moment.format('YYYY-MM-DD')
                        })
                    ]
                    Promise.all(apis)
                        .then(res => {
                            this.$router.push({
                                name: 'Form Call Report',
                                params: {
                                    project_id: projectId,
                                    form_id: 1
                                }
                            })
                        })
                }).catch(this.handleErrorResponse)
            }
        },
        created() {
            this.fetchFormsOrder()
            this.fetchUser()
        },
        computed: {
            forms() {
                let tforms = this.$store.state.StandardForm.formsOrder
                tforms.forEach((form) => {
                    form.selected = '1'
                })
                return tforms
            },
            user() {
                return this.$store.state.User.user
            },
            isLoaded() {
                return this.forms.length && this.user.length !== 0
            }
        },
        watch: {
            showModal: function () {
                if (!this.showModal) this.$router.push('/projects')
            }
        }
    }
</script>
