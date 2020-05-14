<template>
  <div class="main-content">
    <div class="profile">
      <div style="text-align: center">
        <el-avatar :size="128" src="https://george.betterde.com/images/avatar/avatar.jpg"></el-avatar>
      </div>
      <div class="information">
        <div class="name">
          <h1>{{profile.name}}</h1>
        </div>
        <div class="status">
          <ul>
            <li></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="panel">
      <div class="panel-body" :class="classes">
        <el-tabs v-model="active" @tab-click="handleClick">
          <el-tab-pane label="基本信息" name="basic">基本信息</el-tab-pane>
          <el-tab-pane label="我的邀请" name="invitation">我的邀请</el-tab-pane>
          <el-tab-pane label="地址管理" name="address">地址管理</el-tab-pane>
          <el-tab-pane label="重置密码" name="reset">重置密码</el-tab-pane>
        </el-tabs>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: "Profile",
    data() {
      return {
        header: '',
        active: 'basic',
        classes: ['animated', 'fade-in', 'fast'],
        profile: {
          name: 'George'
        }
      }
    },
    methods: {
      handleClick() {

      }
    }
  }
</script>

<style scoped lang="scss">
  .main-content {
    padding: 0 16%;
  }
  .profile {
    color: #5f6268;
    padding: 40px 20px;
    text-align: center;
    background-repeat: repeat;
    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.1);
    background-image: url("../../../assets/images/background.png");
  }
  .panel {
    color: #5f6268;
    .el-tabs__item.is-top {
      color: #5f6268;
    }
  }
  .information {
    margin-top: 20px;
  }
</style>
