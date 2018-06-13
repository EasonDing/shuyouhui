<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data=" newUsers">
                <el-table-column
                        prop="updateTime"
                        label="注册时间">
                </el-table-column>
                <el-table-column
                        prop="man"
                        label="男"
                        :formatter="formatMan">
                </el-table-column>
                <el-table-column
                        prop="woman"
                        label="女"
                        :formatter="formatWoman"
                >
                </el-table-column>
                <el-table-column
                        prop="count"
                        label="人数"
                >
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
                loading: true,
                //每日新增用户
                newUsers: [],

                //分页数据
                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            formatMan:function (row, index) {
                if (row.man > 0){
                    return row.man
                }else {
                    return 0
                }
            },
            formatWoman:function (row, index) {
                if (row.woman > 0){
                    return row.woman
                }else {
                    return 0
                }
            },
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getNewUsers()
            },
            //api获得数据
            getNewUsers: function () {
                this.loading = true
                axios.post('/api/bar/index/new/user?page=' + this.page).then(response => {
                    this. newUsers = response.data.data.data;
                    this.pagesize = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted: function () {
            this.getNewUsers();
        }
    }
</script>