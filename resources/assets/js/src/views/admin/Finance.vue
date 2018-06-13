<template>
    <div class="animated fadeIn">
        <el-row :gutter="20" class="t-finance-data">
            <el-col :span="4">
                <div @click="jump('/admin/trading/money')" class="grid-content medium bg-purple data-box"><p>{{ this.toDayIncome ? this.toDayIncome : 0}}</p>
                    <div class="medium">今日交易金额</div>
                </div>
            </el-col>
            <el-col :span="4">
                <div @click="jump('/admin/recharge/money')" class="grid-content medium bg-purple data-box"><p>{{this.toDayRecharge ? this.toDayRecharge : 0}}</p>
                    <div class="medium">今日充值金额</div>
                </div>
            </el-col>
            <el-col :span="4">
                <div class="grid-content medium bg-purple data-box"><p>{{this.accountAmount ? this.accountAmount : 0}}</p>
                    <div class="medium">帐户余额</div>
                </div>
            </el-col>
        </el-row>
        <div>
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">详细帐单</span>
                <div style="float: right;">
                    <el-form :inline="true">
                        <el-select v-model="type" @change="getFinances" placeholder="消息类型" style="width: 120px;">
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
                                @change="getFinances"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                        <el-input clearable @change="getFinances" style="width: 260px;" v-model="keyword" placeholder="输入手机号"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
        </div>
        <el-table
                v-loading.body="loading"
                element-loading-text="拼命加载中..."
                stripe
                :data="finances">
            <el-table-column
                    prop="type"
                    label="类型">
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
    </div>
</template>

<style>
    .t-finance-data div{
        cursor: pointer;
    }
</style>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: true,
                //财务列表数据
                finances: [],
                //下拉列表框值
                options: [
                    {
                        value: null,
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

                //分页属性
                total: 0,
                page: 1,
                pagesize: 20,

                accountAmount: '',//帐户余额
                toDayIncome: '',//今日交易金额
                toDayRecharge: '',//今日充值金额

                //搜索关键字
                keyword: '',
                date: [],
                //账单类型
                type: '',

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
                this.getFinances();
            },
            //财务详情列表
            getFinances: function () {
                const self = this;
                self.loading = true
                axios.get('/api/admin/finance/index', {
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
            //财务统计数据
            getStatistics:function () {
                axios.get('/api/admin/finance/statistics').then(response => {
                    this.accountAmount = response.data.data.accountAmount;
                    this.toDayIncome = response.data.data.toDayIncome;
                    this.toDayRecharge = response.data.data.toDayRecharge;
                }).catch(function (error) {
                    console.log(error)
                });
            },
            //动态路由跳转
            jump:function (path) {
                this.$router.push(path)
            },
        },
        mounted: function () {
            this.getFinances();
            this.getStatistics();
        }
    }
</script>