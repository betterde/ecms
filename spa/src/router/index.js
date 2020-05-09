import Vue from 'vue'
import store from '../store'
import Router from 'vue-router'

Vue.use(Router);

const originalPush = Router.prototype.push
Router.prototype.push = function push(location) {
  return originalPush.call(this, location).catch(err => err)
};

const router = new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  routes: [
    {
      path: '/',
      name: 'dashboard',
      meta: {
        requiresAuth: true
      },
      component: () => import('../views/Dashboard.vue')
    },
    {
      path: "/order",
      name: "order",
      meta: {
        requiresAuth: true
      },
      component: () => import('../views/order/Index.vue'),
      children: [
        {
          path: ':id/detail',
          name: "orderDetail",
          meta: {
            requiresAuth: true
          },
          component: () => import('../views/order/Detail.vue')
        }
      ]
    },
    {
      path: '/commodity',
      name: 'commodity',
      meta: {
        requiresAuth: true
      },
      component: () => import('../views/commodity/Index.vue'),
      children: [
        {
          path: ':id/detail',
          name: 'commodityDetail',
          meta: {
            requiresAuth: true,
          },
          component: () => import('../views/commodity/Detail.vue')
        }
      ]
    },
    {
      path: '/customer',
      name: 'customer',
      meta: {
        requiresAuth: true
      },
      component: () => import('../views/customer/Index.vue'),
      children: [
        {
          path: ':id/detail',
          name: 'customerDetail',
          meta: {
            requiresAuth: true,
          },
          component: () => import('../views/customer/Detail.vue')
        }
      ]
    },
    {
      path: '/signin',
      name: 'signin',
      meta: {
        requiresAuth: false
      },
      component: () => import('../views/SignIn.vue')
    },
    {
      path: '/forgot',
      name: 'forgot',
      meta: {
        requiresAuth: false
      },
      component: () => import('../views/ForgotPassword.vue')
    },
    {
      path: '/reset',
      name: 'reset',
      meta: {
        requiresAuth: false,
      },
      component: () => import('../views/ResetPassword.vue')
    },
    {
      // 会匹配所有路径
      path: '*',
      name: 'notfound',
      meta: {
        requiresAuth: false
      },
      component: () => import('../views/NotFound.vue')
    }
  ]
});

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
      next()
    }
  } else {
    next()
  }
});

export default router;
