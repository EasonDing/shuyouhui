<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span class="extra-large">图书管理</span>
            </div>
            <el-form :model="formData" :rules="rules" ref="formData" label-width="100px" class="demo-formData">
                <el-form-item label="书名" prop="title">
                    <el-input v-model="formData.title" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="作者" prop="author" >
                    <el-input v-model="formData.author" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="出版社" prop="publisher">
                    <el-input v-model="formData.publisher" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="ISBN" prop="isbn">
                    <el-input v-model="formData.isbn" class="new-book-input"></el-input>
                </el-form-item>
                <!--图片上传-->
                <el-form-item label="封面" prop="image">
                    <el-upload
                            prop="formData.image"
                            class="avatar-uploader"
                            :show-file-list="false"
                            :action="action"
                            accept="image/png,image/jpg"
                            :on-success="handleAvatarSuccess"
                            :before-upload="beforeAvatarUpload">
                        <img v-if="formData.image" :src="formData.image" class="avatar2">
                        <i v-else style="line-height: 178px;" class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item
                        label="价格"
                        prop="price"
                        :rules="[
                        { required: true, message: '价格不能为空'},
                        { type: 'number', message: '价格必须为数字值'}]">
                    <el-input type="number" v-model.number="formData.price" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="推荐语" prop="summary">
                    <el-input type="textarea" v-model="formData.summary" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="submitForm('formData')">保存</el-button>
                    <el-button @click="resetForm('formData')">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<style>
    .new-book-input .el-input__inner {
        width: 30%;
    }
    .new-book-input .el-textarea__inner{
        height: 80px;
    }
</style>

<script>
    export default {
        data() {
            return {
                action: 'http://na.bookfan.cn/api/t_book/upload',
                //图书详细信息
                formData: {
                    title: '',
                    author: '',
                    image: '',
                    publisher: '',
                    isbn: '',
                    price: '',
                    summary: '',
                },

                rules: {
                    title: [
                        { required: true, message: '请输入书名', trigger: 'change' },
                        { min: 1, max: 15, message: '长度在 1 到 15 个字符', trigger: 'change' }
                    ],
                    author: [
                        { required: true, message: '请输入作者', trigger: 'change' }
                    ],
                    publisher: [
                        { required: true, message: '请输入出版社', trigger: 'change' }
                    ],
                    isbn: [
                        { required: true, message: '请输入isbn', trigger: 'change' }
                    ],
                    image: [
                        { required: true, message: '上传书本封面', trigger: 'change' }
                    ],
                    summary: [
                        { required: true, message: '请输入推荐语', trigger: 'change' }
                    ]
                }
            };
        },
        methods: {
            //验证 添加图书
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$confirm('保存图书信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            axios.post('/api/t_book/store', this.formData).then(response => {
                                if (response.data.code === 200) {
                                    this.$notify.success({
                                        title: '系统提示',
                                        message: '添加成功',
                                    });
                                    this.$router.push('/bar/book');
                                } else if (response.data.code === 501){
                                    this.$notify.error({
                                        title: '系统提示',
                                        message: '贝壳书库中已有些书，请到贝壳图书中添加',
                                    });
                                    this.$router.push('/bar/book');
                                }else {
                                    this.$notify.error({
                                        title: '系统提示',
                                        message: '添加失败',
                                    });
                                    this.$router.push('/bar/book');
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
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            //图片地址
            handleAvatarSuccess(res, file) {
                this.formData.image = res;
            },
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
        },
        mounted:function () {

        }
    }
</script>
