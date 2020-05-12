<template>
  <div class="main-content">
    <h1>Hello</h1>
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
          day: 0,
          month: 0,
          daily_turnover: 0.00,
          daily_profit: 0.00,
          inventory_cost: 0.00,
          purchasing_cost: 0.00,
          sales_amount: 0.00,
          gross_profit: 0.00,
          tendency: [],
          order_quantity: []
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
        balance.data(this.summary.tendency);
        balance.interaction('tooltip');
        balance.interaction('sibling-tooltip');
        balance.scale({
          date: {
            range: [0, 1],
          },
          actual: {
            nice: true,
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
        quantity.data(this.summary.order_quantity);
        quantity.interaction('tooltip');
        quantity.interaction('sibling-tooltip');
        quantity.scale({
          date: {
            range: [0, 1],
          },
          quantity: {
            nice: true,
            alias: '销量',
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
