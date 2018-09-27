<template>
    <tempalte>
        <b-row>
            <b-col cols="8" class="text-left">
                <p>Owner/Insured: {{ ownerName }}</p>
                <p>Job Address: {{ jobAddress }}</p>
            </b-col>
            <b-col cols="4" class="text-right">
                <p>Claim# {{ claimNumber }}</p>
            </b-col>
        </b-row>
        <instrument :show-instrument="showInstrument" :project-id="projectId" :instrument="instrument || {}"></instrument>
    </tempalte>
</template>

<script type="text/babel">
    import {mapActions, mapGetters} from 'vuex'
    import Instrument from './partials/Instrument'

    export default {
        data() {
            return {
                projectId: null,
                formId: null
            }
        },
        components: {
            Instrument
        },
        props: {
            showInstrument: {
                type: Boolean,
                default() {
                    return false
                }
            },
            instrument: {
                type: Object,
                default() {
                    return {
                        make: null,
                        model: null
                    }
                }
            }
        },
        created() {
            this.projectId = this.$route.params.project_id
            this.formId = this.$route.params.form_id
            this.init()
        },
        methods: {
            ...mapActions([
                'fetchCallReport'
            ]),
            init() {
                this.$store.state.ProjectForm.projectId = this.$route.params.project_id
                this.fetchCallReport()
            }
        },
        computed: {
            ...mapGetters([
                'callReport'
            ]),
            ownerName: function () {
                return this.callReport ? this.callReport.insured_name : ''
            },
            jobAddress: function () {
                return this.callReport ? (this.callReport.job_address ? this.callReport.job_address + ', ' : '') + (this.callReport.city ? this.callReport.city + ' ' : '') +
                    (this.callReport.state ? this.callReport.state + ' ' : '') +
                    (this.callReport.zip_code ? this.callReport.zip_code + ' ' : '') : ''
            },
            claimNumber: function () {
                return this.callReport ? this.callReport.insurance_claim_no : ''
            }
        }
    }
</script>
