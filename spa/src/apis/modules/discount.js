import Vue from 'vue';

export default {
  fetchDiscount(params) {
    return Vue.axios.get('/api/discount', {
      params: params
    });
  },
  createDiscount(params) {
    return Vue.axios.post('/api/discount', params);
  },
  updateDiscount(id, params) {
    return Vue.axios.put(`/api/discount/${id}`, params);
  },
  deleteDiscount(id) {
    return Vue.axios.delete(`/api/discount/${id}`);
  }
}
