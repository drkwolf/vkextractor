import VueRouter from 'vue-router'
import Vue from 'vue'
import store from '../store'

import DashView from '../components/Dash.vue'
import LoginView from '../components/Login.vue'
import NotFoundView from '../components/404.vue'

// Import Views - Dash
import DashboardView from '../components/dash/Dashboard.vue'
import TablesView from '../components/dash/Tables.vue'
// import TasksView from './components/dash/Tasks.vue'
// import SettingView from './components/dash/Setting.vue'
// import AccessView from './components/dash/Access.vue'
// import ServerView from './components/dash/Server.vue'
// import ReposView from './components/dash/Repos.vue'

Vue.use(VueRouter)
// Routes
//  - https://github.com/vuejs/vue-router/blob/c4f9836aa9676e2574f98ecb7bc76f7d2f628c63/examples/auth-flow/app.js
//  - http://stackoverflow.com/questions/39940665/passing-vuex-module-state-into-vue-router-during-beforeeach
function requireAuth (to, from, next) {
  if (!store.getters.isAuthenticated) {
    next({ path: '/login' })
  } else {
    next()
  }
}

const router = new VueRouter({
  mode: 'history',
  scrollBehavior: function (to, from, savedPosition) {
    return savedPosition || { x: 0, y: 0 }
  },
  routes: [
    {path: '/login', component: LoginView},
    {
      path: '/',
      component: DashView,
      beforeEnter: requireAuth,
      children: [
        {
          path: '',
          name: 'Dashboard',
          component: DashboardView,
          description: 'Overview of environment'
        },
        {
          path: '/tables',
          name: 'Tables',
          component: TablesView,
          description: 'Simple and advance table in CoPilot'
        },
        // {
        //   path:'/setting',
        //   component: SettingView,
        //   name: 'Settings',
        //   description: 'User settings page'
        // },
        // {
        //   path: '/access',
        //   component: AccessView,
        //   name: 'Access',
        //   description: 'Example of using maps'
        // },
        // {
        //   path:'/server',
        //   component: ServerView,
        //   name: 'Servers',
        //   description: 'List of our servers'
        // },
        // {
        //   path:'/repos',
        //   component: ReposView,
        //   name: 'Repository',
        //   description: 'List of popular javascript repos'
        // },
        { // not found handler
          path: '*',
          component: NotFoundView
        },
        {
          path: '/jobs',
          redirect: '/user/jobs'
        },
        {
          path: '/me',
          redirect: '/user'
        }
      ]
    }
  ]
})

export default router
