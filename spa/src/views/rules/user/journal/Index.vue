<template>
  <div class="main-content">
    <el-page-header v-if="action !== 'list'" @back="$router.back()" content="详情页面"></el-page-header>
    <component :is="action"></component>
  </div>
</template>

<script>
  import JournalList from './List.vue';
  import JournalDetail from './Detail.vue';

  export default {
    name: "Index",
    data() {
      return {
        action: 'list',
      }
    },
    components: {
      list: JournalList,
      detail: JournalDetail
    },
    watch: {
      '$route' (to) {
        let uris = to.path.split('/');
        if (uris.length > 2) {
          this.action = uris.pop();
        } else {
          this.action = 'list';
        }
      }
    },
    mounted() {
      let uris = this.$route.path.split('/');
      if (uris.length > 2) {
        this.action = uris.pop();
      } else {
        this.action = 'list';
      }
    }
  }
</script>

<style lang="scss">

</style>
