<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :xs="24" :span="2">
            <el-select style="width: 100%" v-model="params.user_type" @clear="handleClear" clearable placeholder="用户类型">
              <el-option label="管理员" value="user"></el-option>
              <el-option label="客户" value="customer"></el-option>
            </el-select>
          </el-col>
          <el-col :xs="24" :span="4">
            <el-select style="width: 100%" v-model="params.user_id" :loading="search.user.loading" @focus="users = []" :remote-method="searchUsers" :disabled="params.hasOwnProperty('user_type') === false" clearable remote filterable placeholder="用户">
              <el-option v-for="user in users" :key="user.id" :label="user.name" :value="user.id"></el-option>
            </el-select>
          </el-col>
          <el-col :xs="24" :span="2">
            <el-select style="width: 100%" v-model="params.action" @clear="handleClear" clearable placeholder="动作">
              <el-option label="创建" value="创建"></el-option>
              <el-option label="修改" value="修改"></el-option>
              <el-option label="删除" value="删除"></el-option>
            </el-select>
          </el-col>
          <el-col :xs="24" :span="3">
            <el-date-picker style="width: 100%" v-model="params.date" value-format="yyyy-MM-dd" type="date" placeholder="选择日期"></el-date-picker>
          </el-col>
          <el-col :xs="12" :span="2">
            <el-button type="primary" icon="el-icon-search" @click="fetchJournals" plain>搜索</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <div class="panel-body" :class="classes">
      <el-table v-loading="loading" :data="journals" style="width: 100%" @sort-change="changeSort">
        <el-table-column prop="id" label="ID" min-width="80" sortable="custom"></el-table-column>
        <el-table-column prop="journalable.name" label="用户"></el-table-column>
        <el-table-column label="用户类型">
          <template slot-scope="scope">
            <span>{{scope.row.user_type === 'user' ? '管理员' : '客户'}}</span>
          </template>
        </el-table-column>
        <el-table-column prop="action" label="动作"></el-table-column>
        <el-table-column prop="target" label="操作对象"></el-table-column>
        <el-table-column prop="ip" label="IP"></el-table-column>
        <el-table-column prop="created_at" label="时间"></el-table-column>
        <el-table-column prop="option" label="操作" width="130">
          <template slot-scope="scope">
            <el-tooltip class="item" effect="dark" content="详情" placement="top">
              <el-button size="mini" icon="el-icon-tickets" plain circle @click="viewDetails(scope.row)"></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination background :layout="meta.layout"
                       :page-size="params.size"
                       :total="meta.total"
                       :pager-count="meta.pager_count"
                       :page-sizes="meta.page_sizes"
                       :current-page.sync="params.current_page"
                       @current-change="handleCurrentChange"
                       @size-change="handleSizeChange">
        </el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import api from '../../../../apis';

  export default {
    name: "List",
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        loading: false,
        params: {
          size: 10,
          sort: 'id',
          search: '',
          action: '',
          user_id: '',
          user_type: '',
          date: null,
          descend: 1,
          current_page: 1
        },
        timer: null,
        search: {
          user: {
            loading: false
          }
        },
        journals: [],
        users: [],
        meta: {
          total: 0,
          pager_count: 11,
          page_sizes: [10, 20, 50],
          layout: 'sizes, total, prev, pager, next'
        },
      }
    },
    methods: {
      /**
       * 分页跳转时触发
       */
      handleCurrentChange(page) {
        this.params.page = page;
        this.fetchJournals();
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchJournals();
      },
      viewDetails(journal) {
        this.$router.push({path: `/journal/${journal.id}/detail`});
      },
      fetchJournals() {
        this.loading = true;
        api.journal.fetchJournals(this.params).then(res => {
          this.loading = false;
          this.journals = res.data;
          this.meta.total = res.total;
        }).catch(err => {
          this.loading = false;
          this.$message.error({
            offset: 95,
            message: err.message,
          });
        });
      },
      handleClear() {

      },
      changeSort(sortable) {
        this.loading = true;
        this.params.sort = sortable.order === null ? 'id' : sortable.prop;
        this.params.descend = sortable.order === 'descending' ? 1 : 0;
        this.fetchJournals();
      },
      searchUsers(query) {
        if (this.params.hasOwnProperty('user_type') === false) {
          this.$message.warning({
            offset: 95,
            message: '请先选择用户类型，再选择用户！'
          });
          return false;
        }
        if (query !== '') {
          this.search.user.loading = true;
          if (this.timer !== null) {
            clearTimeout(this.timer);
          }
          this.timer = setTimeout(() => {
            if (this.params.user_type === 'user') {
              api.user.fetchUsers({
                search: query,
                scene: 'select'
              }).then(res => {
                this.search.user.loading = false;
                this.users = res.data;
              });
            } else {
              api.customer.fetchCustomers({
                search: query,
                scene: 'select'
              }).then(res => {
                this.search.user.loading = false;
                this.users = res.data;
              });
            }
          }, 500);
        } else {
          this.users = [];
        }
      }
    },
    mounted() {
      this.fetchJournals();
    }
  }
</script>

<style lang="scss">

</style>
