<template>
    <div class="card text-center registration">
        <form data-vv-scope="register-user" @submit.prevent="register">
            <div class="card-header">
                <h4>Sign Up</h4>
            </div>
            <div class="card-body text-left">
                <b-row align-h="center">
                    <b-col cols="3">
                        <button class="social-sign facebook" @click="fbLogin()">Log in with Facebook</button>
                    </b-col>
                    <b-col cols="3">
                        <button class="social-sign google" @click="gLogin()">Log in with Google+</button>
                    </b-col>
                </b-row>
                <div class="form text-right mt-3">
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">First Name</label>
                        <input type="text" class="form-control col-md-6" v-model="user.first_name"
                               placeholder="Input First name"
                               name="firstName" :class="{'is-invalid': errors.has('firstName')}" v-validate
                               data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Last Name</label>
                        <input type="text" class="form-control col-md-6" v-model="user.last_name"
                               placeholder="Input Last name"
                               name="lastName" :class="{'is-invalid': errors.has('lastName')}" v-validate
                               data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Street Address</label>
                        <input type="text" class="form-control col-md-6" v-model="user.address"
                               placeholder="Input Street Address"
                               name="address" :class="{'is-invalid': errors.has('address')}" v-validate
                               data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">City</label>
                        <input type="text" class="form-control col-md-6" v-model="user.city" placeholder="Input City"
                               name="city"
                               :class="{'is-invalid': errors.has('city')}" v-validate data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">State:</label>
                        <select class="form-control col-md-6" v-model="user.state"
                                :class="{'is-invalid': !user.state && !firstTouchState}"
                                @change="firstTouchState = false">
                            <option :value="null">-- Please select --</option>
                            <option v-for="item in states" v-bind:key="item.value" :value="item.value">{{ item.text }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Zip Code</label>
                        <input type="text" class="form-control col-md-6" v-model="user.zip" placeholder="Input Zip Code"
                               name="zip"
                               :class="{'is-invalid': errors.has('zip')}" v-validate data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Phone</label>
                        <masked-input class="form-control col-md-6" v-model="user.phone" mask="(111) 111-1111"
                                      :class="{'is-invalid': !user.phone && firstTouchPhone>=3}"
                                      @input="firstTouchPhone++"
                                      placeholder="(702) 555-1212"/>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Email address</label>
                        <input type="email" class="form-control col-md-6" placeholder="Enter email" v-model="user.email"
                               name="email" :class="{'is-invalid': errors.has('email')}" v-validate
                               data-vv-rules="required">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Password</label>
                        <input type="password" class="form-control col-md-6" placeholder="Password"
                               v-model="user.password"
                               name="password" :class="{'is-invalid': errors.has('password')}" v-validate
                               data-vv-rules="required|min:8">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Password Confirmation</label>
                        <input type="password" class="form-control col-md-6" placeholder="Password, Again"
                               v-model="user.password_confirmation" name="password_confirmation"
                               :class="{'is-invalid': errors.has('password_confirmation')}" v-validate
                               data-vv-rules="required|confirmed:password|min:6">
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Coupon</label>
                        <input type="text" class="form-control col-md-6" v-model="user.coupon">
                    </div>
                    <div class="text-center">
                        <h6>Payment information</h6>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Card Number</label>
                        <card-number class='stripe-element card-number col-md-6 p-0'
                                     :class="{'is-invalid': user.cardnumber === false}"
                                     ref='cardNumber'
                                     :stripe='stripe'
                                     :options='stripeOptions'
                                     @change="change($event, 'cardnumber')"
                        />
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Expiration Date</label>
                        <card-expiry class='stripe-element card-expiry col-md-3 p-0'
                                     :class="{'is-invalid': user.cardexpiry === false}"
                                     ref='cardExpiry'
                                     :stripe='stripe'
                                     :options='stripeOptions'
                                     @change="change($event, 'cardexpiry')"
                        />
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">CVC</label>
                        <card-cvc class='stripe-element card-cvc col-md-2 p-0'
                                  :class="{'is-invalid': user.cardcvc === false}"
                                  ref='cardCvc'
                                  :stripe='stripe'
                                  :options='stripeOptions'
                                  @change="change($event, 'cardcvc')"
                        />
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">Postal Code</label>
                        <postal-code class='stripe-element postal-code col-md-2 p-0'
                                     :class="{'is-invalid': user.postalcode === false}"
                                     ref='postalCode'
                                     :stripe='stripe'
                                     :options='stripeOptions'
                                     @change="change($event, 'postalcode')"
                        />
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <b-checkbox v-model="agreement">I have read and agree to the <a href="#">terms and
                                conditions</a> of
                                DryFormsPlus.com
                            </b-checkbox>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <button type="submit" class="btn btn-primary" :disabled="!agreement">Subscribe</button>
            </div>
        </form>

    </div>
</template>

<script type="text/babel">
    import authorization from '../../mixins/authorization'
    import MaskedInput from 'vue-masked-input'
    import {CardNumber, CardExpiry, CardCvc, PostalCode} from 'vue-stripe-elements-plus'

    export default {
        name: 'Register',
        components: {
            MaskedInput,
            CardNumber,
            CardExpiry,
            CardCvc,
            PostalCode
        },
        mixins: [authorization],
        data() {
            return {
                states: null,
                firstTouchState: true,
                firstTouchPhone: 0,
                agreement: false,
                clicked: false,
                user: {
                    first_name: null,
                    state: null,
                    cardnumber: null,
                    cardexpiry: null,
                    cardcvc: null,
                    postalcode: null,
                    phone: null
                },
                complete: false,
                cardError: {
                    cardnumber: '',
                    cardexpiry: '',
                    cardcvc: '',
                    postalcode: ''
                },
                stripe: '',
                stripeOptions: {
                    style: {
                        base: {
                            color: '#32325d',
                            lineHeight: '18px',
                            fontFamily: '"Raleway", Helvetica, sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '16px',
                            '::placeholder': {
                                color: '#aab7c4'
                            }
                        },
                        invalid: {
                            color: '#dc3545',
                            iconColor: '#dc3545'
                        },
                        complete: {
                            color: 'green'
                        }
                    }
                }
            }
        },
        created() {
            this.stripe = this.$config.get('stripe_key')
        },
        mounted() {
            let jsonStates = this.$config.get('states')
            this.states = Object.keys(jsonStates).map(function (key) {
                return {
                    value: key,
                    text: jsonStates[key]
                }
            })
        },
        methods: {
            fbLogin() {
            },
            gLogin() {
            },
            change(event, label) {
                this.user[label] = event.complete
                this.cardError[label] = event.error ? event.error.message : ''
                this.complete = this.user.cardnumber && this.user.cardexpiry && this.user.cardcvc && this.user.postalcode
            }
        }
    }
</script>

<style type="text/css" lang="scss" rel="stylesheet/scss">
    .registration {
        .social-sign {
            width: 100%;
            border: none;
            border-radius: 2px;
            font-weight: 500;
            color: white;
            padding: 5px;
            cursor: pointer;
            transition: .2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
            &:hover, &:focus {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
                transition: .2s ease;
            }
            &:active {
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.4);
                transition: .2s ease;
            }
        }
        .facebook {
            background-color: #32508E;
        }
        .google {
            background-color: #DD4B39
        }

        .is-invalid {
            .StripeElement {
                border-color: #dc3545;
            }
        }
        .StripeElement {
            background-color: white;
            height: 38px;
            padding: 9.5px 8px;
            border-radius: 0.25rem;
            border: 1px solid #ced4da;
            -webkit-transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .StripeElement--focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .StripeElement--invalid {
            border-color: #dc3545;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        .card-err {
            color: #dc3545;
        }

        label {
            padding: 6px 12px !important;

            .custom-control-description {
                padding-left: 9px;
                margin-top: -6px;
            }
        }
    }
</style>
