const md5 = require('nano-md5')
const uuidv4 = require('uuid/v4')
const reportsConfig = require('../../config/reports')

export default {
  state: {
    auth: null
  },
  mutations: {
    setAuth(state, payload) {
      state.auth = payload
    }
  },
  actions: {
    login({commit}, {password, captcha, captchaCode}) {
      commit('closeMessage')
      commit('setLoading', true)

      try {
        const secret = reportsConfig.AUTH_SECRET
        const rightHash = reportsConfig.AUTH_HASH1
        const verifiableHash = md5(password + secret)

        /** Если не dev сервер, отключаем проверку капчи */
        if (!window.webpackHotUpdate) {
          if (md5(captcha) !== captchaCode) {
            commit('setLoading', false)
            throw new Error('Ошибка авторизации: неверный код с картинки')
          }
        }

        if (verifiableHash !== rightHash) {
          commit('setLoading', false)
          throw new Error('Ошибка авторизации: неверный пароль')
        }

        const uuid = uuidv4()
        commit('setAuth', uuid)
        sessionStorage.setItem('zr.auth', uuid)
        commit('setMessage', {
          type: 'success',
          text: 'Добро пожаловать'
        })
        commit('setLoading', false)
      } catch (err) {
        commit('setMessage', {
          type: 'error',
          text: err.message
        })
        commit('setLoading', false)
        throw err
      }
    },
    autoLogin({commit}, payload) {
      commit('setAuth', payload)
    },
    logout({commit}) {
      sessionStorage.removeItem('zr.auth')
      commit('setAuth', null)
      commit('setMessage', {
        type: 'info',
        text: 'Вы вышли из системы'
      })
    }
  },
  getters: {
    user(state) {
      return state.auth
    },
    isLoggedIn(state) {
      return state.auth !== null
    }
  }
}
