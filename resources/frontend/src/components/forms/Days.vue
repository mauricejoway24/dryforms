<template>
    <b-modal id="selectForm" title="Add days for this project" v-model="showModal" v-if="isLoaded">
        <div>
            <label>Pick Date: </label>
            <date-picker ref="rangePicker" format="YYYY-MM-DD" v-model="date_range" lang="en" range :shortcuts="shortcuts" confirm></date-picker>
        </div>
        <div v-for="area in projectAreas">
            <b-form-checkbox v-model="selectedAreas[area.id]" :value='area.id' :unchecked-value='null'>
                {{ area.title }}
            </b-form-checkbox>
        </div>
        <div slot="modal-footer" class="w-100">
            <b-btn class="float-right" variant="secondary" @click="showModal=false">
                Close
            </b-btn>
            <b-btn class="float-right mr-2" variant="primary" @click="save()">
                Ok
            </b-btn>
        </div>
    </b-modal>
    <loading v-else></loading>
</template>

<script type="text/babel">
    import Loading from '../layout/Loading'
    import apiProjectAreas from '../../api/project_areas'
    import apiProjectMoistureForm from '../../api/project_moisture_form'
    import apiProjectPsychometricDays from '../../api/project_psychometric_days'
    import ErrorHandler from '../../mixins/error-handler'
    import DatePicker from 'vue2-datepicker'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler],
        components: {
            Loading,
            DatePicker
        },
        data() {
            return {
                showModal: true,
                projectAreas: [],
                selectedAreas: [],
                projectId: null,
                prevformId: null,
                isLoaded: false,
                shortcuts: [
                    {
                        text: 'Today',
                        start: new Date(),
                        end: new Date()
                    }
                ],
                date_range: null
            }
        },
        created() {
            this.init()
        },
        methods: {
            init() {
                this.projectId = parseInt(this.$route.params.project_id)
                this.prevformId = parseInt(this.$route.params.prev_id)
                apiProjectAreas.index({
                    project_id: this.projectId
                }).then(res => {
                    let projectAreas = res.data.project_areas
                    projectAreas.forEach(projectArea => {
                        projectArea.title = projectArea.standard_area.title
                    })
                    this.projectAreas = projectAreas
                    this.isLoaded = true
                }).catch(this.handleErrorResponse)
            },
            save() {
                let areas = _.filter(this.selectedAreas, function(area) { return area })
                if (this.prevformId === 7) {
                    apiProjectMoistureForm.addDates({
                        selected_areas: areas,
                        date_range: this.$refs.rangePicker.text,
                        project_id: this.projectId
                    }).then(res => {
                        this.showModal = false
                    }).catch(this.handleErrorResponse)
                } else if (this.prevformId === 8) {
                    apiProjectPsychometricDays.store({
                        selected_areas: areas,
                        date_range: this.$refs.rangePicker.text
                    }).then(res => {
                        this.showModal = false
                    }).catch(this.handleErrorResponse)
                }
            }
        },
        watch: {
            showModal: function () {
                if (this.showModal) {
                    this.init()
                }
                if (!this.showModal) this.$router.go(-1)
            }
        }
    }
</script>
<style type="text/css" lang="scss" rel="stylesheet/scss">
    .mx-datepicker-popup div.mx-calendar::before {
        content: "Start Date";
        position: relative;
        top: 2px;
        font-weight: 600;
        left: 79px;
    }

    .mx-datepicker-popup div.mx-calendar ~ div.mx-calendar::before {
        content: "End Date";
    }
</style>
