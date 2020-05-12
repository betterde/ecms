import Vue from 'vue'

export default {
  fetchInvitations(params) {
    return Vue.axios.get('/api/invitation', {
      params: params
    });
  },
  createInvitation(params) {
    return Vue.axios.post('/api/invitation', params);
  },
  fetchInvitation(id) {
    return Vue.axios.post(`/api/invitation/${id}`);
  }
}
