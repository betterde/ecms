<template>
  <div class="signinview">
    <div class="panel signin">
      <div class="panel-header">
        <h1 class="panel-title">ECMS</h1>
      </div>
      <div class="panel-body">
        <el-form :model="credentials" :rules="rules" ref="signin">
          <el-form-item prop="username">
            <el-input v-model="credentials.username" autocomplete="off" placeholder="邮箱"></el-input>
          </el-form-item>
          <el-form-item prop="password">
            <el-input type="password" v-model="credentials.password" @keyup.enter.native="submit('signin')" placeholder="密码" show-password></el-input>
          </el-form-item>
          <el-form-item class="login-button">
            <el-button type="primary" plain class="pull-right" style="width: 100%" @click="submit('signin')" :loading="loading">登录</el-button>
          </el-form-item>
          <el-form-item>
            <div id="sign-in-with-google" style="text-align: center"></div>
          </el-form-item>
          <div class="tips">
            <p>如果你忘记了密码，<router-link to="/forgot">请点击这里</router-link></p>
          </div>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../apis';
  import store from '../store';

  export default {
    name: "SignIn",
    data() {
      return {
        loading: false,
        credentials: {
          username: '',
          password: ''
        },
        rules: {
          username: [
            {required: true, message: '请输入邮箱地址', trigger: 'blur'},
          ],
          password: [
            {required: true, message: '请输入密码', trigger: 'blur'},
          ]
        },
      }
    },
    methods: {
      submit(name) {
        this.$refs[name].validate((valid) => {
          if (valid) {
            this.loading = true;
            store.dispatch('signIn', this.credentials).then(() => {
              store.dispatch("fetchProfile").then(() => {
                this.$router.replace('/');
              });
            }).catch(err => {
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
        this.loading = false;
      },
      initGoogleAPI() {
        // eslint-disable-next-line
        gapi.signin2.render('sign-in-with-google', {
          'scope': 'profile email',
          'height': 40,
          'longtitle': true,
          'theme': 'dark',
          'onsuccess': user => {
            // eslint-disable-next-line
            gapi.auth2.getAuthInstance().disconnect();
            store.dispatch('verification', {
              access_token: user.getAuthResponse(true).id_token,
              platform: 'Google'
            }).then(() => {
              store.dispatch('fetchProfile').then(() => {
                this.$router.replace('/')
              });
            }).catch(err => {
              if (err.hasOwnProperty('exception')) {
                this.$message.error(`${err.exception}: ${err.message}`);
              } else {
                this.$message.error(err.message);
              }
            });
          },
          'onfailure': err => {
            this.$message.error(err)
          }
        });
      }
    },
    mounted() {
      api.system.fetchPlatform().then(res => {
        if (res.data.google === true) {
          let script = document.createElement('script');
          script.setAttribute('src', 'https://apis.google.com/js/platform.js');
          script.async = true;
          script.defer = true;
          document.body.appendChild(script);
          script.onload = () => this.initGoogleAPI();
        }
      });
    }
  }
</script>

<style lang="scss">
  html, body {
    color: #76838f;
    margin: 0;
  }

  .signinview {
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
