<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="8">
            <el-input placeholder="在这里输入要搜索的内容，按下回车进行搜索" v-model="params.search" @keyup.enter.native="fetchCustomers" @clear="handleClear" clearable>
              <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
          </el-col>
          <el-col :span="16" style="text-align: right"></el-col>
        </el-row>
      </div>
    </div>
    <el-dialog title="创建库存" :visible.sync="create.dialog" @close="handleClose('create')" width="500px" :close-on-click-modal="false">
      <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="姓名" prop="name">
              <el-input v-model="create.params.name" autocomplete="off" placeholder="请输入姓名"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="手机" prop="mobile">
              <el-input v-model="create.params.mobile" autocomplete="off" placeholder="请输入手机号"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="备注" prop="description">
          <el-input v-model="create.params.description" autocomplete="off" placeholder="请输客户备注信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="create.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('create')">确定</el-button>
      </div>
    </el-dialog>
    <div class="panel-body" :class="classes">
      <el-table v-loading="loading" :data="customers" :default-sort="meta.sort" style="width: 100%" ref="pipeline">
        <el-table-column prop="name" label="姓名" width="100"></el-table-column>
        <el-table-column prop="email" label="邮箱"></el-table-column>
        <el-table-column prop="balance" label="余额" width="80"></el-table-column>
        <el-table-column prop="vip" label="会员" width="50">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.vip === 0" size="small" type="info">否</el-tag>
            <el-tag v-else size="small">是</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="vip" label="折扣" width="80">
          <template slot-scope="scope"><span>{{discount(scope.row)}}</span></template>
        </el-table-column>
        <el-table-column prop="created_at" label="注册于"></el-table-column>
        <el-table-column prop="remark" label="备注"></el-table-column>
        <el-table-column prop="option" label="操作" width="100">
          <template slot-scope="scope">
            <el-tooltip class="item" effect="dark" content="详情" placement="top">
              <el-button size="mini" icon="el-icon-tickets" plain circle
                         @click="viewDetails(scope.row)"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
              <el-button size="mini" icon="el-icon-delete" type="danger" plain circle
                         @click="handleDelete(scope.row)"></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination background layout="sizes, total, prev, pager, next" :page-size="params.size" :page-sizes="meta.page_sizes" :current-page.sync="params.current_page" :total="meta.total"
                       @current-change="handleCurrentChange" @size-change="handleSizeChange"></el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import api from "../../../../apis";

  export default {
    name: "List",
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        loading: false,
        params: {
          size: 15,
          sort: 'created_at',
          search: '',
          descend: 1,
          current_page: 1
        },
        create: {
          dialog: false,
          params: {
            name: '',
            mobile: '',
            remark: ''
          },
          rules: {
            name: [
              {type: 'string', required: true, message: '请输入姓名', trigger: 'blur'}
            ],
            mobile: [
              {type: 'string', required: true, message: '请输入手机号', trigger: 'blur'}
            ],
          }
        },
        customers: [],
        options: [],
        meta: {
          total: 0,
          page_sizes: [10, 20, 50],
          sort: {
            prop: 'created_at',
            order: 'descending'
          }
        }
      }
    },
    methods: {
      discount(row) {
        if (row.discount === 100) {
          return '无折扣';
        }

        let tensDigit = Math.floor(row.discount % 100 / 10);
        let unitsDigit = Math.floor(row.discount % 10);
        if (unitsDigit === 0) {
          return `${tensDigit} 折`
        }
        return `${tensDigit}${unitsDigit} 折`
      },
      changePage(page) {
        this.meta.page = page;
        this.params.page = page;
      },
      handleCreate() {
        this.create.dialog = true;
      },
      handleClear() {

      },
      submit() {
        api.customer.createCustomer(this.create.params).then(() => {
          this.handleClose('create');
          this.fetchCustomers();
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
        });
      },
      viewDetails(customer) {
        this.$router.push({path: `/customer/${customer.id}/detail`});
      },
      handleDelete(customer) {
        api.customer.fetchCustomer(customer.id).then(res => {
          if (res.data.orders.length > 0) {
            this.$confirm('该客户有历史订单，是否要删除该用户？', '警告', {
              confirmButtonText: '删除',
              cancelButtonText: '取消',
              type: 'warning'
            }).then(() => {
              api.customer.deleteCustomer(customer.id).then(res => {
                this.fetchCustomers();
                this.$message.success({
                  offset: 95,
                  message: res.message
                });
              }).catch(err => {
                this.$message.error({
                  offset: 95,
                  message: err.message
                });
              });
            }).catch(() => {
              this.$message.success({
                offset: 95,
                message: '您已取消删除操作。'
              });
            });
          } else {
            api.customer.deleteCustomer(customer.id).then(res => {
              this.fetchCustomers();
              this.$message.success({
                offset: 95,
                message: res.message
              });
            }).catch(err => {
              this.$message.error({
                offset: 95,
                message: err.message
              });
            });
          }
        });
      },
      handleClose(form) {
        switch (form) {
          case 'create':
            this.$refs.create.resetFields();
            this.create.dialog = false;
            break;
          default:
        }
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchCustomers();
      },
      handleCurrentChange(page) {
        this.params.page = page;
      },
      fetchCustomers() {
        this.loading = true;
        api.customer.fetchCustomers(this.params).then(res => {
          this.customers = res.data;
          this.meta.per_page = res.per_page;
          this.meta.current_page = res.current_page;
          this.meta.total = res.total;
          this.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
        });
      }
    },
    mounted() {
      this.fetchCustomers();
    }
  }
</script>

<style lang="scss">
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }

  .fade-enter, .fade-leave-to
  {
    opacity: 0;
  }
</style>
