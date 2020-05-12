<template>
  <div class="main-content">
    <div class="panel">
      <div class="panel-header" :class="classes">
        <div class="panel-tools">
          <el-row :gutter="20">
            <el-col :xs="12" :span="3">
              <el-select v-model="params.brand" @focus="fetchCustomers" @clear="handleClear" :loading="select.loading" clearable filterable placeholder="发起人">
                <el-option v-for="customer in customers" :key="customer.id" :label="customer.name" :value="customer.id"></el-option>
              </el-select>
            </el-col>
            <el-col :xs="24" :span="5">
              <el-input placeholder="输入手机或邮箱进行查找" v-model="params.account" @keyup.enter.native="fetchInvitations" @clear="handleClear" clearable>
                <i slot="prefix" class="el-input__icon el-icon-user"></i>
              </el-input>
            </el-col>
            <el-col :span="10">
              <el-button type="primary" icon="el-icon-search" @click="fetchInvitations" plain>搜索</el-button>
            </el-col>
            <el-col :xs="24" :span="6" style="text-align: right">
              <el-button type="primary" plain @click="handleCreate">发起邀请</el-button>
            </el-col>
          </el-row>
        </div>
      </div>
      <el-dialog title="发起邀请" :visible.sync="create.dialog" @close="handleClose('create')" width="600px" :close-on-click-modal="false">
        <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="联系方式" prop="account">
                <el-input v-model="create.params.account" autocomplete="off" placeholder="请输入名称"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="发送方式" prop="mode">
                <el-select style="width: 100%" v-model="create.params.mode" default-first-option allow-create clearable placeholder="请选择">
                  <el-option label="短信" value="mobile"></el-option>
                  <el-option label="邮件" value="email"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="6">
              <el-form-item label="有效期" prop="expires">
                <el-input-number id="expires" v-model="create.params.expires" :min="30" :max="480" :controls="false" placeholder="单位：分钟"></el-input-number>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="create.dialog = false">取消</el-button>
          <el-button type="primary" @click="submit('create')">确定</el-button>
        </div>
      </el-dialog>
