<template>
    <div class="card">
        <loader :is-running="isRunning"></loader>

        <div class="card-body text-center">
            <form-header></form-header>
            <h4 class="mb-2">{{ $route.meta.title }}</h4>
            <div class="dropdown-divider"></div>
            <form-banner :show-instrument="true" :instrument="project.instrument"></form-banner>
            <div v-for="(day, index) in moistureForm.days">
                <b-row class="mt-3">
                    <b-col>
                        <table>
                            <tr>
                                <th colspan="20" class="bg-grey"><span v-if="index === 0">Initial</span> Inspection Date
                                    <datepicker format="MM-dd-yyyy" bootstrap-styling="true" v-model="day.date"
                                                :input-class='{"form-control-sm datepicker-input":true}'
                                                @input="updateDate(day.id, $moment(day.date).format('YYYY-MM-DD'))">
                                    </datepicker>
                                </th>
                            </tr>
                        </table>
                    </b-col>
                </b-row>
                <b-row class="mt-3">
                    <b-col>
                        <moisture-area v-for="area in day.days_data" :area="area" :structures="structures" :materials="materials" class="mt-3"></moisture-area>
                    </b-col>
                </b-row>
            </div>
            <notes class="mt-3"></notes>
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapActions} from 'vuex'
    import FormHeader from './FormHeader'
    import FormBanner from './FormBanner'
    import MoistureArea from './MoistureArea'
    import Notes from './Notes'
    import apiStandardStructure from '../../api/standard_structures'
    import apiStandardMaterials from '../../api/standard_materials'
    import apiProjectMoistureForm from '../../api/project_moisture_form'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import Datepicker from 'vuejs-datepicker'
    import ErrorHandlerMixin from '../../mixins/error-handler'

    export default {
        mixins: [DataProcessMixin, ErrorHandlerMixin],
        components: {
            FormHeader,
            FormBanner,
            MoistureArea,
            Notes,
            Loader,
            Datepicker
        },
        data() {
            return {
                projectAreas: [],
                standardAreas: [],
                project_id: null,
                structures: [{value: null, text: 'n/a'}],
                materials: [{value: null, text: 'n/a'}],
                moistureForm: {
                    days: []
                },
                project: {}
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
            this.$on('reload', () => {
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
                const apis = [
                    apiProjectMoistureForm.show(this.project_id),
                    apiStandardStructure.index(),
                    apiStandardMaterials.index()
                ]

                return Promise.all(apis)
                    .then(response => {
                        this.moistureForm = response[0].data.moisture_form
                        this.project = response[0].data.project
                        this.setCompanyDetails(response[0].data.project.company_details)
                        let structures = response[1].data.structures
                        structures.forEach(structure => {
                            structure.text = structure.title
                            structure.value = structure.title
                            this.structures.push(structure)
                        })
                        let materials = response[2].data.materials
                        materials.forEach(material => {
                            material.text = material.title
                            material.value = material.title
                            this.materials.push(material)
                        })
                        this.$emit('data-ready')
                    })
                    .catch(response => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(response)
                    })
            },
            updateDate(dayId, newTime) {
                apiProjectMoistureForm.updateDate(dayId, {
                    date: newTime
                }).then(response => {

                }).catch(response => {
                    this.handleErrorResponse(response)
                })
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss" scoped>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        padding: 3px;
    }

    .row-border {
        border: 1px solid black;
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

