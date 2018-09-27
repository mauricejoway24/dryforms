<template>
    <div class="forms-order">

        <loader :is-running="isRunning"></loader>

        <div class="card text-center">
            <div class="card-header">
                <h5> {{ $route.meta.title }} </h5>
            </div>
            <div class="card-body text-left pt-3 pb-3">
                <b-container>
                    <b-list-group>
                        <draggable v-model="forms" @update="updateOrder()" @change="sort">
                            <transition-group name="list-complete">
                                <b-list-group-item v-for="item in forms" :key="item.name"
                                                   class="mb-2 list-complete-item text-center"> {{ item.name }}
                                </b-list-group-item>
                            </transition-group>
                        </draggable>
                    </b-list-group>
                </b-container>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapActions} from 'vuex'
    import draggable from 'vuedraggable'
    import _ from 'lodash'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import ErrorHandler from '../../mixins/error-handler'
    import apiFormsOrder from '../../api/forms_order'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        name: 'Forms Order',
        components: {
            draggable,
            Loader
        },
        data() {
            return {
                forms: [],
                callReportForm: {}
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
        },
        methods: {
            ...mapActions([
                'fetchFormsOrder'
            ]),
            init() {
                this.run()
                apiFormsOrder.index()
                    .then(response => {
                        this.callReportForm = _.find(response.data, {form_id: 1})
                        this.forms = _.filter(response.data, (form) => {
                            return form.form_id !== 1
                        })
                        this.$emit('data-ready')
                    })
                    .catch(response => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(response)
                    })
            },
            updateOrder: _.debounce(function () {
                let forms = this.preparePayload()
                apiFormsOrder.store({
                    forms: forms
                }).then(response => {
                    this.fetchFormsOrder()
                }).catch(this.handleErrorResponse)
            }, 500),
            preparePayload() {
                let forms = [{
                    id: this.callReportForm.form_id
                }]
                _.forEach(this.forms, form => {
                    forms.push({
                        id: form.form_id
                    })
                })

                return forms
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .list-complete-item {
        transition: all 0.3s;
        cursor: pointer;
    }

    .list-complete-enter, .list-complete-leave-active {
        opacity: 0;
    }

    .forms-order {
        .list-group-item {
            border: 0px;
            border-radius: 3px;
            margin-bottom: 6px;
            padding-top: 10px;
            background: -webkit-gradient(linear, left top, left bottom, color-stop(0, #ececec), color-stop(10%, rgba(36, 36, 36, 0.08)), color-stop(90%, rgba(36, 36, 36, 0.08)), to(#a2a2a2));
            background: linear-gradient(180deg, #fff5e8 0, rgba(99, 93, 55, 0.3) 12%, rgba(36, 36, 36, 0.2) 90%, #a2a2a2);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#a6000000",endColorstr="#00000000",GradientType=0);
            color: #422100;
        }
    }
</style>
