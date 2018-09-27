import axios from 'axios'

const moistureResource = '/api/project/moisture'
const moistureResourceDate = '/api/project/moisture/date'

export default {
    index (data) {
        return axios.get(moistureResource, {params: data})
    },
    store (data) {
        return axios.post(moistureResource, data)
    },
    updateDate (id, data) {
        return axios.patch(moistureResourceDate + '/' + id, data)
    },
    addDates(data) {
        return axios.post(moistureResourceDate, data)
    },
    patch (id, data) {
        return axios.patch(moistureResource + '/' + id, data)
    },
    show(id) {
        return axios.get(moistureResource + '/' + id)
    },
    delete(id) {
        return axios.delete(moistureResource + '/' + id)
    }
}
