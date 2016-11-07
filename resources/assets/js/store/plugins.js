import Vue from 'vue'
import router from '../router'

const redirect = function (router) {
  return store => {
    store.subscribe(mutation => {
      if (mutation.type === 'REDIRECT_TO') {
        router.push({path: mutation.payload})
      }
    })
  }
}

const login = function () {
  return store => {
    store.subscribe(mutation => {
      switch (mutation.type) {
        case 'LOGIN':
          if (window.localStorage) {
            let token = mutation.payload
            if (token) {
              window.localStorage.setItem('token', token)
              Vue.http.headers.common['Authorization'] = 'Bearer ' + token
            }
          }
          break
        case 'LOGOUT':
          if (window.localStorage) {
            window.localStorage.removeItem('token')
            Vue.http.headers.common['Authorization'] = null
          }
      }
    })
  }
}
// Till now no plugin in the dev
export default process.env.NODE_ENV !== 'production'
  ? [login(), redirect(router)]
  : [login(), redirect(router)]
