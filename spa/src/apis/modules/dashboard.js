import Vue from 'vue'

export default {
  fetchDashboard() {
    return Vue.axios.get('/api/dashboard');
  },
}
