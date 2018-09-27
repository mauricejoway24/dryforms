<template>
    <b-container>
        <b-row class="mb-2">
            <b-col cols="3" class="text-left pt-4">
                <h6>Owner/Insured:</h6>
                <p>{{ ownerName }}</p>
            </b-col>
            <b-col cols="6">
                <b-row align-h="center">
                    <b-col cols="1" class="text-right">
                        <i class="fa fa-times" @click="clearOwner"></i>
                    </b-col>
                    <b-col cols="9" style="height:150px; border-bottom: 1px solid;" @click="showOwnerModal">
                        <b-row>
                            <img class="m-auto" v-if="ownerSignaturePng" :src="ownerSignaturePng"/>
                        </b-row>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="3" class="text-right pt-4">
                <date-picker v-if="!isSafari" v-model="ownerSignatureUpdatedAt" type="datetime"
                             format="MM-dd-yyyy HH:mm:ss" lang="en" @input="changeOwnerDate(ownerSignatureUpdatedAt)"
                             placeholder="Select Datetime"></date-picker>
                <div v-else>
                    <input type="text" :value="ownerSignatureUpdatedAt"/>
                </div>
            </b-col>
        </b-row>
        <b-row>
            <b-col cols="3" class="text-left pt-4">
                <h6>Company:</h6>
                <p>{{ companyName }}</p>
            </b-col>
            <b-col cols="6">
                <b-row align-h="center">
                    <b-col cols="1" class="text-right">
                        <i class="fa fa-times" @click="clearCompany"></i>
                    </b-col>
                    <b-col cols="9" style="height:150px; border-bottom: 1px solid;" @click="showCompanyModal">
                        <img class="m-auto" v-if="companySignaturePng" :src="companySignaturePng"/>
                    </b-col>
                </b-row>
            </b-col>
            <b-col cols="3" class="text-right pt-4">
                <date-picker v-model="companySignatureUpdatedAt" type="datetime" format="MM-dd-yyyy HH:mm:ss" lang="en"
                             @input="changeCompanyDate(companySignatureUpdatedAt)"
                             placeholder="Select Datetime"></date-picker>
            </b-col>
        </b-row>
        <b-modal ref="ownerSignatureModalRef" size="md" title="Owner Signature" @ok="saveOwnerSignature">
            <vueSignature ref="ownerSignature" w="400px" h="150px" :sigOption="onwerSignOption"
                          class="signature m-auto"></vueSignature>
        </b-modal>
        <b-modal ref="companySignatureModalRef" size="md" title="CompanySignature" @ok="saveCompanySignature">
            <vueSignature ref="companySignature" w="400px" h="150px" :sigOption="companySignOption"
                          class="signature m-auto"></vueSignature>
        </b-modal>
    </b-container>
</template>

<script type="text/babel">
    import ErrorHandler from '../../mixins/error-handler'
    import apiProjectFormSignature from '../../api/project_signature'
    import DatePicker from 'vue2-datepicker'

    export default {
        mixins: [ErrorHandler],
        name: 'signature',
        components: {DatePicker},
        props: {
            form: {
                required: true,
                type: Object
            }
        },
        data() {
            return {
                test: null,
                date: '12/12/2017',
                time: '0:00:00',
                ownerSignaturePng: '',
                ownerSignatureUpdatedAt: null,
                companySignaturePng: '',
                companySignatureUpdatedAt: null,
                form_id: '',
                project_id: '',
                onwerSignOption: {
                    penColor: 'rgb(255, 0, 0)'
                },
                companySignOption: {
                    penColor: 'rgb(255, 0, 0)'
                },
                isSafari: false
            }
        },
        created() {
            this.project_id = this.$route.params.project_id
            this.form_id = this.$route.params.form_id
            if (this.browserDetect() === 'Safari') this.isSafari = true
        },
        methods: {
            init() {
                this.ownerSignaturePng = this.form.insured_signature
                this.companySignaturePng = this.form.company_signature
                this.ownerSignatureUpdatedAt = this.form.insured_signature_upated_at
                this.companySignatureUpdatedAt = this.form.company_signature_upated_at
            },
            showOwnerModal() {
                this.$refs.ownerSignatureModalRef.show()
                let canvasEle = document.getElementsByTagName('canvas')
                canvasEle[0].setAttribute('width', '400')
                canvasEle[0].setAttribute('height', '150')
            },
            showCompanyModal() {
                this.$refs.companySignatureModalRef.show()
                let canvasEle = document.getElementsByTagName('canvas')
                canvasEle[1].setAttribute('width', '400')
                canvasEle[1].setAttribute('height', '150')
            },
            saveOwnerSignature() {
                let moment = this.$moment()
                this.ownerSignatureUpdatedAt = moment.format('YYYY-MM-DD hh:mm:ss')
                this.ownerSignaturePng = this.$refs.ownerSignature.save()
                this.saveSignature()
            },
            saveCompanySignature() {
                let moment = this.$moment()
                this.companySignatureUpdatedAt = moment.format('YYYY-MM-DD hh:mm:ss')
                this.companySignaturePng = this.$refs.companySignature.save()
                this.saveSignature()
            },
            clearOwner() {
                let _this = this
                _this.$refs.ownerSignature.clear()
                this.ownerSignaturePng = ''
                this.ownerSignatureUpdatedAt = null
                this.saveSignature()
            },
            clearCompany() {
                let _this = this
                _this.$refs.companySignature.clear()
                this.companySignaturePng = ''
                this.companySignatureUpdatedAt = null
                this.saveSignature()
            },
            saveSignature() {
                apiProjectFormSignature.store({
                    project_id: this.project_id,
                    form_id: this.form_id,
                    insured_signature: this.ownerSignaturePng,
                    company_signature: this.companySignaturePng,
                    insured_signature_upated_at: this.ownerSignatureUpdatedAt,
                    company_signature_upated_at: this.companySignatureUpdatedAt
                }).then(res => {

                }).catch(this.handleErrorResponse)
            },
            changeOwnerDate(signatureUpdatedAt) {
                if (signatureUpdatedAt) {
                    let moment = this.$moment(signatureUpdatedAt)
                    this.ownerSignatureUpdatedAt = moment.format('YYYY-MM-DD hh:mm:ss')
                }
                this.saveSignature()
            },
            changeCompanyDate(signatureUpdatedAt) {
                if (signatureUpdatedAt) {
                    let moment = this.$moment(signatureUpdatedAt)
                    this.companySignatureUpdatedAt = moment.format('YYYY-MM-DD hh:mm:ss')
                }
                this.saveSignature()
            }
        },
        computed: {
            ownerName: function () {
                return this.$store.state.ProjectForm.callReport ? this.$store.state.ProjectForm.callReport.insured_name : ''
            },
            companyName: function () {
                return this.$store.state.User.companyDetails.length !== 0 ? this.$store.state.User.companyDetails.name : ''
            }
        },
        watch: {
            '$route'(to, from) {
                this.project_id = this.$route.params.project_id
                this.form_id = this.$route.params.form_id
            },
            'form'(newValue, oldValue) {
                if (newValue !== {}) {
                    this.init()
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .signature {
        border-bottom: 1px solid black;
    }
</style>
