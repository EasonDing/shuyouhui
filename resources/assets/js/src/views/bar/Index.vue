<template>
    <div class="animated fadeIn">
        <!--吧主-->
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <span class="extra-large">{{this.groupName}}</span>
            </div>
            <el-row :span="24">书吧数据概括</el-row>
            <el-row :gutter="20" class="index-data">
                <el-col :span="6">
                    <div @click="jump('/bar/new/user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newUsers ? this.newUsers : 0}}人</p>
                        <div class="medium">今日新增用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/bar/active/user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.activeUsers ? this.activeUsers : 0}}人</p>
                        <div class="medium">今日活跃用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/bar/revenue')" class="grid-content medium bg-purple data-box"><p>
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
                    <div @click="jump('/bar/new/order')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newOrders ? this.newOrders : 0}}单</p>
                        <div class="medium">今日新增订单</div>
                    </div>
                </el-col>
            </el-row>
            <el-row :span="24">常用功能</el-row>
            <el-row :gutter="20" class="index-function">
                <el-col :span="4">
                    <div @click="jump('/bar/money/out')" class="grid-content medium bg-purple function-box">
                        <div class="medium">收入提现</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div @click="jump('/bar/banner')" class="grid-content medium bg-purple function-box">
                        <div class="medium">编辑书吧banner</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div @click="jump('/bar/group/user')" class="grid-content medium bg-purple function-box">
                        <div class="medium">群发消息</div>
                    </div>
                </el-col>
                <!--<el-col :span="4">-->
                    <!--<div @click="jump('/bar/book/add')" class="grid-content medium bg-purple function-box">-->
                        <!--<div class="medium">添加图书</div>-->
                    <!--</div>-->
                <!--</el-col>-->
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
        data() {
            return {
                loading: true,

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
                axios.get('/api/bar/index/count').then(response => {
                    this.allMoney = response.data.data.allMoney;
                    this.groupName = response.data.data.groupName;
                    this.newUsers = response.data.data.newUsers;
                    this.activeUsers = response.data.data.activeUsers;
                    this.toDayMoney = response.data.data.toDayMoney;
                    this.newOrders = response.data.data.newOrders;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error)
                });
            },
            jump($path){
                this.$router.push($path);
            },
        },
        mounted: function () {
            this.getData();
        }
    }
</script>
