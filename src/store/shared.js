export default {
  state: {
    loading: false,
    message: null
  },
  mutations: {
    setLoading(state, payload) {
      state.loading = payload
    },
    setMessage(state, payload) {
      state.message = payload
    },
    closeMessage(state) {
      state.message = null
    }
  },
  actions: {
    setLoading({commit}, payload) {
      commit('setLoading', payload)
    },
    setMessage({commit}, payload) {
      commit('setMessage', payload)
    },
    closeMessage({commit}) {
      commit('closeMessage')
    }
  },
  getters: {
    loading(state) {
      return state.loading
    },
    message(state) {
      return state.message
    }
  }
}
