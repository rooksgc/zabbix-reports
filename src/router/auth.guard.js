import store from '../store'

/**
 * Защита роутов
 * @param to
 * @param from
 * @param {Function} next - выполнить переход по роуту
 */
export default function (to, from, next) {
  if (store.getters.isLoggedIn) {
    next()
  } else {
    next('/')
  }
}
