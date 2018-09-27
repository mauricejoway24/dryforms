<template>
    <div class="settings-company">
        <loader :is-running="isRunning"></loader>

        <div class="card text-center">
            <cancel-subscription-modal></cancel-subscription-modal>

            <div class="card-header">
                <h5>{{ $route.meta.title }}</h5>
            </div>
            <div class="card-body text-left pt-3 pb-3">
                <b-container class="">
                    <div class="row">
                        <div class="col-lg-6 col-md-12">

                            <div class="form-group logo-preview" v-if="company && company.logo">
                                <img :src="company.public_logo_path" height="90"> <br/>
                                <button class="btn btn-default btn-sm mt-2" @click.prevent="removeImage">Remove Logo</button>
                            </div>
                            <div class="form-group" v-if="company && !company.logo">
                                <vue-dropzone ref="companyLogoUpload" id="dropzone" :options="dropzoneOptions"
                                              v-on:vdropzone-sending="uploadLogo"
                                ></vue-dropzone>
                            </div>
                            <div class="form-group">
                                <label>Company Name:</label>
                                <b-form-input :size="template_size" type="text" v-model="company.name" name="name"
                                              placeholder="Input Company Name"
                                              :class="{'is-invalid': errors.has('name')}" v-validate
                                              data-vv-rules="required"/>
                            </div>
                            <div class="form-group">
                                <label>Street Address:</label>
                                <b-form-input :size="template_size" type="text" v-model="company.street"
                                              name="street" placeholder="Input Street Address"
                                              :class="{'is-invalid': errors.has('street')}" v-validate
                                              data-vv-rules="required"/>
                            </div>
                            <div class="form-group">
                                <label>City:</label>
                                <b-form-input :size="template_size" type="text" v-model="company.city" name="city"
                                              placeholder="Input City" :class="{'is-invalid': errors.has('city')}"
                                              v-validate data-vv-rules="required"/>
                            </div>
                            <div class="form-group">
                                <label>State:</label>
                                <select class="form-control form-control-sm" v-model="company.state"
                                        :class="{'is-invalid': !company.state}">
                                    <option :value="null">-- Please select --</option>
                                    <option v-for="item in states" v-bind:key="item.value" :value="item.value">
                                        {{ item.text }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Zip Code:</label>
                                <b-form-input :size="template_size" type="number" v-model="company.zip" name="zip"
                                              placeholder="Input Zip Code"
                                              :class="{'is-invalid': errors.has('zip')}" v-validate
                                              data-vv-rules="required"/>
                            </div>
                            <div class="form-group">
                                <label>Phone:</label>
                                <masked-input class="form-control form-control-sm" v-model="company.phone"
                                              mask="(111) 111-1111" placeholder="(702) 555-1212" name="phone"
                                              :class="{'is-invalid': !company.phone}" v-validate
                                              data-vv-rules="required"/>
                            </div>
                            <div class="form-group">
                                <label>Email:</label>
                                <b-form-input :size="template_size" type="text" v-model="company.email" name="email"
                                              placeholder="Input Email Address"
                                              :class="{'is-invalid': errors.has('email')}" v-validate
                                              data-vv-rules="required"/>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="form-group row mr-0">
                                <div class="col-lg-12 mb-1 col-xl-6">
                                    <input type="button" class="btn btn-sm btn-primary float-left"
                                           @click.prevent="goToCreditCard"
                                           value="Update Credit Card Info"/>
                                </div>
                                <div class="col-lg-12 mb-1 col-xl-4" v-if="isSubscribed">
                                    <input type="button" class="btn btn-sm btn-danger float-left"
                                           @click.prevent="cancelSubscription"
                                           value="Cancel Subscription"/>
                                </div>
                                <div class="col-lg-12 mb-1 col-xl-4" v-else-if="!isSubscribed && isStripeAssigned">
                                    <input type="button" class="btn btn-sm btn-primary float-left"
                                           @click.prevent="resumeSubscription"
                                           value="Resume Subscription"/>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <input type="button" class="btn btn-sm btn-success float-left"
                                       @click.prevent="dropboxAuth"
                                       :value="!company.dbx_token ? 'Dropbox Authorization' : 'Change Dropbox Account'"/>
                                <div style="clear:both"></div>
                            </div>
                            <div class="form-group">
                                <label>Cloud Link:</label>
                                <b-form-input :size="template_size" type="text" v-model="company.cloud_link"
                                              placeholder="Cloud Link"/>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-primary" @click="update()" :disabled="isRunning">Submit</button>
                </b-container>
            </div>
            <div class="card-footer text-muted">
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    import MaskedInput from 'vue-masked-input'

    import vue2Dropzone from 'vue2-dropzone'

    import Loader from '../layout/Loader'
    import DataProcessMixin from '../../mixins/data-process'
    import ErrorHandlerMixin from '../../mixins/error-handler'
    import apiAccount from '../../api/account'
    import apiCompanies from '../../api/companies'
    import CancelSubscriptionModal from './modals/CancelSubscriptionModal'
    import {mapGetters, mapActions} from 'vuex'
    import {Dropbox} from 'dropbox'

    export default {
        name: 'Company',
        mixins: [DataProcessMixin, ErrorHandlerMixin],
        components: {
            Loader,
            MaskedInput,
            vueDropzone: vue2Dropzone,
            CancelSubscriptionModal
        },
        data() {
            return {
                states: [],
                dropzoneOptions: {
                    url: 'https://httpbin.org/post',
                    thumbnailWidth: 150,
                    maxFilesize: 4,
                    maxFiles: 1,
                    addRemoveLinks: true,
                    dictDefaultMessage: '<i class="fa fa-cloud-upload"></i> Set Company Logo',
                    headers: {
                        'My-Awesome-Header': 'header value'
                    }
                }
            }
        },
        created() {
            let jsonStates = this.$config.get('states')
            this.states = Object.keys(jsonStates).map(function (key) {
                return {
                    value: key,
                    text: jsonStates[key]
                }
            })
            this.$on('continueCancel', payload => {
                this.continueCancel(payload.feedback)
            })
            this.$on('vdropzone-complete', payload => {
                console.log(payload)
            })
        },
        methods: {
            ...mapActions([
                'setCompany',
                'fetchUser'
            ]),
            validateBeforeSubmit() {
                this.$validator.validateAll()
                if (this.errors.any() || !this.company.phone || !this.company.state) {
                    return
                }
                this.update()
            },
            goToCreditCard() {
                this.$router.push({name: 'CreditCard'})
            },
            cancelSubscription() {
                this.$emit('openCancelSubscriptionModal')
            },
            continueCancel(feedback) {
                this.run()
                apiAccount.cancelSubscription({feedback: feedback})
                    .then(response => {
                        this.fetchUser().then(() => {
                            this.$notify({
                                type: 'info',
                                title: response.data.message
                            })
                            this.$emit('data-ready')
                        })
                    })
                    .catch(error => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(error)
                    })
            },
            resumeSubscription() {
                this.run()
                apiAccount.resumeSubscription()
                    .then(response => {
                        this.fetchUser()
                            .then(() => {
                                this.$notify({
                                    type: 'info',
                                    title: response.data.message
                                })
                                this.$emit('data-ready')
                            })
                    })
                    .catch(error => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(error)
                    })
            },
            update() {
                this.run()
                apiCompanies.patch(this.company.id, this.company)
                    .then(response => {
                        this.$emit('data-ready')
                        this.$notify({
                            type: 'info',
                            title: response.data.message
                        })
                    })
                    .catch(error => {
                        this.$emit('data-failed')
                        this.handleErrorResponse(error)
                    })
            },
            setCookie(name, value, hours) {
                let expires = ''
                if (hours) {
                    let date = new Date()
                    date.setTime(date.getTime() + (hours * 60 * 60 * 1000))
                    expires = '; expires=' + date.toUTCString()
                }
                document.cookie = name + '=' + (value || '') + expires + '; path=/'
            },
            dropboxAuth() {
                let dbx = new Dropbox()
                dbx.setClientId(this.$config.get('dropbox_client_id'))
                let url = location.protocol + '//' + location.hostname + (location.port ? ':' + location.port : '')
                let authUrl = dbx.getAuthenticationUrl(url + '/dropbox-auth')
                this.setCookie('apiToken', localStorage.getItem('apiToken'), 1)
                let win = window.open(authUrl, '_blank', 'toolbar=0,location=0,directories=0,status=1,menubar=0,titlebar=0,scrollbars=1,resizable=1,width=' + 600 + ',height=' + 500)
                win.focus()
            },
            uploadLogo(file, xhr, formData) {
                formData.append('logo', file)
                apiCompanies.storeLogo(this.company.id, formData)
                    .then(response => {
                        this.setCompany(response.data.company)
                    })
                    .catch(response => {

                    })
            },
            removeImage() {
                apiCompanies.removeLogo(this.company.id)
                    .then(response => {
                        this.setCompany(response.data.company)
                    })
                    .catch(response => {

                    })
            }
        },
        computed: {
            ...mapGetters([
                'company',
                'user'
            ]),
            isSubscribed() {
                return this.user.isSubscribed
            },
            isGracePeriod() {
                return this.user.isGracePeriod
            },
            isStripeAssigned() {
                return this.user.is_stripe_assigned
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    @import 'vue2-dropzone/dist/vue2Dropzone.min.css';

    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 3px 12px;
        cursor: pointer;
    }

    .logo-preview img {
        max-width: 100%;
        height: auto;
    }

    .vue-dropzone {
        padding: 5px;
        border: 1px solid #ced4da;
        border-radius: 0.2rem;
        cursor: pointer;
    }
</style>
