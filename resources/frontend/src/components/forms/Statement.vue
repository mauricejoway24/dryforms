<template>
    <div class="text-left">
        <template v-if="!showCheckboxes">
            <div v-for="item in statements" :key="item.id">
                <span v-if="item.title">{{ item.title }}</span>
                <div class="m-3" v-html="item.statement">
                </div>
                <hr>
            </div>
        </template>
        <template v-else>
            <div v-for="item in statements" :key="item.id">
                <h6 class="mt-3">
                    <input type="radio" name="selectStatement" :checked="item.checked" :value="item.id" @change="select(item.id)"/>
                    <span v-if="statements.length > 1 && item.title">{{ item.title }}</span>
                </h6>
                <div class="m-3" v-html="item.statement">
                </div>
                <hr>
            </div>
        </template>
    </div>
</template>

<script type="text/babel">
    import ErrorHandler from '../../mixins/error-handler'
    import apiProjectStatement from '../../api/project_statements'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'
    import _ from 'lodash'

    export default {
        mixins: [ErrorHandler],
        name: 'statement',
        props: {
            statements: {
                type: Array,
                required: true
            },
            showCheckboxes: {
                type: Boolean,
                required: false,
                default() {
                    return false
                }
            }
        },
        data() {
            return {
                project_id: null,
                form_id: null,
                selected: null,
                config: {
                    key: this.$config.get('froala_key'),
                    events: {
                        'froalaEditor.initialized': function () {
                        }
                    }
                }
            }
        },
        methods: {
            updateStatements: _.debounce(function(statementInfo) {
                apiProjectStatement.update(statementInfo.id, statementInfo)
                .then(response => {
                }).catch(this.handleErrorResponse)
            }, 500),
            select(id) {
                this.project_id = this.$route.params.project_id
                this.form_id = this.$route.params.form_id
                apiProjectStatement.check({
                    form_id: this.form_id,
                    project_id: this.project_id,
                    selected_id: id
                }).then(response => {
                }).catch(this.handleErrorResponse)
            }
        },
        watch: {
            '$route' (to, from) {
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
    #footerEditor {
        height: 150px !important;
    }
    .fr-box.fr-basic.fr-top .fr-wrapper {
        height: 400px;
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
</style>
