<template>
  <div class="main-content">
    <div class="panel">
      <el-row :gutter="20">
        <el-col :span="6" :xs="12">
          <div class="card" :class="classes">
            <div class="card_body">
              <h1 class="number">{{summary.total}}</h1>
            </div>
            <div class="card_footer">
              <h4>总订单数</h4>
            </div>
          </div>
        </el-col>
        <el-col :span="6" :xs="12">
          <div class="card" :class="classes">
            <div class="card_body">
              <h1 class="number">{{summary.pending}}</h1>
            </div>
            <div class="card_footer">
              <h4>待确认</h4>
            </div>
          </div>
        </el-col>
        <el-col :span="6" :xs="12">
          <div class="card" :class="classes">
            <div class="card_body">
              <h1 class="number">{{summary.confirmed}}</h1>
            </div>
            <div class="card_footer">
              <h4>待发货</h4>
            </div>
          </div>
        </el-col>
        <el-col :span="6" :xs="12">
          <div class="card" :class="classes">
            <div class="card_body">
              <h1 class="number">{{summary.completed}}</h1>
            </div>
            <div class="card_footer">
              <h4>已发货</h4>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
    <div class="panel" style="margin-top: 20px">
      <el-row :gutter="20">
        <el-col :span="24">
          <div id="chart" class="card" style="padding: 20px;" :class="classes"></div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
  import api from '../../../apis'
  import { Chart } from '@antv/g2';

  export default {
    name: "Dashboard",
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        summary: {
          total: 0,
          pending: 0,
          confirmed: 0,
          completed: 0,
          expense: [],
          quantity: []
        },
        date: new Date(),
      }
    },
    mounted() {
      api.dashboard.fetchDashboard().then(res => {
        this.summary = res.data;
        const chart = new Chart({
          container: 'chart',
          autoFit: true,
          height: 500,
          defaultInteractions:[]
        });
        chart.tooltip({
          showCrosshairs: true
        });
        chart.removeInteraction('tooltip');
        chart.scale('date', {
          sync: true,
          tickCount: 15,
          range: [0, 1]
        });
        chart.scale('actual', {
          sync: true
        });
        const balance = chart.createView({
          region: {
            start: {
              x: 0,
              y: 0
            },
            end: {
              x: 1,
              y: 0.6
            }
          },
          padding: [20, 20, 40, 60]
        });
        balance.animate(true);
        balance.legend({
          position: 'top',
        })
        balance.data(this.summary.expense);
        balance.interaction('tooltip');
        balance.interaction('sibling-tooltip');
        balance.scale({
          date: {
            range: [0, 1],
          },
          actual: {
            nice: true,
            alias: '成本',
            type: 'linear'
          },
        });

        balance.tooltip({
          showCrosshairs: true,
          shared: true,
        });

        balance.axis('actual', {
          label: {
            formatter: (val) => {
              return `¥ ${val}`;
            },
          },
        });

        balance.line().position('date*actual').color('type').shape('smooth');
        balance.point().position('date*actual').color('type').shape('circle');

        const quantity = chart.createView({
          region: {
            start: {
              x: 0,
              y: 0.7
            },
            end: {
              x: 1,
              y: 1
            }
          },
          padding: [0, 20, 40, 60]
        });
        quantity.data(this.summary.quantity);
        quantity.interaction('tooltip');
        quantity.interaction('sibling-tooltip');
        quantity.scale({
          date: {
            range: [0, 1],
          },
          quantity: {
            nice: true,
            alias: '采购订单',
            type: 'linear',
            tickInterval: 1
          },
        });
        quantity.tooltip({
          showCrosshairs: true,
          shared: true
        });
        quantity.axis('quantity', {
          label: {
            formatter: (val) => {
              return `${val} 笔`;
            },
          }
        });
        quantity.line().position('date*quantity').color('#6A6873').shape('smooth');
        quantity.point().position('date*quantity').color('#6A6873').shape('circle');
        chart.render();
      });
    }
  }
</script>

<style lang="scss">
</style>
