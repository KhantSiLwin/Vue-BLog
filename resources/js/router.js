import Vue from 'vue'
import Router from 'vue-router'


Vue.use(Router)
import firstPage from './components/pages/myFirstVuePage'
import home from './components/pages/home'
import tags from './admin/pages/tags'
import category from './admin/pages/category'
import role from './admin/pages/role'
import assignRole from './admin/pages/assignRole'
import adminusers from './admin/pages/adminusers'
import login from './admin/pages/login'

const routes = [
    
    {
        path : '/',
        component: home,
        name:'/'
    },

    {
        path : '/tags',
        component: tags,
        name:'tags'
    },

    {
        path : '/category',
        component: category,
        name:'category'
    },

     {
        path : '/role',
        component: role,
        name:'role'
    },

    {
        path : '/assignRole',
        component: assignRole,
        name:'assignRole'
    },

    {
        path : '/adminusers',
        component: adminusers,
        name:'adminusers'
    },

    {
        path : '/login',
        component: login,
        name:'login'
    },




















    {
        path : '/my-vue-route',
        component: firstPage
    }
]

export default new Router({
    mode: 'history',
    routes
})