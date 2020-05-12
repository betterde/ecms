function registration(type) {
  return [
    {
      path: '/',
      name: 'dashboard',
      meta: {
        rules: ['user', 'customer'],
        requiresAuth: true
      },
      component: () => import(`../views/rules/${type}/Dashboard.vue`)
    },
    {
      path: "/order",
      name: "order",
      meta: {
        requiresAuth: true,
        rules: ['user', 'customer']
      },
      component: () => import(`../views/rules/${type}/order/Index.vue`),
      children: [
        {
          path: ':id/detail',
          name: "orderDetail",
          meta: {
            requiresAuth: true,
            rules: ['user', 'customer']
          },
          component: () => import(`../views/rules/${type}/order/Detail.vue`)
        }
      ]
    },
  ];
}

export default (type) => {
  return registration(type);
}
