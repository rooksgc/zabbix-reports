import {HTTP, BASE_URL} from '../services/http-common'

export default {
  state: {
    services: null
  },
  mutations: {
    setServices(state, payload) {
      state.services = payload
    }
  },
  actions: {
    setServices({commit}, payload) {
      commit('setLoading', true)

      HTTP.post(`${BASE_URL}/api/v1/services/`, {
        period: {
          from: payload.from,
          to: payload.to
        }
      })
        .then(res => {
          if (res.data) {
            commit('setServices', res.data)
            commit('setLoading', false)
          }
        })
        .catch(err => {
          commit('setMessage', {
            type: 'error',
            text: err.message
          })
          commit('setLoading', false)
        })
    }
  },
  getters: {
    services(state) {
      return state.services
    }
  }
}
