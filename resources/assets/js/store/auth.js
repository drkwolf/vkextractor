import api from '../api'

const state = {
  token: null,
  userInfo: { // TODO rename DATA
    user_info: {
      first_name: '',
      last_name: '',
      photo_50: 'http://vk.com/images/camera_c.gif',
    },
    messages: {
      count: 0
    },
    friends_recent: {
      count: 0
    },
    friends: {
      count: 0
    },
  }
}

const mutations = {
  SET_TOKEN (state, token) {
    state.token = token
  },
  SET_DATA (state, data) { // TODO move to user module
    state.userInfo = data
  },
  LOGIN (state, token) {
    state.token = token
  },
  LOGOUT (state) {
    state.token = null
  },
  LOGIN_FAILED (state, message) {
    state.response = JSON.stringify(message)
  }
}

const actions = {
  authenticate: function ({ commit }, credential) {
    api.authenticate(credential)
      .then((response) => {
        if (response.data) {
          var data = response.data
          if (data.error) { //
            commit('LOGIN_FAILED', data.error)
          } else { //  success. Let's load up the dashboard
            commit('LOGIN', data.token)
            commit('REDIRECT_TO', '/')
          }
        } else {
          let msg = 'Did not receive a response. Please try again in a few minutes'
          commit('LOGIN_FAILED', msg)
        }
      }, function (response) { // server error
        commit('LOGIN_FAILED', response)
        commit('SET_RESPONSE', 'Server Error, try Again')
      })
  },
  fetchUser: function ({ commit }) { // TODO move to user module
    api.getUserData()
      .then(response => { // sucess
        return response.data.response
      }, response => { // fails
        commit('SET_RESPONSE', 'fetching user data failed')
      }).then( response => {
      if ( response.last_load === null) {
        commit('ADD_NOTIFICATION', 'your data will be available soon')
      } else {
        commit('SET_DATA', response.data)
      }
    })
  }
}

const getters = {
  isAuthenticated: state => {
    return !!state.token
  }
}

export default {
  state,
  mutations,
  getters,
  actions
}
