<template>
    <el-form :model="formData" :rules="rules" ref="formData" label-width="140px" class="demo-formData">

        <el-form-item label="账户余额" >
            <label style="width: 100px; color:#13CE66;text-align: center;">{{ this.moneyCount }} 元</label>
        </el-form-item>

        <el-form-item label="可提现余额(元)">
            <template>
                <label style="width: 100px; color:#13CE66;text-align: center;">{{ this.moneyCount > 1200 ? this.moneyCount - 1200 : 0 }} 元</label>
                <span>书友会不会加收手续费用，只需承担微信收取的0.6%的交易手续费，支付通道商手续费收取规则详见:<a style="color: #13CE66;" href="#">微信官方公告</a></span>
            </template>
        </el-form-item>

        <el-form-item label="到账微信号" prop="wechatName">
            <el-input v-model="formData.wechatName" type="text" style="width: 260px;"></el-input>
            <span style="margin-left: 30px;color: #13CE66;">绑定微信号</span>
        </el-form-item>

        <el-form-item
                label="提现金额"
                prop="money"
                :rules="[
                        { required: true, message: '金额不能为空'},
                        { type: 'number', message: '金额必须为数字值'}]">
            <el-input type="number" v-model.number="formData.money" style="width: 100px;"></el-input><span style="margin-left: 10px;">元</span>
            <span style="margin-left: 30px;color: #6e6e6e;">最低提现额度为200元,今日已提现<span style="color: #13CE66;">{{ this.out }}</span>元</span>
        </el-form-item>

        <el-form-item label="提现说明">
            <span>微信实名谁后每日能提现的上限为2万，未谁每日提现的上限为2000元</span><br/>
            <span>微信支付的结算周期为T+7提现申请后，7天后款项为自动转至您的微信钱包</span>
        </el-form-item>
            <div v-if="this.moneyCount - 1200 < 0" style="text-align: center; margin-right: 140px;">
                <el-button type="primary" disabled @click="submitForm('formData')">不可提现</el-button>
            </div>
            <div v-else style="text-align: center; margin-right: 140px;">
                <el-button type="primary" @click="submitForm('formData')">确认提现</el-button>
            </div>
    </el-form>
</template>
<script>
    export default {
        data() {
            return {
                formData: {},
                rules: {
                    wechatName: [
                        { required: true, message: '请输入微信号', trigger: 'change' },
                    ],
                },

                moneyCount: '',//账户余额
                out: '', //已申请提现金额
            };
        },
        methods: {
            getData:function () {
                axios.get('/api/t_money').then(response => {
                    this.moneyCount = response.data.data.moneyCount
                    this.out = response.data.data.out
                    console.log(this.out)
                }).catch(function (error) {
                    console.log(error)
            });
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.add()
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                })
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            //申请提现
            add:function () {
                this.$confirm('申请提现, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/t_money_out/store', this.formData).then(response => {
                        if (response.data) {
                            this.$notify.success({
                                title: '系统提示',
                                message: '申请提交成功',
                            });
                            this.$router.push('/t_finance_manage')
                        } else {
                            this.$notify.error({
                                title: '系统提示',
                                message: '申请提交失败',
                            });
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消保存'
                    });
                });
            }
        },
        //进入页面会自动执行该方法
        mounted: function () {
            this.getData();
        }
    }
</script>
