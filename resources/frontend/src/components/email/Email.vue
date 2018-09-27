<template>
    <b-modal id="selectForm" :title="$route.meta.title" v-model="showModal">
        <loader :is-running="isRunning"></loader>

        <h6>Select Forms to Email</h6>
        <b-form-checkbox-group id="selectedForms" name="selectedForms" v-model="selectedForms">
            <b-form-checkbox v-for="form in forms" :value="form.form_id" v-if="form.form_id !== 1 && form.form_id !== 12">
                {{ form.title }}
            </b-form-checkbox>
        </b-form-checkbox-group>

        <h6>Select PDF type</h6>
        <b-form-radio-group stacked v-model="selectedPDFType" :options="pdfTypes"></b-form-radio-group>

        <h6>Select recipients of the Email</h6>
        <b-form-checkbox-group stacked v-model="selectedRecipients" :options="recipients"></b-form-checkbox-group>

        <b-form-input v-if="customEmailRequested" v-model="customEmail"></b-form-input>

        <div slot="modal-footer" class="w-100">
            <b-btn variant="primary" class="float-right" v-if="!sent" @click="saveForm()">Send</b-btn>
            <b-btn variant="default" class="float-right" v-if="sent" @click="close()">Close</b-btn>
        </div>

    </b-modal>
</template>

<script type="text/babel">
    import apiProjectEmail from '../../api/email'
    import apiProjectForms from '../../api/project_forms'
    import Loader from '../layout/Loader'
    import ErrorHandler from '../../mixins/error-handler'
    import DataProcessMixin from '../../mixins/data-process'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: {
            Loader
        },
        data() {
            return {
                customEmail: null,
                showModal: true,
                selectedForms: [],
                forms: [],
                selectedPDFType: 'multiple',
                selectedRecipients: [],
                projectId: null,
                sent: false,
                pdfTypes: [
                    {text: 'Sent as individual PDF', value: 'multiple'},
                    {text: 'Sent as one PDF', value: 'single'}
                ],
                recipients: [
                    {text: 'Owner/Insured', value: 'owner'},
                    {text: 'Insurance Adjuster', value: 'insurance_adjuster'},
                    {text: 'Self', value: 'self'},
                    {text: 'Manual', value: 'manual'}
                ]
            }
        },
        computed: {
            customEmailRequested() {
                return _.indexOf(this.selectedRecipients, 'manual') !== -1
            }
        },
        created() {
            this.projectId = parseInt(this.$route.params.project_id)
            this.init()
        },
        watch: {
            showModal: function () {
                if (!this.showModal) this.$router.go(-1)
            }
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
            saveForm() {
                this.run()
                apiProjectEmail.send({
                    forms: this.selectedForms,
                    pdf_type: this.selectedPDFType,
                    recipients: this.selectedRecipients,
                    project_id: this.projectId,
                    custom_email: this.customEmail
                }).then(response => {
                    this.$emit('data-ready')
                    this.sent = true
                    this.$notify({
                        type: 'success',
                        title: 'Success',
                        text: response.data.message
                    })
                }).catch(response => {
                    this.$emit('data-failed')
                    this.handleErrorResponse(response)
                })
            },
            close() {
                this.$router.go(-1)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .custom-control.custom-checkbox {
        width: 100%;
    }
</style>
