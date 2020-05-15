import Vue from 'vue'

export default {
  register(query, params) {
    return Vue.axios.post(`/api/auth/register?account=${query.account}&expires=${query.expires}&initiator=${query.initiator}&initiator_type=${query.initiator_type}&signature=${query.signature}`, params);
  },
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
  },
  /**
   * Modify password
   * @param params
   * @returns {*}
   */
  password(params) {
    return Vue.axios.post('/api/profile/password', params);
  },
  /**
   * Modify address
   * @param params
   * @returns {*}
   */
  address(params) {
    return Vue.axios.post('/api/profile/address', params);
  }
}
