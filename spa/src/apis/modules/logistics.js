import Vue from 'vue';

export default {
  create(params) {
    return Vue.axios.post('/api/logistics', params);
  },
  update(id, params) {
    return Vue.axios.put(`/api/logistics/${id}`, params);
  },
  updateNumber(id, params) {
    return Vue.axios.put(`/api/logistics/${id}/number`, params);
  }
}
