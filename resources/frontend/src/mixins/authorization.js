import axios from 'axios'
import apiAuth from '../api/auth'
import errorHandler from './error-handler'
import {createToken} from 'vue-stripe-elements-plus'

export default {
    mixins: [errorHandler],
    data() {
        return {}
    },
    methods: {
        logout() {
            apiAuth.logout().then(res => {
                axios.defaults.headers.common['Authorization'] = null
                this.$session.destroy()
                this.$localStorage.remove('apiToken')
                this.$router.push('/login')
                location.reload()
            })
        },
        login() {
            apiAuth.login(this.user)
                .then(response => {
                    this._setToken(response)
                })
                .catch(this.handleErrorResponse)
        },
        register() {
            this.$validator.validateAll()
            this.firstTouchState = false
            if (this.errors.any() || !this.complete || !this.agreement || !this.user.state) {
                if (this.user.cardnumber === null) this.user.cardnumber = false
                if (this.user.cardexpiry === null) this.user.cardexpiry = false
                if (this.user.cardcvc === null) this.user.cardcvc = false
                if (this.user.postalcode === null) this.user.postalcode = false
                return
            }
            createToken().then(data => {
                this.isLoaded = false
                this.$session.start()
                this.user.stripeToken = data.token
                apiAuth.register(this.user)
                    .then(response => {
                        let self = this
                        self.$localStorage.set('apiToken', response.data.token)
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token
                        if (self.$route.query.redirect) {
                            self.$router.push(self.$route.query.redirect)
                        } else {
                            self.$router.push('/')
                        }
                        this.isLoaded = true
                    })
                    .catch(response => {
                        this.$session.destroy()
                        this.isLoaded = true
                        this.handleErrorResponse(response)
                    })
            }).catch(response => {
                this.$session.destroy()
                this.isLoaded = true
                this.handleErrorResponse(response.error)
            })
        },
        _setToken(response) {
            this.$session.start()
            // this.$session.set('apiToken', response.data.token)
            this.$localStorage.set('apiToken', response.data.token)
            axios.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token
            if (this.$route.query.redirect) {
                this.$router.push(this.$route.query.redirect)
            } else {
                this.$router.push('/')
            }
        }
    }
}
