<template>
    <b-row class="PsyArea">
        <b-col>
            <table>
                <tr>
                    <th colspan="20">
                        <i class="fa fa-times text-danger" @click="removeArea()"></i> {{ formTitle }} <i class="fa fa-plus text-primary" @click="addArea()"></i>
                    </th>
                </tr>
                <tr>
                    <td class="bg-grey">
                        <input type="text" v-model="area.outside.temp" @input="calculate('outside', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.outside.rh" @input="calculate('outside', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.outside.gpp" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.outside.dew" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.unaffected.temp" @input="calculate('unaffected', area)" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.unaffected.rh" @input="calculate('unaffected', area)" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.unaffected.gpp" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.unaffected.dew" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.affected.temp" @input="calculate('affected', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.affected.rh" @input="calculate('affected', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.affected.gpp" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.affected.dew" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.dehumidifier.temp" @input="calculate('dehumidifier', area)" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.dehumidifier.rh" @input="calculate('dehumidifier', area)" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.dehumidifier.gpp" placeholder="0">
                    </td>
                    <td>
                        <input type="text" v-model="area.dehumidifier.dew" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.hvac.temp" @input="calculate('hvac', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.hvac.rh" @input="calculate('hvac', area)" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.hvac.gpp" placeholder="0">
                    </td>
                    <td class="bg-grey">
                        <input type="text" v-model="area.hvac.dew" placeholder="0">
                    </td>
                </tr>
            </table>
        </b-col>
        <b-modal ref="addContaminants" title="Add Containments" @ok="addContaminants">
            <div class="form-group text-left">
                <label>Numbers of Containments:</label>
                <input type="text" name="title" class="form-control" v-model="contaminantsQuantity" placeholder="Enter Number of Containments">
            </div>
        </b-modal>
    </b-row>
</template>

<script type="text/babel">
    import apiProjectPsychometricDays from '../../api/project_psychometric_days'
    import apiPsychometricCalculations from '../../api/psychometric_calculations'
    import _ from 'lodash'

    export default {
        props: [
            'title',
            'area',
            'apply'
        ],
        data() {
            return {
                outside: [],
                unaffected: [],
                affected: [],
                dehumidifier: [],
                hvac: [],
                containId: null,
                curdate: null,
                contaminantsQuantity: null
            }
        },
        created() {
            this.$bus.$on('outside', (payload) => {
                if (payload.curdate === this.curdate && payload.title !== this.title) {
                    this.outside = payload.data
                    this.save()
                }
            })
            this.$bus.$on('unaffected', (payload) => {
                if (payload.curdate === this.curdate && payload.title !== this.title) {
                    this.unaffected = payload.data
                    this.save()
                }
            })
            this.$bus.$on('affected', (payload) => {
                if (payload.curdate === this.curdate && payload.title !== this.title) {
                    this.affected = payload.data
                    this.save()
                }
            })
            this.$bus.$on('dehumidifier', (payload) => {
                if (payload.curdate === this.curdate && payload.title !== this.title) {
                    this.dehumidifier = payload.data
                    this.save()
                }
            })
            this.$bus.$on('hvac', (payload) => {
                if (payload.curdate === this.curdate && payload.title !== this.title) {
                    this.hvac = payload.data
                    this.save()
                }
            })
        },
        computed: {
            formTitle() {
                this.containId = this.area.containment_id
                if (this.containId) {
                    return this.title + ' (Containment Zone ' + this.containId + ')'
                }
                return this.title
            }
        },
        methods: {
            removeArea() {
                apiProjectPsychometricDays.delete(this.area.id).then(response => {
                    this.$bus.$emit('remove')
                }).catch(this.$parent.handleErrorResponse)
            },
            addArea() {
                this.$refs.addContaminants.show()
            },
            addContaminants() {
                apiProjectPsychometricDays.update(this.area.area_id, {
                    contaminants_quantity: this.contaminantsQuantity
                }).then(res => {
                    this.$bus.$emit('add')
                }).catch(this.$parent.handleErrorResponse)
            },
            calculate: _.debounce(function (type, area) {
                if (!area[type].temp || !area[type].rh) {
                    return
                }
                area[type].temp = parseInt(area[type].temp)
                area[type].rh = parseInt(area[type].rh)

                const apis = [
                    apiPsychometricCalculations.index({
                        temperature: area[type].temp,
                        rh: area[type].rh
                    }),
                    apiPsychometricCalculations.calculateDew({
                        temperature: area[type].temp,
                        rh: area[type].rh
                    })
                ]
                Promise.all(apis)
                    .then(res => {
                        area[type].gpp = res[0].data.result
                        area[type].dew = res[1].data.result
                        this.save(area)
                        if (type === 'outside' || type === 'unaffected') {
                            this.apply(area)
                        }
                    }).catch(this.$parent.handleErrorResponse)
            }, 300),
            save(area) {
                apiProjectPsychometricDays.updateMeasurements(area.id, {
                    outside: area.outside,
                    unaffected: area.unaffected,
                    affected: area.affected,
                    dehumidifier: area.dehumidifier,
                    hvac: area.hvac
                }).then(response => {

                }).catch(this.$parent.handleErrorResponse)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .PsyArea {
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

        .pull-right.custom-control.custom-checkbox.form-control-sm {
            margin-bottom: 0px;
            .custom-control-description {
                font-weight: 400
            }
            .custom-control-indicator {
                top: 7px !important;
            }
        }
    }
</style>
