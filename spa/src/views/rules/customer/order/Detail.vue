<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="12" style="text-align: left">
            <el-button type="info" plain @click="$router.back()">返回</el-button>
          </el-col>
          <el-col :span="12" style="text-align: right">
            <el-button v-if="active === 'logistic' && order.logistic === null" @click="logistic.create.dialog = true">添加收件人</el-button>
            <el-button v-if="active === 'logistic' && order.logistic !== null" @click="logisticEditHandler">编辑收件人</el-button>
            <el-button v-if="active === 'order'" type="primary" plain @click="handleCreate">添加商品</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <el-dialog title="添加收件人" :visible.sync="logistic.create.dialog" @close="handleClose('createLogistic')" width="600px" :close-on-click-modal="false">
      <el-form :model="logistic.create.params" :rules="logistic.create.rules" ref="createLogistic" label-position="top">
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="收件人" prop="receiver">
              <el-input v-model="logistic.create.params.receiver" placeholder="请输入收件人姓名" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="联系方式" prop="mobile">
              <el-input v-model="logistic.create.params.mobile" placeholder="请输入收件人联系方式" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item label="付费方式">
              <el-select style="width: 100%" v-model="logistic.create.params.type" @clear="handleClear" clearable filterable placeholder="类型">
                <el-option label="寄付" value="寄付"></el-option>
                <el-option label="到付" value="到付"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="地址" prop="address">
              <el-input v-model="logistic.create.params.address" placeholder="请输入收件人地址" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="备注" prop="remark">
              <el-input v-model="logistic.create.params.remark" placeholder="请输入备注信息" clearable></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="logistic.create.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('createLogistic')">确定</el-button>
      </div>
    </el-dialog>
    <el-dialog title="编辑收件人" :visible.sync="logistic.update.dialog" @close="handleClose('updateLogistic')" width="600px" :close-on-click-modal="false">
      <el-form :model="logistic.update.params" :rules="logistic.update.rules" ref="updateLogistic" label-position="top">
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="收件人" prop="receiver">
              <el-input v-model="logistic.update.params.receiver" placeholder="请输入收件人姓名" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="10">
            <el-form-item label="联系方式" prop="mobile">
              <el-input v-model="logistic.update.params.mobile" placeholder="请输入收件人联系方式" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="6">
            <el-form-item label="付费方式">
              <el-select style="width: 100%" v-model="logistic.update.params.type" @clear="handleClear" clearable filterable placeholder="类型">
                <el-option label="寄付" value="寄付"></el-option>
                <el-option label="到付" value="到付"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="地址" prop="address">
              <el-input v-model="logistic.update.params.address" placeholder="请输入收件人地址" clearable></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="24">
            <el-form-item label="备注" prop="remark">
              <el-input v-model="logistic.update.params.remark" placeholder="请输入备注信息" clearable></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="logistic.update.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('updateLogistic')">确定</el-button>
      </div>
    </el-dialog>
    <el-dialog title="创建订单详情" :visible.sync="create.dialog" @close="handleClose('create')" width="600px"
               :close-on-click-modal="false">
      <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
        <el-row :gutter="10">
          <el-col :span="24">
            <el-form-item label="商品信息" prop="commodity_id">
              <el-select style="width: 100%" v-model="create.params.commodity_id" :loading="search.loading" @change="changeCommodity" @focus="fetchCommodities" :remote-method="searchCommodities" clearable remote filterable placeholder="请选择商品">
                <el-option-group v-for="group in commodities" :key="group.label" :label="group.label">
                  <el-option v-for="item in group.options" :key="item.id" :label="`${item.brand} ${item.name} ${item.specification ? item.specification : ''}`" :value="item.id" :disabled="order.type === '销售' && item.amount === 0">
                    <span style="float: left">{{ item.brand }} {{ item.name }} {{ item.specification ? item.specification : '' }}</span>
                    <span style="float: right; color: #8492a6; font-size: 13px">剩余 {{ item.amount }} {{ item.unit }}</span>
                  </el-option>
                </el-option-group>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="数量" prop="amount">
              <el-input-number v-model="create.params.amount" :min="1" :max="order.type === '销售' ? create.amount : 99999" :controls="false" @change="changeAmount" placeholder="请输入数量"></el-input-number>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="单价" prop="price">
              <el-input-number v-model="create.params.price" :min="0" :precision="2" :controls="false" :disabled="order.type === '损耗'" @change="changePrice" placeholder="请输入单价"></el-input-number>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="总价">
              <el-input-number v-model="create.params.total" :min="0" :precision="2" :controls="false" disabled></el-input-number>
            </el-form-item>
          </el-col>
        </el-row>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="create.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('create')">确定</el-button>
      </div>
    </el-dialog>
    <div class="panel-body" :class="classes">
      <div class="details">
        <table>
          <tbody>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">订单ID</span>
              <span class="detail-item-content">{{order.id}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">总金额</span>
              <span class="detail-item-content">{{order.total}}
                <el-tooltip class="item" effect="dark" content="所有商品的总金额之和" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">订单日期</span>
              <span class="detail-item-content">{{order.date}}</span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">折扣</span>
              <span class="detail-item-content">{{discount()}}
                <el-tooltip class="item" effect="dark" content="每笔订单的折扣" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">实际金额</span>
              <span class="detail-item-content">{{order.actual}}
                <el-tooltip class="item" effect="dark" content="总金额打折后的实际金额" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">备注</span>
              <span class="detail-item-content">{{order.remark === null ? '无备注' : order.remark}}</span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <el-tabs style="padding: 0 20px" v-model="active">
        <el-tab-pane label="订单列表" name="order">
          <el-table v-loading="loading" :data="tradings" style="width: 100%" ref="pipeline">
            <el-table-column prop="id" label="ID" min-width="100"></el-table-column>
            <el-table-column prop="brand" label="品牌"></el-table-column>
            <el-table-column prop="name" label="名称"></el-table-column>
            <el-table-column prop="specification" label="规格"></el-table-column>
            <el-table-column prop="unit" label="单位"></el-table-column>
            <el-table-column prop="amount" label="数量"></el-table-column>
            <el-table-column prop="price" label="单价"></el-table-column>
            <el-table-column prop="total" label="总价"></el-table-column>
            <el-table-column prop="option" label="操作" width="80">
              <template slot-scope="scope">
                <el-tooltip class="item" effect="dark" content="删除" placement="top">
                  <el-button size="mini" icon="el-icon-delete" type="danger" plain circle
                             @click="handleDelete(scope.row)"></el-button>
                </el-tooltip>
              </template>
            </el-table-column>
          </el-table>
          <div class="pagination">
            <el-pagination background layout="sizes, total, prev, pager, next"
                           :page-size="params.size" :page-sizes="meta.page_sizes"
                           :current-page.sync="params.page" :total="meta.total"
                           @current-change="handleCurrentChange"
                           @size-change="handleSizeChange">
            </el-pagination>
          </div>
        </el-tab-pane>
        <el-tab-pane label="收件人信息" name="logistic">
          <div v-if="order.logistic !== null" class="details">
            <table>
              <tbody>
              <tr>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">收件人</span>
                  <span class="detail-item-content">{{order.logistic.receiver}}</span>
                </td>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">联系方式</span>
                  <span class="detail-item-content">{{order.logistic.mobile}}
                <el-tooltip class="item" effect="dark" content="发货时该号码会收到提醒" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
                </td>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">收件地址</span>
                  <span class="detail-item-content">{{order.logistic.address}}</span>
                </td>
              </tr>
              <tr>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">运单号</span>
                  <span class="detail-item-content">{{order.logistic.number ? order.logistic.number : '暂无'}}
                <el-tooltip class="item" effect="dark" content="未发货状态无快递信息" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
                </td>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">快递公司</span>
                  <span class="detail-item-content">{{order.logistic.company !== null ? order.logistic.company : '暂无'}}
                <el-tooltip class="item" effect="dark" content="未发货状态无快递信息" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
                </td>
              </tr>
              <tr>
                <td class="detail-item">
                  <span class="detail-item-label detail-item-colon">备注</span>
                  <span class="detail-item-content">{{order.logistic.remark === null ? '无备注' : order.remark}}</span>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis'

  export default {
    name: "Detail",
    data() {
      return {
        active: 'order',
        loading: false,
        classes: ['animated', 'fade-in', 'fast'],
        order: {
          id: '',
          type: '',
          date: '',
          cost: 0,
          discount: 100,
          total: '',
          profit: 0,
          actual: 0,
          remark: '',
          created_at: '',
          logistic: null,
        },
        tradings: [],
        commodities: [],
        logistic: {
          create: {
            dialog: false,
            params: {
              order_id: 0,
              type: '寄付',
              receiver: '',
              mobile: '',
              address: '',
              remark: ''
            },
            rules: {
              type: [
                {type: 'string', required: true, message: '付费方式', trigger: 'change'}
              ],
              receiver: [
                {type: 'string', required: true, message: '请输入收件人姓名', trigger: 'blur'}
              ],
              mobile: [
                {type: 'string', required: true, message: '请输入联系方式', trigger: 'blur'}
              ],
              address: [
                {type: 'string', required: true, message: '请输入收件地址', trigger: 'blur'}
              ]
            }
          },
          update: {
            dialog: false,
            params: {
              order_id: 0,
              type: '寄付',
              receiver: '',
              mobile: '',
              address: '',
              remark: ''
            },
            rules: {
              type: [
                {type: 'string', required: true, message: '付费方式', trigger: 'change'}
              ],
              receiver: [
                {type: 'string', required: true, message: '请输入收件人姓名', trigger: 'blur'}
              ],
              mobile: [
                {type: 'string', required: true, message: '请输入联系方式', trigger: 'blur'}
              ],
              address: [
                {type: 'string', required: true, message: '请输入收件地址', trigger: 'blur'}
              ]
            }
          }
        },
        create: {
          amount: 0,
          dialog: false,
          params: {
            order_id: null,
            commodity_id: null,
            amount: 0,
            price: 0.00,
            total: 0.00
          },
          rules: {
            commodity_id:[
              {type: 'number', required: true, message: '请选择商品', trigger: 'change'}
            ],
            amount:[
              {type: 'number', required: true, message: '请输入数量', trigger: 'blur'}
            ],
            price:[
              {type: 'number', required: true, message: '请输入单价', trigger: 'blur'}
            ]
          }
        },
        search: {
          loading: false,
          params: {
            scene: 'select'
          }
        },
        params: {
          order: parseInt(this.$route.params.id),
          size: 5,
          page: 1,
          search: ''
        },
        meta: {
          total: 0,
          page_sizes: [5, 10, 20]
        },
      }
    },
    methods: {
      fetchOrder() {
        if (this.$route.params.hasOwnProperty('id')) {
          api.order.fetchOrder(this.$route.params.id).then(res => {
            this.order = res.data;
          }).catch(err => {
            this.$message.error({
              offset: 95,
              message: err.message
            });
          });
        } else {
          this.$message.error({
            offset: 95,
            message: '缺少ID参数'
          });
        }
      },
      fetchTradings() {
        if (this.$route.params.hasOwnProperty('id')) {
          api.trading.fetchTradings(this.params).then(res => {
            this.tradings = res.data;
            this.meta.total = res.total;
          }).catch(err => {
            this.$message.error({
              offset: 95,
              message: err.message
            })
          });
        }
      },
      fetchCommodities() {
        api.commodity.fetchCommodities({
          scene: 'select'
        }).then(res => {
          this.commodities = res.data;
        });
      },
      searchCommodities(search) {
        if (search !== '') {
          this.search.loading = true;
          api.commodity.fetchCommodities({
            scene: 'select',
            search: search
          }).then(res => {
            this.commodities = res.data;
            this.search.loading = false;
          }).catch(err => {
            this.$message({
              offset: 95,
              message: err.message
            })
          });
        }
      },
      handleClear() {

      },
      handleCreate(){
        this.create.dialog = true;
      },
      logisticEditHandler() {
        Object.keys(this.logistic.update.params).forEach(value => {
          this.logistic.update.params[value] = this.order.logistic[value];
        });
        this.logistic.update.dialog = true;
      },
      discount() {
        if (this.order.discount === 100) {
          return '无折扣';
        }

        let tensDigit = Math.floor(this.order.discount % 100 / 10);
        let unitsDigit = Math.floor(this.order.discount % 10);
        if (unitsDigit === 0) {
          return `${tensDigit} 折`
        }
        return `${tensDigit}${unitsDigit} 折`
      },
      handleClose(form) {
        switch (form) {
          case 'create':
            this.$refs.create.resetFields();
            this.create.dialog = false;
            break;
          case 'update':
            break;
          case 'createLogistic':
            this.$refs.createLogistic.resetFields();
            this.logistic.create.dialog = false;
            break;
          case 'updateLogistic':
            this.$refs.updateLogistic.resetFields();
            this.logistic.update.dialog = false;
            break;
        }
      },
      submit(action) {
        switch (action) {
          case 'create':
            this.$refs.create.validate(valid => {
              if (valid) {
                this.create.params.total = this.create.params.amount * this.create.params.price;
                api.trading.createTrading(this.create.params).then(res => {
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.order.total += this.create.params.total;
                  this.fetchOrder();
                  this.fetchTradings();
                  this.create.dialog = false;
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
          case 'createLogistic':
            this.$refs.createLogistic.validate(valid => {
              if (valid) {
                this.loading = true;
                this.logistic.create.params.order_id = this.order.id;
                api.logistics.create(this.logistic.create.params).then(res => {
                  this.order.logistic = res.data;
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.loading = false;
                  this.logistic.create.dialog = false;
                }).catch(err => {
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  })
                  this.loading = false;
                });
              }
            });
            break;
          case 'updateLogistic':
            this.$refs.updateLogistic.validate(valid => {
              if (valid) {
                this.loading = true;
                api.logistics.update(this.order.logistic.id, this.logistic.update.params).then(res => {
                  this.order.logistic = res.data;
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.loading = false;
                  this.logistic.update.dialog = false;
                }).catch(err => {
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  })
                  this.loading = false;
                });
              }
            });
            break;
          case 'update':
            break;
        }
      },
      changeCommodity(id) {
        this.commodities.forEach((group) => {
          group.options.forEach((commodity) => {
            if (commodity.id === id) {
              this.create.amount = commodity.amount;
            }
          });
        });
      },
      changeAmount(amount) {
        this.create.params.total = amount * this.create.params.price;
      },
      changePrice(price) {
        this.create.params.total = this.create.params.amount * price;
      },
      handleDelete(row) {
        this.$confirm('此操作将删除订单中的商品，是否继续', '警告', {
          confirmButtonText: '继续',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          api.trading.deleteTrading(row.id).then(res => {
            this.$message({
              type: 'success',
              offset: 95,
              message: res.message
            });
            this.fetchOrder();
            this.fetchTradings();
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
      handleCurrentChange(page) {
        this.params.page = page;
        this.fetchTradings();
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchTradings();
      },
    },
    mounted() {
      this.create.params.order_id = this.$route.params.id;
      this.fetchOrder();
      this.fetchTradings();
    }
  }
</script>

<style lang="scss">
  .details {
    padding: 20px;
    width: 100%;
    font-size: 14px;
    table {
      width: 100%;
    }
    .detail-item-colon:after {
      position: relative;
      content: ':';
      margin: 0 8px 0 2px;
    }
    .detail-item-label {
      color: #303133;
    }
    .detail-item-content {
      color: #5d5e5f;
    }
    .detail-item {
      padding-bottom: 20px;
    }
  }
  .logistics {
  }
</style>
