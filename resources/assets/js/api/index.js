import {AuthResource, UserResource} from './resources'

export default {
  authenticate: function (data) {
    return AuthResource.save({id: 'authenticate'}, data)
  },
  getUserData: function () {
    return UserResource.get({id: 'data'})
  }
}
