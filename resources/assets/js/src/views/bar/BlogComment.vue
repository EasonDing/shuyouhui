<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中"
                    :data="blogComments"
                    :show-header="false">
                <el-table-column
                        label="用户信息"
                        width="320">
                    <template slot-scope="scope">
                        <img class="t-article-img-userLogo" :src="scope.row.user ? scope.row.user.UserLogo : ''"/>
                        <ul class="t-article-ul-userinfo" type="none">
                            <li>{{ scope.row.user ? scope.row.user.username : '' }}</li>
                            <li>{{ scope.row.updateTime ? scope.row.updateTime : '' }}</li>
                        </ul>
                    </template>
                </el-table-column>
                <el-table-column
                        label="标题">
                    <template slot-scope="scope">
                        <div style="text-align:center">
                            {{ scope.row.content }}
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        label="操作"
                        width="180">
                    <template slot-scope="scope">
                        <el-button class="t-article-button-del" size="small" type="danger" @click="destroyComment(scope.row)">删除</el-button>
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
        name: 'tArticleManage',
        data(){
            return {
                loading: true,
                blogComments: [],
                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            //获取书吧帖子评论列表
            getBlogComments: function () {
                this.loading = true
                axios.get('/api/bar/blog/comment/' + this.$route.params.id).then(response => {
                    if (response.data.code === 200) {
                        var data = response.data.data.data;
                        var pagination = response.data.data.meta.pagination;
                        this.pagesize = pagination.per_page;
                        this.total = pagination.total;
                        this.blogComments = data;
                    } else {
                        this.$message.error('哈哈！有人要被扣奖金啦！');
                    }
                    this.loading = false;
                }).catch(function (error) {
                    this.$message.error('服务器错误');
                    this.loading = false;
                });
            },
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getBlogComments();
            },
            //删除评论
            destroyComment: function (row) {
                this.$confirm('删除此条评论, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/bar/blog/destroy/' + row.id + '/comment').then(response => {
                        var data = response.data;
                        if (data.code === 200) {
                            this.$message.success(data.message);
                            this.getBlogComments()
                        } else {
                            this.$message.error(data.message);
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            //通知框
            notify:function($message, $type) {
                this.$notify({
                    title: '系统提示',
                    message: $message,
                    type: $type
                });
            }
        },
        mounted: function () {
            this.getBlogComments()
        }
    }
</script>