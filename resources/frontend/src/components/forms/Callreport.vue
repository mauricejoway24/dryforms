<template>
    <div class="card mb-3">
        <div class="card-body text-center">
            <loader :is-running="isRunning"></loader>

            <form-header></form-header>
            <h4 class="mb-2">{{ title }}</h4>
            <div class="dropdown-divider"></div>
            <b-row>
                <b-col class="text-left">
                    <b-row>
                        <b-col class="text-center"><h5>Job Site</h5></b-col>
                    </b-row>
                    <b-row>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_residential" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Residential
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_commercial" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Commercial
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_insured" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Owner/Insured
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_tenant" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Tenant
                            </b-form-checkbox>
                        </b-col>
                    </b-row>

                    <div>
                        <label>Contact Name:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.contact_name"></b-form-input>
                    </div>
                    <div>
                        <label>Contact Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.contact_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Site Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.site_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Date Contacted:</label>
                        <date-picker v-model="callReport.date_contacted" type="date" format="MM-dd-yyyy" lang="en"
                                     @input="changeDate(callReport.date_contacted, 'date_contacted')" style="width: 100%"></date-picker>
                    </div>
                    <div>
                        <label>Date of Loss:</label>
                        <date-picker v-model="callReport.date_loss" type="date" format="MM-dd-yyyy" lang="en"
                                     @input="changeDate(callReport.date_loss, 'date_loss')" style="width: 100%"></date-picker>
                    </div>
                    <div>
                        <label>Point of Loss:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.point_loss"></b-form-input>
                    </div>
                    <div>
                        <label>Date Completed:</label>
                        <date-picker v-model="callReport.date_completed" type="date" format="MM-dd-yyyy" lang="en"
                                     @input="changeDate(callReport.date_completed, 'date_completed')" style="width: 100%"></date-picker>
                    </div>

                    <b-row class="mt-2">
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_water" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Water
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_sewage" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Sewage
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_mold" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Mold
                            </b-form-checkbox>
                        </b-col>
                        <b-col class="col-md-6">
                            <b-form-checkbox v-model="callReport.is_fire" :value="1" :unchecked-value='0'
                                             @change="save(callReport)">Fire
                            </b-form-checkbox>
                        </b-col>
                    </b-row>

                    <div>
                        <label>Category:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.category"></b-form-input>
                    </div>
                    <div>
                        <label>Class:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.class"></b-form-input>
                    </div>
                    <div>
                        <label>Job Address:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.job_address"></b-form-input>
                    </div>
                    <div>
                        <label>City:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.city"></b-form-input>
                    </div>
                    <div>
                        <label>State:</label>
                        <select class="form-control" v-model="callReport.state" @change="save(callReport)">
                            <option :value="null">-- Please select --</option>
                            <option v-for="state in states" :key="state.value" :value="state.value">
                                {{ state.text }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label>Zip Code:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.zip_code"></b-form-input>
                    </div>
                    <div>
                        <label>Cross Streets:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.cross_streets"></b-form-input>
                    </div>
                    <div>
                        <label>Apartment Name:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.apartment_name"></b-form-input>
                    </div>
                    <div>
                        <label>Building #:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.building_no"></b-form-input>
                    </div>
                    <div>
                        <label>Unit #:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.apartment_no"></b-form-input>
                    </div>
                    <div>
                        <label>Gate Code:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.gate_code"></b-form-input>
                    </div>
                    <div>
                        <label> Assigned to: </label>
                        <select class="form-control" v-model="callReport.assigned_to" @change="save(callReport)">
                            <option :value="null">-- Please select --</option>
                            <option v-for="item in teams" v-bind:key="item.id" :value="item.id">
                                {{ item.name }}
                            </option>
                        </select>
                    </div>
                </b-col>

                <b-col class="text-left">
                    <b-row>
                        <b-col class="text-center"><h5>Owner/Insured Information</h5></b-col>
                    </b-row>

                    <div>
                        <label>Owner/Insured Name:</label>
                        <b-form-checkbox @change="save()" v-model="sameContactName">Same as job address</b-form-checkbox>
                        <b-form-input @input="save(callReport)" v-model="callReport.insured_name"></b-form-input>
                    </div>
                    <div>
                        <label>Billing Address:</label>
                        <b-form-checkbox @change="save()" v-model="sameJobAddress">Same as job address</b-form-checkbox>
                        <b-form-input @input="save(callReport)" v-model="callReport.billing_address"></b-form-input>
                    </div>
                    <div>
                        <label>City:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insured_city"></b-form-input>
                    </div>
                    <div>
                        <label>State:</label>
                        <select class="form-control" v-model="callReport.insured_state" @change="save(callReport)">
                            <option :value="null">-- Please select --</option>
                            <option v-for="state in states" :key="state.value" :value="state.value">
                                {{ state.text }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label>Zip Code:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insured_zip_code"></b-form-input>
                    </div>
                    <div>
                        <label>Home Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insured_home_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Cell Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insured_cell_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Work Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insured_work_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Email:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insured_email"></b-form-input>
                    </div>
                    <div>
                        <label>Fax:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insured_fax"></b-form-input>
                    </div>

                    <b-row class="mt-2">
                        <b-col class="text-center"><h5>Insurance Information</h5></b-col>
                    </b-row>

                    <div>
                        <label>Claim #:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_claim_no"></b-form-input>
                    </div>
                    <div>
                        <label>Insurance Company:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_company"></b-form-input>
                    </div>
                    <div>
                        <label>Policy #:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_policy_no"></b-form-input>
                    </div>
                    <div>
                        <label>Deductible:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_deductible"></b-form-input>
                    </div>
                    <div>
                        <label>Insurance Adjuster:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_adjuster"></b-form-input>
                    </div>
                    <div>
                        <label>Address:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_address"></b-form-input>
                    </div>
                    <div>
                        <label>City:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_city"></b-form-input>
                    </div>
                    <div>
                        <label>State:</label>
                        <select class="form-control" v-model="callReport.insurance_state" @change="save(callReport)">
                            <option :value="null">-- Please select --</option>
                            <option v-for="state in states" :key="state.value" :value="state.value">
                                {{ state.text }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label>Zip Code:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_zip_code"></b-form-input>
                    </div>
                    <div>
                        <label>Work Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insurance_work_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Cell Phone:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insurance_cell_phone"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>
                    <div>
                        <label>Email:</label>
                        <b-form-input @input="save(callReport)" v-model="callReport.insurance_email"></b-form-input>
                    </div>
                    <div>
                        <label>Fax:</label>
                        <masked-input @input="save(callReport)" class="form-control" v-model="callReport.insurance_fax"
                                      mask="(111) 111-1111" placeholder="(702) 555-1212"/>
                    </div>

                </b-col>
            </b-row>
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapGetters, mapActions} from 'vuex'
    import FormHeader from './FormHeader'
    import Notes from './Notes'
    import ErrorHandler from '../../mixins/error-handler'
    import apiProjectCallReports from '../../api/project_call_reports'
    import apiTeams from '../../api/teams'

    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'

    import MaskedInput from 'vue-masked-input'
    import DatePicker from 'vue2-datepicker'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: {FormHeader, Notes, MaskedInput, DatePicker, Loader},
        props: ['title'],
        data() {
            return {
                projectId: null,
                formId: null,
                callReport: {
                    id: null,
                    company_id: null,
                    project_id: null,
                    contact_name: null,
                    contact_phone: null,
                    site_phone: null,
                    date_contacted: null,
                    time_contacted: null,
                    date_loss: null,
                    point_loss: null,
                    date_completed: null,
                    category: null,
                    class: null,
                    job_address: null,
                    city: null,
                    state: null,
                    zip_code: null,
                    cross_streets: null,
                    apartment_name: null,
                    building_no: null,
                    apartment_no: null,
                    gate_code: null,
                    assigned_to: null,
                    is_residential: null,
                    is_commercial: null,
                    is_insured: null,
                    is_tenant: null,
                    is_water: null,
                    is_sewage: null,
                    is_mold: null,
                    is_fire: null,
                    insured_name: null,
                    billing_address: null,
                    insured_city: null,
                    insured_state: null,
                    insured_zip_code: null,
                    insured_home_phone: null,
                    insured_cell_phone: null,
                    insured_work_phone: null,
                    insured_email: null,
                    insured_fax: null,
                    insurance_claim_no: null,
                    insurance_company: null,
                    insurance_policy_no: null,
                    insurance_deductible: null,
                    insurance_adjuster: null,
                    insurance_address: null,
                    insurance_city: null,
                    insurance_state: null,
                    insurance_zip_code: null,
                    insurance_work_phone: null,
                    insurance_cell_phone: null,
                    insurance_email: null,
                    insurance_fax: null
                },
                assignee: null,
                project: null,
                teams: [],
                isLoaded: false,
                states: null,
                customerTypes: [
                    {text: 'Residential', value: 'is_residential'},
                    {text: 'Commercial', value: 'is_commercial'},
                    {text: 'Owner/Insured', value: 'is_insured'},
                    {text: 'Tenant', value: 'is_tenant'}
                ],
                customerTypesMatch: {
                    isResidential: 'is_residential',
                    isCommercial: 'is_commercial',
                    isInsured: 'is_insured',
                    isTenant: 'is_tenant'
                },
                selectedCustomerType: [],
                sameJobAddress: false,
                sameContactName: false,
                timeConfig: {
                    format: 'hh:mm:ss',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                    keepOpen: true,
                    debug: true
                },
                dateConfig: {
                    format: 'YYYY/MM/D ',
                    useCurrent: false,
                    showClear: true,
                    showClose: true,
                    keepOpen: true
                }
            }
        },
        created() {
            this.projectId = this.$route.params.project_id
            this.formId = this.$route.params.form_id
            let jsonStates = this.$config.get('states')
            this.states = Object.keys(jsonStates).map(function (key) {
                return {
                    value: key,
                    text: jsonStates[key]
                }
            })
            this.init()
        },
        methods: {
            ...mapActions([
                'setCompanyDetails'
            ]),
            init() {
                this.run()
                const apis = [
                    apiProjectCallReports.index(this.projectId, {}),
                    apiTeams.index()
                ]
                Promise.all(apis).then(response => {
                    this.teams = response[1].data.data
                    this.callReport = response[0].data.call_report
                    this.setCompanyDetails(response[0].data.project.company_details)
                    this.$emit('data-ready')
                })
            },
            save: _.debounce((callReport) => {
                if (!callReport || !callReport.project_id) return

                if (!callReport.id) {
                    apiProjectCallReports.store(callReport).then(response => {
                        this.callReport = response.data.call_report
                    }).catch(ErrorHandler.handleErrorResponse)
                } else {
                    apiProjectCallReports.update(callReport.id, callReport).then(response => {
                        this.callReport = response.data.call_report
                    }).catch(ErrorHandler.handleErrorResponse)
                }
            }, 500),
            changeDate(model, param) {
                let moment = this.$moment(model)
                this.callReport[param] = moment.format('YYYY-MM-DD')
                this.save(this.callReport)
            }
        },
        watch: {
            sameJobAddress () {
                if (this.sameJobAddress) {
                    this.callReport.billing_address = this.callReport.job_address
                    this.callReport.insured_city = this.callReport.city
                    this.callReport.insured_state = this.callReport.state
                    this.callReport.insured_zip_code = this.callReport.zip_code
                } else {
                    this.callReport.billing_address = null
                    this.callReport.insured_city = null
                    this.callReport.insured_state = null
                    this.callReport.insured_zip_code = null
                }
            },
            sameContactName() {
                this.callReport.insured_name = this.sameContactName ? this.callReport.contact_name : null
            }
        },
        computed: {
            ...mapGetters([
               'company'
            ])
        }
    }
</script>
