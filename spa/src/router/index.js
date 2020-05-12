import Vue from 'vue'
import store from '../store'
import Router from 'vue-router'
import register from "./register";

Vue.use(Router);

const originalPush = Router.prototype.push
Router.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => err)
};

const routes = [
  {
    path: '/commodity',
    name: 'commodity',
    meta: {
      requiresAuth: true,
      rules: ['user']
    },
    component: () => import('../views/commodity/Index.vue'),
    children: [
      {
        path: ':id/detail',
        name: 'commodityDetail',
        meta: {
          requiresAuth: true,
          rules: ['user']
        },
        component: () => import('../views/commodity/Detail.vue')
      }
    ]
  },
  {
    path: '/customer',
    name: 'customer',
    meta: {
      requiresAuth: true,
      rules: ['user', 'customer']
    },
    component: () => import('../views/customer/Index.vue'),
    children: [
      {
        path: ':id/detail',
        name: 'customerDetail',
        meta: {
          requiresAuth: true,
          rules: ['user', 'customer']
        },
        component: () => import('../views/customer/Detail.vue')
      }
    ]
  },
  {
    path: '/invitation',
    name: 'invitation',
    meta: {
      requiresAuth: true,
      rules: ['user', 'customer']
    },
    component: () => import('../views/Invitation.vue')
  },
  {
    path: '/register',
    name: 'register',
    meta: {
      rules: [],
      requiresAuth: false
    },
    component: () => import('../views/rules/guest/Register.vue')
  },
  {
    path: '/signin',
    name: 'signin',
    meta: {
      rules: [],
      requiresAuth: false
    },
    component: () => import('../views/rules/guest/SignIn.vue')
  },
  {
    path: '/forgot',
    name: 'forgot',
    meta: {
      rules: [],
      requiresAuth: false
    },
    component: () => import('../views/rules/guest/ForgotPassword.vue')
  },
  {
    path: '/reset',
    name: 'reset',
    meta: {
      rules: [],
      requiresAuth: false,
    },
    component: () => import('../views/rules/guest/ResetPassword.vue')
  },
  {
    // 会匹配所有路径
    path: '*',
    name: 'notfound',
    meta: {
      rules: [],
      requiresAuth: false
    },
    component: () => import('../views/rules/guest/NotFound.vue')
  }
];

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: routes
});

if (store.state.account.access_token && store.state.account.profile.type) {
  router.addRoutes(register(store.state.account.profile.type));
}


/**
 * Routing to intercept
 */
router.beforeEach((to, from, next) => {
  // 如果跳转到NotFound页面则提前设置视图的Layout 为 guest
  if (to.name === 'notfound') {
    store.commit('SET_LAYOUT_CURRENT', 'guest');
  }

  // 如果从NotFound 页面返回，并且需要认证的话，则设置视图的Layout 为 backend
  if (from.name === 'notfound' && to.meta.requiresAuth === true) {
    store.commit('SET_LAYOUT_CURRENT', 'backend')
  }

  if (store.state.account.access_token && to.path === '/signin') {
    next({
      path: "/"
    })
  }

  /**
   * Determine if auth is required
   */
  if (to.matched.some(record => record.meta.requiresAuth)) {
    //通过access_token判断用户是否已经登录
    if (store.state.account.access_token === null) {
      next({
        path: '/signin'
      })
    } else {
      // 判断用户是否拥有指定角色
      if (to.meta.rules.includes(store.state.account.profile.type)) {
        next();
      } else {
        next('/order');
      }
    }
  } else {
    next()
  }
});

export default router;
