<template>
  <router-view>
    <div id="app">
      <div><p> </p></div>
    </div>
  </router-view>
</template>
<script>
export default {
  name: 'App',
  data: function () {
    return {
      section: 'Head',
      version: '0.10.0',
      callingAPI: false,
      serverURI: 'http://localhost:8080',
      caller: this.$http
    }
  },
  methods: {
    callAPI: function (method, url, data) {
      this.callingAPI = true
      url = url || this.serverURI // if no url is passed then inheret local server URI
      return this.caller({
        url: url,
        method: method,
        data: data
      })
    },
    logout: function () { // TODO
      this.$store.commit('LOGOUT')
      this.$store.commit('REDIRECT_TO', '/login')
    }
  }
}
</script>
