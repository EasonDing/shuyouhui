<template>
    <div class="animated fadeIn">
        <!--管理员-->
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <el-row :span="24">书友会数据概括</el-row>
            <el-row :gutter="20" class="index-data">
                <el-col :span="6">
                    <div @click="jump('/admin/new/user')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.newUserCount ? this.newUserCount : 0}}人</p>
                        <div class="medium">今日新增用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/admin/user')" class="grid-content medium bg-purple data-box"><p>
                        {{this.userCount ? this.userCount : 0}}人</p>
                        <div class="medium">所有用户</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/admin/new/group')" class="grid-content medium bg-purple data-box"><p>
                        {{this.newGroupCount ? this.newGroupCount : 0}}个</p>
                        <div class="medium">今日新增书吧</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/admin/group')" class="grid-content medium bg-purple data-box"><p>
                        {{this.groupCount ? this.groupCount : 0}}个</p>
                        <div class="medium">所有书吧</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/admin/trading/money')" class="grid-content medium bg-purple data-box"><p>
                        {{ this.toDayIncome ? this.toDayIncome : 0}}元</p>
                        <div class="medium">今日交易金额</div>
                    </div>
                </el-col>
                <el-col :span="6">
                    <div @click="jump('/admin/recharge/money')" class="grid-content medium bg-purple data-box"><p>
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
                        <div class="medium" @click="jump('/admin/user')">群发消息</div>
                    </div>
                </el-col>
                <el-col :span="4">
                    <div class="grid-content medium bg-purple function-box" @click="jump('/admin/group/admin')">
                        <div class="medium">创建帐号</div>
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
        data() {
            return {
                loading: true,
                newUserCount: '',//今日新增用户数量
                userCount: '',//所有用户总数
                newGroupCount: '',//今日新增书吧
                groupCount: '',//所有书吧
                accountAmount: '',//帐户余额
                toDayIncome: '',//今日交易金额
                toDayRecharge: '',//今日充值金额
            }

        },
        methods: {
            //首页数据 分管理员和分组
            getData: function () {
                //管理员
                axios.get('/api/admin/index/count').then(response => {
                    this.newUserCount = response.data.data.newUserCount;
                    this.userCount = response.data.data.userCount;
                    this.newGroupCount = response.data.data.newGroupCount;
                    this.groupCount = response.data.data.groupCount;
                    this.accountAmount = response.data.data.accountAmount;
                    this.toDayIncome = response.data.data.toDayIncome;
                    this.toDayRecharge = response.data.data.toDayRecharge;
                    this.loading = false
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
