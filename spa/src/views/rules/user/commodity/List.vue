<template>
  <div class="panel">
    <div class="panel-header" :class="classes">
      <div class="panel-tools">
        <el-row :gutter="20">
          <el-col :xs="12" :span="3">
            <el-select v-model="params.brand" @focus="fetchBrands" @clear="handleClear" :loading="select.loading" clearable filterable placeholder="品牌">
              <el-option v-for="brand in brands" :key="brand.name" :label="brand.name" :value="brand.name"></el-option>
            </el-select>
          </el-col>
          <el-col :xs="12" :span="2">
            <el-select v-model="params.category" @focus="fetchCategories" @clear="handleClear" :loading="select.loading" clearable filterable placeholder="分类">
              <el-option v-for="category in categories" :key="category.name" :label="category.name" :value="category.name"></el-option>
            </el-select>
          </el-col>
          <el-col :xs="24" :span="6">
            <el-input placeholder="在这里输入要搜索的内容" v-model="params.search" @keyup.enter.native="fetchCommodities"
                      @clear="handleClear" clearable>
              <i slot="prefix" class="el-input__icon el-icon-search"></i>
            </el-input>
          </el-col>
          <el-col :span="2">
            <el-switch style="height: 40px" v-model="params.zero" active-text="缺货" :active-value="true" :inactive-value="false"></el-switch>
          </el-col>
          <el-col :span="2">
            <el-button type="primary" icon="el-icon-search" @click="fetchCommodities" plain>搜索</el-button>
          </el-col>
          <el-col :xs="24" :span="9" style="text-align: right">
            <el-button type="primary" plain @click="handleCreate">创建</el-button>
          </el-col>
        </el-row>
      </div>
    </div>
    <el-dialog title="创建库存" :visible.sync="create.dialog" @close="handleClose('create')" width="600px" :close-on-click-modal="false">
      <el-form :model="create.params" :rules="create.rules" ref="create" label-position="top">
        <el-row :gutter="10">
          <el-col :span="24" style="text-align: center">
            <el-upload class="avatar-uploader" name="image" action="/api/commodity/image" :headers="upload.headers" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
              <img v-if="create.params.image" :src="create.params.image" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="品牌" prop="brand">
              <el-select style="width: 100%" v-model="create.params.brand" @focus="fetchBrands" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="输入或选择一个品牌">
                <el-option v-for="brand in brands" :key="brand.name" :label="brand.name" :value="brand.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="16">
            <el-form-item label="名称" prop="name">
              <el-input v-model="create.params.name" autocomplete="off" placeholder="请输入名称"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="分类" prop="category">
              <el-select style="width: 100%" v-model="create.params.category" @focus="fetchCategories" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="输入或选择一个分类">
                <el-option v-for="category in categories" :key="category.name" :label="category.name" :value="category.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="单位" prop="unit">
              <el-select style="width: 100%" v-model="create.params.unit" @focus="fetchUnits" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请选择商品单位">
                <el-option v-for="unit in units" :key="unit.name" :label="unit.name" :value="unit.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="规格" prop="specification">
              <el-input v-model="create.params.specification" placeholder="请输入商品规格"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="描述" prop="description">
          <el-input v-model="create.params.description" autocomplete="off" placeholder="请输商品描述信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="create.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('create')">确定</el-button>
      </div>
    </el-dialog>
    <el-dialog title="编辑库存" :visible.sync="update.dialog" @close="handleClose('update')" width="600px" :close-on-click-modal="false">
      <el-form :model="update.params" :rules="update.rules" ref="update" label-position="top">
        <el-row :gutter="10">
          <el-col :span="24" style="text-align: center">
            <el-upload class="avatar-uploader" name="image" action="/api/commodity/image" :headers="upload.headers" :show-file-list="false" :on-success="handleAvatarSuccess" :before-upload="beforeAvatarUpload">
              <img v-if="update.params.image" :src="update.params.image" class="avatar">
              <i v-else class="el-icon-plus avatar-uploader-icon"></i>
            </el-upload>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="品牌" prop="brand">
              <el-select style="width: 100%" v-model="update.params.brand" @focus="fetchBrands" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请输入或选择一个品牌">
                <el-option v-for="brand in brands" :key="brand.name" :label="brand.name" :value="brand.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="16">
            <el-form-item label="名称" prop="name">
              <el-input v-model="update.params.name" autocomplete="off" placeholder="请输入名称"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-row :gutter="10">
          <el-col :span="8">
            <el-form-item label="分类" prop="category">
              <el-select style="width: 100%" v-model="update.params.category" @focus="fetchCategories" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请输入或选择一个分类">
                <el-option v-for="category in categories" :key="category.name" :label="category.name" :value="category.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="单位" prop="unit">
              <el-select style="width: 100%" v-model="update.params.unit" @focus="fetchUnits" :loading="select.loading" default-first-option allow-create clearable filterable placeholder="请选择商品单位">
                <el-option v-for="unit in units" :key="unit.name" :label="unit.name" :value="unit.name"></el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="规格" prop="specification">
              <el-input v-model="update.params.specification" placeholder="请输入商品规格"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
        <el-form-item label="描述" prop="description">
          <el-input v-model="update.params.description" autocomplete="off" placeholder="请输商品描述信息"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="update.dialog = false">取消</el-button>
        <el-button type="primary" @click="submit('update')">确定</el-button>
      </div>
    </el-dialog>
    <div class="panel-body" :class="classes">
      <el-table v-loading="loading" :data="inventories" @sort-change="changeSort" style="width: 100%" ref="pipeline">
        <el-table-column label="图片">
          <template slot-scope="scope">
            <el-image style="width: 80px; height: 80px" :src="scope.row.image" fit="cover"></el-image>
          </template>
        </el-table-column>
        <el-table-column prop="brand" label="品牌"></el-table-column>
        <el-table-column prop="category" label="分类"></el-table-column>
        <el-table-column prop="name" label="名称" min-width="100"></el-table-column>
        <el-table-column prop="specification" label="规格"></el-table-column>
        <el-table-column prop="amount" label="数量" sortable="custom"></el-table-column>
        <el-table-column prop="unit" label="单位"></el-table-column>
        <el-table-column prop="option" label="操作" width="130">
          <template slot-scope="scope">
            <el-tooltip class="item" effect="dark" content="编辑" placement="top">
              <el-button size="mini" icon="el-icon-edit" circle
                         @click="handleEdit(scope.row)"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="趋势" placement="top">
              <el-button size="mini" icon="el-icon-tickets" plain circle
                         @click="viewTrend(scope.row)"></el-button>
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="删除" placement="top">
              <el-button :disabled="scope.row.amount > 0" size="mini" icon="el-icon-delete" type="danger" plain circle
                         @click="handleDelete(scope.row)"></el-button>
            </el-tooltip>
          </template>
        </el-table-column>
      </el-table>
      <div class="pagination">
        <el-pagination background layout="sizes, total, prev, pager, next"
                       :page-size="params.size" :page-sizes="meta.page_sizes"
                       :current-page.sync="params.current_page" :total="meta.total"
                       @current-change="handleCurrentChange"
                       @size-change="handleSizeChange">

        </el-pagination>
      </div>
    </div>
  </div>
