import Vue from 'vue'
import VueResource from 'vue-resource'

import store from '../store'
import {API_ROOT} from '../config'

Vue.use(VueResource)

Vue.http.options.root = API_ROOT
Vue.http.headers.common['X-CSRF-TOKEN'] = document.getElementsByName('csrf-token')[0].getAttribute('content')
Vue.http.interceptors.push((request, next) => {
  next((response) => {
    if (response.status === 401) {
      store.commit('LOGOUT')
      store.commit('REDIRECT_TO', '/login')
    }
  })
})

// add resource if needed
export const UserResource = Vue.resource(API_ROOT + 'user{/id}')
export const AuthResource = Vue.resource(API_ROOT + 'auth{/id}')
