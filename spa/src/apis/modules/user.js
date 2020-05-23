import Vue from 'vue';

export default {
  fetchUsers(params) {
    return Vue.axios.get('/api/user', {
      params: params
    });
  }
}
