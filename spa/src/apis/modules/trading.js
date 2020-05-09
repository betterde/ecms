import Vue from 'vue'

export default {
  fetchTradings(params) {
    return Vue.axios.get('/api/trading', {
      params: params
    });
  },
  createTrading(params) {
    return Vue.axios.post('/api/trading', params);
  },
  updateTrading(id, params) {
    return Vue.axios.put(`/api/trading/${id}`, params);
  },
  deleteTrading(id) {
    return Vue.axios.delete(`/api/trading/${id}`);
  }
}
