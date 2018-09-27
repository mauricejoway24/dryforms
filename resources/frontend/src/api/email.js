import axios from 'axios'

const projectEmailSend = '/api/project/email'

export default {
    send (data) {
        return axios.post(projectEmailSend, data)
    }
}
