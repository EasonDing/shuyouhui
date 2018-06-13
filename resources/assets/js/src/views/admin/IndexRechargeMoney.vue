<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="rechargeMoney">
                <el-table-column
                        prop="updateTime"
                        label="时间">
                </el-table-column>
                <el-table-column
                        prop=""
                        label="">
                </el-table-column>
                <el-table-column
                        prop=""
                        label="">
                </el-table-column>
                <el-table-column
                        prop="count"
                        label="金额">
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
    
</style>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: false,
                //表格数据
                rechargeMoney: [],

                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            //翻页事件
            paginate: function (val) {
                this.page = val
                this.getRechargeMoney()
            },
            //api获得数据
            getRechargeMoney: function () {
                this.loading = true;
                axios.get('/api/admin/finance/recharge/count?page=' + this.page).then(response => {
                    this.rechargeMoney = response.data.data.data;
                    this.pagesize = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted: function () {
            this.getRechargeMoney();
        }
    }
</script>