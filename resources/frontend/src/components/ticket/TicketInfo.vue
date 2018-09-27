<template>
    <div class="card text-center settings-account" v-if="isLoaded">
        <div class="card-header">
            <h5>#{{ ticketID }} - {{ticket.title ? ticket.title : ""}}</h5>
        </div>
        <div class="card-body text-left">
            <b-container class="">
                <p class="card-text" v-html="ticket.message">
                </p>
                <p class="card-text">
                    <label> Category: </label> {{ticket.ticket_category.name}}
                </p>
                <p class="card-text">
                    <label> Status: </label> 
                    <span class="label text-success" v-if="ticket.status === 'Open'">{{ticket.status}}</span>
                    <span class="label text-danger" v-else>{{ticket.status}}</span>
                </p>
                <p class="card-text">
                    <label> Created on: </label> {{ticket.diffTime}}
                </p>
                <hr>
                <b-card v-for="comment in ticket.comments" v-bind:key="comment.id"
                        tag="article"
                        :class="{'text-success': ticket.user_id !== comment.user_id}"
                        class="mb-2">
                    <div slot="header">
                        <strong> {{comment.user.first_name + ' ' + comment.user.last_name}} </strong>
                        <span class="pull-right">{{ comment.created_at }}</span>
                    </div>
                    <p class="card-text" v-html="comment.comment">
                    </p>
                </b-card>
                <form @submit.prevent="validateBeforeSubmit()" v-if="ticket.status === 'Open'">
                    <div class="form-group">
                        <froala :class="{'is-invalid': comment === ''}" :tag="'textarea'" :config="config" v-model="comment"></froala>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary pull-right" :disabled="isPendingSubmit">Submit</button>
                </form>
            </b-container>
        </div>
        <div class="card-footer text-muted">
        </div>
    </div>
    <loading v-else></loading>
</template>

<script>
    import Loading from '../layout/Loading'
    import apiTickets from '../../api/tickets'
    import apiTicketComment from '../../api/ticket_comment'
    import ErrorHandler from '../../mixins/error-handler'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'

    export default {
        name: 'TicketInfo',
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        data() {
            return {
                isLoaded: false,
                ticketID: this.$route.params.ticket_id,
                ticket: null,
                isPendingSubmit: false,
                comment: '',
                isFocused: false,
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
            this.init()
        },
        methods: {
            init() {
                apiTickets.show(this.ticketID)
                    .then(res => {
                        this.isLoaded = true
                        this.ticket = res.data
                        this.isPendingSubmit = false
                    }).catch(this.handleErrorResponse)
            },
            sendComment() {
                this.isPendingSubmit = true
                apiTicketComment.store({
                    ticket_id: this.ticket.id,
                    comment: this.comment
                })
                    .then(res => {
                        this.comment = ''
                        this.init()
                    }).catch(this.handleErrorResponse)
            },
            validateBeforeSubmit() {
                this.comment = this.comment ? this.comment : ''
                if (this.comment === '') {
                    this.$notify({
                        type: 'error',
                        text: 'The comment field is required.'
                    })
                    return
                }
                this.sendComment()
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
