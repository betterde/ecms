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
      <div class="details">
        <table>
          <tbody>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">客户ID</span>
              <span class="detail-item-content">{{customer.id}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">姓名</span>
              <span class="detail-item-content">{{customer.name}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">手机号码</span>
              <span class="detail-item-content">{{customer.mobile}}</span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">会员</span>
              <span class="detail-item-content">{{customer.vip === true ? '是' : '否'}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">余额</span>
              <span class="detail-item-content">{{customer.balance}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">折扣</span>
              <span class="detail-item-content">{{discount(this.customer.discount)}}</span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">省</span>
              <span class="detail-item-content">{{customer.province}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">市</span>
              <span class="detail-item-content">{{customer.municipality}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">县</span>
              <span class="detail-item-content">{{customer.prefecture}}</span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">地址</span>
              <span class="detail-item-content">{{customer.address}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">注册日期</span>
              <span class="detail-item-content">{{customer.created_at}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">备注</span>
              <span class="detail-item-content">{{customer.remark === null ? '无备注' : customer.remark}}</span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <el-divider content-position="center">订单列表</el-divider>
      <el-table v-loading="loading" :data="customer.orders" style="width: 100%" ref="pipeline">
        <el-table-column prop="id" label="ID" min-width="100"></el-table-column>
        <el-table-column prop="total" label="总金额"></el-table-column>
        <el-table-column prop="actual" label="实际金额"></el-table-column>
        <el-table-column prop="discount" label="折扣"></el-table-column>
        <el-table-column prop="date" label="日期"></el-table-column>
        <el-table-column prop="remark" label="备注"></el-table-column>
        <el-table-column prop="option" label="操作" width="130">
          <template slot-scope="scope">
            <el-tooltip class="item" effect="dark" content="详情" placement="top">
              <el-button size="mini" icon="el-icon-tickets" plain circle
                         @click="viewDetails(scope.row)" :disabled="['邮费', '满减'].includes(scope.row.type)"></el-button>
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
  import api from '../../apis'

  export default {
    name: "Detail",
    data() {
      return {
        loading: false,
        classes: ['animated', 'fade-in', 'fast'],
        params: {
          order: parseInt(this.$route.params.id),
          size: 5,
          page: 1,
          search: ''
        },
        customer: {
          id: '',
          name: '',
          mobile: '',
          discount: 100,
          balance: 0.00,
          vip: false,
          province: '',
          municipality: '',
          prefecture: '',
          address: '',
          remark: '',
          created_at: '',
          orders: []
        },
        meta: {
          total: 0,
          page_sizes: [5, 10, 20]
        }
      }
    },
    methods: {
      viewDetails(row) {
        this.$router.push({path: `/order/${row.id}/detail`});
      },
      discount(discount) {
        if (discount === 100) {
          return '无折扣';
        }

        let tensDigit = Math.floor(discount % 100 / 10);
        let unitsDigit = Math.floor(discount % 10);
        if (unitsDigit === 0) {
          return `${tensDigit} 折`
        }
        return `${tensDigit}${unitsDigit} 折`
      },
      handleCurrentChange(page) {
        this.params.page = page;
        this.fetchTradings();
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchTradings();
      },
      fetchCustomer() {
        api.customer.fetchCustomer(this.$route.params.id).then(res => {
          res.data.orders.forEach(order => {
            order.discount = this.discount(order.discount);
          });
          this.customer = res.data;
        });
      }
    },
    mounted() {
      this.fetchCustomer();
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
