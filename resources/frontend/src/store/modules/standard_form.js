import apiFormsOrder from '../../api/forms_order'

const state = {
    formsOrder: [],
    projectSidebar: []
}

const getters = {
    formPerID(state) {
        return function(formID) {
            return state.formsOrder.filter(form => {
                return form.form_id === parseInt(formID)
            })
        }
    },
    formsOrder(state) {
        return state.formsOrder
    },
    projectSidebar(state) {
        return state.projectSidebar
    }
}

const actions = {
    fetchFormsOrder ({dispatch, commit}) {
        apiFormsOrder.index().then(response => {
            commit('setFormsOrder', response.data)
        })
    },
    fetchProjectSidebar({dispatch, commit}, projectId) {
        apiFormsOrder.index({type: 'project', project_id: projectId}).then(response => {
            commit('setProjectSidebar', response.data)
        })
    }
}

const mutations = {
    setFormsOrder (state, formsOrder) {
        state.formsOrder = formsOrder
    },
    setProjectSidebar(state, sidebar) {
        state.projectSidebar = sidebar
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
