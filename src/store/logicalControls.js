import {HTTP, BASE_URL} from '../services/http-common'

export default {
  state: {
    logicalControls: null
  },
  mutations: {
    setLogicalControls(state, payload) {
      state.logicalControls = payload
    }
  },
  actions: {
    setLogicalControls({commit}, payload) {
      commit('setLoading', true)

      HTTP.post(`${BASE_URL}/api/v1/logical-controls/`, {
        period: {
          from: payload.from,
          to: payload.to
        }
      })
        .then(res => {
          if (res.data) {
            commit('setLogicalControls', res.data)
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
    },
    fetchLogicalControlsToday({commit, getters, dispatch}) {
      commit('setLoading', true)

      HTTP.get(`${BASE_URL}/api/v1/logical-controls/update/`)
        .then(_ => {
          dispatch('setLogicalControls', {
            from: getters.from,
            to: getters.to
          })
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
    logicalControls(state) {
      return state.logicalControls
    }
  }
}
