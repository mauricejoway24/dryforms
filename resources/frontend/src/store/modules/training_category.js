import apiTrainingCategories from '../../api/training_categories'

const state = {
    trainingCategories: null
}

const getters = {
    trainingCategoryPerID(state) {
        return function(categoryID) {
            return state.trainingCategories.filter(category => {
                return category.id === parseInt(categoryID)
            })
        }
    }
}

const actions = {

    fetchTrainingCategory ({dispatch, commit}) {
        apiTrainingCategories.index().then((response) => {
            commit('setTrainingCategories', response.data)
        })
    }
}

const mutations = {
    setTrainingCategories (state, trainingCategories) {
        state.trainingCategories = trainingCategories
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
