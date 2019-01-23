export default {
  state: {
    from: null,
    to: null,
    period: null
  },
  mutations: {
    setFrom(state, payload) {
      state.from = payload
    },
    setTo(state, payload) {
      state.to = payload
    },
    setPeriod(state, payload) {
      state.period = payload
    }
  },
  actions: {
    setFrom({commit}, payload) {
      commit('setFrom', payload)
    },
    setTo({commit}, payload) {
      commit('setTo', payload)
    },
    setPeriod({commit}, payload) {
      commit('setPeriod', payload)
    }
  },
  getters: {
    from(state) {
      return state.from
    },
    to(state) {
      return state.to
    },
    period(state) {
      return state.period
    }
  }
}
