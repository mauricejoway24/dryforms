<template>
    <div class="settings-reviews">
        <div class="card text-center" v-if="isLoaded">
            <create-review-link-modal></create-review-link-modal>
            <delete-review-link-modal></delete-review-link-modal>

            <div class="card-header">
                <h5>{{ $route.meta.title }}</h5>
                <button class="btn pull-right btn-primary btn-create" @click="openCreateModal()"><i class="fa fa-plus"></i></button>
            </div>
            <div class="card-body text-left pt-3 pb-3">
                <b-table ref="reviewLinkTable" :busy.sync="isBusy" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" :items="getReviewLinks" small striped hover  :fields="fields" :current-page="currentPage" :per-page="perPage" head-variant="">
                  <template slot="action" slot-scope="row">
                    <button class="btn btn-xs btn-default" @click="openEditModal(row.item.id)">
                        <i class="fa fa-pencil"></i> Edit
                    </button>
                    <button class="btn btn-xs btn-danger" @click="openDeleteModal(row.item.id)">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                  </template>
                  <template slot="activate" slot-scope="row">
                      <input type="checkbox" v-model="row.item.activate" true-value=1 false-value=0 :disabled="activateCount>=3 && row.item.activate==0" @change="saveLink(row.item)">
                  </template>
                </b-table>
                <div class="justify-content-center row-margin-tweak row">
                  <b-pagination v-if="!isBusy" :size="template_size" :total-rows="count" :per-page="perPage" limit="5" v-model="currentPage" />
                  <div v-else>...</div>
                </div>
                <div class="row mr-5 ml-5 justify-content-start mt-4">
                    <div class="col-md-12 text-center">
                        <p> <strong>Review Request Message </strong> </p>
                    </div>
                    <div class="col-md-12">
                        <froala :tag="'textarea'" :config="config" v-model="reviewRequestMessage.message" @input="changeReviewRequestMessage"></froala>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
            </div>
        </div>
        <loading v-else></loading>
    </div>
</template>

<script type="text/babel">
    import Loading from '../layout/Loading'
    import apiReviewLinks from '../../api/review_links'
    import apiReviewRequestMessage from '../../api/review_request_message'
    import CreateReviewLinkModal from './modals/CreateReviewLinkModal'
    import DeleteReviewLinkModal from './modals/DeleteReviewLinkModal'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'
    import _ from 'lodash'

    export default {
        name: 'Reviews',
        components: {Loading, CreateReviewLinkModal, DeleteReviewLinkModal},
        data() {
            return {
                isLoaded: false,
                isBusy: false,
                currentPage: 1,
                perPage: 10,
                count: 0,
                activateCount: 0,
                sortBy: '',
                sortDesc: false,
                fields: {
                  url: {
                    label: 'Review Link',
                    sortable: true,
                    'class': 'text-center field-url'
                  },
                  activate: {
                    label: 'State',
                    sortable: false,
                    'class': 'text-center field-activate'
                  },
                  action: {
                    label: 'Actions',
                    sortable: false,
                    'class': 'text-center field-act'
                  }
                },
                reviewLinks: [],
                reviewRequestMessage: {
                    company_id: null,
                    message: ''
                },
                config: {
                    key: this.$config.get('froala_key'),
                    events: {
                        'froalaEditor.initialized': function () {
                            console.log('initialized')
                        }
                    }
                }
            }
        },
        created() {
            this.$nextTick(() => {
                this.initData()
            })
            this.$on('reloadData', () => {
                this.initData()
                this.$refs.reviewLinkTable.refresh()
            })
        },
        methods: {
            initData() {
                const apis = [
                    apiReviewLinks.index({page: 1}),
                    apiReviewRequestMessage.index()
                ]
                return Promise.all(apis)
                    .then(response => {
                        this.count = response[0].data.reviewLinks.total
                        this.activateCount = response[0].data.activateCount
                        this.reviewRequestMessage = response[1].data
                        this.isLoaded = true
                        return response
                    })
            },
            getReviewLinks(ctx) {
                let data = {
                  page: ctx.currentPage,
                  per_page: ctx.perPage
                }
                if (ctx.sortBy) {
                    data.sort_by = ctx.sortBy
                    data.sort_type = ctx.sortDesc ? 'desc' : 'asc'
                }
                return apiReviewLinks.index(data)
                    .then(response => {
                        this.reviewLinks = response.data.reviewLinks.data || []
                        this.count = response.data.reviewLinks.total
                        this.activateCount = response.data.activateCount
                        return this.reviewLinks
                    })
            },
            openCreateModal() {
                this.$emit('openCreateModal', {
                    activateCount: this.activateCount
                })
            },
            openEditModal(id) {
                this.$emit('openEditModal', {
                    id: id,
                    activateCount: this.activateCount
                })
            },
            openDeleteModal(id) {
                this.$emit('openDeleteModal', {
                    id: id
                })
            },
            saveLink(item) {
                apiReviewLinks.patch(item.id, item)
                    .then(response => {
                        this.$refs.reviewLinkTable.refresh()
                    })
                    .catch(error => {
                        console.log(error)
                    })
            },
            changeReviewRequestMessage: _.debounce(function () {
                apiReviewRequestMessage
                    .patch(this.reviewRequestMessage.company_id, this.reviewRequestMessage)
                    .then(response => {
                    })
            }, 500)
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import 'froala-editor/css/froala_editor.pkgd.min.css';
    @import 'froala-editor/css/froala_style.min.css';
    .settings-reviews {
        .card-body {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: .25rem;
        }
        .card-header {
            position: relative;
            .btn-create {
                position: absolute;
                top: 10px;
                bottom: 10px;
                right: 3px;
                font-size: 15px;
            }
        }
        .field-url {
            width: 60%;
        }
        .field-activate {
            width: 15%;
        }
        .field-act {
            width: 25%;
        }

        #bodyEditor {
            min-height: 500px !important;
        }
        #footerEditor {
            height: 150px !important;
        }
        .fr-box.fr-basic.fr-top .fr-wrapper {
            height: 300px;
            overflow: auto;
        }
    }
</style>
