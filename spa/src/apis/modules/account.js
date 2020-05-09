import Vue from 'vue'

export default {
  forgot(params) {
    return Vue.axios.post('/api/auth/password/email', params);
  },
  resetPassword(params) {
    return Vue.axios.post('/api/auth/password/reset', params);
  },
  /**
   * User sign in
   * @param params
   * @returns {AxiosPromise<any>}
   */
  signin(params) {
    return Vue.axios.post('/api/auth/signin', params);
  },
  /**
   * Get uset token by third platform
   * @param params
   * @returns {*}
   */
  verification(params) {
    return Vue.axios.post('/api/auth/issue', params);
  },
  /**
   * Fetch user profile
   * @returns {AxiosInstance}
   */
  profile() {
    return Vue.axios.get('/api/auth/profile');
  }
}
