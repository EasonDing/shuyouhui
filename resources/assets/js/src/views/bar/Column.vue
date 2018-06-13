<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="addColumn()">添加</el-button>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="columns">
                <el-table-column
                        prop="title"
                        :show-overflow-tooltip="true"
                        label="标题">
                </el-table-column>
                <el-table-column
                        width="400"
                        :show-overflow-tooltip="true"
                        label="内容">
                    <template slot-scope="scope" v-html="scope.row.content">
                        <div v-html="scope.row.content"></div>
                    </template>
                </el-table-column>
                <el-table-column
                        width="180"
                        label="图片">
                    <template slot-scope="scope">
                        <img :src="scope.row.image" style="height: 80px;"/>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="添加日期"
                >
                </el-table-column>
                <el-table-column label="操作" width="180">
                    <template slot-scope="scope">
                        <el-button type="info" size="small" @click="editColumn(scope.row)">编辑</el-button>
                        <el-button type="danger" size="small" @click="destroyColumn(scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: false,
                //专栏列表数据
                columns: [],
                //分页相关数据
                total: 0,
                page: 1,
                pagesize: 20
            }
        },
        methods: {
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getColumns();
            },
            //获取专栏列表
            getColumns: function () {
                const self = this;
                self.loading = true;
                axios.get('/api/bar/column/index?page=' + self.page).then(response => {
                    if (response.data.code === 200) {
                        var data = response.data.data.data;
                        var pagination = response.data.data.meta.pagination;
                        self.pagesize = pagination.per_page;
                        self.total = pagination.total;
                        self.columns = data;
                    }
                    self.loading = false;
                }).catch(function (error) {
                    self.$message.error('服务器错误!');
                    self.loading = false;
                });
            },
            addColumn: function () {
                this.$router.push('/bar/column/add');
            },
            editColumn:function (row) {
                this.$router.push('/bar/column/'  + row.id + '/edit/');
            },
            //删除专栏
            destroyColumn: function (row) {
                const self = this;
                self.$confirm('删除此专栏, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.get('/api/bar/column/destroy/' + row.id).then(response => {
                        var data = response.data;
                        if (data.code === 200) {
                            self.$message.success(data.message);
                            self.getColumns();
                        } else {
                            self.$message.error(data.message);
                        }
                    }).catch(function (error) {
                        self.$message.error('服务器错误!');
                    });
                }).catch(() => {
                    self.$message.info('已取消删除');
                });
            },
        },
        mounted: function () {
            this.getColumns();
        }
    }
</script>