import {HTTP, BASE_URL} from '../services/http-common'

export default {
  state: {
    rubricators: null
  },
  mutations: {
    setRubricators(state, payload) {
      state.rubricators = payload
    }
  },
  actions: {
    setRubricators({commit}, payload) {
      commit('setLoading', true)

      HTTP.post(`${BASE_URL}/api/v1/rubricators/`, {
        period: {
          from: payload.from,
          to: payload.to
        }
      })
      .then(res => {
        if (res.data) {
          commit('setRubricators', res.data)
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
    rubricators(state) {
      return state.rubricators
    }
  }
}
