import Vuex from 'vuex'
import Vue from 'vue'

import auth from './auth'
import frontend from './frontend'
import plugins from './plugins'

Vue.use(Vuex)
export default new Vuex.Store({
  modules: { auth, frontend },
  plugins: plugins,
  strict: process.env.NODE_ENV !== 'production'
})
