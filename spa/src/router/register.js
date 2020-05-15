function registration(type) {
  if (type === 'user') {
    return [
      {
        path: '/',
        name: 'dashboard',
        meta: {
          rules: ['user', 'customer'],
          requiresAuth: true
        },
        component: () => import('../views/rules/user/Dashboard.vue')
      },
      {
        path: '/order',
        name: 'order',
        meta: {
          requiresAuth: true,
          rules: ['user', 'customer']
        },
        component: () => import('../views/rules/user/order/Index.vue'),
        children: [
          {
            path: ':id/detail',
            name: 'orderDetail',
            meta: {
              requiresAuth: true,
              rules: ['user', 'customer']
            },
            component: () => import('../views/rules/user/order/Detail.vue')
          }
        ]
      },
      {
        path: '/commodity',
        name: 'commodity',
        meta: {
          requiresAuth: true,
          rules: ['user']
        },
        component: () => import('../views/rules/user/commodity/Index.vue'),
        children: [
          {
            path: ':id/detail',
            name: 'commodityDetail',
            meta: {
              requiresAuth: true,
              rules: ['user']
            },
            component: () => import('../views/rules/user/commodity/Detail.vue')
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
        component: () => import('../views/rules/user/customer/Index.vue'),
        children: [
          {
            path: ':id/detail',
            name: 'customerDetail',
            meta: {
              requiresAuth: true,
              rules: ['user', 'customer']
            },
            component: () => import('../views/rules/user/customer/Detail.vue')
          }
        ]
      },
      {
        path: '/profile',
        name: 'profile',
        meta: {
          requiresAuth: true,
          rules: ['user', 'customer']
        },
        component: () => import('../views/rules/user/Profile.vue')
      }
    ];
  } else {
    return [
      {
        path: '/',
        name: 'dashboard',
        meta: {
          rules: ['user', 'customer'],
          requiresAuth: true
        },
        component: () => import('../views/rules/customer/Dashboard.vue')
      },
      {
        path: '/order',
        name: 'order',
        meta: {
          requiresAuth: true,
          rules: ['user', 'customer']
        },
        component: () => import('../views/rules/customer/order/Index.vue'),
        children: [
          {
            path: ':id/detail',
            name: 'orderDetail',
            meta: {
              requiresAuth: true,
              rules: ['user', 'customer']
            },
            component: () => import('../views/rules/customer/order/Detail.vue')
          }
        ]
      },
      {
        path: '/profile',
        name: 'profile',
        meta: {
          requiresAuth: true,
          rules: ['user', 'customer']
        },
        component: () => import('../views/rules/customer/Profile.vue')
      }
    ];
  }
}

export default (type) => {
  return registration(type);
}
