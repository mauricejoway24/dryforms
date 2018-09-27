<template>
    <div class="projects-scope-form">
        <div class="card" v-if="isLoaded">
            <div class="card-body text-center">
                <form-header></form-header>
                <calculator-modal></calculator-modal>
                <h4 class="mb-2">{{ $route.meta.title }}</h4>
                <div class="dropdown-divider"></div>
                <form-banner></form-banner>
                <div v-for="(area_index) in _.range(curAreaNum)" :key="area_index.id">
                    <table>
                        <tr>
                            <td colspan="2" class="bg-grey">
                                <h5 class="m-0 pt-1 pb-1"> {{projectAreas[area_index].standard_area.title}} </h5>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%" class="bg-grey">
                                Overall Square Feet
                                <b-button size="sm" variant="success" class="pull-right" @click="openCalculatorModal(area_index)">
                                    <i class="fa fa-calculator"></i>
                                </b-button>

                            </td>
                            <td style="width: 50%">
                                <b-form-input size="sm" type="number" v-model="projectAreas[area_index].overal_square_feet" @input="updateOverallSquareFeet(projectAreas[area_index])"></b-form-input>
                            </td>
                        </tr>
                    </table>
                    <scope-list class="mt-1 mb-5"
                        :projectAreaID="projectAreas[area_index].id"
                        :projectID="project_id"
                        :uoms="uoms"
                        isMisc=0
                    ></scope-list>
                </div>
                <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading">
                    <div slot="no-more">
                    </div>
                </infinite-loading>
                <table>
                    <tr>
                        <td colspan="2" class="bg-grey">
                            <h5 class="m-0 pt-1 pb-1"> Miscellaneous </h5>
                        </td>
                    </tr>
                </table>
                <scope-list class="mt-1 mb-5"
                    :projectID="project_id"
                    :uoms="uoms"
                    :miscScopes="miscScopes"
                    isMisc=1
                ></scope-list>
                <footer-text class="mt-0"></footer-text>
            </div>
        </div>
        <loading v-else></loading>
    </div>
</template>

<script type="text/babel">
    import FormHeader from './FormHeader'
    import FormBanner from './FormBanner'
    import ScopeList from './ScopeList'
    import FooterText from './FooterText'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'
    import apiProjectAreas from '../../api/project_areas'
    import apiProjectScope from '../../api/project_scope'
    import apiUom from '../../api/uom'
    import ErrorHandler from '../../mixins/error-handler'
    import Loading from '../layout/Loading'
    import InfiniteLoading from 'vue-infinite-loading'
    import _ from 'lodash'
    import CalculatorModal from './modals/Calculator'

    export default {
        mixins: [ErrorHandler],
        components: {FormHeader, FormBanner, ScopeList, Loading, InfiniteLoading, FooterText, CalculatorModal},
        data() {
            return {
                projectAreas: [],
                curAreaNum: 0,
                projectScopes: [],
                miscScopes: [],
                project_id: null,
                isLoaded: false,
                uoms: []
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
            this.$on('changeOSF', (data) => {
                this.projectAreas[data.area_index].overal_square_feet = data.val
                this.updateOverallSquareFeet(this.projectAreas[data.area_index])
            })
        },
        methods: {
            init: function() {
                this.project_id = this.$route.params.project_id
                const apis = [
                    apiProjectAreas.index({
                        project_id: this.project_id
                    }),
                    apiUom.index(),
                    apiProjectScope.index({
                        project_id: this.project_id,
                        curPageNum: 0
                    })
                ]
                Promise.all(apis)
                .then(res => {
                    this.projectAreas = res[0].data.project_areas
                    if (this.projectAreas.length === 0) {
                        this.$router.push({
                            name: 'Form Affected Areas',
                            params: {
                                project_id: this.project_id,
                                form_id: 12
                            }
                        })
                        return
                    }
                    this.uoms = res[1].data.uoms
                    this.miscScopes = res[2].data.misc_page_scopes
                    this.isLoaded = true
                }).catch(this.handleErrorResponse)
            },
            infiniteHandler($state) {
                this.fetchNextPageScopes()
            },
            fetchNextPageScopes() {
                this.curAreaNum++
                if (this.curAreaNum > this.projectAreas.length - 1) {
                    this.curAreaNum = this.projectAreas.length
                    this.$refs.infiniteLoading.$emit('$InfiniteLoading:complete')
                } else {
                    this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded')
                }
            },
            updateOverallSquareFeet: _.debounce(function(area) {
                apiProjectAreas.patch(area.id, area)
                .then(res => {
                }).catch(this.handleErrorResponse)
            }, 500),
            openCalculatorModal(areaIndex) {
                this.$emit('openCalculatorModal', areaIndex)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import 'froala-editor/css/froala_editor.pkgd.min.css';
    @import 'froala-editor/css/froala_style.min.css';
    .projects-scope-form {
        table, th, td {
            border-collapse: collapse;
            padding: 3px;
        }

        table {
            width: 100%;
            input {
                text-align: center;
                width: 100%;
                background-color: transparent;
                border: none;
            }
        }

        table,th, td {
            border: 1px solid #737373 !important;
        }
        .w-50 {
            width: 50%;
        }

        .bg-grey {
            background-color: #c3c3c3;
        }

        .infinite-status-prompt {
            display: none !important;
        }
    }
</style>
