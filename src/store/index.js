import Vue from 'vue'
import Vuex from 'vuex'

// Store modules
import shared from './shared'
import datepicker from './datepicker'
import user from './user'
import portals from './portals'
import rubricators from './rubricators'
import services from './services'
import logicalControls from './logicalControls'

Vue.use(Vuex)

Vue.config.devtools = true

export default new Vuex.Store({
  modules: {
    shared,
    datepicker,
    user,
    portals,
    rubricators,
    services,
    logicalControls
  }
})
