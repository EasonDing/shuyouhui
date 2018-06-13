<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <el-table
                    :data="tableData"
                    style="width: 100%"
                    :show-header="false">
                <el-table-column type="expand" >
                    <template slot-scope="props">
                        <el-form label-position="left" inline class="demo-table-expand">
                            <el-form-item label="消息详情">
                                <span>{{ props.row.content }}</span>
                            </el-form-item>
                        </el-form>
                    </template>
                </el-table-column>
                <el-table-column
                        label="标题"
                        prop="title">
                </el-table-column>
                <el-table-column
                        label="消息日期"
                        prop="updateTime">
                </el-table-column>
                <el-table-column
                        width="80">
                    <template slot-scope="props">
                        <span v-if="props.row.ChatResult == 0" style="color: red;font-size: 18px;">new</span>
                    </template>
                </el-table-column>
                <!--利用div撑高-->
                <el-table-column width="5">
                    <template slot-scope="scope">
                        <div style="height: 55px;"></div>
                    </template>
                </el-table-column>
            </el-table>

            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination layout="total, prev, pager, next" @current-change="handleCurrentChange" :page-size="pagesize" :total="total" style="float: right;">
                </el-pagination>
            </el-col>
        </el-card>
    </div>
</template>

<style>
    .demo-table-expand {
        font-size: 0;
    }
    .demo-table-expand label {
        width: 90px;
        color: #99a9bf;
    }
    .demo-table-expand .el-form-item {
        margin-right: 0;
        margin-bottom: 0;
        width: 100%;
    }

</style>

<script>
    export default{
        data(){
            return {
                loading: true,
                tableData: [],

                //分页数据
                total: 0,
                page: 1,
                pagesize: 20,

            }
        },
        methods: {
            //获取书本列表
            getData: function () {
                this.loading = true
                axios.get('/api/alerts?page=' + this.page).then(response => {
                    this.pagesize = response.data.data.per_page
                    this.total = response.data.data.total
                    this.tableData = response.data.data.data
                    this.loading = false
                }).catch(function (error) {
                    console.log(error)
                });
            },
            //翻页事件
            handleCurrentChange:function(val) {
                this.page = val
                this.getData()
            }
        },
        mounted: function () {
            this.getData()
        },
    }
</script>