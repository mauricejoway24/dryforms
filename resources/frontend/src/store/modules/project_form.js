import apiProjectCallReports from '../../api/project_call_reports'
import apiProjectForms from '../../api/project_forms'

const state = {
    projectForms: [],
    projectId: null,
    callReport: {}
}

const getters = {
    projectFormPerID(state) {
        return function(formID) {
            return state.projectForms.filter(form => {
                return form.form_id === parseInt(formID)
            })
        }
    },
    callReport() {
        return state.callReport
    }
}

const actions = {
    fetchCallReport({dispatch, commit}) {
        apiProjectCallReports.index(this.state.ProjectForm.projectId, {})
            .then(response => {
                commit('setCallReport', response.data.call_report)
            })
    },
    fetchProjectForm({dispatch, commit}) {
        apiProjectForms.index({
            project_id: this.state.ProjectForm.projectId
        }).then((response) => {
            if (response.data.length > 0) {
                commit('setProjectForms', response.data)
            }
        })
    }
}

const mutations = {
    setCallReport (state, callReport) {
        state.callReport = callReport
    },
    setProjectForms (state, projectForms) {
        state.projectForms = projectForms
    }
}

export default {
    state,
    getters,
    mutations,
    actions
}
