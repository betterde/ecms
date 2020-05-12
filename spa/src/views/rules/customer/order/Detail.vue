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
      <el-divider content-position="center">商品列表</el-divider>
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
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis'

  export default {
    name: "Detail",
    data() {
      return {
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
          created_at: ''
        },
        tradings: [],
        commodities: [],
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
            this.create.dialog = false;
            break;
          case 'update':
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
                  this.handleClose(action);
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
</style>
