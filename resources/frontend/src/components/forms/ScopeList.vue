<template>
    <div>
        <loader :is-running="isRunning"></loader>

        <div>
            <template v-if="isMisc==='0'">
                <b-row v-for="(page_index) in _.range(curPageNum)" :key="page_index">
                    <div class="col-md-12">
                        <b-row align-h="end">
                            <b-col cols="2" class="text-center" style="font-size:14px; color: black;">
                                Page: {{page_index + 1}} &nbsp;&nbsp;
                                <hr class="mt-0">
                            </b-col>
                        </b-row>
                    </div>
                    <b-list-group class="col-md-6 pr-3 scope-list-group">
                        <b-list-group-item v-for="(scope, index) in leftProjectScopes[page_index]" :key="index" class='list-complete-item fr-box p-0'>
                            <div @mouseover="mouseOver(index, page_index, 'left')" @mouseleave="mouseLeave(index, page_index, 'left')">
                                <div class="fr-quick-insert" :class="scope.hover?'fr-visible':''">
                                    <a class="fr-floating-btn" role="button" tabindex="-1" @click="setHeader(index, page_index, 'left')">
                                        <i class="fa fa-header" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="fr-quick-insert fr-quick-right" :class="scope.hover?'fr-visible':''">
                                    <a class="fr-floating-btn" role="button" tabindex="-1" @click="removeItem(index, page_index, 'left')">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <b-row class="m-0">
                                    <div v-if="scope.is_header" class="bg-grey w-selected">X</div>
                                    <div v-else class="w-selected">
                                        <b-form-checkbox v-model="scope.selected" value="1" unchecked-value="0" @change="updateScope(scope)"></b-form-checkbox>
                                    </div>
                                    <div :class="scope.is_header ? 'bg-grey w-service': 'w-service'">
                                        <b-form-input v-model="scope.service" @input="updateScope(scope)"></b-form-input>
                                    </div>
                                    <div v-if="scope.is_header" class="bg-grey w-uom">UOM</div>
                                    <div v-else class="w-uom">
                                        <select v-model='scope.uom' class='form-control header-uom pt-0 pb-0' @change="updateScope(scope)">
                                            <option :value='null'> ---- </option>
                                            <option v-for='uom in uoms' :key='uom.id' :value='uom.id'>{{ uom.title }}</option>
                                        </select>
                                    </div>
                                    <div v-if="scope.is_header" class="bg-grey w-qty">QTY</div>
                                    <div v-else class="w-qty">
                                        <b-form-input v-model="scope.qty" @input="updateScope(scope)"></b-form-input>
                                    </div>
                                </b-row>
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                    <b-list-group class="col-md-6 pl-3 scope-list-group">
                        <b-list-group-item v-for="(scope, index) in rightProjectScopes[page_index]" :key="index" class='list-complete-item fr-box p-0'>
                            <div @mouseover="mouseOver(index, page_index, 'right')" @mouseleave="mouseLeave(index, page_index, 'right')">
                                <div class="fr-quick-insert" :class="scope.hover?'fr-visible':''">
                                    <a class="fr-floating-btn" role="button" tabindex="-1" @click="setHeader(index, page_index, 'right')">
                                        <i class="fa fa-header" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <div class="fr-quick-insert fr-quick-right" :class="scope.hover?'fr-visible':''">
                                    <a class="fr-floating-btn" role="button" tabindex="-1" @click="removeItem(index, page_index, 'right')">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                    </a>
                                </div>
                                <b-row class="m-0">
                                    <div v-if="scope.is_header" class="bg-grey w-selected">X</div>
                                    <div v-else class="w-selected">
                                        <b-form-checkbox v-model="scope.selected" value="1" unchecked-value="0" @change="updateScope(scope)"></b-form-checkbox>
                                    </div>
                                    <div :class="scope.is_header ? 'bg-grey w-service': 'w-service'">
                                        <b-form-input v-model="scope.service" @input="updateScope(scope)"></b-form-input>
                                    </div>
                                    <div v-if="scope.is_header" class="bg-grey w-uom">UOM</div>
                                    <div v-else class="w-uom">
                                        <select v-model='scope.uom' class='form-control header-uom pt-0 pb-0' @change="updateScope(scope)">
                                            <option :value='null'> ---- </option>
                                            <option v-for='uom in uoms' :key='uom.id' :value='uom.id'>{{ uom.title }}</option>
                                        </select>
                                    </div>
                                    <div v-if="scope.is_header" class="bg-grey w-qty">QTY</div>
                                    <div v-else class="w-qty">
                                        <b-form-input v-model="scope.qty" @input="updateScope(scope)"></b-form-input>
                                    </div>
                                </b-row>
                            </div>
                        </b-list-group-item>
                    </b-list-group>
                </b-row>
                <infinite-loading @infinite="infinitePageHandler" ref="infinitePageLoading">
                    <div slot="no-more">
                    </div>
                </infinite-loading>
            </template>
            <template v-else>
                <b-row>
                    <div class="col-md-12">
                        <b-row align-h="end">
                            <b-col cols="2" class="text-center" style="font-size:14px; color: black;">
                                Misc Page: &nbsp;&nbsp;
                                <hr class="mt-0">
                            </b-col>
                        </b-row>
                    </div>
                    <b-list-group class="col-md-6 pr-3 scope-list-group">
                        <b-list-group-item v-for="(scope, index) in leftMiscScopes" :key="index" class='p-0'>
                            <b-row class="m-0">
                                <div v-if="scope.is_header" class="bg-grey w-selected">X</div>
                                <div v-else class="w-selected">
                                    <b-form-checkbox v-model="scope.selected" value="1" unchecked-value="0" @change="updateScope(scope)"></b-form-checkbox>
                                </div>
                                <div :class="scope.is_header ? 'bg-grey w-service': 'w-service'">
                                    <b-form-input v-model="scope.service" @input="updateScope(scope)"></b-form-input>
                                </div>
                                <div v-if="scope.is_header" class="bg-grey w-uom">UOM</div>
                                <div v-else class="w-uom">
                                    <select v-model='scope.uom' class='form-control header-uom pt-0 pb-0' @change="updateScope(scope)">
                                        <option :value='null'> ---- </option>
                                        <option v-for='uom in uoms' :key='uom.id' :value='uom.id'>{{ uom.title }}</option>
                                    </select>
                                </div>
                                <div v-if="scope.is_header" class="bg-grey w-qty">QTY</div>
                                <div v-else class="w-qty">
                                    <b-form-input v-model="scope.qty" @input="updateScope(scope)"></b-form-input>
                                </div>
                            </b-row>
                        </b-list-group-item>
                    </b-list-group>
                    <b-list-group class="col-md-6 pl-3 scope-list-group">
                        <b-list-group-item v-for="(scope, index) in rightMiscScopes" :key="index" class='p-0'>
                            <b-row class="m-0">
                                <div v-if="scope.is_header" class="bg-grey w-selected">X</div>
                                <div v-else class="w-selected">
                                    <b-form-checkbox v-model="scope.selected" value="1" unchecked-value="0" @change="updateScope(scope)"></b-form-checkbox>
                                </div>
                                <div :class="scope.is_header ? 'bg-grey w-service': 'w-service'">
                                    <b-form-input v-model="scope.service" @input="updateScope(scope)"></b-form-input>
                                </div>
                                <div v-if="scope.is_header" class="bg-grey w-uom">UOM</div>
                                <div v-else class="w-uom">
                                    <select v-model='scope.uom' class='form-control header-uom pt-0 pb-0' @change="updateScope(scope)">
                                        <option :value='null'> ---- </option>
                                        <option v-for='uom in uoms' :key='uom.id' :value='uom.id'>{{ uom.title }}</option>
                                    </select>
                                </div>
                                <div v-if="scope.is_header" class="bg-grey w-qty">QTY</div>
                                <div v-else class="w-qty">
                                    <b-form-input v-model="scope.qty" @input="updateScope(scope)"></b-form-input>
                                </div>
                            </b-row>
                        </b-list-group-item>
                    </b-list-group>
                </b-row>
            </template>
        </div>
    </div>
