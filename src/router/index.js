import Vue from 'vue'
import Router from 'vue-router'
import AuthGuard from './auth.guard'

/** Components */
import Login from '@/components/Auth/Login'
import Portals from '@/components/Pages/Portals'
import LogicalControls from '@/components/Pages/LogicalControls'
import Rubricators from '@/components/Pages/Rubricators'
import Services from '@/components/Pages/Services'
import Reference from '@/components/Pages/Reference'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      component: Login
    },
    {
      path: '/portals',
      component: Portals,
      beforeEnter: AuthGuard
    },
    {
      path: '/logical-controls',
      component: LogicalControls,
      beforeEnter: AuthGuard
    },
    {
      path: '/rubricators',
      component: Rubricators,
      beforeEnter: AuthGuard
    },
    {
      path: '/services',
      component: Services,
      beforeEnter: AuthGuard
    },
    {
      path: '/reference',
      component: Reference,
      beforeEnter: AuthGuard
    },
    {
      path: '*',
      component: Login
    }
  ]
})
