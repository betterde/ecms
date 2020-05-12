<template>
  <div class="auth-view">
    <div class="panel signin">
      <div class="panel-header">
        <h1 class="panel-title">重置您的密码</h1>
      </div>
      <div class="panel-body">
        <el-form :model="credentials" :rules="rules" @submit.native.prevent ref="reset">
          <el-form-item prop="password">
            <el-input type="password" v-model="credentials.password" placeholder="请输入新的密码" show-password></el-input>
          </el-form-item>
          <el-form-item prop="password">
            <el-input type="password" v-model="credentials.password_confirmation" @keyup.enter.native="submit" placeholder="请再次输入密码" show-password></el-input>
          </el-form-item>
          <el-form-item class="login-button">
            <el-button type="primary" plain class="pull-right" style="width: 100%" @click="submit" :loading="loading">确认重置</el-button>
          </el-form-item>
          <div class="tips">
            <p><router-link to="/signin">返回登录页面</router-link></p>
          </div>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../../../apis';

  export default {
    name: "SignIn",
    data() {
      let confirmed = (rule, value, callback) => {
        if (value === '') {
          callback(new Error('请再次输入密码'));
        } else if (value !== this.credentials.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        loading: false,
        credentials: {
          token: '',
          email: '',
          password: '',
          password_confirmation: '',
        },
        rules: {
          password: [
            {required: true, message: '请输入密码', trigger: 'blur'},
            {min: 8, max: 16, message: '长度在 8 到 16 个字符'}
          ],
          password_confirmation: [
            {validator: confirmed, trigger: 'blur'},
          ]
        }
      }
    },
    methods: {
      submit() {
        this.$refs.reset.validate((valid) => {
          if (valid) {
            this.loading = true;
            api.account.resetPassword(this.credentials).then(res => {
              this.$message.success(res.message);
              this.$router.replace("/signin");
              this.loading = false;
            }).catch(err => {
              if (err.hasOwnProperty('exception')) {
                this.$message.error(`${err.exception}: ${err.message}`);
              } else {
                this.$message.error(err.message);
              }
              this.loading = false;
            });
          } else {
            return false;
          }
        });
      }
    },
    mounted() {
      let query = this.$route.query;
      this.credentials.token = query.token;
      this.credentials.email = query.email;
    }
  }
</script>

<style lang="scss">
  html, body {
    color: #76838f;
    margin: 0;
  }

  .auth-view {
    height: 100%;
    display: flex;
    background: #62a8ea;
    background-repeat: repeat-x;
    background-image: -o-linear-gradient(top, #62a8ea 0, #3583ca 100%);
    background-image: linear-gradient(to bottom, #62a8ea 0, #3583ca 100%);
    background-image: -webkit-linear-gradient(top, #62a8ea 0, #3583ca 100%);
    background-image: -webkit-gradient(linear, left top, left bottom, from(#62a8ea), to(#3583ca));

    .panel-title {
      font-size: 28px;
      margin: 0 0 30px 0;
    }

    .footer-row {
      height: 100%;
      z-index: 0;
    }
  }

  .signin {
    z-index: 10;
    width: 400px;
    padding: 40px;
    margin: 0 auto;
    border-radius: 4px;
    align-self: center;
    background-color: #FFF;
    box-shadow: 0 2px 2px rgba(0, 0, 0, .05);

    .panel-header {
      text-align: center;

      .panel-title {
        color: #76838f;
      }
    }

    .panel-body {
      text-align: center;
      padding: 10px;

      .tips {
        font-size: 12px;
        font-weight: 400;
      }
    }

    .login-button {
      padding-top: 10px;
    }
  }

  @media screen and (max-width: 734px) and (min-width: 0px) {
    .signin {
      width: 80%;
      padding: 4%;
    }
  }
</style>
