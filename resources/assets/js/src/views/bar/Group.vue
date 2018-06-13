<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">书吧装修</span>
            </div>
            <el-form ref="form" :model="form" label-width="120px" id="bookInfoForm">
                <el-form-item label="书吧名称:">
                    <span style="margin-left: 50px;">{{ form.groupName }}</span>
                </el-form-item>
                <el-form-item label="书吧地址:">
                    <span id="address" style="margin-left: 50px;">http://www.bookfan.com</span>
                    <el-button type="primary" @click="copy()" style="margin-left: 50px">复制地址</el-button>
                </el-form-item>
                <el-form-item label="书吧二维码:">
                    <div>
                        <img id="code" src="/images/code.jpg" download style="width: 120px; height: 120px; margin-left: 50px;"/>
                    </div>
                    <el-button type="primary" @click="download()" style="margin:20px 50px;">下载二维码</el-button>
                </el-form-item>
                <el-form-item
                        prop="groupIntro"
                        label="书吧介绍:"
                        :rules="[
                        { required: true, message: '内容不能为空'}]">
                    <el-input
                            type="textarea" :rows="7"
                            v-model="form.groupIntro" style="width: 500px;"
                            placeholder="填写书吧介绍，用于网页分享介绍本书吧"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitForm('form')" style="margin-left: 50px">保存</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<style>

</style>

<script>
    export default {
        data() {
            return {
                loading: true,
                form: {}
            }
        },
        methods: {
            getData(){
                axios.get('/api/bar/group/show').then(response => {
                    this.form = response.data.data
                    this.loading = false
                }).catch(function (error) {
                    console.log(error)
                });
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$confirm('确定提交吗?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            axios.post('/api/bar/group/update', this.form).then(response => {
                                var data = response.data;
                                if(data.code === 200){
                                    this.$message.success(data.message);
                                    this.getData()
                                }else{
                                    this.$message.error(data.message);
                                }
                            }).catch(function (error) {
                                console.log(error)
                            });
                        }).catch(() => {
                            this.$message.info('已取消');
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            //复制
            copy:function () {
                var text = $('#address').text()
                    clipboard.copy(text).then(function(){
                        alert('复制成功')
                    }, function(err){
                        alert('复制失败')
                    });
            },
            //下载二维码
            download:function () {
                var codeUrl = $("#code").attr('src')
                var $a = $("<a></a>").attr("href", codeUrl).attr("download", "code.jpg");
                $a[0].click();
            }
        },
        mounted: function () {
            this.getData()
        }
    }
</script>
