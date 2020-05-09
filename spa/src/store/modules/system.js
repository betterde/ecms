import Vue from 'vue'

export default {
  state: {
    layout: {
      current: 'guest'
    },
    action: 'list',
  },
  mutations: {
    SET_LAYOUT_CURRENT: (state, data) => {
      Vue.set(state.layout, 'current', data)
    }
  }
}
