<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :span="12" style="text-align: left">
            <el-button type="info" plain @click="$router.back()">返回</el-button>
          </el-col>
          <el-col :span="12" style="text-align: right"></el-col>
        </el-row>
      </div>
    </div>
    <div class="panel-body" :class="classes">
      <div id="chart"></div>
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
        loading: false,
        classes: ['animated', 'fade-in', 'fast'],
      }
    },
    methods: {
      fetchCommodity(id) {
        api.commodity.fetchCommodity(id).then(res => {
          const chart = new Chart({
            container: 'chart',
            autoFit: true,
            height: 500,
          });

          chart.data(res.data.pricings);
          chart.scale({
            date: {
              range: [0, 1],
            },
            buying: {
              nice: true,
            },
          });

          chart.tooltip({
            showCrosshairs: true,
            shared: true,
          });

          chart.axis('buying', {
            label: {
              formatter: (val) => {
                return `¥ ${val}`;
              },
            },
          });

          chart
            .line()
            .position('date*buying')
            .shape('smooth');

          chart
            .point()
            .position('date*buying')
            .shape('circle')
            .style({
              stroke: '#fff',
              lineWidth: 1,
            });

          chart.render();
        })
      }
    },
    mounted() {
      this.fetchCommodity(this.$route.params.id);
    }
  }
</script>

<style lang="scss">

</style>
