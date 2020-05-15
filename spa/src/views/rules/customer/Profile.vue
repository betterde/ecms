<template>
  <div class="main-content">
    <div class="profile" :class="classes">
      <div style="text-align: center">
        <el-avatar :size="128" style="font-size: 86px">{{profile.name.slice(0,1)}}</el-avatar>
      </div>
      <div class="information">
        <div class="name">
          <h1>{{profile.name}}</h1>
        </div>
        <div class="status">
          <ul style="list-style: none">
            <li>
              <span>{{profile.province ? profile.province : '暂未填写'}}</span>
            </li>
            <li>
              <span>高级会员</span>
            </li>
            <li>
              <span>¥ {{profile.balance}}</span>
            </li>
            <li>
              <span>{{discount(profile.discount)}}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="panel">
      <div class="panel-body" :class="classes">
        <el-tabs v-model="active">
          <el-tab-pane label="地址管理" name="address">
            <div style="width: 60%; margin: auto; padding-top: 20px">
              <el-form ref="address" :model="addrform" label-position="top" label-width="80px">
                <el-row :gutter="10">
                  <el-col :span="20">
                    <el-form-item>
                      <el-input v-model="addrform.address" type="text" placeholder="请输入您的详细地址"></el-input>
                    </el-form-item>
                  </el-col>
                  <el-col :span="4">
                    <el-form-item>
                      <el-button type="primary" @click="submit('address')">确认</el-button>
                    </el-form-item>
                  </el-col>
                </el-row>
              </el-form>
            </div>
          </el-tab-pane>
          <el-tab-pane label="修改密码" name="password">
            <div class="password" style="max-width: 400px; margin: auto">
              <el-form ref="password" :model="params" :rules="rules" label-position="top" label-width="80px">
                <el-form-item label="当前密码" prop="old">
                  <el-input v-model="params.old" type="password" placeholder="请输入当前的密码" show-password></el-input>
                </el-form-item>
                <el-form-item label="新的密码" prop="password">
                  <el-input v-model="params.password" type="password" placeholder="请输入您的新密码" show-password></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirmation">
                  <el-input v-model="params.password_confirmation" type="password" placeholder="请再次输入您的新密码" show-password></el-input>
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" :loading="loading" @click="submit('password')">确认修改</el-button>
                </el-form-item>
              </el-form>
            </div>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../../../apis';

  export default {
    name: "Profile",
    data() {
      let confirmation = (rule, value, callback) => {
        if (value !== this.params.password) {
          callback(new Error('两次输入密码不一致!'));
        } else {
          callback();
        }
      };
      return {
        header: '',
        loading: false,
        active: 'address',
        classes: ['animated', 'fade-in', 'fast'],
        addrform: {
          address: ''
        },
        profile: {
          name: '',
          email: '',
          mobile: '',
          balance: '',
          discount: '',
          vip: '',
          province: '',
          municipality: '',
          prefecture: '',
          address: ''
        },
        params: {
          old: '',
          password: '',
          password_confirmation: ''
        },
        rules: {
          old: [
            {required: true, message: '请输入密码', trigger: 'blur'}
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
      discount(number) {
        if (number === 100) {
          return '无折扣';
        }

        let tensDigit = Math.floor(number % 100 / 10);
        let unitsDigit = Math.floor(number % 10);
        if (unitsDigit === 0) {
          return `${tensDigit} 折`
        }
        return `${tensDigit}${unitsDigit} 折`
      },
      submit(form) {
        switch (form) {
          case 'password':
            this.$refs.password.validate(valid => {
              if (valid) {
                this.loading = true;
                api.account.password(this.params).then(res => {
                  this.loading = false;
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.$refs.password.resetFields();
                }).catch(err => {
                  this.loading = false;
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  });
                });
              } else {
                return false;
              }
            });
            break;
          case 'address':
            this.$refs.address.validate(valid => {
              if (valid) {
                this.loading = true;
                api.account.address(this.addrform).then(res => {
                  this.loading = false;
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.$refs.address.resetFields();
                }).catch(err => {
                  this.loading = false;
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  });
                });
              } else {
                return false;
              }
            });
            break;
          default:
            return false;
        }
      },
      fetchProfile() {
        api.account.profile().then(res => {
          this.profile = res.data;
        });
      }
    },
    mounted() {
      this.fetchProfile();
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
    border-radius: 4px;
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
  .address {
    text-align: center;
  }
  .information {
    margin-top: 20px;
    .status {
      margin: 10px 0 0 0;
      text-align: center;
      width: 100%;
      ul {
        width: auto;
        margin: 0 auto;
        overflow: auto;
        list-style: none;
        line-height: 40px;
        display: inline-block;
        :after {
          clear: both;
        }
      }
      li {
        margin: 0 5px;
        display: inline;
        line-height: 40px;
        float: left;
        :last-child {
          clear: both;
        }
      }
    }
  }
</style>
