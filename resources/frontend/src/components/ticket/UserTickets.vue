<template>
    <div class="card text-center settings-account">
        <div class="card-header">
            <h5>{{ $route.meta.title }}</h5>
        </div>
        <div class="card-body text-left">
            <b-container class="">
                <b-row align-h="between" class="mr-0 ml-0">
                    <b-col cols="4">
                        <router-link :to="{name: 'CreateTicket'}" style="color: white !important" class="mt-3 pointer text-info btn btn-sm btn-success">
                            Create new ticket
                        </router-link>
                    </b-col>
                    <b-col cols="2">
                        <b-form-group vertical :label-cols="3" label-text-align="left" label="<b>Page Size:</b>"
                                        :label-size="template_size" class="p-0 m-0">
                            <b-form-select :size="template_size" :disabled="isBusy" class="form-control"
                                            :options="pageSizeOption" v-model="perPage">
                            </b-form-select>
                        </b-form-group>
                    </b-col>
                </b-row>
            </b-container>
            <b-table ref="TicketTable" :busy.sync="isBusy" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc"
                        :items="getTickets" small striped hover  :fields="fields"
                        :current-page="currentPage" :per-page="perPage" head-variant=""
                        align-v="center" class="mt-2">
                <template slot="category" slot-scope="row">
                    {{row.item.ticket_category ? row.item.ticket_category.name : 'n/a'}}
                </template>
                <template slot="title" slot-scope="row">
                    <router-link :to="{name: 'TicketInfo', params: {ticket_id: row.item.ticket_id}}" class="pointer text-info">
                        #{{row.item.ticket_id}} - {{row.item.title}}
                    </router-link>
                </template>
                <template slot="status" slot-scope="row">
                    <span class="label text-success" v-if="row.item.status === 'Open'">{{row.item.status}}</span>
                    <span class="label text-danger" v-else>{{row.item.status}}</span>
                </template>
                <template slot="updated_at" slot-scope="row">
                    {{row.item.updated_at}}
                </template>
            </b-table>
            <div class="justify-content-center row-margin-tweak row">
                <b-pagination v-if="!isBusy" :size="template_size" :total-rows="count" :per-page="perPage" limit="5"
                                v-model="currentPage"/>
                <div v-else>...</div>
            </div>
        </div>
        <div class="card-footer text-muted">
        </div>
    </div>
</template>

<script>
    import Loading from '../layout/Loading'
    import apiTickets from '../../api/tickets'
    import ErrorHandler from '../../mixins/error-handler'

    export default {
        name: 'CreateTicket',
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        data() {
            return {
                isLoaded: false,
                tickets: [],
                ticketCategories: [],
                fields: {
                    category: {
                        label: 'Category',
                        'class': 'text-center field-cat',
                        variant: 'green'
                    },
                    title: {
                        label: 'Title',
                        sortable: true,
                        'class': 'text-center field-title'
                    },
                    status: {
                        label: 'Status',
                        sortable: true,
                        'class': 'text-center field-status'
                    },
                    updated_at: {
                        label: 'Last Updated',
                        sortable: true,
                        'class': 'text-center field-updated-at'
                    }
                },
                isBusy: false,
                currentPage: 1,
                perPage: 10,
                count: 0,
                sortBy: '',
                sortDesc: false
            }
        },
        created() {
        },
        methods: {
            getTickets(ctx) {
                let data = {
                    page: ctx.currentPage,
                    sort_by: ctx.sortBy || '',
                    sort_type: (ctx.sortDesc ? 'desc' : 'asc'),
                    // filter: ctx.filter,
                    per_page: ctx.perPage
                }
                return apiTickets.index(data)
                    .then(response => {
                        this.tickets = response.data.tickets.data || []
                        this.count = response.data.tickets.total
                        this.ticketCategories = response.data.ticket_categories
                        return (this.tickets)
                    })
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .field-cat {
        width: 20%;
    }
    .field-title {
        width: 40%;
    }
    .field-status {
        width: 10%;
    }
    .field-updated-at {
        width: 30%;
    }
</style>
