<template>
    <div>
        <loader :is-running="isRunning"></loader>

        <div class="card">
            <div class="card-body text-center">
                <form-header></form-header>
                <h4 class="mb-2">{{ $route.meta.title }}</h4>
                <div class="dropdown-divider"></div>
                <form-banner :show-instrument="true" :instrument="project.instrument"></form-banner>
                <div v-for="(measurement, date, index) in measurements" class="timefield">
                    <b-row class="mt-3">
                        <b-col>
                            <table>
                                <tr>
                                    <th colspan="20" class="bg-grey">
                                        <i class="fa fa-times text-danger" @click="removeDay(measurement)"></i>
                                        <span v-if="index === 0">Initial</span> Inspection Date
                                        <datepicker format="MM-dd-yyyy" bootstrap-styling="true" v-model="measurement.date"
                                                    :input-class='{"form-control-sm datepicker-input":true}'
                                                    @input="save(date, $moment(measurement.date).format('YYYY-MM-DD'))">
                                        </datepicker>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="4" class="bg-grey">Outside</td>
                                    <td colspan="4"> Unaffected</td>
                                    <td colspan="4" class="bg-grey"> Affected</td>
                                    <td colspan="4"> Dehumidifier 1</td>
                                    <td colspan="4" class="bg-grey"> Dehumidifier 2</td>
                                </tr>
                                <tr>
                                    <td class="bg-grey">TEMP</td>
                                    <td class="bg-grey">RH%</td>
                                    <td class="bg-grey">GPP</td>
                                    <td class="bg-grey">DEW</td>
                                    <td>TEMP</td>
                                    <td>RH%</td>
                                    <td>GPP</td>
                                    <td>DEW</td>
                                    <td class="bg-grey">TEMP</td>
                                    <td class="bg-grey">RH%</td>
                                    <td class="bg-grey">GPP</td>
                                    <td class="bg-grey">DEW</td>
                                    <td>TEMP</td>
                                    <td>RH%</td>
                                    <td>GPP</td>
                                    <td>DEW</td>
                                    <td class="bg-grey">TEMP</td>
                                    <td class="bg-grey">RH%</td>
                                    <td class="bg-grey">GPP</td>
                                    <td class="bg-grey">DEW</td>
                                </tr>
                            </table>
                        </b-col>
                    </b-row>
                    <b-row class="mt-3">
                        <b-col>
                            <div v-for="area in measurement.areas">
                                <psy-area v-for="areaData in area.measurements" :title="area.title" :area="areaData" :apply="apply" class="mt-3"></psy-area>
                            </div>
                        </b-col>
                    </b-row>
                </div>
                <notes class="mt-3"></notes>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapActions} from 'vuex'
    import FormHeader from './FormHeader'
    import FormBanner from './FormBanner'
    import PsyArea from './PsyArea'
    import Notes from './Notes'
    import apiProjectPsychometricDays from '../../api/project_psychometric_days'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import ErrorHandlerMixin from '../../mixins/error-handler'
    import _ from 'lodash'
    import Datepicker from 'vuejs-datepicker'

    export default {
        mixins: [DataProcessMixin, ErrorHandlerMixin],
        components: {
            FormHeader,
            FormBanner,
            PsyArea,
            Notes,
            Loader,
            Datepicker
        },
        data() {
            return {
                project: {},
                measurements: [],
                project_id: null
            }
        },
        created() {
            this.init()
            this.$bus.$on('remove', () => {
                this.init()
            })
            this.$bus.$on('add', () => {
                this.init()
            })
        },
        methods: {
            ...mapActions([
                'setCompanyDetails'
            ]),
            init() {
                this.run()
                this.project_id = this.$route.params.project_id

                apiProjectPsychometricDays.show(this.project_id)
                    .then(response => {
                        this.project = response.data.project
                        this.setCompanyDetails(response.data.project.company_details)
                        this.measurements = response.data.measurements
                        this.$emit('data-ready')
                    }).catch(response => {
                    this.$emit('data-failed')
                    this.handleErrorResponse(response)
                })
            },
            save: _.debounce(function (oldTime, newTime) {
                apiProjectPsychometricDays.updateTime({
                    project_id: this.project_id,
                    old_time: oldTime,
                    new_time: newTime
                }).then(res => {
                }).catch(this.handleErrorResponse)
            }, 500),
            apply(area) {
                apiProjectPsychometricDays.updateMeasurements(area.id, {
                    outside: area.outside,
                    unaffected: area.unaffected,
                    affected: area.affected,
                    dehumidifier: area.dehumidifier,
                    hvac: area.hvac,
                    project_id: this.project_id,
                    update_all_measurements: true
                }).then(response => {
                    this.init()
                }).catch(this.$parent.handleErrorResponse)
            },
            removeDay(measurement) {
                let id = _.last(measurement.areas[1].measurements).id
                apiProjectPsychometricDays.deleteDay(id).then(response => {
                    this.init()
                }).catch(this.$parent.handleErrorResponse)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    .timefield {
        padding-bottom: 20px;
    }

    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 3px;
    }

    table {
        width: 100%;
        td {
            width: 5%;
        }
        input {
            text-align: center;
            width: 100%;
            background-color: transparent;
            border: none;
        }
    }

    .bg-grey {
        background-color: #c3c3c3;
    }
</style>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .vdp-datepicker {
        width: 150px;
        display: inline-block;
        margin-left: 20px;
        background: none;
    }

    .datepicker-input {
        background: inherit !important;
        border: none !important;
    }
</style>
