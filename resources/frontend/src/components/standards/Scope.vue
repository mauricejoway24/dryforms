<template>
    <div class="project-scope">
        <loader :is-running="isRunning"></loader>

        <div class="card text-left">
            <div class="card-header text-center">
                <h5> {{ form.name }} </h5>
            </div>
            <div class="card-body text-left pt-3 pb-3">
                <b-container class="content-container">
                    <label>* Enter side menu name</label>
                    <input type="text" class="form-control mb-3" v-model="form.name" @input="saveForm">
                    <div class="mt-3 mb-1">
                        <label>* Enter form title</label>
                        <div role="group" class="input-group">
                            <input type="text" class="form-control" v-model="form.title" @input="saveForm">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-info" @click="showRevertConfirm()">Revert</button>
                            </div>
                        </div>
                    </div>
                    <scope-list class="mt-4 mb-3"
                                v-for="(page_index) in _.range(curPageNum)" :key="page_index"
                                :leftScope="leftScopes[page_index]"
                                :rightScope="rightScopes[page_index]"
                                :pageCount='pageCount'
                                :pageIndex="page_index"
                                :uoms="uoms"
                                :form="form"
                                :isbusy="isbusy"
                                @changed="changed" @deletePage="deletePage">
                    </scope-list>
                    <infinite-loading @infinite="infiniteHandler" ref="infiniteLoading">
                        <div slot="no-more">
                            <div class="text-left">
                                <b-row class="mt-3" align-h="center">
                                    <b-col cols="6" class="text-center">
                                        <b-button :size="template_size" variant="primary" @click="addNewPage"
                                                  :disabled="isbusy">
                                            Add New Page
                                        </b-button>
                                        <hr/>
                                    </b-col>
                                </b-row>
                                <scope-list class=""
                                            :leftScope="leftMiscScopes"
                                            :rightScope="rightMiscScopes"
                                            :pageIndex="'misc'"
                                            :noteRowStart="noteRowStart"
                                            :defLen="defLen"
                                            :uoms="uoms"
                                            :form="form"
                                            :isbusy="isbusy"
                                            @changed="changed">
                                </scope-list>
                                <div class="mt-3">
                                    <b-form-checkbox v-model="addNotes"
                                                     @change="setAndFilter('additional_notes_show', $event)">Addtional
                                        notes.(Select if you wish to have Additional notes text box)
                                    </b-form-checkbox>
                                </div>
                                <div>
                                    <b-form-checkbox v-model="addFooter"
                                                     @change="setAndFilter('footer_text_show', $event)">Footer
                                        Text.(Select if you wish to have a footer text)
                                    </b-form-checkbox>
                                    <div v-if="form.footer_text_show">
                                        <froala :tag="'textarea'" id="footerEditor" :config="config"
                                                v-model="form.footer_text" @input="saveForm" class="mb-3"></froala>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </infinite-loading>
                </b-container>
            </div>
            <div class="card-footer"></div>
        </div>

        <b-modal id="revertScope" title="Revert Standard Scope" class="text-left" @ok="revertScope()"
                 v-model="showRevertScope"
                 :ok-title="'Confirm'" :ok-variant="'danger'">
            <h5>Are you sure you want to revert to the default scopes?</h5>
        </b-modal>
    </div>
</template>

