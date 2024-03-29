import Vue from 'vue'
import api from '../../apis'
import * as types from '../types'

let profile = {
  id: '',
  name: '',
  email: '',
  avatar: '',
  type: false
};

let str = localStorage.getItem('profile');

if (str) {
  profile = global.JSON.parse(str);
}

export default {
  state: {
    profile: profile,
    access_token: localStorage.getItem('access_token'),
  },
  mutations: {
    SET_PROFILE: (state, data) => {
      // 如果数据无效则删除
      if (data === false){
        Vue.set(state, 'profile', {
          id: '',
          name: '',
          email: '',
          avatar: '',
          type: ''
        });
        localStorage.removeItem('profile')
      } else {
        let profile = {
          id: data.id,
          name: data.name,
          email: data.email,
          avatar: data.avatar,
          type: data.type
        };
        Vue.set(state, 'profile', profile);
        localStorage.setItem('profile', global.JSON.stringify(profile))
      }
    },
    SET_ACCESS_TOKEN: (state, data) => {
      if (data === false) {
        Vue.set(state, 'expires_in', '');
        Vue.set(state, 'access_token', null);
        // 删除 localStorage 中的用户凭证信息
        localStorage.removeItem('expires_in');
        localStorage.removeItem('access_token');
      } else {
        Vue.set(state, 'access_token', data.access_token);
        // 写入 localStorage 防止刷新后用户凭证丢失
        localStorage.setItem('access_token', data.access_token);
      }
    }
  },
  actions: {
    /**
     * Register user request
     * @param commit
     * @param payload
     * @returns {Promise<unknown>}
     */
    register({ commit }, payload) {
      return new Promise((resolve, reject) => {
        api.account.register(payload.query, payload.params).then(res => {
          commit(types.SET_ACCESS_TOKEN, res.data);
          resolve(res);
        }).catch(err => {
          reject(err);
        });
      });
    },
    /**
     * Sign in request
     * @param commit
     * @param params
     * @returns {Promise<unknown>}
     */
    signIn({ commit }, params) {
      return new Promise((resolve, reject) => {
        api.account.signin(params).then(res => {
          commit(types.SET_ACCESS_TOKEN, res.data);
          resolve(res);
        }).catch(err => {
          reject(err);
        });
      });
    },
    verification({ commit }, params) {
      return new Promise((resolve, reject) => {
        api.account.verification(params).then(res => {
          commit(types.SET_ACCESS_TOKEN, res.data);
          resolve(res);
        }).catch(err => {
          reject(err);
        });
      });
    },
    fetchProfile({ commit }) {
      return new Promise((resolve, reject) => {
        api.account.profile().then(res => {
          commit(types.SET_PROFILE, res.data);
          resolve(res);
        }).catch(err => {
          commit(types.SET_PROFILE, false);
          reject(err);
        });
      })
    }
  }
}

