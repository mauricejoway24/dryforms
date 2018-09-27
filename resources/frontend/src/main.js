// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import VueConfig from 'vue-configuration'
import BootstrapVue from 'bootstrap-vue'
import axios from 'axios'
import VueSession from 'vue-session'
import Notifications from 'vue-notification'
import vueSignature from 'vue-signature'
import lodash from 'lodash'
import VueLodash from 'vue-lodash'
import App from './components/layout/Main'
import router from './router'
import store from './store'
import VeeValidate from 'vee-validate'
import globalMixin from './mixins/global-mixin'
import appConfig from './config/app'
import FullCalendar from 'vue-full-calendar'
import VueFroala from 'vue-froala-wysiwyg'
import moment from 'moment'
import VueMomentJS from 'vue-momentjs'
import VueYouTubeEmbed from 'vue-youtube-embed'
import VueLocalStorage from 'vue-localstorage'

Vue.config.productionTip = false
Vue.config.devtools = true

Vue.use(VueConfig, {
    config: appConfig
})
Vue.use(BootstrapVue)
Vue.use(VueSession)
Vue.use(Notifications)
Vue.use(VueLodash, lodash)
Vue.use(VeeValidate)
Vue.use(FullCalendar)
Vue.use(VueFroala)
Vue.use(vueSignature)
Vue.use(VueMomentJS, moment)
Vue.mixin(globalMixin)
Vue.use(VueYouTubeEmbed)
Vue.use(VueLocalStorage)

const bus = new Vue()
Vue.prototype.$bus = bus

axios.interceptors.request.use(config => {
    config.headers['Authorization'] = 'Bearer ' + Vue.localStorage.get('apiToken')
    return config
}, error => {
    return Promise.reject(error)
})

axios.interceptors.response.use(response => {
    return response
}, error => {
    let res = error.response
    let tokenVerifyErrors = [
        'Token Signature could not be verified.',
        'Token has expired',
        'Server Error'
    ]

    if (error.response.status === 422) {
        return Promise.reject(res)
    }

    if (error.response.status === 401 && error.response.statusText === 'Unauthorized' && vue.$route.name !== 'Login' && tokenVerifyErrors.indexOf(res.data.message.trim()) <= -1) {
        axios.defaults.headers.common['Authorization'] = null
        Vue.prototype.$session.destroy()
        Vue.localStorage.remove('apiToken')
        vue.$router.push('/')
        return Promise.reject(res)
    }
    let lastRefresh = Vue.prototype.$session.get('lastRefresh')

    if ((tokenVerifyErrors.indexOf(res.data.message.trim()) > -1) && Vue.localStorage.get('apiToken') && res.config && !res.config.__isRetryRequest) {
        if (!lastRefresh || new Date().getTime() - lastRefresh > 1000 * 60 * 5) {
            return new Promise((resolve, reject) => {
                Vue.prototype.$session.set('lastRefresh', new Date().getTime())
                axios.get('/api/refresh')
                    .then(response => {
                        Vue.localStorage.remove('apiToken')
                        Vue.localStorage.set('apiToken', response.data.token)
                        error.config.__isRetryRequest = true
                        error.config.headers['Authorization'] = 'Bearer ' + response.data.token
                        resolve(axios(error.config))
                    })
                    .catch(err => {
                        // Vue.prototype.$session.remove('apiToken')
                        axios.defaults.headers.common['Authorization'] = null
                        Vue.prototype.$session.destroy()
                        Vue.localStorage.remove('apiToken')
                        vue.$router.push('/login')
                        return reject(err)
                    })
            })
        } else {
            error.config.__isRetryRequest = false
            error.config.headers['Authorization'] = 'Bearer ' + Vue.localStorage.get('apiToken')
            return Promise.resolve(axios(error.config))
        }
    }
    if (res.data.message === 'The token has been blacklisted') {
        axios.defaults.headers.common['Authorization'] = null
        Vue.prototype.$session.destroy()
        Vue.localStorage.remove('apiToken')
        vue.$router.push('/login')
        return Promise.reject(res)
    }
    return Promise.reject(res)
})

/* eslint-disable no-new */
const vue = new Vue({
    el: '#app',
    router,
    store,
    template: '<App/>',
    components: {App},
    http: {
        root: '/root',
        headers: {
            Authorization: 'Basic YXBpOnBhc3N3b3Jk'
        }
    }
})
