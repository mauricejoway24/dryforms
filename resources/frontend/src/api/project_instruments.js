import axios from 'axios'

const projectInstrumentsResource = '/api/project/instrument'

export default {
    update(id, data) {
        return axios.patch(projectInstrumentsResource + '/' + id, data)
    },
    show(id) {
        return axios.get(projectInstrumentsResource + '/' + id)
    }
}
