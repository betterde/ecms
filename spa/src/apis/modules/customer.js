import Vue from 'vue'

export default {
  fetchCustomers(params) {
    return Vue.axios.get('/api/customer', {
      params: params
    });
  },
  fetchCustomer(id) {
    return Vue.axios.get(`/api/customer/${id}`);
  },
  checkAvailability(params) {
    return Vue.axios.post('/api/customer/check', params);
  },
  createCustomer(params) {
    return Vue.axios.post('/api/customer', params);
  },
  updateCustomer(id, params) {
    return Vue.axios.put(`/api/customer/${id}`, params);
  },
  deleteCustomer(id) {
    return Vue.axios.delete(`/api/customer/${id}`);
  }
}