<script type="text/babel">
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import draggable from 'vuedraggable'
    import InfiniteLoading from 'vue-infinite-loading'
    import _ from 'lodash'

    import ErrorHandler from '../../mixins/error-handler'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'
    import ScopeList from './ScopeList'
    import apiStandardScope from '../../api/standard_scope'
    import apiStandardForm from '../../api/standard_form'
    import apiUom from '../../api/uom'
    import '../froala-plugins/tags'

    export default {
        name: 'standards-project-scope',
        mixins: [ErrorHandler, DataProcessMixin],
        components: {Loader, draggable, ScopeList, InfiniteLoading},
        data() {
            return {
                addNotes: false,
                addFooter: false,
                leftScopes: [],
                rightScopes: [],
                leftMiscScopes: [],
                rightMiscScopes: [],
                form: {},
                isbusy: false,
                noteRowStart: this.defLen,
                curPageNum: 0,
                pageCount: 0,
                uoms: [],
                showRevertScope: false,
                config: {
                    key: this.$config.get('froala_key'),
                    events: {
                        'froalaEditor.initialized': function () {

                        }
                    },
                    toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript',
                        '|', 'fontFamily', 'fontSize', 'color', 'inlineStyle', 'paragraphStyle', '|', 'paragraphFormat',
                        'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', '-', 'insertLink', 'insertImage',
                        'insertVideo', 'insertFile', 'insertTable', '|', 'emoticons', 'specialCharacters', 'insertHR',
                        'selectAll', 'clearFormatting', '|', 'print', 'help', 'html', '|', 'undo', 'redo', '|', 'Tokens']
                }
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
        },
        beforeDestroy() {
        },
        methods: {
            init() {
                this.run()
                let formId = this.$route.params.form_id
                apiStandardForm.show(formId)
                    .then(response => {
                        this.form = response.data.form
                        this.statements = response.data.statements
                        this.$emit('data-ready')
                    })
                    .catch(response => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(response)
                    })
            },
            initGlobalSettings() {
                this.leftScopes = []
                this.rightScopes = []
                this.leftMiscScopes = []
                this.rightMiscScopes = []
                this.isLoaded = false
                this.isbusy = false
                this.noteRowStart = this.defLen
                this.curPageNum = 0
                this.pageCount = 0
            },
            infiniteHandler($state) {
                this.fetchNextPageScopes()
            },
            fetchNextPageScopes() {
                this.curPageNum++
                return apiStandardScope.index({curPageNum: this.curPageNum})
                    .then(response => {
                        this.pageCount = response.data.maxPage
                        // check if current Page is last page, if true fetch last page
                        if (this.curPageNum > this.pageCount) {
                            this.curPageNum = this.pageCount
                            this.loadingCompleted()
                            return 1
                        }
                        // if false add loaded scopes to left and right scopes variable
                        this.addLoadedScopes(response.data.curPageScopes)
                        return 0
                    })
            },
            addLoadedScopes(curPageScopes) {
                let leftPageScopes = []
                let rightPageScopes = []
                let length = curPageScopes.length
                for (let i = 0; i < this.defLen - length; i++) {
                    curPageScopes.push({
                        page: this.curPageNum,
                        service: '',
                        units: '',
                        uom: 0
                    })
                }
                length = curPageScopes.length

                for (let i = 0; i < length; i++) {
                    curPageScopes[i].uom = curPageScopes[i].uom ? curPageScopes[i].uom : 0
                    if (i < length / 2) {
                        leftPageScopes.push(curPageScopes[i])
                    } else {
                        rightPageScopes.push(curPageScopes[i])
                    }
                }

                this.leftScopes.push(leftPageScopes)
                this.rightScopes.push(rightPageScopes)
                this.$refs.infiniteLoading.$emit('$InfiniteLoading:loaded')
            },
            loadingCompleted() {
                apiStandardScope.index({curPageNum: 0})
                    .then(response => {
                        this.pageCount = response.data.maxPage
                        let miscPageScopes = response.data.curPageScopes
                        let length = miscPageScopes.length
                        for (let i = 0; i < this.defLen - length; i++) {
                            miscPageScopes.push({
                                page: 0,
                                service: '',
                                units: '',
                                uom: 0
                            })
                        }
                        length = miscPageScopes.length
                        this.noteRowStart = this.form.additional_notes_show === 1 ? length * 3 / 4 : length

                        for (let i = 0; i < length; i++) {
                            miscPageScopes[i].uom = miscPageScopes[i].uom ? miscPageScopes[i].uom : 0
                            if (i < length / 2) {
                                this.leftMiscScopes.push(miscPageScopes[i])
                            } else {
                                this.rightMiscScopes.push(miscPageScopes[i])
                            }
                        }
                        this.$refs.infiniteLoading.$emit('$InfiniteLoading:complete')
                    }).catch(this.handleErrorResponse)
            },
            changed(payload) {
                if (payload.pageIndex !== 'misc') {
                    this.leftScopes[payload.pageIndex] = payload.leftPageScopes
                    this.rightScopes[payload.pageIndex] = payload.rightPageScopes
                } else {
                    this.leftMiscScopes = payload.leftPageScopes
                    this.rightMiscScopes = payload.rightPageScopes
                }
            },
            addNewPage() {
                this.isbusy = true
                let scopes = []
                let no = 0

                for (let i = 0; i < this.defLen; i++) {
                    scopes.push({
                        page: this.curPageNum + 1,
                        service: '',
                        units: '',
                        uom: 0,
                        no: ++no
                    })
                    if (i === 0 || i === Math.floor(this.defLen / 2)) {
                        scopes[i].is_header = 1
                    }
                }
                if (this.form.id) {
                    const apis = [
                        apiStandardScope.store({
                            scopes: scopes
                        }),
                        apiStandardForm.patch(this.form.id, this.form)
                    ]
                    Promise.all(apis)
                        .then(response => {
                            this.refreshNewPageScopes(response[0].data.scopes)
                        }).catch(this.handleErrorResponse)
                } else {
                    const apis = [
                        apiStandardScope.store({
                            scopes: scopes
                        }),
                        apiStandardForm.store(this.form)
                    ]
                    Promise.all(apis)
                        .then(response => {
                            this.refreshNewPageScopes(response[0].data.scopes)
                            this.form.id = response[1].data.form.id
                        }).catch(this.handleErrorResponse)
                }
            },
            refreshNewPageScopes(scopes) {
                let leftPageScopes = []
                let rightPageScopes = []
                let length = scopes.length

                for (let i = 0; i < length; i++) {
                    scopes[i].uom = scopes[i].uom ? scopes[i].uom : 0
                    if (i < length / 2) {
                        leftPageScopes.push(scopes[i])
                    } else {
                        rightPageScopes.push(scopes[i])
                    }
                }
                this.pageCount++
                this.curPageNum = this.pageCount
                this.leftScopes.push(leftPageScopes)
                this.rightScopes.push(rightPageScopes)
                this.isbusy = false
            },
            deletePage(payload) {
                let pageIndex = payload.pageIndex
                this.leftScopes.splice(pageIndex, 1)
                this.rightScopes.splice(pageIndex, 1)
                this.pageCount--
                this.curPageNum = this.pageCount
                apiStandardScope.delete(pageIndex + 1)
                    .then(response => {
                        this.$emit('updateList')
                    }).catch(this.handleErrorResponse)
            },
            setAndFilter(field, value) {
                this.form[field] = (value ? 1 : 0)
                if (field === 'footer_text_show' && !value) this.form.footer_text = null
                if (field === 'additional_notes_show') {
                    if (value) {
                        this.noteRowStart = this.defLen * 3 / 4
                    } else {
                        this.noteRowStart = this.defLen
                    }
                }
                this.saveForm()
            },
            saveForm: _.debounce(function () {
                if (this.form.id) {
                    apiStandardForm.patch(this.form.id, this.form)
                        .then(response => {
                        }).catch(this.handleErrorResponse)
                } else {
                    apiStandardForm.store(this.form)
                        .then(response => {
                            this.form.id = response.data.form.id
                        }).catch(this.handleErrorResponse)
                }
            }, 500),
            showRevertConfirm() {
                this.showRevertScope = true
            },
            revertScope() {
                this.initGlobalSettings()
                apiStandardScope.revert()
                    .then((response) => {
                        this.isLoaded = true
                    })
                    .catch(this.handleErrorResponse)
            }
        },
        watch: {
            '$store.state.StandardForm.formsOrder': function () {
                this.isLoaded = false
                apiUom.index().then(response => {
                    let uoms = response.data.uoms
                    this.uoms = ['----']
                    uoms.forEach((uom) => {
                        this.uoms[uom.id] = uom.title
                    })
                }).catch(this.handleErrorResponse)
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import 'froala-editor/css/froala_editor.pkgd.min.css';
    @import 'froala-editor/css/froala_style.min.css';
    @import './scss/Scope.scss';
</style>
