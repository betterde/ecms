<template>
  <el-container>
    <el-header>
      <el-row :gutter="20">
        <el-col :span="4">
          <div class="grid-content logo">
            <h3 style="line-height: 36px;"><router-link to="/">ECMS</router-link></h3>
          </div>
        </el-col>
        <el-col :span="16">
          <el-menu :default-active="module" class="el-menu-nav" mode="horizontal" router>
            <el-menu-item index="/">数据分析</el-menu-item>
            <el-menu-item index="/order">订单管理</el-menu-item>
            <el-menu-item index="/commodity">商品管理</el-menu-item>
            <el-submenu index="2">
              <template slot="title">客户管理</template>
              <el-menu-item index="/invitation">注册管理</el-menu-item>
              <el-menu-item index="/customer">客户管理</el-menu-item>
            </el-submenu>
          </el-menu>
        </el-col>
        <el-col :span="4" style="float: right">
          <el-dropdown trigger="click" @command="handleCommand">
          <span class="el-dropdown-link">
            <div class="avatar grid-content" v-html="profile.name.slice(0,1)"></div>
          </span>
            <el-dropdown-menu slot="dropdown">
              <el-dropdown-item command="profile">个人信息</el-dropdown-item>
              <el-dropdown-item command="signOut">退出登陆</el-dropdown-item>
            </el-dropdown-menu>
          </el-dropdown>
        </el-col>
      </el-row>
    </el-header>
    <el-main>
      <el-row justify="center">
        <el-col :xs="24" :sm="24" :md="24" :lg="{ span: 20, offset: 2 }" :xl="{ span: 20, offset: 2 }">
          <router-view></router-view>
        </el-col>
      </el-row>
    </el-main>
  </el-container>
</template>

<script>
  import {mapState} from 'vuex'

  export default {
    name: 'backend',
    data() {
      return {
        year: '',
      }
    },
    methods: {
      handleCommand(command) {
        switch (command) {
          case 'signOut':
            this.signOut();
            break;
          case 'profile':
            this.$router.push({path: '/profile'});
            break;
        }
      },
      signOut(){
        this.$store.commit('SET_ACCESS_TOKEN', false);
        this.$store.commit('SET_PROFILE', false);
        this.$message.success('注销成功');
        // 这里用原生页面跳转而不是 this.$router.push("/signin"), 避免再次登录重复添加路由
        window.location.href = '/signin';
      }
    },
    computed: {
      ...mapState({
        profile: state => state.account.profile,
        access_token: state => state.account.access_token
      }),
      module() {
        let path = this.$route.path.match(/\/[a-z]*/);
        return path.shift();
      }
    },
    mounted() {
      let date = new Date();
      this.year = date.getFullYear();
    }
  }
</script>

<style lang="scss">
  #app {
    height: 100%;
    background-color: #f0f2f5;

    .el-header {
      width: 100%;
      z-index: 100;
      background-color: white;
      box-shadow: 0 1px 4px rgba(0, 21, 41, .08);
      .el-dropdown {
        float: right;
      }
      .avatar {
        height: 36px;
        width: 36px;
        background-color: #8c939d;
        float: right;
      }
    }

    .el-menu-nav {
      display: table;
      margin: auto;
      align-items: center;
      text-align: center;

      &.el-menu--horizontal {
        border: none;
      }
    }

    .el-container {
      height: 100%;
      min-height: 100%;

      .grid-content {
        margin: 12px 0;
        border-radius: 4px;
        min-height: 36px;
        line-height: 36px;
        font-size: 26px;
        text-align: center;
        cursor: pointer;
        color: #e9e9e9;
      }
      .logo {
        a {
          color: #8c939d;
        }
        text-align: left;
      }
    }

    .el-main {
      display: block;
      padding: 20px 0 0 0;

      .el-col {
        height: 100%;
      }
    }
  }

  .main-content {
    border-radius: 4px;
    padding: 0 20px;
    .el-page-header {
      margin-bottom: 20px;
    }
    .el-page-header__title {
      font-weight: normal;
    }
    .el-page-header__content {
      color: #909399;
      font-size: 14px;
    }
  }
</style>
