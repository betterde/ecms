<template>
  <div class="main-content">
    <el-page-header v-if="action !== 'list'" @back="$router.back()" content="详情页面"></el-page-header>
    <component :is="action"></component>
  </div>
</template>

<script>
  import CustomerList from './List'
  import CustomerDetail from './Detail'

  export default {
    name: "Index",
    data() {
      return  {
        breadcrumbs: [],
        action: 'list',
        actions: {
          detail: {
            path: null,
            label: '客户信息'
          }
        }
      }
    },
    methods: {

    },
    components: {
      list: CustomerList,
      detail: CustomerDetail
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

</style>
