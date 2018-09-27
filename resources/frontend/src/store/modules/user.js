import axios from 'axios'

const state = {
    user: {},
    company: {},
    companyDetails: {},
    isSubscribed: false,
    isGracePeriod: false
}

const actions = {
    fetchUser({dispatch, commit}) {
        axios.get('/api/account').then(response => {
            commit('setUser', response.data.user)
            commit('setCompany', response.data.company)
            commit('setSubscription', {
                isSubscribed: response.data.isSubscribed,
                isGracePeriod: response.data.isGracePeriod
            })
        })
    },
    setCompany({commit, state}, company) {
        commit('setCompany', company)
    },
    setCompanyDetails({commit, state}, companyDetails) {
        commit('setCompanyDetails', companyDetails)
    }
}

const mutations = {
    setCompany(state, company) {
        state.company = company || {state_id: null}
    },
    setCompanyDetails(state, companyDetails) {
        state.companyDetails = companyDetails
    },
    setUser(state, user) {
        state.user = user
    },
    setSubscription(state, data) {
        state.isSubscribed = data.isSubscribed
        state.isGracePeriod = data.isGracePeriod
    }
}

const getters = {
    companyId: state => {
        return state.company.id
    },
    userId: state => {
        return state.user.id
    },
    company: state => {
        return state.company
    },
    companyDetails: state => {
        return state.companyDetails
    },
    user: state => {
        return state.user
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
