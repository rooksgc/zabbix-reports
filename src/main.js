import Vue from 'vue'
import App from './App'
import router from './router/index'
import store from './store'
import Vuetify from 'vuetify'
import ru from './i18n/ru'

import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify, {
  lang: {
    locales: { ru },
    current: 'ru'
  }
})

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})

Vue.config.devtools = true
