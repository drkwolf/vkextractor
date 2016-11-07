
const state = {
  path: '/',
  searching: '',
  callingApi: false,
  response: '' // TODO change me to notification/validation_msg
}

const mutations = {
  TOGGLE_LOADING (state) {
    state.callingAPI = !state.callingAPI
  },
  TOGGLE_SEARCHING (state) {
    state.searching = (state.searching === '') ? 'loading' : ''
  },
  SET_RESPONSE (state, response) {
    state.response = response
  },
  REDIRECT_TO (state, route) {
    state.path = route
  }
}

const getters = {
  response: state => {
    return state.response
  },
  loading: state => {
    return state.searching
  }
}

export default {
  state,
  mutations,
  getters
}