</template>

<script>
  import api from "../../../../apis";
  import {mapState} from 'vuex';

  export default {
    name: "List",
    data() {
      return {
        classes: ['animated', 'fade-in', 'fast'],
        loading: false,
        params: {
          page: 1,
          size: 10,
          sort: 'id',
          brand: '',
          zero: null,
          search: '',
          category: '',
          descend: 0,
        },
        create: {
          dialog: false,
          params: {
            image: '',
            name: '',
            brand: '',
            description: '',
            unit: '',
            category: '',
            specification: '',
          },
          rules: {
            name: [
              {type: 'string', required: true, message: '请输入名称', trigger: 'blur'}
            ],
            brand: [
              {type: 'string', required: true, message: '请选择或输入商品品牌', trigger: 'change'}
            ],
            unit: [
              {type: 'string', required: true, message: '请输入单位', trigger: 'blur'}
            ],
            category: [
              {type: 'string', required: true, message: '请选择或输入商品分类', trigger: 'change'}
            ]
          }
        },
        update: {
          id: null,
          dialog: false,
          params: {
            image: '',
            name: '',
            brand: '',
            description: '',
            unit: '',
            category: '',
            specification: '',
          },
          rules: {
            name: [
              {type: 'string', required: true, message: '请输入名称', trigger: 'blur'}
            ],
            brand: [
              {type: 'string', required: true, message: '请选择或输入商品品牌', trigger: 'change'}
            ],
            unit: [
              {type: 'string', required: true, message: '请输入单位', trigger: 'blur'}
            ],
            category: [
              {type: 'string', required: true, message: '请选择或输入商品分类', trigger: 'change'}
            ]
          }
        },
        units:[],
        brands: [],
        categories: [],
        inventories: [],
        select: {
          loading: false
        },
        upload: {
          headers: {}
        },
        meta: {
          total: 0,
          page_sizes: [10, 20, 50]
        }
      }
    },
    computed: {
      ...mapState({
        access_token: state => state.account.access_token,
      })
    },
    methods: {
      fetchUnits() {
        this.select.loading = true;
        api.commodity.fetchUnits().then(res => {
          this.units = res.data;
          this.select.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
          this.select.loading = false;
        });
      },
      fetchBrands() {
        this.select.loading = true;
        api.commodity.fetchBrands().then(res => {
          this.brands = res.data;
          this.select.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
          this.select.loading = false;
        });
      },
      fetchCategories() {
        this.select.loading = true;
        api.commodity.fetchCategories().then(res => {
          this.categories = res.data;
          this.select.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
          this.select.loading = false;
        })
      },
      /**
       * 分页跳转时触发
       */
      handleCurrentChange(page) {
        this.fetchCommodities(page);
      },
      handleClose(form) {
        switch (form) {
          case 'create':
            this.$refs.create.resetFields();
            this.create.dialog = false;
            break;
          case 'update':
            this.$refs.update.resetFields();
            this.update.dialog = false;
            this.update.id = null;
            this.update.index = null;
            break;
        }
      },
      handleAvatarSuccess(res) {
        this.create.params.image = 'http://ecms.it' + res.data;
      },
      beforeAvatarUpload() {

      },
      /**
       * Submit form
       * @param form
       */
      submit(form){
        switch (form) {
          case 'create':
            this.$refs.create.validate((valid) => {
              if (valid) {
                api.commodity.createCommodity(this.create.params).then(res => {
                  this.$message.success({
                    offset: 95,
                    message: res.message
                  });
                  this.fetchCommodities();
                  this.handleClose(form);
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
            api.commodity.updateCommodity(this.update.id, this.update.params).then(res => {
              this.$message.success({
                offset: 95,
                message: res.message
              });
              this.fetchCommodities();
              this.handleClose(form);
            }).catch(err => {
              this.$message.error({
                offset: 95,
                message: err.message
              });
            });
            break;
        }
      },
      handleCreate() {
        this.create.dialog = true;
      },
      changeSort(sortable) {
        this.loading = true;
        this.params.sort = sortable.order === null ? 'id' : sortable.prop;
        this.params.descend = sortable.order === 'descending' ? 1 : 0;
        this.fetchCommodities();
      },
      handleEdit(row) {
        this.update.dialog = true;
        this.update.id = row.id;
        let keys = ['amount', 'buying', 'selling'];
        Object.keys(this.update.params).forEach((value) => {
          if (keys.indexOf(value) > -1) {
            this.update.params[value] = parseFloat(row[value]);
          } else {
            this.update.params[value] = row[value];
          }
        });
      },
      handleDelete(row) {
        this.$confirm('此操作将删除商品信息', '警告', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          api.commodity.deleteCommodity(row.id).then(res => {
            this.$message({
              type: 'success',
              offset: 95,
              message: res.message
            });
            this.fetchCommodities();
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
      viewTrend(row) {
        this.$router.push({
          name: 'commodityDetail',
          params: {
            id: row.id
          }
        })
      },
      handleClear() {
        this.fetchCommodities();
      },
      handleSizeChange(size) {
        this.params.size = size;
        this.fetchCommodities();
      },
      fetchCommodities(page = 1) {
        this.loading = true;
        this.params.page = page;
        api.commodity.fetchCommodities(this.params).then(res => {
          this.inventories = res.data;
          this.params.current_page = res.current_page;
          this.meta.total = res.total;
          this.loading = false;
        }).catch(err => {
          this.$message.error({
            offset: 95,
            message: err.message
          });
        });
      }
    },
    mounted() {
      this.upload.headers = {
        Authorization: 'Bearer ' + this.access_token
      };
      this.fetchCommodities();
    }
  }
</script>

<style lang="scss">
  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409EFF;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
