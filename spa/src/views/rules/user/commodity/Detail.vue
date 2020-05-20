<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="12" style="text-align: left">
            <el-button type="info" plain @click="$router.back()">返回</el-button>
          </el-col>
          <el-col :span="12" style="text-align: right">
            <el-button type="primary" plain @click="create.dialog = true">添加折扣</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <el-dialog title="添加折扣" :visible.sync="create.dialog" @close="handleClose('create')" width="500px" :close-on-click-modal="false">
      <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="折扣" prop="number">
              <el-select v-model="create.params.number" clearable placeholder="请选择类型" @change="changeDiscount" style="width: 100%">
                <el-option label="无折扣" :value="100"></el-option>
                <el-option label="95折" :value="95"></el-option>
                <el-option label="9折" :value="90"></el-option>
                <el-option label="85折" :value="85"></el-option>
                <el-option label="8折" :value="80"></el-option>
                <el-option label="75折" :value="75"></el-option>
                <el-option label="7折" :value="70"></el-option>
                <el-option label="65折" :value="65"></el-option>
                <el-option label="6折" :value="60"></el-option>
                <el-option label="55折" :value="55"></el-option>
                <el-option label="5折" :value="50"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="价格" prop="price">
              <el-input v-model="create.params.price" autocomplete="off" placeholder="价格可不填"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="create.params.remark" autocomplete="off" placeholder="请输入备注信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="create.dialog = false">取消</el-button>
        <el-button type="primary" :loading="loading" @click="submit('create')">确定</el-button>
      </div>
    </el-dialog>
    <el-dialog title="编辑折扣" :visible.sync="update.dialog" @close="handleClose('update')" width="500px" :close-on-click-modal="false">
      <el-form :model="update.params" :rules="update.rules" ref="update" label-position="top">
        <el-row :gutter="10">
          <el-col :span="12">
            <el-form-item label="折扣" prop="number">
              <el-select v-model="update.params.number" clearable placeholder="请选择类型" @change="changeDiscount" style="width: 100%">
                <el-option label="无折扣" :value="100"></el-option>
                <el-option label="95折" :value="95"></el-option>
                <el-option label="9折" :value="90"></el-option>
                <el-option label="85折" :value="85"></el-option>
                <el-option label="8折" :value="80"></el-option>
                <el-option label="75折" :value="75"></el-option>
                <el-option label="7折" :value="70"></el-option>
                <el-option label="65折" :value="65"></el-option>
                <el-option label="6折" :value="60"></el-option>
                <el-option label="55折" :value="55"></el-option>
                <el-option label="5折" :value="50"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="12">
            <el-form-item label="价格" prop="price">
              <el-input v-model="update.params.price" autocomplete="off" placeholder="价格可不填"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="备注" prop="remark">
          <el-input v-model="update.params.remark" autocomplete="off" placeholder="请输入备注信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="update.dialog = false">取消</el-button>
        <el-button type="primary" :loading="loading" @click="submit('update')">确定</el-button>
      </div>
    </el-dialog>
    <div class="panel-body" :class="classes">
      <el-tabs v-model="active" @tab-click="changeTab">
        <el-tab-pane label="价格管理" name="discount">
          <el-table v-loading="loading" :data="commodity.discounts" style="width: 100%">
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column label="折扣">
              <template slot-scope="scope"><span>{{discount(scope.row)}}</span></template>
            </el-table-column>
            <el-table-column prop="price" label="价格"></el-table-column>
            <el-table-column prop="created_at" label="创建于"></el-table-column>
            <el-table-column prop="remark" label="备注" min-width="100"></el-table-column>
            <el-table-column prop="option" label="操作" width="130">
              <template slot-scope="scope">
                <el-tooltip class="item" effect="dark" content="编辑" placement="top">
                  <el-button size="mini" icon="el-icon-edit" circle
                             @click="handleEdit(scope.row)"></el-button>
                </el-tooltip>
                <el-tooltip class="item" effect="dark" content="删除" placement="top">
                  <el-button :disabled="scope.row.amount > 0" size="mini" icon="el-icon-delete" type="danger" plain circle
                             @click="handleDelete(scope.row)"></el-button>
                </el-tooltip>
              </template>
            </el-table-column>
          </el-table>
        </el-tab-pane>
        <el-tab-pane label="价格趋势" name="tendency">
          <div id="chart"></div>
        </el-tab-pane>
      </el-tabs>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis'
  import { Chart } from '@antv/g2'

  export default {
    name: "Detail",
    data() {
      return {
        active: 'discount',
        loading: false,
        classes: ['animated', 'fade-in', 'fast'],
        commodity: {
          pricings: [],
          discounts: []
        },
        create: {
          dialog: false,
          params: {
            commodity_id: this.$route.params.id,
            number: '',
            price: '',
            remark: ''
          },
          rules: {
            number: [
              {type: 'number', required: true, message: '请输入折扣', trigger: 'blur'}
            ]
          }
        },
        update: {
          id: null,
          dialog: false,
          params: {
            commodity_id: this.$route.params.id,
            number: 100,
            price: '',
            remark: ''
          },
          rules: {
            number: [
              {type: 'number', required: true, message: '请输入折扣', trigger: 'blur'}
            ]
          }
        },
        params: {},
        tendency: null
      }
    },
    methods: {
      changeTab(current) {
        if (current.name === 'tendency') {
          if (this.tendency === null) {
            this.$nextTick(() => {
              this.tendency = new Chart({
                container: 'chart',
                autoFit: true,
                height: 300,
              });
              this.tendency.data(this.commodity.pricings);
              this.tendency.scale({
                date: {
                  range: [0, 1],
                },
                buying: {
                  nice: true,
                  alias: '价格'
                },
              });

              this.tendency.tooltip({
                showCrosshairs: true,
                shared: true,
              });

              this.tendency.axis('buying', {
                label: {
                  formatter: (val) => {
                    return `¥ ${val}`;
                  },
                },
              });

              this.tendency
                .line()
                .position('date*buying')
                .shape('smooth');

              this.tendency
                .point()
                .position('date*buying')
                .shape('circle')
                .style({
                  stroke: '#fff',
                  lineWidth: 1,
                });
              this.tendency.render();
            });
          }
        }
      },
      discount(row) {
        if (row.number === 100) {
          return '无折扣';
        }

        let tensDigit = Math.floor(row.number % 100 / 10);
        let unitsDigit = Math.floor(row.number % 10);
        if (unitsDigit === 0) {
          return `${tensDigit} 折`
        }
        return `${tensDigit}${unitsDigit} 折`
      },
      changeDiscount(discount) {
        this.commodity.discounts.forEach(item => {
          if (item.number === 100) {
            this.create.params.price = (item.price / 100) * discount;
          }
        });
      },
      fetchCommodity(id) {
        api.commodity.fetchCommodity(id).then(res => {
          this.commodity = res.data;
        });
      },
      handleClose(form) {
        switch (form) {
          case 'create':
            this.$refs.create.resetFields();
            break;
          case 'update':
            this.$refs.update.resetFields();
            break;
        }
      },
      submit(form) {
        switch (form) {
          case 'create':
            this.$refs.create.validate(valid => {
              if (valid) {
                this.loading = true;
                api.discount.createDiscount(this.create.params).then(res => {
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.fetchCommodity(this.$route.params.id);
                  this.loading = false;
                  this.create.dialog = false;
                }).catch(err => {
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  });
                  this.loading = false;
                })
              } else {
                return false;
              }
            });
            break;
          case 'update':
            this.$refs.update.validate(valid => {
              if (valid) {
                this.loading = true;
                api.discount.updateDiscount(this.update.id, this.update.params).then(res => {
                  this.fetchCommodity(this.$route.params.id);
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.loading = false;
                }).catch(err => {
                  this.$message.error({
                    offset: 95,
                    message: err.message
                  });
                })
              } else {
                return false;
              }
            });
            break;
        }
      },
      handleEdit(discount) {
        Object.keys(this.update.params).forEach(key => {
          this.update.params[key] = discount[key];
        });
        this.update.id = discount.id;
        this.update.dialog = true;
      },
      handleDelete(discount) {
        this.$confirm('此操作将删除折扣信息，是否继续', '警告', {
          confirmButtonText: '继续',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          api.discount.deleteDiscount(discount.id).then(res => {
            this.$message.success({
              offset: 95,
              message: res.message
            });
            this.fetchCommodity(this.$route.params.id);
          }).catch(err => {
            this.$message.error({
              offset: 95,
              message: err.message
            });
          });
        }).catch(() => {
          this.$message.info({
            offset: 95,
            message: '操作已取消'
          });
        });
      }
    },
    mounted() {
      this.fetchCommodity(this.$route.params.id);
    }
  }
</script>

<style lang="scss">

</style>
