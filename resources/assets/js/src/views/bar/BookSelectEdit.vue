<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">编辑图书</span>
            </div>
            <el-form :model="formData" :rules="rules" ref="formData" label-width="100px" class="demo-formData">
                <el-form-item>
                    <div>
                        <el-row :span="24">
                            <el-col :span="6" style="width: 500px;">
                                <div>
                                    <img style="width: 100px; height: 120px; float:left" :src="books.image"/>
                                    <ul class="bookrack-ul-bookinfo select-edit" type="none">
                                        <li>书名：{{ books.title }}</li>
                                        <li>作者：{{ books.author}}</li>
                                        <li>简介：{{ books.summary }}</li>
                                        <li class="price">价格：{{ books.price ? books.price : 0}}元</li>
                                    </ul>
                                </div>
                            </el-col>
                            <el-col :span="5" style="height: 100px; line-height: 100px;">
                                上传日期：{{ books.created_at }}
                            </el-col>
                        </el-row>
                    </div>
                </el-form-item>
                <el-form-item label="推荐语" prop="summary">
                    <el-input type="textarea" :rows="10" v-model="formData.summary" placeholder="请编写对此书的推荐语" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="updateBook('formData')">保存</el-button>
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
        height: 120px;
    }
    .select-edit li{
        height: 28px;
    }

</style>

<script>
    export default {
        data() {
            return {
                //图书详细信息
                formData: {
                    summary: '',
                },
                form: {

                },
                books: [],//书本信息

                rules: {
                    summary: [
                        { required: true, message: '请输入推荐语', trigger: 'change' }
                    ]
                }
            };
        },
        methods: {
            getBook:function () {
                axios.get('/api/bar/book/edit/' + this.$route.params.id ).then(response => {
                    if(response.data.code === 200){
                        this.books = response.data.data;
                        this.formData = {
                            summary: this.books.summary,
                        };
                    }else{
                        this.$message.error('错了哦，没有找到该图书');
                        this.$router.push('/bar/book');
                    }
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //验证
            updateBook(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.$confirm('保存图书信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            axios.post('/api/bar/book/update/'+ this.books.isbn, this.formData).then(response => {
                                if(response.data){
                                    this.$notify.success({
                                        title: '系统提示',
                                        message: '更新成功',
                                    });
                                    this.$router.push('/bar/book')
                                }else{
                                    this.$notify.error({
                                        title: '系统提示',
                                        message: '更新失败',
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
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
        },
        mounted: function () {
            this.getBook()
        }
    }
</script>
