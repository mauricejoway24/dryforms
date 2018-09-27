<template>
    <b-row>
        <b-col>
            <table>
                <tr>
                    <th colspan="8"><i class="fa fa-times text-danger" @click="deleteArea(area.id)"></i>{{ area.area.standard_area.title }}</th>
                </tr>
                <tr>
                    <td v-for="(dataSet, index) in area.data" :class="{'bg-grey': (index === 0 || index % 2 === 0)}">
                        <b-form-select :options="structures" @change="update()" v-model="dataSet.structure"></b-form-select>
                    </td>
                </tr>
                <tr>
                    <td v-for="(dataSet, index) in area.data" :class="{'bg-grey': (index === 0 || index % 2 === 0)}">
                        <b-form-select :options="materials" @change="update()" v-model="dataSet.material"></b-form-select>
                    </td>
                </tr>
                <tr>
                    <td v-for="(dataSet, index) in area.data" :class="{'bg-grey': (index === 0 || index % 2 === 0)}">
                        <input type="text" @input="update()" v-model="dataSet.value">
                    </td>
                </tr>
            </table>
        </b-col>
    </b-row>
</template>

<script type="text/babel">
    import apiProjectMoistureForm from '../../api/project_moisture_form'
    import DatePicker from 'vue2-datepicker'
    import _ from 'lodash'
    import ErrorHandlerMixin from '../../mixins/error-handler'

    export default {
        mixins: [ErrorHandlerMixin],
        components: {DatePicker},
        props: {
            area: {
                type: Object,
                required: true
            },
            structures: {
                type: Array,
                required: true
            },
            materials: {
                type: Array,
                required: true
            }
        },
        data() {
            return {}
        },
        methods: {
            update() {
                this.save(this.area)
            },
            save: _.debounce((area) => {
                apiProjectMoistureForm.patch(area.id, area)
                    .then(response => {

                    }).catch(ErrorHandlerMixin.handleErrorResponse)
            }, 500),
            deleteArea (areaId) {
                apiProjectMoistureForm.delete(areaId).then(response => {
                    this.$parent.$emit('reload')
                }).catch(this.handleErrorResponse)
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

    table {
        width: 100%;
        td {
            width: 12.5%;
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

    .custom-select {
        background-color: transparent;
        border: none;
        padding: 0;
    }

    select.form-control:not([size]):not([multiple]) {
        height: 24px;
        padding-left: 2em;
    }
</style>