<!--      <el-dialog title="编辑库存" :visible.sync="update.dialog" @close="handleClose('update')" width="600px" :close-on-click-modal="false">-->
<!--        <el-form :model="update.params" :rules="update.rules" ref="update" label-position="top">-->
<!--          <el-row :gutter="10">-->
<!--            <el-col :span="24">-->
<!--              <el-form-item label="名称" prop="name">-->
<!--                <el-input v-model="update.params.name" autocomplete="off" placeholder="请输入名称"></el-input>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
<!--          </el-row>-->
<!--          <el-row :gutter="10">-->
<!--            <el-col :span="12">-->
<!--              <el-form-item label="品牌" prop="brand">-->
<!--                <el-select style="width: 100%" v-model="update.params.brand" @focus="fetchBrands" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请输入或选择一个品牌">-->
<!--                  <el-option v-for="brand in brands" :key="brand.name" :label="brand.name" :value="brand.name"></el-option>-->
<!--                </el-select>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
<!--            <el-col :span="12">-->
<!--              <el-form-item label="分类" prop="category">-->
<!--                <el-select style="width: 100%" v-model="update.params.category" @focus="fetchCategories" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请输入或选择一个分类">-->
<!--                  <el-option v-for="category in categories" :key="category.name" :label="category.name" :value="category.name"></el-option>-->
<!--                </el-select>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
<!--          </el-row>-->
<!--          <el-row :gutter="10">-->
<!--            <el-col :span="12">-->
<!--              <el-form-item label="单位" prop="unit">-->
<!--                <el-select style="width: 100%" v-model="update.params.unit" @focus="fetchUnits" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请选择商品单位">-->
<!--                  <el-option v-for="unit in units" :key="unit.name" :label="unit.name" :value="unit.name"></el-option>-->
<!--                </el-select>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
<!--            <el-col :span="12">-->
<!--              <el-form-item label="规格" prop="specification">-->
<!--                <el-input v-model="update.params.specification" placeholder="请输入商品规格"></el-input>-->
<!--              </el-form-item>-->
<!--            </el-col>-->
<!--          </el-row>-->
<!--          <el-form-item label="描述" prop="description">-->
<!--            <el-input v-model="update.params.description" autocomplete="off" placeholder="请输商品描述信息"></el-input>-->
<!--          </el-form-item>-->
<!--        </el-form>-->
<!--        <div slot="footer" class="dialog-footer">-->
<!--          <el-button @click="update.dialog = false">取消</el-button>-->
<!--          <el-button type="primary" @click="submit('update')">确定</el-button>-->
<!--        </div>-->
<!--      </el-dialog>-->
      <div class="panel-body" :class="classes">
        <el-table v-loading="loading" :data="invitations"  style="width: 100%" ref="pipeline">
          <el-table-column prop="initiator.name" label="发起人"></el-table-column>
          <el-table-column prop="initiator_type" label="用户组">
            <template slot-scope="scope">{{scope.row.initiator_type === 'user' ? '管理员' : '客户'}}</template>
          </el-table-column>
          <el-table-column prop="account" label="联系信息" min-width="100"></el-table-column>
          <el-table-column prop="mode" label="发送方式">
            <template slot-scope="scope">{{scope.row.mode === 'mobile' ? '短信' : '邮件'}}</template>
          </el-table-column>
          <el-table-column prop="expires" label="有效期" min-width="100"></el-table-column>
          <el-table-column prop="status" label="邀请状态">
            <template slot-scope="scope">
              <el-tag :type="scope.row.status === 'unregistered' ? 'info' : 'success'">{{scope.row.status === 'unregistered' ? '未注册' : '已注册'}}</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="created_at" label="创建于"></el-table-column>
          <el-table-column prop="option" label="操作" width="130">
            <template slot-scope="scope">
              <el-tooltip class="item" effect="dark" content="复制连接" placement="top">
                <el-button size="mini" icon="el-icon-link" circle @click="generateRegisterLink(scope.row)"></el-button>
              </el-tooltip>
              <el-tooltip class="item" effect="dark" content="删除" placement="top">
                <el-button :disabled="scope.row.status === 'registered'" size="mini" icon="el-icon-delete" type="danger" plain circle
                           @click="handleDelete(scope.row)"></el-button>
              </el-tooltip>
            </template>
          </el-table-column>
        </el-table>
        <div class="pagination">
          <el-pagination background layout="sizes, total, prev, pager, next"
                         :page-size="params.size" :page-sizes="meta.page_sizes"
                         :current-page.sync="params.current_page" :total="meta.total"
                         @current-change="handleCurrentChange"
                         @size-change="handleSizeChange">

          </el-pagination>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../apis';
  import copy from 'copy-to-clipboard';

  export default {
    name: "Invitation",
    data() {
      return {
        loading: false,
        classes: ['animated', 'fade-in', 'fast'],
        params: {
          account: '',
          size: 10,
        },
        create: {
          dialog: false,
          params: {
            mode: '',
            account: '',
            expires: 30
          },
          rules: {
            mode: [
              {type: 'string', required: true, message: '请选择发送方式', trigger: 'change'}
            ],
            account: [
              {type: 'string', required: true, message: '请输入手机号码', trigger: 'blur'}
            ],
            expires: [
              {type: 'number', required: true, message: '请输入过期时间', trigger: 'blur'}
            ]
          }
        },
        customers: [],
        invitations: [],
        select: {
          loading: false
        },
        meta: {
          total: 0,
          page_sizes: [10, 20, 50]
        }
      }
    },
    methods: {
      fetchCustomers() {
        api.customer.fetchCustomers({
          scene: 'select'
        }).then(res => {
          this.customers = res.data;
        });
      },
      fetchInvitations() {
        this.loading = true;
        api.invitation.fetchInvitations(this.params).then(res => {
          this.invitations = res.data;
          this.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
        });
      },
      handleClear() {
        this.fetchInvitations();
      },
      handleCreate() {
        this.create.dialog = true;
      },
      handleClose(form) {
        switch (form) {
          case 'create':
            this.$refs.create.resetFields();
            this.create.dialog = false;
            break;
          case 'update':
            this.$refs.update.resetFields();
            this.update.dialog = false;
            this.update.id = null;
            this.update.index = null;
            break;
        }
      },
      submit(form) {
        switch (form) {
          case 'create':
            this.$refs.create.validate((valid) => {
              if (valid) {
                api.invitation.createInvitation(this.create.params).then(res => {
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.fetchInvitations();
                  this.handleClose(form);
                }).catch(err => {
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
          case 'update':
            api.commodity.updateCommodity(this.update.id, this.update.params).then(res => {
              this.$message.success({
                offset: 95,
                message: res.message
              });
              this.fetchCommodities();
              this.handleClose(form);
            }).catch(err => {
              this.$message.error({
                offset: 95,
                message: err.message
              });
            });
            break;
        }
      },
      /**
       * 分页跳转时触发
       */
      handleCurrentChange(page) {
        // this.fetchCommodities(page);
      },
      handleSizeChange(size) {
        this.params.size = size;
        // this.fetchCommodities();
      },
      generateRegisterLink(invitation) {
        let date = new Date(invitation.expires);
        let timestamp = date.getTime() / 1000;
        let url = `${window.location.protocol}//${window.location.host}/register?account=${invitation.account}&expires=${timestamp}&initiator=${invitation.initiator_id}&initiator_type=${invitation.initiator_type}&signature=${invitation.signature}`;
        let result = copy(url);
        if (result) {
          this.$message.success({
            offset: 95,
            message: '复制成功，将连接发送个好友注册吧！'
          });
        }
      },
      handleDelete(invitation) {

      }
    },
    mounted() {
      this.fetchInvitations();
    }
  }
</script>

<style lang="scss">
  #expires.el-input-number {
    width: 100%;
  }
</style>
