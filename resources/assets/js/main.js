// Import System requirements
import Vue from 'vue'

// Import Helpers for filters
import { domain, count, prettyDate, pluralize } from './filters'

// Import Views - Top level
import AppView from './components/App.vue'

// order important!
import store from './store'
import router from './router'

// Import Install and register helper items
// TODO remove
Vue.filter('count', count)
Vue.filter('domain', domain)
Vue.filter('prettyDate', prettyDate)
Vue.filter('pluralize', pluralize)

if (window.localStorage) {
  let localStorage = window.localStorage
  let token = localStorage.getItem('token')
  if (token) {
    store.commit('SET_TOKEN', token)
    Vue.http.headers.common['Authorization'] = 'Bearer ' + token
  }
}
/* eslint-disable no-new */
new Vue({// Start out app!
  el: '#app',
  router,
  store,
  render: h => h(AppView)
})
