<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    :data="blogs">
                <el-table-column
                        label="发帖人"
                        width="320">
                    <template slot-scope="scope">
                        {{ scope.row.user ? scope.row.user.username : '' }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="内容"
                        :show-overflow-tooltip="true">
                    <template slot-scope="scope">
                        {{ scope.row.content }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="浏览次数">
                    <template slot-scope="scope">

                    </template>
                </el-table-column>
                <el-table-column
                        label="日期">
                    <template slot-scope="scope">
                        {{ scope.row.updateTime }}
                    </template>
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="180">
                    <template slot-scope="scope">
                        <el-button type="info" size="small" @click="getComments(scope.row)">查看评论</el-button>
                        <el-button class="t-article-button-del" size="small" type="danger" @click="destroyBlog(scope.row)">删除</el-button>
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

<style>
    .t-article-img-userLogo {
        width: 50px;
        height: 50px;
    }
    .t-article-ul-userinfo {
        display: inline-block;
        padding: 0;
        vertical-align: middle;
        margin: 10px 0 10px 15px;
    }
    .t-article-button-del {

    }
</style>

<script>
    export default {
        data(){
            return {
                loading: true,
                blogs: [],
                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            //获取书吧帖子列表
            getBlogs: function () {
                this.loading = true;
                axios.get('/api/bar/blog/index?page=' + this.page).then(response => {
                    var data = response.data.data.data;
                    var pagination = response.data.data.meta.pagination;
                    this.pagesize = pagination.per_page;
                    this.total = pagination.total;
                    this.blogs = data;
                    this.loading = false;
                });
            },
            //获取帖子评论
            getComments:function (row) {
                this.$router.push('/bar/blog/' + row.id + '/comment');
            },
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getBlogs();
            },
            //删除帖子
            destroyBlog: function (row) {
                this.$confirm('删除此条帖子, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/bar/blog/destroy/' + row.id).then(response => {
                        var data = response.data;
                        if (data.code === 200) {
                            this.$message.success(data.message);
                            this.getBlogs();
                        } else {
                            this.$message.error(data.message);
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });
                }).catch(() => {
                    this.$message.info('已取消删除');
                });
            },
        },
        mounted: function () {
            this.getBlogs()
        }
    }
</script>