import Vue from 'vue';

export default {
  fetchJournals(params) {
    return Vue.axios.get('/api/journal', {
      params: params
    });
  },
  fetchJournal(id) {
    return Vue.axios.get(`/api/journal/${id}`);
  }
}
