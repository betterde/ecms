import Vue from 'vue'

export default {
  fetchCommodities(params) {
    return Vue.axios.get('/api/commodity', {
      params: params
    });
  },
  fetchUnits() {
    return Vue.axios.get('/api/commodity/unit');
  },
  fetchBrands() {
    return Vue.axios.get('/api/commodity/brand');
  },
  fetchCategories() {
    return Vue.axios.get('/api/commodity/category');
  },
  createCommodity(params) {
    return Vue.axios.post('/api/commodity', params);
  },
  updateCommodity(id, params) {
    return Vue.axios.put(`/api/commodity/${id}`, params);
  },
  fetchCommodity(id) {
    return Vue.axios.get(`/api/commodity/${id}`);
  },
  deleteCommodity(id) {
    return Vue.axios.delete(`/api/commodity/${id}`);
  }
}
