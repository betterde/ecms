import Vue from 'vue'

export default {
  fetchOrders(params) {
    return Vue.axios.get('/api/order', {
      params: params
    });
  },
  createOrder(params) {
    return Vue.axios.post('/api/order', params);
  },
  fetchOrder(id, scene = 'detail') {
    if (scene === 'detail') {
      return Vue.axios.get(`/api/order/${id}`);
    } else {
      return Vue.axios.get(`/api/order/${id}`, {
        params: {
          scene: scene,
        },
        responseType: 'blob'
      });
    }

  },
  updateOrder(id, params) {
    return Vue.axios.put(`/api/order/${id}`, params)
  },
  deleteOrder(id) {
    return Vue.axios.delete(`/api/order/${id}`);
  }
}
