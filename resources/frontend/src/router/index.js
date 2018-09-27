import Vue from 'vue'
import VueRouter from 'vue-router'
import Dashboard from '@/components/main/Dashboard'
import Login from '@/components/auth/Login'
import Register from '@/components/auth/Register'
import CreditCard from '@/components/auth/CreditCard'

import settings from './settings'
import equipment from './equipment'
import projects from './projects'
import standards from './standards'
import forms from './forms'
import training from './training'

Vue.use(VueRouter)

const router = new VueRouter({
    routes: [
        {
            path: '/',
            name: 'Dashboard',
            component: Dashboard
        },
        {
            path: '/login',
            name: 'Login',
            component: Login
        },
        {
            path: '/register',
            name: 'Register',
            component: Register
        },
        {
            path: '/company/credit-card',
            name: 'CreditCard Register',
            component: CreditCard
        },
        ...projects(),
        ...settings(),
        ...equipment(),
        ...standards(),
        ...forms(),
        ...training()
    ]
})

export default router
