<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :xs="24" :span="6">
            <el-input placeholder="在这里输入要搜索的内容" v-model="params.search" @keyup.enter.native="fetchOrders" @clear="handleClear" clearable>
              <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
          </el-col>
          <el-col :xs="24" :span="3">
            <el-date-picker style="width: 100%" v-model="params.date" value-format="yyyy-MM-dd" type="date" placeholder="选择日期"></el-date-picker>
          </el-col>
          <el-col :xs="12" :span="2">
            <el-button type="primary" icon="el-icon-search" @click="fetchOrders" plain>搜索</el-button>
          </el-col>
          <el-col :xs="12" :span="13" style="text-align: right">
            <el-button type="primary" plain @click="handleCreate">创建</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <el-dialog title="创建订单" :visible.sync="create.dialog" @close="handleClose('create')" width="600px"
               :close-on-click-modal="false">
      <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
        <el-form-item label="备注" prop="remark">
          <el-input v-model="create.params.remark" autocomplete="off" placeholder="请输订单描述信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="create.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('create')">确定</el-button>
      </div>
    </el-dialog>
    <div class="panel-body" :class="classes">
      <el-table v-loading="loading" :data="orders" @sort-change="changeSort" style="width: 100%">
        <el-table-column prop="id" label="ID" min-width="80"></el-table-column>
        <el-table-column prop="total" label="总价"></el-table-column>
        <el-table-column prop="discount" label="折扣">
          <template slot-scope="scope"><span>{{discount(scope.row)}}</span></template>
        </el-table-column>
        <el-table-column prop="actual" label="实际金额"></el-table-column>
        <el-table-column prop="date" label="日期" sortable="custom"></el-table-column>
        <el-table-column prop="status" label="状态">
          <template slot-scope="scope">
            <el-tag v-if="scope.row.status === 'pending'" size="small" type="info">待确认</el-tag>
            <el-tag v-else-if="scope.row.status === 'confirmed'" size="small">已确认</el-tag>
            <el-tag v-else size="small" type="success">已发货</el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="remark" label="备注" min-width="100px"></el-table-column>
        <el-table-column prop="option" label="操作" width="130">
          <template slot-scope="scope">
            <el-tooltip class="item" effect="dark" content="详情" placement="top">
              <el-button size="mini" icon="el-icon-tickets" plain circle
                         @click="viewDetails(scope.row)" :disabled="['邮费', '满减'].includes(scope.row.type)"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="导出Excel" placement="top">
              <el-button size="mini" icon="el-icon-document" plain circle
                         @click="downloadExcel(scope.row)" :disabled="scope.row.type !== '采购'"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
              <el-button size="mini" icon="el-icon-delete" type="danger" plain circle
                         @click="handleDelete(scope.row)"></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination background :layout="meta.layout"
                       :page-size="params.size"
                       :total="meta.total"
                       :pager-count="meta.pager_count"
                       :page-sizes="meta.page_sizes"
                       :current-page.sync="params.current_page"
                       @current-change="handleCurrentChange"
                       @size-change="handleSizeChange">
        </el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis';

  export default {
    name: 'OrderList',
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        loading: false,
        params: {
          size: 10,
          sort: 'id',
          brand: '',
          zero: null,
          search: '',
          type: '',
          date: null,
          descend: 1,
          current_page: 1
        },
        create: {
          dialog: false,
          params: {
            remark: ''
          }
        },
        brands: [],
        categories: [],
        orders: [],
        customers: [],
        meta: {
          total: 0,
          pager_count: 11,
          page_sizes: [10, 20, 50],
          layout: 'sizes, total, prev, pager, next'
        },
      }
    },
    methods: {
      /**
       * 分页跳转时触发
       */
      handleCurrentChange(page) {
        this.params.page = page;
        this.fetchOrders();
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
      fetchCustomers() {
        api.customer.fetchCustomers({
          scene: 'select'
        }).then(res => {
          this.customers = res.data;
        });
      },
      searchCustomers() {

      },
      /**
       * Submit form
       * @param form
       */
      submit(form){
        switch (form) {
          case 'create':
            this.$refs.create.validate((valid) => {
              if (valid) {
                api.order.createOrder(this.create.params).then(res => {
                  let type = this.create.params.type;
                  this.handleClose(form);
                  if (['销售', '采购', '损耗'].includes(type)) {
                    this.$confirm('订单创建成功，是否进入该订单详情页面？', '成功', {
                      confirmButtonText: '进入',
                      cancelButtonText: '取消',
                      type: 'success'
                    }).then(() => {
                      this.$router.push({
                        name: 'orderDetail',
                        params: {
                          id: res.data.id
                        }
                      })
                    }).catch(() => {
                      this.$message.success({
                        offset: 95,
                        message: res.message
                      });
                      this.fetchOrders();
                    });
                  } else {
                    this.$message.success({
                      offset: 95,
                      message: res.message
                    });
                    this.fetchOrders();
                  }
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
            api.order.updateOrder(this.update.id, this.update.params).then(res => {
              this.$message.success({
                offset: 95,
                message: res.message
              });
              this.fetchOrders();
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
      handleCreate() {
        this.create.dialog = true;
      },
      changeSort(sortable) {
        this.loading = true;
        this.params.sort = sortable.order === null ? 'id' : sortable.prop;
        this.params.descend = sortable.order === 'descending' ? 1 : 0;
        this.fetchOrders();
      },
      handleEdit(row) {
        this.update.dialog = true;
        this.update.id = row.id;
        this.update.params.date = row.date;
        this.update.params.type = row.type;
        this.update.params.remark = row.remark;
        this.update.params.total = row.total;
      },
      handleDelete(row) {
        this.$confirm('此操作将删除商品，是否继续', '警告', {
          confirmButtonText: '继续',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          api.order.deleteOrder(row.id).then(res => {
            this.$message({
              type: 'success',
              offset: 95,
              message: res.message
            });
            this.fetchOrders();
          }).catch(err => {
            this.$message.error({
              offset: 95,
              message: err.message
            })
          });
        }).catch(() => {
          this.$message.info({
            offset: 95,
            message: '操作已取消'
          });
        });
      },
      viewDetails(row) {
        this.$router.push({path: `/order/${row.id}/detail`});
      },
      downloadExcel(row) {
        api.order.fetchOrder(row.id, 'excel').then(res => {
          let descriptions = res.headers['content-disposition'].split('; ');
          let blob = new Blob([res.data], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=utf-8'
          });
          let downloadElement = document.createElement('a');
          let href = window.URL.createObjectURL(blob);
          if (descriptions.length > 0) {
            descriptions.forEach(item => {
              if (item.search('filename=') !== -1) {
                downloadElement.download = item.replace('filename=', '');
              }
            })
          }
          downloadElement.href = href;
          document.body.appendChild(downloadElement);
          downloadElement.click();
          document.body.removeChild(downloadElement);
          window.URL.revokeObjectURL(href);
        }).catch(err => {
          window.console.log(err);
        });
      },
      handleClear() {
        this.fetchOrders();
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchOrders();
      },
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
      fetchOrders() {
        this.loading = true;
        api.order.fetchOrders(this.params).then(res => {
          this.orders = res.data;
          this.params.current_page = res.current_page;
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
      if (document.body.clientWidth < 767) {
        this.meta.pager_count = 5;
        this.meta.layout = 'prev, pager, next';
      }
      this.fetchOrders();
    }
  }
</script>

<style lang="scss">

</style>
