<template>
    <div class="animated fadeIn">
        <div class="binding-wechat-box">
            <el-row :gutter="5">
                <el-col >
                    <p >提现前需先绑定到微信账号(到账微信号一经绑定不可更改)。微信实名认证后每日能提现上限两万,未认证每日提现2000元！:<a href="#">如何实名认证？</a></p>
                </el-col>
                <el-col>
                    <el-button class="bind-wechat" type="success">绑定微信</el-button>
                    <ul style="height: 180px; padding:0; margin:0" type="none">
                        <li>微信昵称：</li>
                        <li>微信头像：</li>
                    </ul>
                </el-col>
                <el-col>
                        <p>获取验证码</p>
                    <p>验证码将发送到管理员绑定手机号:<span style="color: red;">{{ this.tableData.bindPhone }}</span>,请注意查收</p>
                    <div>
                        <label>验证码：</label>
                        <el-input placeholder="输入验证码" style="width: 220px;"></el-input>
                        <el-button v-if="!sendMsgDisabled" style="marign-left:20px" type="success" @click="sendSMS()">发送验证码
                        </el-button>
                        <el-button v-if="sendMsgDisabled" :disabled="true" style="marign-left:20px" type="success" @click="sendSMS()">{{this.time + '秒后获取'}}
                        </el-button>
                    </div>
                </el-col>
            </el-row>
            <div style="text-align: center;">
                <el-button type="primary" @click="addNewBook()">确认绑定</el-button>
            </div>
        </div>
    </div>
</template>

<style>
    .binding-wechat-box .bind-wechat{
        margin: 20px 0;
    }
</style>

<script>
    export default {
        name: 'tColumn',
        data() {
            return {
                //加载动画
                loading: true,
                //表格数据
                tableData: [],

                total: 0,
                page: 1,
                pagesize: 20,

                //短信计时
                sendMsgDisabled: '',
                time: 60,
                code: '',//验证码
            }
        },
        methods: {
            //api获取管理员相关信息
            getData: function () {
                axios.post('/api/t_finance/barAdminInfo').then(response => {
                    this.tableData = response.data.data;
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //发送验证码
            sendSMS: function () {
                axios.post('/api/t_send_sms?tel=' + this.tableData.bindPhone).then(response => {
                    if (response.data.code === 200){
                        let me = this;
                        me.sendMsgDisabled = true;
                        let interval = window.setInterval(function() {
                            if ((me.time--) <= 0) {
                                me.time = 60;
                                me.sendMsgDisabled = false;
                                window.clearInterval(interval);
                            }
                        }, 1000);
                    }else if (response.data.code === 500){
                        this.$notify.error({
                            title: '系统提示',
                            message: '手机号不能为空'
                        })
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            }
        },
        mounted: function () {
            this.getData();
        }
    }
</script>