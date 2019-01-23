import {HTTP, BASE_URL} from '../services/http-common'

export default {
  state: {
    portals: null
  },
  mutations: {
    setPortals(state, payload) {
      state.portals = payload
    }
  },
  actions: {
    setPortals({commit}, payload) {
      commit('setLoading', true)

      HTTP.post(`${BASE_URL}/api/v1/portals/`, {
        period: {
          from: payload.from,
          to: payload.to
        }
      })
      .then(res => {
        if (res.data) {
          commit('setPortals', res.data)
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
    portals(state) {
      return state.portals
    }
  }
}
