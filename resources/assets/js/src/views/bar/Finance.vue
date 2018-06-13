<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <el-row :gutter="10" class="t-finance-data">
                <el-col :span="3">
                    <div @click="jump('/bar/revenue')" class="grid-content medium bg-purple data-box"><p>{{ this.toDayIncome ? this.toDayIncome : 0}}.00元</p>
                        <div class="medium">今日收入</div>
                    </div>
                </el-col>
                <el-col :span="3">
                    <div class="grid-content medium bg-purple data-box"><p>{{ this.totalIncome ? this.totalIncome : 0}}.00元</p>
                        <div class="medium">总收入</div>
                    </div>
                </el-col>
                <el-col :span="3">
                    <div @click="jump('/bar/expenditure')" class="grid-content medium bg-purple data-box"><p>{{ this.toDayPay ? this.toDayPay : 0}}.00元</p>
                        <div class="medium">今日支出</div>
                    </div>
                </el-col>
                <el-col :span="3">
                    <div class="grid-content medium bg-purple data-box"><p>{{ this.totalSpending ? this.totalSpending : 0}}.00元</p>
                        <div class="medium">总支出</div>
                    </div>
                </el-col>
                <el-col :span="3">
                    <div class="grid-content medium bg-purple data-box"><p>{{this.allMoney ? this.allMoney : 0}}.00元</p>
                        <div class="medium">帐户余额</div>
                    </div>
                </el-col>
                <el-col :span="9">
                    <div class="data-box pay-box">
                        <el-button type="success" @click="jump('/bar/money/out')">提现</el-button>
                        <el-button type="primary" @click="jump('/bar/bind/wechat')">绑定微信</el-button>
                    </div>
                </el-col>
            </el-row>
            <div>
                <div slot="header" class="clearfix">
                    <span style="line-height: 36px;">详细帐单</span>
                    <div style="float: right;">
                        <el-form :inline="true">
                            <el-select v-model="type" @change="getCounts" placeholder="消息类型" style="width: 120px;">
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
                                    @change="getCounts"
                                    value-format="yyyy-MM-dd"
                                    :picker-options="pickerOptions">
                            </el-date-picker>
                            <el-input clearable @change="getCounts" style="width: 260px;" v-model="keyword" placeholder="输入手机号"></el-input>
                            <el-button type="primary">搜索</el-button>
                        </el-form>
                    </div>
                </div>
            </div>
            <el-table
                    stripe
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    :data="finances">
                <el-table-column
                        prop="type"
                        label="类型"
                        :formatter="formatType">
                </el-table-column>
                <el-table-column
                        prop="content"
                        label="内容">
                </el-table-column>
                <el-table-column
                        prop="phone"
                        label="手机号">
                </el-table-column>
                <el-table-column
                        prop="updateTime"
                        label="时间"
                >
                </el-table-column>
                <el-table-column
                        prop="money"
                        label="金额"
                >
                </el-table-column>
                <el-table-column
                        prop="money"
                        label="收入"
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
        name: 'userManage',
        data() {
            return {
                //加载动画
                loading: true,
                dialogLoading: false,
                //表格数据
                finances: [],

                //下拉列表框值
                options: [
                    {
                        value: '0',
                        label: '全部'
                    },
                    {
                        value: '1',
                        label: '收入'
                    },
                    {
                        value: '2',
                        label: '支出'
                    },
                ],

                //搜索关键字
                keyword: '',
                date: [],
                //账单类型
                type: '',

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

                allMoney: '',//帐户余额
                toDayIncome: '',//今日收入
                totalIncome: '',//总收入
                toDayPay: '',//今日支出
                totalSpending: '',//总支出
            }
        },
        methods: {
            formatType:function (row, index) {
                if (row.type == 1){
                    return "收入"
                }
                if (row.type == 2){
                    return "支出"
                }
            },
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getCounts();
            },
            //获取财务信息
            getCounts: function () {
                const self = this;
                self.loading = true;
                axios.get('/api/bar/finance/index', {
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
                    self.finances = data;
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            moneyOut: function () {
                this.$router.push('/t_finance_money_out/')
            },
            //动态路由跳转
            jump:function (path) {
                this.$router.push(path)
            },
            //财务统计数据
            getCount:function () {
                axios.get('/api/bar/finance/count').then(response => {
                    this.allMoney = response.data.data.allMoney;
                    this.toDayIncome = response.data.data.toDayIncome;
                    this.totalIncome = response.data.data.totalIncome;
                    this.toDayPay = response.data.data.toDayPay;
                    this.totalSpending = response.data.data.totalSpending;
                }).catch(function (error) {
                    console.log(error)
                });
            },
        },
        mounted: function () {
            this.getCounts();
            this.getCount();
        }
    }
</script>