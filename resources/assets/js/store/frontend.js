
const state = {
  path: '/',
  searching: '',
  callingApi: false,
  response: '', // TODO api response
  notifications:  {
    count: 0,
    items: []
  },
  tasks:  {
    count: 0,
    items: []
  }
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
  },
  ADD_NOTIFICATION (state, notif) {
    let count;
    count = state.notifications.items.push(notif)
    state.notifications.count = count
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
