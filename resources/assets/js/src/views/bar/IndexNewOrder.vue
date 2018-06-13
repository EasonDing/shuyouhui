<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中"
                    stripe
                    :data="tableData">
                <el-table-column
                        prop="updateTime"
                        label="下单时间">
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
                        label="订单数">
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
    .t-finance-data .data-box{
        text-align: center;
        margin: 10px 0;
        height: 110px;
    }
    .t-finance-data p{
        margin-bottom: 5px;
        padding-top: 25px;
    }
    .t-finance-data .pay-box{
        padding-top: 70px;
    }
</style>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: true,
                //表格数据
                tableData: [],
                //下拉列表框值

                total: 0,
                page: 1,
                pagesize: 20,
            }
        },
        methods: {
            //翻页事件
            paginate: function (val) {
                this.page = val
                this.getData()
            },
            //api获得数据
            getData: function () {
                this.loading = true;
                axios.get('/api/bar/order/new/order?page=' + this.page).then(response => {
                    this.tableData = response.data.data.data;
                    this.pagesize = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            moneyOut: function () {
                this.$router.push('/t_finance_money_out/')
            }
        },
        mounted: function () {
            this.getData();
        }
    }
</script>