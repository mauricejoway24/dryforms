<template>
    <div>
        <loader :is-running="isRunning"></loader>
        <b-container v-if="!isRunning" class="pt-2">
            <delete-modal></delete-modal>
            <b-row>
                <b-col cols="3" class="text-left">
                    <b-form-select v-model="selectedYear" :options="years" id="select_year" @input="loadProjects()"></b-form-select>
                    <b-form-select v-model="selectedStatus" :options="statuses" class="mt-2" @input="loadProjects()"></b-form-select>
                </b-col>
                <b-col cols="6" class="text-center logo-preview">
                    <img v-if="company.logo" :src="company.logo_public_path" alt="Company Logo"
                         height="90">
                    <img v-else :src="companyLogo" alt="Company Logo" height="90">
                </b-col>
                <b-col cols="3" class="text-right">
                    <input type="text" class="form-control" placeholder="Search..." v-model="filter"
                           @input="filtering()">
                </b-col>
            </b-row>
            <div class="card text-center mt-3">
                <div class="card-header">
                </div>
                <div class="card-body text-left p-0">
                    <b-table ref="projectTable" :items="projects" small :busy.sync="isBusy"
                             striped hover :fields="fields" :current-page="currentPage" :per-page="perPage"
                             :filter="filter" head-variant="">
                        <template slot="insured" slot-scope="row">
                            {{ row.item.owner_name ? row.item.owner_name : 'n/a'}}
                        </template>
                        <template slot="assigned" slot-scope="row">
                            {{ row.item.assignee ? row.item.assignee.name: 'n/a' }}
                        </template>
                        <template slot="status" slot-scope="row">
                            {{ row.item.status_info ? row.item.status_info.name: 'n/a' }}
                        </template>
                        <template slot="createdAt" slot-scope="row">
                            {{ $moment(row.item.created_at).format('MM/DD/YYYY') }}
                        </template>
                        <template slot="actions" slot-scope="row">
                            <div v-if="row.item.status === 3">
                                <button class="btn btn-xs btn-info" @click='restoreProject(row.item.id)'>
                                    <i class="fa fa-undo"></i> Restore
                                </button>
                            </div>
                            <div v-else>
                                <button class="btn btn-xs btn-default" @click='editProject(row.item.id)'>
                                    <i class="fa fa-pencil"></i> Edit
                                </button>
                                <button class="btn btn-xs btn-danger" @click='removeProject(row.item.id)'>
                                    <i class="fa fa-trash"></i> Remove
                                </button>
                            </div>
                        </template>
                    </b-table>
                    <div class="justify-content-center row-margin-tweak row">
                        <b-pagination v-if="!isBusy" :size="template_size" :total-rows="count" :per-page="perPage"
                                      limit="5" v-model="currentPage"/>
                        <div v-else>...</div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                </div>
            </div>
        </b-container>
    </div>
</template>

<script type="text/babel">
    import { mapGetters } from 'vuex'
    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import apiProjects from '../../api/projects'
    import apiProjectStatus from '../../api/project_status'
    import ErrorHandler from '../../mixins/error-handler'
    import DeleteModal from './modals/Remove'

    export default {
        mixins: [ErrorHandler, DataProcessMixin],
        components: {Loader, DeleteModal},
        data() {
            return {
                selectedYear: new Date().getFullYear(),
                selectedStatus: null,
                companyLogo: require('../../assets/fallback-logo.jpg'),
                years: [],
                statuses: [],
                fields: {
                    insured: {
                        label: 'Owner/Insured',
                        sortable: false,
                        'class': 'text-center'
                    },
                    address: {
                        label: 'Address',
                        sortable: false,
                        'class': 'text-center'
                    },
                    phone: {
                        label: 'Phone',
                        sortable: false,
                        'class': 'text-center'
                    },
                    assigned: {
                        label: 'Assigned To',
                        sortable: false,
                        'class': 'text-center'
                    },
                    status: {
                        label: 'Status',
                        sortable: false,
                        'class': 'text-center'
                    },
                    createdAt: {
                        label: 'Created At',
                        sortable: false,
                        'class': 'text-center'
                    },
                    actions: {
                        label: 'Action',
                        sortable: false,
                        'class': 'text-center'
                    }
                },
                filter: '',
                isBusy: false,
                currentPage: 1,
                perPage: 20,
                count: 0,
                projects: null
            }
        },
        created() {
            this.$nextTick(() => {
                this.initData()
            })
            this.$on('reloadData', () => {
                this.initData()
                this.$refs.projectTable.refresh()
            })
        },
        methods: {
            initData() {
                this.run()
                this.years = []
                for (let year = new Date().getFullYear(); year >= 2015; year--) {
                    this.years.push({
                        value: year,
                        text: year
                    })
                }
                const apis = [
                    apiProjects.index(),
                    apiProjectStatus.index()
                ]
                Promise.all(apis)
                    .then(response => {
                        this.projects = response[0].data.data || []
                        this.count = response[0].data.total
                        let statuses = response[1].data.statuses
                        this.statuses = [{
                            text: 'All',
                            value: null
                        }]
                        statuses.forEach(status => {
                            this.statuses.push({
                                text: status.name,
                                value: status.id
                            })
                        })
                        this.$emit('data-ready')
                    }).catch(response => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(response)
                })
            },
            loadProjects() {
                this.isBusy = true
                let data = {
                    page: this.$refs.projectTable.currentPage,
                    filter: this.$refs.projectTable.filter,
                    per_page: this.$refs.projectTable.perPage,
                    status: this.selectedStatus,
                    year: this.selectedYear
                }
                apiProjects.index(data)
                    .then(response => {
                        let items = response.data.data
                        this.count = response.data.total
                        this.projects = items || []
                        this.isBusy = false
                    }).catch(response => {
                        this.isBusy = false
                        this.handleErrorResponse(response)
                })
            },
            editProject(projectId) {
                this.$router.push({
                    name: 'Form Call Report',
                    params: {
                        project_id: projectId,
                        form_id: 1
                    }
                })
            },
            removeProject(projectId) {
                this.$emit('openRemoveModal', {
                    id: projectId
                })
            },
            restoreProject(projectId) {
                apiProjects.restore({
                    project_id: projectId
                })
                    .then(response => {
                        this.$refs.projectTable.refresh()
                    })
            }
        },
        computed: {
            ...mapGetters([
                'company',
                'user'
            ])
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .logo-preview img {
        max-width: 100%;
        height: auto;
        max-height: 100px;
    }
</style>
