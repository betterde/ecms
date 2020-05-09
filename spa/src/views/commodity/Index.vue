<template>
  <div class="main-content">
    <el-page-header v-if="action !== 'list'" @back="$router.back()" content="详情页面"></el-page-header>
    <component :is="action"></component>
  </div>
</template>

<script>
  import CommodityList from './List'
  import CommodityDetail from "./Detail"

  export default {
    name: "Index",
    data() {
      return {
        breadcrumbs: [],
        filtered: false,
        action: 'list',
        actions: {
          detail: {
            path: null,
            label: '订单详情',
          }
        }
      }
    },
    components: {
      list: CommodityList,
      detail: CommodityDetail
    },
    watch: {
      '$route' (to) {
        let uris = to.path.split('/');
        if (uris.length > 2) {
          this.action = uris.pop();
          this.breadcrumbs.push({
            path: '/order',
            label: '订单管理',
          });
          let current = this.actions[this.action];
          this.breadcrumbs.push(current)
        } else {
          this.action = 'list';
        }
        if (this.action === 'list') {
          this.breadcrumbs = [];
        }
      }
    },
    mounted() {
      let uris = this.$route.path.split('/');
      if (uris.length > 2) {
        this.action = uris.pop();
        this.breadcrumbs.push({
          path: '/order',
          label: '订单管理',
        });
        let current = this.actions[this.action];
        this.breadcrumbs.push(current)
      } else {
        this.action = 'list';
      }
    }
  }
</script>

<style lang="scss">
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }

  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */
  {
    opacity: 0;
  }

  .el-switch__label, .el-switch__label--right {
    color: #909399;
  }
  .el-input-number {
    width: 100%;
    .el-input__inner {
      text-align: left;
    }
  }
</style>
