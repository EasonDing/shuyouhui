<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    :data="articles"
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
                        <el-button class="t-article-button-del" size="small" type="danger" @click="deleteComment(scope.row)">删除</el-button>
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
                articles: [],
                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            //获取书吧帖子评论列表
            getData: function () {
                this.loading = true
                axios.get('/api/t_comments/' + this.$route.params.id).then(response => {
                    this.articles = response.data.data;
                    this.pagesize = response.data.per_page
                    this.total = response.data.total
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //翻页事件
            handleCurrentChange: function (val) {
                this.page = val;
                this.getData();
            },
            //删除评论
            deleteComment: function (row) {
                this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.get('/api/t_comment/' + row.commentId + '/destroy').then(response => {
                        if (response) {
                            this.notify('评论删除成功!', 'success')
                            this.getData()
                        } else {
                            this.notify('评论删除失败!', 'error')
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
            this.getData()
        }
    }
</script>