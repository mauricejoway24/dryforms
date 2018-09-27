<template>
    <div class="standards-main">
        <loader :is-running="isRunning"></loader>

        <div class="card text-left">
            <add-statement></add-statement>
            <div class="card-header text-center">
                <h5> {{ form.title }} </h5>
            </div>
            <div class="card-body text-left pt-3 pb-3">
                <b-container class="content-container">
                    <label>* Enter side menu name</label>
                    <input type="text" class="form-control mb-3" v-model="form.name" @input="save">
                    <label>* Enter form title</label>
                    <input type="text" class="form-control" v-model="form.title" @input="save">
                    <div v-for="(item, index) in statements" :key="item.id">
                        <div class="mt-3 mb-1">
                            <div role="group" class="input-group">
                                <button class="btn btn-sm btn-info" @click="showRevertConfirm(item)">Revert</button>
                            </div>
                        </div>
                        <h5 v-if="item.title">{{ item.title }}</h5>
                        <froala :tag="'textarea'" :config="config" v-model="item.statement" @input="save"></froala>
                    </div>
                    <div class="mt-3">
                        <b-form-checkbox v-model="addNotes" @change="setAndFilter('additional_notes_show', $event)">Additional notes.(Select if you wish to have Additional notes text box)</b-form-checkbox>
                    </div>
                    <div>
                        <b-form-checkbox v-model="addFooter" @change="setAndFilter('footer_text_show', $event)">Footer Text.(Select if you wish to have a footer text)</b-form-checkbox>
                        <div v-show="form.footer_text_show" class="footerText">
                            <froala :tag="'textarea'" id="footerEditor" :config="config" v-model="form.footer_text" class="mb-3" @input="save"></froala>
                        </div>
                    </div>
                    <b-row class="info-bottom mt-3">
                        <b-col cols="4">
                            <div class="info-line-bottom">
                                <label>Insured</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                            <div class="info-line-bottom">
                                <label>Company</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                        </b-col>
                        <b-col cols="4">
                            <div class="info-line-bottom" @click="signatureModal('insured')">
                                <label>Signature</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                            <div class="info-line-bottom" @click="signatureModal('company')">
                                <label>Signature</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                        </b-col>
                        <b-col cols="4">
                            <div class="info-line-bottom">
                                <label>Date</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                            <div class="info-line-bottom">
                                <label>Date</label>
                                <label class="entry">&nbsp;</label>
                            </div>
                        </b-col>
                    </b-row>
                </b-container>
            </div>
            <div class="card-footer"></div>
        </div>
        <b-modal id="rejectStatement" title="Revert Statement" class="text-left" @ok="revertStatement()" v-model="show"
                :ok-title="'Confirm'" :ok-variant="'danger'">
            <h5>Are you sure you want to revert to the default statement?</h5>
        </b-modal>
    </div>
</template>

<script type="text/babel">
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import ErrorHandler from '../../mixins/error-handler'
    import AddStatement from './modals/AddStatement'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'
    import '../froala-plugins/tags'
    import apiStandardForm from '../../api/standard_form'
    import apiProjectStatements from '../../api/project_statements'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: { Loader, AddStatement },
        data () {
            return {
                form: {},
                statementToRevert: null,
                addNotes: false,
                addFooter: false,
                config: {
                    key: this.$config.get('froala_key'),
                    toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript',
                        '|', 'fontFamily', 'fontSize', 'color', 'inlineStyle', 'paragraphStyle', '|', 'paragraphFormat',
                        'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', '-', 'insertLink', 'insertImage',
                        'insertVideo', 'insertFile', 'insertTable', '|', 'emoticons', 'specialCharacters', 'insertHR',
                        'selectAll', 'clearFormatting', '|', 'print', 'help', 'html', '|', 'undo', 'redo', '|', 'Tokens']
                },
                revertingIndex: null,
                show: false
            }
        },
        created() {
            this.$nextTick(() => {
                this.init()
            })
            this.$on('reloadStatement', () => {
                this.setForm(this.$route.params.form_id)
            })
        },
        methods: {
            save: _.debounce(function () {
                this.form.statements = this.statements
                apiStandardForm.patch(this.form.id, this.form)
                    .then(response => {
                    }).catch(this.handleErrorResponse)
            }, 300),
            setAndFilter(field, value) {
                this.form[field] = (value ? 1 : 0)
                if (field === 'footer_text_show' && !value) this.form.footer_text = null
                this.save()
            },
            setForm(formID) {
                let formPerID = this.$store.getters.formPerID(formID)
                if (formPerID.length !== 0) {
                    this.form = formPerID[0]
                    this.addNotes = (this.form.additional_notes_show === 1)
                    this.addFooter = (this.form.footer_text_show === 1)
                } else {
                    this.form = null
                }
            },
            showRevertConfirm(statement) {
                this.statementToRevert = statement
                this.show = true
            },
            revertStatement() {
                apiProjectStatements.revert(this.statementToRevert.id, {
                    form_id: this.form.form_id
                }).then(response => {
                    this.init()
                }).catch(response => {
                    this.handleErrorResponse(response)
                })
            },
            init() {
                this.run()
                let formId = this.$route.params.form_id
                apiStandardForm.show(formId)
                    .then(response => {
                        this.form = response.data.form
                        this.form.footer_text = this.form.footer_text || ' '
                        this.statements = response.data.statements
                        this.$emit('data-ready')
                    })
                    .catch(response => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(response)
                    })
            }
        },
        watch: {
            '$store.state.StandardForm.formsOrder': function() {
                this.isLoaded = false
                this.setForm(this.$route.params.form_id)
            },
            '$route.params.form_id': function() {
                this.init()
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import 'froala-editor/css/froala_editor.pkgd.min.css';
    @import 'froala-editor/css/froala_style.min.css';

    #bodyEditor {
        min-height: 500px !important;
    }
    .fr-box.fr-basic.fr-top .fr-wrapper {
        height: 400px;
        overflow: auto;
    }
    .footerText .fr-box.fr-basic.fr-top .fr-wrapper {
        height: 150px;
        overflow: auto;
    }
    .info-line-bottom label {
        width: 90%;
    }
    .entry {
        background: #E5E5E5;
    }
    .info-bottom label {
        font-weight: 600;
    }
    #standardsSignature .modal-dialog {
        width: 500px !important;
    }
</style>
