<template>
    <div class="card text-center settings-account" v-if="isLoaded">
        <div class="card-header">
            <h5>{{ $route.meta.title }}</h5>
        </div>
        <div class="card-body text-left">
          <b-container class="">
            <form @submit.prevent="validateBeforeSubmit()" data-vv-scope="form-ticket-create">
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" class="form-control form-control-sm" :class="{'is-invalid': errors.has('title')}" v-validate data-vv-rules="required" placeholder="Enter Title" v-model="newTicket.title">
                    <p class="text-danger" v-if="errors.has('title')">{{ errors.first('title') }}</p>
                </div>
                <div class="form-group">
                    <label>Category:</label>
                    <select class="form-control form-control-sm" v-model="newTicket.category" name="category" :class="{'is-invalid': !newTicket.category && !firstTouchCategory}" @change="firstTouchCategory = false">
                        <option :value="null">-- Select Category --</option>
                        <option v-for="category in categories" v-bind:key="category.id" :value="category.id">{{ category.name }}
                        </option>
                    </select>
                    <p class="text-danger" v-if="!newTicket.category && !firstTouchCategory">The category is required.</p>
                </div>
                <div class="form-group">
                    <label>Priority:</label>
                    <select class="form-control form-control-sm" v-model="newTicket.priority" name="priority" :class="{'is-invalid': !newTicket.priority && !firstTouchPriority}" @change="firstTouchPriority = false">
                        <option :value="null">-- Select Priority --</option>
                        <option v-for="priority in priorities" v-bind:key="priority.value" :value="priority.value">{{ priority.text }}
                        </option>
                    </select>
                    <p class="text-danger" v-if="!newTicket.priority && !firstTouchPriority">The priority is required</p>
                </div>
                <div class="form-group">
                    <label>Message:</label>                    
                    <froala :class="{'is-invalid': newTicket.message === ''}" :tag="'textarea'" :config="config" v-model="newTicket.message"></froala>
                    <p class="text-danger" v-if="newTicket.message === ''">The message field is required.</p>
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
    import ErrorHandler from '../../mixins/error-handler'
    import '../../../node_modules/froala-editor/js/froala_editor.pkgd.min'

    export default {
        name: 'CreateTicket',
        mixins: [ErrorHandler],
        components: {
            Loading
        },
        data() {
            return {
                firstTouchCategory: true,
                firstTouchPriority: true,
                isLoaded: false,
                newTicket: {
                    title: '',
                    category: null,
                    priority: null,
                    message: null
                },
                categories: [],
                priorities: [
                    {value: 'low', text: 'Low'},
                    {value: 'medium', text: 'Medium'},
                    {value: 'high', text: 'High'}
                ],
                isPendingSubmit: false,
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
                apiTickets.categories()
                    .then(res => {
                        this.isLoaded = true
                        this.categories = res.data
                    }).catch(this.handleErrorResponse)
            },
            createTicket() {
                this.isPendingSubmit = true
                apiTickets.store(this.newTicket)
                    .then(response => {
                        this.isPendingSubmit = false
                        this.$router.push({
                            name: 'UserTickets'
                        })
                    })
                    .catch(this.handleErrorResponse)
            },
            validateBeforeSubmit() {
                this.newTicket.message = this.newTicket.message ? this.newTicket.message : ''
                this.firstTouchCategory = false
                this.firstTouchPriority = false
                if (!this.newTicket.message || !this.newTicket.category || !this.newTicket.priority) return
                this.errors.clear()
                this.$validator.validateAll()
                if (!this.errors.any()) {
                    this.createTicket()
                }
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
