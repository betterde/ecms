<template>
  <div class="forgotview">
    <div class="panel forgot">
      <div class="panel-header">
        <h1 class="panel-title">发送重置链接</h1>
      </div>
      <div class="panel-body">
        <el-form :model="credentials" :rules="rules" @submit.native.prevent ref="forgot">
          <el-form-item prop="email">
            <el-input v-model="credentials.email" autocomplete="off" @keyup.enter.native="submit" placeholder="请输入您的邮箱地址"></el-input>
          </el-form-item>
          <el-form-item class="login-button">
            <el-button type="primary" plain class="pull-right" style="width: 100%" @click="submit" :loading="loading">发送邮件</el-button>
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
    name: "ForgetPassword",
    data() {
      return {
        loading: false,
        credentials: {
          email: '',
        },
        rules: {
          email: [
            {
              required: true,
              message: '请输入邮箱地址',
              trigger: 'blur'
            },
            {
              type: 'email',
              message: '请输入正确的邮箱地址',
              trigger: ['blur', 'change']
            }
          ]
        }
      }
    },
    methods: {
      submit() {
        this.$refs.forgot.validate((valid) => {
          if (valid) {
            this.loading = true;
            api.account.forgot(this.credentials).then(res => {
              this.loading = false;
              this.$message.success(res.message);
              this.$refs.forgot.resetFields();
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
    }
  }
</script>

<style lang="scss">
  html, body {
    color: #76838f;
    margin: 0;
  }

  .forgotview {
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
      margin: 0 0 40px 0;
    }

    .footer-row {
      height: 100%;
      z-index: 0;
    }
  }

  .forgot {
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
    .forgot {
      width: 80%;
      padding: 4%;
    }
  }
</style>
