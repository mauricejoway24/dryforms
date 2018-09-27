import axios from 'axios'

const projectCallReportsResource = '/api/project/{project_id}/call-report'

export default {
    index (projectId, data) {
        let resource = projectCallReportsResource.replace('{project_id}', projectId)
        return axios.get(resource, {params: data})
    },
    store (data) {
        return axios.post(projectCallReportsResource, data)
    },
    update (id, data) {
        return axios.patch(projectCallReportsResource + '/' + id, data)
    },
    show(id) {
        return axios.get(projectCallReportsResource + '/' + id)
    },
    delete(id) {
        return axios.delete(projectCallReportsResource + '/' + id)
    }
}
