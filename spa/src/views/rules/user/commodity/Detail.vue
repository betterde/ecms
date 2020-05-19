<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="12" style="text-align: left">
            <el-button type="info" plain @click="$router.back()">返回</el-button>
          </el-col>
          <el-col :span="12" style="text-align: right">
            <el-button type="primary" plain @click="discount.dialog = true">添加折扣</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <div class="panel-body" :class="classes">
      <el-tabs v-model="active" @tab-click="changeTab">
        <el-tab-pane label="价格管理" name="discount">
          <el-table v-loading="loading" :data="commodity.discounts" style="width: 100%">
            <el-table-column prop="id" label="ID"></el-table-column>
            <el-table-column prop="number" label="折扣"></el-table-column>
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
        discount: {
          dialog: false,
          params: {},
          rules: {}
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
      fetchCommodity(id) {
        api.commodity.fetchCommodity(id).then(res => {
          this.commodity = res.data;
        });
      },
      handleEdit(discount) {},
      handleDelete(discount) {}
    },
    mounted() {
      this.fetchCommodity(this.$route.params.id);
    }
  }
</script>

<style lang="scss">

</style>