</template>

<script type="text/babel">
    import InfiniteLoading from 'vue-infinite-loading'
    import apiProjectScope from '../../api/project_scope'
    import ErrorHandler from '../../mixins/error-handler'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: { Loader, InfiniteLoading },
        data() {
            return {
                tableCols: new Array(2),
                services: new Array(40),
                leftProjectScopes: [],
                rightProjectScopes: [],
                leftMiscScopes: [],
                rightMiscScopes: [],
                curPageNum: 0,
                maxPage: 0,
                isLoaded: false
            }
        },
        props: ['projectID', 'projectAreaID', 'uoms', 'miscScopes', 'isMisc'],
        created() {
            this.init()
        },
        methods: {
            init() {
                this.run()
                if (this.isMisc === '1') {
                    this.leftMiscScopes = []
                    this.rightMiscScopes = []
                    this.miscScopes.forEach((scope, index) => {
                        if (index < this.miscScopes.length / 2) {
                            this.leftMiscScopes.push(scope)
                        } else {
                            this.rightMiscScopes.push(scope)
                        }
                    })
                    this.$emit('data-ready')
                } else {
                    this.$emit('data-ready')
                }
            },
            infinitePageHandler($state) {
                this.fetchNextPageScopes()
            },
            fetchNextPageScopes() {
                this.curPageNum++

                return apiProjectScope.index({
                    project_id: this.projectID,
                    project_area_id: this.projectAreaID,
                    curPageNum: this.curPageNum
                }).then(res => {
                    this.maxPage = res.data.max_page
                    if (this.curPageNum > this.maxPage) {
                        this.curPageNum = this.maxPage
                        this.$refs.infinitePageLoading.$emit('$InfiniteLoading:complete')
                        return 1
                    }

                    let curPageScopes = res.data.cur_page_scopes
                    let leftPageScopes = []
                    let rightPageScopes = []
                    curPageScopes.forEach((scope, index) => {
                        if (index < curPageScopes.length / 2) {
                            leftPageScopes.push(scope)
                        } else {
                            rightPageScopes.push(scope)
                        }
                    })
                    this.leftProjectScopes.push(leftPageScopes)
                    this.rightProjectScopes.push(rightPageScopes)
                    this.$refs.infinitePageLoading.$emit('$InfiniteLoading:loaded')
                    return 0
                }).catch(this.handleErrorResponse)
            },
            updateScope: _.debounce(function(scope) {
                if (scope.uom === 0) scope.uom = null
                apiProjectScope.patch(scope.id, scope)
                .then(res => {
                }).catch(this.handleErrorResponse)
            }, 500),
            mouseOver(index, pageIndex, side) {
                if (side === 'left' && index >= this.leftProjectScopes[pageIndex].length) return
                if (side === 'right' && index >= this.rightProjectScopes[pageIndex].length) return
                if (this.dragging) {
                    if (side === 'left') {
                        this.$set(this.leftProjectScopes[pageIndex][index], 'hover', false)
                    } else {
                        this.$set(this.rightProjectScopes[pageIndex][index], 'hover', false)
                    }
                } else {
                    if (side === 'left') {
                        this.$set(this.leftProjectScopes[pageIndex][index], 'hover', true)
                    } else {
                        this.$set(this.rightProjectScopes[pageIndex][index], 'hover', true)
                    }
                }
            },
            mouseLeave(index, pageIndex, side) {
                if (side === 'left' && index >= this.leftProjectScopes[pageIndex].length) return
                if (side === 'right' && index >= this.rightProjectScopes[pageIndex].length) return
                if (side === 'left') {
                    this.$set(this.leftProjectScopes[pageIndex][index], 'hover', false)
                } else {
                    this.$set(this.rightProjectScopes[pageIndex][index], 'hover', false)
                }
            },
            setHeader(index, pageIndex, side) {
                this.removeItem(index, pageIndex, side, true)
                if (side === 'left') {
                    if (this.leftProjectScopes[pageIndex][index].is_header) {
                        this.$set(this.leftProjectScopes[pageIndex][index], 'is_header', 0)
                    } else {
                        this.$set(this.leftProjectScopes[pageIndex][index], 'is_header', 1)
                    }
                    this.updateScope(this.leftProjectScopes[pageIndex][index])
                } else {
                    if (this.rightProjectScopes[pageIndex][index].is_header) {
                        this.$set(this.rightProjectScopes[pageIndex][index], 'is_header', 0)
                    } else {
                        this.$set(this.rightProjectScopes[pageIndex][index], 'is_header', 1)
                    }
                    this.updateScope(this.rightProjectScopes[pageIndex][index])
                }
            },
            removeItem(index, pageIndex, side, noSave = false) {
                if (side === 'left') {
                    this.$set(this.leftProjectScopes[pageIndex][index], 'service', '')
                    this.$set(this.leftProjectScopes[pageIndex][index], 'qty', '')
                    this.$set(this.leftProjectScopes[pageIndex][index], 'selected', 0)
                    this.$set(this.leftProjectScopes[pageIndex][index], 'uom', 0)
                    if (!noSave) this.updateScope(this.leftProjectScopes[pageIndex][index])
                } else {
                    this.$set(this.rightProjectScopes[pageIndex][index], 'service', '')
                    this.$set(this.rightProjectScopes[pageIndex][index], 'qty', '')
                    this.$set(this.rightProjectScopes[pageIndex][index], 'selected', 0)
                    this.$set(this.rightProjectScopes[pageIndex][index], 'uom', 0)
                    if (!noSave) this.updateScope(this.rightProjectScopes[pageIndex][index])
                }
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import "./scss/ScopeList.scss";
</style>
