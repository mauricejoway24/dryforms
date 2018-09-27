import axios from 'axios'

const psychometricResource = '/api/project/psychometric'
const psychometricUpdateTime = '/api/project/psychometric/update-time'
const psychometricUpdateMeasurements = '/api/project/psychometric/update-measurements'

export default {
    show (id) {
        return axios.get(psychometricResource + '/' + id)
    },
    store (data) {
        return axios.post(psychometricResource, data)
    },
    updateMeasurements (id, data) {
        return axios.patch(psychometricUpdateMeasurements + '/' + id, data)
    },
    update (id, data) {
        return axios.patch(psychometricResource + '/' + id, data)
    },
    delete(id) {
        return axios.delete(psychometricResource + '/' + id)
    },
    deleteDay(id) {
        return axios.delete(psychometricResource + '/destroy-day/' + id)
    },
    updateTime(data) {
        return axios.post(psychometricUpdateTime, data)
    }
}
