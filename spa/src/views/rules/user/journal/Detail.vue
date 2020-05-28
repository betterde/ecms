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
      <div class="details" style="padding: 20px">
        <table>
          <tbody>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">日志ID</span>
              <span class="detail-item-content">{{journal.id}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">用户ID</span>
              <span class="detail-item-content"><router-link :to="`/${journal.user_type}/${journal.user_id}/detail`">{{journal.journalable.name}}</router-link></span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">用户类型</span>
              <span class="detail-item-content">{{journal.user_type === 'user' ? '管理员' : '客户'}}</span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">动作</span>
              <span class="detail-item-content">{{journal.action}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">操作对象</span>
              <span class="detail-item-content">{{journal.target}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">请求URI</span>
              <span class="detail-item-content">{{journal.path}}
                <el-tooltip class="item" effect="dark" content="请求的资源路径" placement="top">
                  <i class="el-icon-question"></i>
                </el-tooltip>
              </span>
            </td>
          </tr>
          <tr>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">请求方法</span>
              <span class="detail-item-content">{{journal.method}}</span>
            </td>
            <td class="detail-item">
              <span class="detail-item-label detail-item-colon">时间</span>
              <span class="detail-item-content">{{journal.created_at}}</span>
            </td>
          </tr>
          </tbody>
        </table>
        <el-divider v-if="journal.query.length > 0" content-position="center">查询参数</el-divider>
        <pre class="json-code" v-if="journal.query.length > 0">{{journal.query}}</pre>
        <el-divider content-position="center">请求参数</el-divider>
        <pre class="json-code" >{{journal.params}}</pre>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis';

  export default {
    name: "Detail",
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        loading: false,
        journal: {
          id: 0,
          user_id: '',
          user_type: '',
          action: '',
          target: '',
          method: '',
          path: '',
          query: null,
          params: null,
          ip: '',
          journalable: {
            name: ''
          },
          created_at: ''
        }
      }
    },
    methods: {
      fetchJournal(id) {
        api.journal.fetchJournal(id).then(res => {
          this.journal = res.data;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
        });
      }
    },
    mounted() {
      this.fetchJournal(this.$route.params.id);
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
  .json-code {
    white-space: pre-wrap;
    word-wrap: break-word;
  }
</style>
