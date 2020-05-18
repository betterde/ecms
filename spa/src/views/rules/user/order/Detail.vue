<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="12" style="text-align: left">
            <el-button type="info" plain @click="$router.back()">返回</el-button>
          </el-col>
          <el-col :span="12" style="text-align: right">
            <el-button type="primary" plain @click="handleCreate">添加商品</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <div v-if="order.status !== null" class="panel-infix">
      <el-steps :active="step" align-center finish-status="success">
        <el-step title="完善信息" description="添加商品并填写收件人信息"></el-step>
        <el-step title="确认订单" description="确认订单并付款后等待商家发货"></el-step>
        <el-step title="已完成" description="商家已经发货可查询运单号"></el-step>
      </el-steps>
    </div>
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
      <div class="details" style="padding: 20px">
        <table>
          <tbody>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">订单ID</span>
              <span class="detail-item-content">{{order.id}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">订单类型</span>
              <span class="detail-item-content">{{order.type}}
                <el-tooltip class="item" effect="dark" content="采购：代表进货的订单明细；销售：代表售出的订单明细" placement="top">
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
              <span class="detail-item-label detail-item-colon">总金额</span>
              <span class="detail-item-content">{{order.total}}
                <el-tooltip class="item" effect="dark" content="所有商品的总金额之和" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
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
              <span class="detail-item-label detail-item-colon">成本</span>
              <span class="detail-item-content">{{order.cost}}
                <el-tooltip class="item" effect="dark" content="所有商品的进货成本之和" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">利润</span>
              <span class="detail-item-content">{{order.profit}}
                <el-tooltip class="item" effect="dark" content="实际金额减去成本" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
            <td v-if="order.type === '销售' && order.customer !== null" class="detail-item">
              <span class="detail-item-label detail-item-colon">客户</span>
              <span class="detail-item-content">{{order.customer === null ? '无客户信息' : order.customer.name}}</span>
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
    <div v-if="order.status === 'confirmed'" class="panel-footer">
      <el-dialog title="添加快递信息" :visible.sync="logistic.dialog" @close="handleClose('updateLogistic')" width="600px"
                 :close-on-click-modal="false">
        <el-form :model="logistic.params" :rules="logistic.rules" ref="updateLogistic" label-position="top">
          <el-row :gutter="10">
            <el-col :span="12">
              <el-form-item label="快递公司" prop="company">
                <el-select style="width: 100%" v-model="logistic.params.company" clearable placeholder="请选择公司名称">
                  <el-option label="顺丰速运" value="顺丰速运"></el-option>
                  <el-option label="百世快递" value="百世快递"></el-option>
                  <el-option label="申通快递" value="申通快递"></el-option>
                  <el-option label="中通快递" value="中通快递"></el-option>
                  <el-option label="圆通速递" value="圆通速递"></el-option>
                  <el-option label="韵达快递" value="韵达快递"></el-option>
                  <el-option label="天天快递" value="天天快递"></el-option>
                  <el-option label="京东快递" value="京东快递"></el-option>
                  <el-option label="德邦快递" value="德邦快递"></el-option>
                </el-select>
              </el-form-item>
            </el-col>
            <el-col :span="12">
              <el-form-item label="运单号" prop="number">
                <el-input v-model="logistic.params.number" placeholder="请输入运单号"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="logistic.dialog = false">取消</el-button>
          <el-button type="primary" @click="submit('updateLogistic')">确定</el-button>
        </div>
      </el-dialog>
      <el-button :disabled="order.status !== 'confirmed'" style="float: right" type="primary" @click="logistic.dialog = true">确认发货</el-button>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis'

  export default {
    name: "Detail",
    data() {
      return {
        step: 0,
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
          logistic: null,
          created_at: ''
        },
        tradings: [],
        commodities: [],
        logistic: {
          dialog: false,
          params: {
            number: '',
            company: '',
          },
          rules: {
            number: [
              {type: 'string', required: true, message: '请填写运单号', trigger: 'change'}
            ],
            company: [
              {type: 'string', required: true, message: '请选择快递公司', trigger: 'blur'}
            ]
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
            switch (this.order.status) {
              case 'pending':
                this.step = 0;
                break;
              case 'confirmed':
                this.step = 2;
                break;
              case 'completed':
                this.step = 3;
                break;
              default:
                this.step = 0;
                break;
            }
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
            window.console.log(err);
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
      handleCreate(){
        this.create.dialog = true;
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
            break;
          case 'update':
            break;
          case 'updateLogistic':
            this.$refs.updateLogistic.resetFields();
            break;
        }
      },
      submit(action) {
        switch (action) {
          case 'create':
            this.$refs.create.validate((valid) => {
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
          case 'update':
            break;
          case 'updateLogistic':
            this.$refs.updateLogistic.validate((valid) => {
              if (valid) {
                api.logistics.updateNumber(this.order.logistic.id, this.logistic.params).then(res => {
                  this.fetchOrder();
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.logistic.dialog = false;
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
  .statement {
    text-align: center;
  }
</style>
