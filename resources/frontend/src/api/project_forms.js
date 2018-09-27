import axios from 'axios'

const projectFormsResource = '/api/project/forms'

export default {
    index (data) {
        return axios.get(projectFormsResource, {params: data})
    },
    store (data) {
        return axios.post(projectFormsResource, data)
    },
    patch (id, data) {
        return axios.patch(projectFormsResource + '/' + id, data)
    },
    show(id, data) {
        return axios.get(projectFormsResource + '/' + id, {params: data})
    },
    delete(id) {
        return axios.delete(projectFormsResource + '/' + id)
    },
    print (id, data) {
        return axios.post('/project/print/' + id, data)
    }
}
