<template>
    <div class="animated fadeIn">
        <!--管理员-->
        <el-card v-show="admin" class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <el-row :span="24">书友会数据概括</el-row>
            <el-row :gutter="20" class="index-data">
                <el-col :span="6">
                    <div @click="jump('/today_new_user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newUserCount ? this.newUserCount : 0}}人</p>
                        <div class="medium">今日新增用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/user')" class="grid-content medium bg-purple data-box"><p>
                        {{this.userCount ? this.userCount : 0}}人</p>
                        <div class="medium">所有用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/today_new_group')" class="grid-content medium bg-purple data-box"><p>
                        {{this.newGroupCount ? this.newGroupCount : 0}}个</p>
                        <div class="medium">今日新增书吧</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/group')" class="grid-content medium bg-purple data-box"><p>
                        {{this.groupCount ? this.groupCount : 0}}个</p>
                        <div class="medium">所有书吧</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/today_trading_money')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.toDayIncome ? this.toDayIncome : 0}}元</p>
                        <div class="medium">今日交易金额</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/today_recharge_money')" class="grid-content medium bg-purple data-box"><p>
                        {{this.toDayRecharge ? this.toDayRecharge : 0}}元</p>
                        <div class="medium">今日充值金额</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div class="grid-content medium bg-purple data-box">
                        <p>{{this.accountAmount ? this.accountAmount : 0}}元</p>
                        <div class="medium">帐户金额</div>
                    </div>
                </el-col>
            </el-row>

            <el-row :span="24">常用功能</el-row>
            <el-row :gutter="20" class="index-function">
                <el-col :span="4">
                    <div class="grid-content medium bg-purple function-box">
                        <div class="medium" @click="jump('/user')">群发消息</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div class="grid-content medium bg-purple function-box" @click="jump('/group_admin')">
                        <div class="medium">创建帐号</div>
                    </div>
                </el-col>
            </el-row>
        </el-card>

        <!--吧主-->
        <el-card v-show="bar" class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <span class="extra-large">{{this.groupName}}</span>
            </div>
            <el-row :span="24">书吧数据概括</el-row>
            <el-row :gutter="20" class="index-data">
                <el-col :span="6">
                    <div @click="jump('/t_today_new_user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newUsers ? this.newUsers : 0}}人</p>
                        <div class="medium">今日新增用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/t_today_active_user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.activeUsers ? this.activeUsers : 0}}人</p>
                        <div class="medium">今日活跃用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/t_today_revenue')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.toDayMoney ? this.toDayMoney : 0}}元</p>
                        <div class="medium">今日收入</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div class="grid-content medium bg-purple data-box"><p>
                        {{ this.allMoney ? this.allMoney : 0}}元</p>
                        <div class="medium">帐户余额</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/t_today_new_order')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newOrders ? this.newOrders : 0}}单</p>
                        <div class="medium">今日新增订单</div>
                    </div>
                </el-col>
            </el-row>
            <el-row :span="24">常用功能</el-row>
            <el-row :gutter="20" class="index-function">
                <el-col :span="4">
                    <div @click="jump('/t_money_out')" class="grid-content medium bg-purple function-box">
                        <div class="medium">收入提现</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div @click="jump('/t_banner')" class="grid-content medium bg-purple function-box">
                        <div class="medium">编辑书吧banner</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div @click="jump('/t_user')" class="grid-content medium bg-purple function-box">
                        <div class="medium">群发消息</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div @click="jump('/t_book_add')" class="grid-content medium bg-purple function-box">
                        <div class="medium">添加图书</div>
                    </div>
                </el-col>
            </el-row>
        </el-card>
    </div>
</template>

<style>
    .el-row {
        margin-bottom: 20px;
    }

    :last-child {
        margin-bottom: 0;
    }

    .el-col {
        border-radius: 4px;
    }

    .bg-purple-dark {
        background: #99a9bf;
    }

    .bg-purple {
        background: #EFF2F7;
    }

    .bg-purple-light {
        background: #e5e9f2;
    }

    .grid-content {
        border-radius: 4px;
        min-height: 36px;
    }

    .row-bg {
        padding: 10px 0;
        background-color: #f9fafc;
    }

    /*数据概括*/
    .index-data .data-box {
        text-align: center;
        margin: 10px 0;
        height: 130px;
    }

    .data-box p {
        margin-bottom: 5px;
        padding-top: 35px;
        color: #13CE66;
    }

    .index-data div {
        cursor: pointer;
    }

    /*常用功能*/
    .index-function .function-box {
        height: 80px;
        line-height: 80px;
        text-align: center;
    }
</style>

<script>
    export default {
        name: 'index',
        data() {
            return {
                loading: true,

                admin: false,
                bar: false,

                options: [],//未关联书吧管理员的列表
                formLabelWidth: '120px',
                newUserCount: '',//今日新增用户数量
                userCount: '',//所有用户总数
                newGroupCount: '',//今日新增书吧
                groupCount: '',//所有书吧
                accountAmount: '',//帐户余额
                toDayIncome: '',//今日交易金额
                toDayRecharge: '',//今日充值金额

                allMoney: '',//帐户余额
                groupName: '',//书吧名字
                newUsers: '',//今日新增用户
                activeUsers: '',//今日活跃用户
                toDayMoney: '',//今日收入
                newOrders: '',//今日新增订单
            }

        },
        methods: {
            //首页数据 分管理员和分组
            getData: function () {
                //管理员
                if (this.admin){
                    axios.post('/api/index').then(response => {
                        this.newUserCount = response.data.data.newUserCount
                        this.userCount = response.data.data.userCount
                        this.newGroupCount = response.data.data.newGroupCount
                        this.groupCount = response.data.data.groupCount
                        this.accountAmount = response.data.data.accountAmount
                        this.toDayIncome = response.data.data.toDayIncome
                        this.toDayRecharge = response.data.data.toDayRecharge
                        this.loading = false
                    }).catch(function (error) {
                        console.log(error)
                    });
                }
                //吧主
                if (this.bar){
                    axios.get('/api/t_index').then(response => {
                        this.allMoney = response.data.data.allMoney
                        this.groupName = response.data.data.groupName
                        this.newUsers = response.data.data.newUsers
                        this.activeUsers = response.data.data.activeUsers
                        this.toDayMoney = response.data.data.toDayMoney
                        this.newOrders = response.data.data.newOrders
                        this.loading =false
                    }).catch(function (error) {
                        console.log(error)
                    });
                }
            },

        },
        mounted: function () {
            //判断是登录用户是管理员还是吧主
            axios.get('/api/roles').then(response => {
                if (response.data.data === 1){
                    this.admin = true;
                }else {
                    this.bar = true
                }
                this.getData()
            }).catch(function (error) {
                console.log(error)
            });
        }
    }
</script>
