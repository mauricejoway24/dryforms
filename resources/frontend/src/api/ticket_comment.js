import axios from 'axios'

const ticketCommentResource = '/api/ticket/comment'

export default {
    index (data) {
        return axios.get(ticketCommentResource, {params: data})
    },
    store (data) {
        return axios.post(ticketCommentResource, data)
    },
    patch (id, data) {
        return axios.patch(ticketCommentResource + '/' + id, data)
    },
    show(id) {
        return axios.get(ticketCommentResource + '/' + id)
    },
    delete(id) {
        return axios.delete(ticketCommentResource + '/' + id)
    }
}
