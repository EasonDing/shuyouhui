<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="addBanner()">添加</el-button>
            </div>
            <el-table
                    stripe
                    :data="banners"
                    style="width: 100%">
                <el-table-column
                        prop="title"
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
                        label="图片">
                    <template slot-scope="scope">
                        <img :src="scope.row.image" style="width: 120px; height: 80px;"/>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="updateTime"
                        label="发送时间"
                >
                </el-table-column>
                <el-table-column label="操作" width="220">
                    <template slot-scope="scope">
                        <el-button type="info" size="small" @click="editBanner(scope.row)">编辑</el-button>
                        <el-button :type="scope.row.status  ? 'success' : ''" size="small" @click="putaway(scope.row)">{{ scope.row.status ? '下架' : '上架' }}</el-button>
                        <el-button type="danger" size="small" @click="destroyBanner(scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination layout="total, prev, pager, next" @current-change="paginage" :page-size="pagesize" :total="total" style="float: right;">
                </el-pagination>
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
                //表格数据
                banners: [],
                total: 0,
                page: 1,
                pagesize: 20
            }
        },
        methods: {
            paginage: function (val) {
                this.page = val;
                this.getBanners();
            },
            getBanners: function () {
                const self = this;
                self.loading = true
                axios.get('/api/bar/banner/index', {
                    params:{
                        page: self.page,
                    }
                }).then(response => {
                    if (response.data.code === 200) {
                        var data = response.data.data.data;
                        var pagination = response.data.data.meta.pagination;
                        self.pagesize = pagination.per_page;
                        self.total = pagination.total;
                        self.banners = data;
                    }
                    self.loading = false;
                }).catch(function (error) {
                    self.$message.error('服务器错误!');
                    self.loading = false;
                });
            },
            addBanner: function () {
                this.$router.push('/bar/banner/add');
            },
            editBanner:function (row) {
                this.$router.push('/bar/banner/'+ row.id +'/edit/');
            },
            //删除banner
            destroyBanner:function (row) {
                const self = this;
                self.$confirm('删除此 Banner, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.get('/api/bar/banner/destroy/' + row.id).then(response => {
                        var data = response.data;
                        if (data.code === 200) {
                            self.$message.success(data.message);
                            self.getBanners();
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
            //banner上架、下架
            putaway: function (row) {
                const self = this;
                var status = row.status ? '下架' : '上架';
                self.$confirm('确认' + status + ', 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/bar/banner/putaway', row).then(response => {
                        var data = response.data;
                        if (data.code === 200) {
                            self.$message.success(data.message);
                            self.getBanners()
                        } else {
                            self.$message.error(data.message);
                        }
                    }).catch(function (error) {
                        self.$message.error('服务器错误!');
                    });
                }).catch(() => {
                    self.$message.info('已取消' + status);
                });
            }
        },
        mounted: function () {
            this.getBanners();
        }
    }
</script>