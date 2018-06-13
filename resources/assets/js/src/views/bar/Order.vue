<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">订单管理</span>
                <div style="float: right;">
                    <el-form :inline="true">
                        <el-select v-model="orderStatus" @change="getOrders()" placeholder="订单状态" style="width: 120px;">
                            <el-option
                                    v-for="item in options"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
                        <el-date-picker
                                v-model="date"
                                type="daterange"
                                align="right"
                                unlink-panels
                                range-separator="-"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                @change="getOrders"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                        <el-input @change="getOrders" clearable  style="width: 260px;" v-model="keyword" placeholder="输入订单号"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="orders"
                    :show-overflow-tooltip="true">
                <el-table-column
                        :show-overflow-tooltip="true"
                        prop="orderNo"
                        label="订单号">
                </el-table-column>
                <el-table-column
                        width="400"
                        label="商品">
                    <template slot-scope="scope">
                        <div v-if="scope.row.book">
                            <img :src="scope.row.book.image"
                                 style="width: 80px;height: 100px; float:left; padding: 7px;"/>
                            <ul class="order-ul-goodinfo" type="none">
                                <li>{{ scope.row.book.title }}</li>
                                <li>{{ scope.row.book.author }}</li>
                                <li>{{ scope.row.book.summary }}</li>
                            </ul>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="money"
                        label="金额"
                >
                </el-table-column>
                <el-table-column
                        :show-overflow-tooltip="true"
                        prop="phone"
                        label="手机号"
                >
                </el-table-column>
                <el-table-column
                        :show-overflow-tooltip="true"
                        prop="updateTime"
                        label="下单时间"
                >
                </el-table-column>
                <el-table-column
                        prop="orderStatus"
                        label="状态"
                >
                </el-table-column>

                <el-table-column label="操作" width="180">
                    <template slot-scope="scope">
                        <el-button type="danger" size="small" @click="destroyOrder(scope.row)">删除</el-button>
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
    .order-ul-goodinfo{
        float: left;
        margin:0 0 0 10px;
        padding:0;
    }
    .order-ul-goodinfo li{
        width: 200px;
        margin-top: 5px;
        overflow : hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
<script>
    export default {
        data() {
            return {
                //加载动画
                loading: true,
                //表格数据
                orders: [],

                //下拉列表框值
                options: [
                    {
                        value: "0",
                        label: '全部'
                    },
                    {
                        value: '1',
                        label: '未完成'
                    },
                    {
                        value: "2",
                        label: '已完成'
                    },

                ],

                //搜索关键字
                keyword: '',
                date: [],
                orderStatus: '',

                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,

                pickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
            }
        },
        methods: {
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getOrders();
            },
            //获得订单列表
            getOrders: function () {
                const self = this;
                self.loading = true
                axios.get('/api/bar/order/index',{
                    params: {
                        page: self.page,
                        search: self.keyword,
                        date: self.date,
                        orderBy: 'updateTime',
                        sortedBy: 'desc',
                    }
                }).then(response => {
                    var data = response.data.data.data;
                    var pagination = response.data.data.meta.pagination;

                    self.pagesize = pagination.per_page;
                    self.total = pagination.total;
                    self.orders = data;
                    self.loading = false;
                });
            },
            destroyOrder:function (row) {
                this.$confirm('删除此订单？, 是否继续', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.get('/api/bar/order/destroy/' + row.id).then(response => {
                        var data = response.data;
                        this.$message.success(data.message);
                        this.getOrders();
                    });
                }).catch(() => {
                    this.$message.info('已取消删除');
                });
            },
        },
        mounted: function () {
            this.getOrders();
        }
    }
</script>