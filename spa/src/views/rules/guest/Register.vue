<template>
  <div class="register-view">
    <div class="panel signin">
      <div class="panel-header">
        <h1 class="panel-title">注册账户</h1>
      </div>
      <div class="panel-body">
        <el-form :model="credentials" :rules="rules" ref="register">
          <el-form-item prop="name">
            <el-input v-model="credentials.name" autocomplete="off" placeholder="请输入您的姓名"></el-input>
          </el-form-item>
          <el-form-item prop="email">
            <el-input v-model="credentials.email" autocomplete="off" placeholder="请输入邮箱地址"></el-input>
          </el-form-item>
          <el-form-item prop="password">
            <el-input type="password" v-model="credentials.password" @keyup.enter.native="submit" placeholder="请输入密码" show-password></el-input>
          </el-form-item>
          <el-form-item prop="password_confirmation">
            <el-input type="password" v-model="credentials.password_confirmation" @keyup.enter.native="submit" placeholder="请再次输入密码" show-password></el-input>
          </el-form-item>
          <el-form-item class="login-button">
            <el-button type="primary" plain class="pull-right" style="width: 100%" @click="submit" :loading="loading">注册</el-button>
          </el-form-item>
          <el-form-item>
            <div id="sign-in-with-google" style="text-align: center"></div>
          </el-form-item>
          <div class="tips">
            <p>已有账户？请点击这里 <router-link to="/signin">登录</router-link></p>
          </div>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
  import store from '../../../store';
  import register from "../../../router/register";

  export default {
    name: "Register",
    data() {
      let confirmation = (rule, value, callback) => {
        if (value !== this.credentials.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        query: {
          account: '',
          expires: '',
          signature: '',
          initiator: '',
          initiator_type: '',
        },
        loading: false,
        credentials: {
          name: '',
          email: '',
          password: '',
          password_confirmation: ''
        },
        rules: {
          name: [
            {required: true, message: '请输入姓名', trigger: 'blur'},
          ],
          email: [
            {required: true, message: '请输入邮箱地址', trigger: 'blur'},
          ],
          password: [
            {required: true, message: '请输入密码', trigger: 'blur'}
          ],
          password_confirmation: [
            {required: true, message: '请输入密码', trigger: 'blur'},
            {validator: confirmation, trigger: 'blur'}
          ]
        }
      }
    },
    methods: {
      submit() {
        this.$refs.register.validate(valid => {
          if (valid) {
            this.loading = true;
            store.dispatch('register', {query: this.query, params: this.credentials}).then(() => {
              store.dispatch("fetchProfile").then(res => {
                this.loading = false;
                this.$router.addRoutes(register(res.data.type));
                this.$router.replace('/');
              });
            }).catch(err => {
              this.loading = false;
              if (err.hasOwnProperty('exception')) {
                this.$message.error(`${err.exception}: ${err.message}`);
              } else {
                this.$message.error(err.message);
              }
            });
          } else {
            return false;
          }
        });
      }
    },
    mounted() {
      let query = this.$route.query;
      if (query.hasOwnProperty('initiator') && query.hasOwnProperty('account') && query.hasOwnProperty('expires') && query.hasOwnProperty('signature')) {
        this.credentials.email = query.account;
        this.query.account = query.account;
        this.query.expires = query.expires;
        this.query.signature = query.signature;
        this.query.initiator = query.initiator;
        this.query.initiator_type = query.initiator_type;
        let now = new Date();
        if (now.getTime() > query.expires * 1000) {
          this.$message.error('连接已过期，请联系管理员重新邀请。');
        }
      } else {
        this.$router.replace({
          name: 'notfound'
        });
      }
    }
  }
</script>

<style lang="scss">
  html, body {
    color: #76838f;
    margin: 0;
  }

  .register-view {
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
    #sign-in-with-google {
      .abcRioButton {
        width: 100% !important;
      }
    }
  }

  @media screen and (max-width: 734px) and (min-width: 0px) {
    .signin {
      width: 80%;
      padding: 4%;
    }
  }
</style>
