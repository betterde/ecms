import Vue from 'vue';

export default {
  fetchPlatform() {
    return Vue.axios.get('/api/system/oauth/platform');
  }
}
