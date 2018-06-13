<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px;">图书管理</span>
            </div>
            <el-form :model="books" :rules="rules" ref="books" label-width="100px" class="demo-books">
                <el-form-item label="书名" prop="title">
                    <el-input v-model="books.title" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="作者" prop="author" >
                    <el-input v-model="books.author" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="出版社" prop="publisher">
                    <el-input v-model="books.publisher" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="ISBN" prop="isbn">
                    <el-input v-model="books.isbn" class="new-book-input"></el-input>
                </el-form-item>
                <!--图片上传-->
                <el-form-item label="封面" prop="image">
                    <el-upload
                            class="avatar-uploader2"
                            :show-file-list="false"
                            :auto-upload="false"
                            action=""
                            accept="image/*"
                            :on-change="upload"
                            :before-upload="beforeAvatarUpload">
                        <img v-if="books.image" :src="books.image" class="avatar2">
                        <i v-else style="line-height: 178px;" class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="介绍长图" prop="image_text">
                    <el-upload
                            class="avatar-uploader2"
                            :show-file-list="false"
                            :auto-upload="false"
                            action=""
                            accept="image/*"
                            :on-change="upload2"
                            :before-upload="beforeAvatarUpload2">
                        <img v-if="books.image_text" :src="books.image_text" class="avatar2">
                        <i v-else style="line-height: 178px;" class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item
                        label="价格"
                        prop="price"
                        :rules="[
                        { required: true, message: '价格不能为空'},
                        { type: 'number', message: '价格必须为数字值'}]">
                    <el-input type="number" v-model.number="books.price" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="图书简介" prop="introduction">
                    <el-input type="textarea" v-model="books.introduction" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="addBook('books')">保存</el-button>
                    <el-button @click="resetForm('books')">重置</el-button>
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

    .avatar-uploader .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader .el-upload:hover {
        border-color: #20a0ff;
    }
    .avatar-uploader-icon {
        font-size: 28px;
        color: #8c939d;
        width: 140px;
        height: 178px;
        line-height: 178px;
        text-align: center;
    }
    .avatar2 {
        width: 140px;
        height: 178px;
        display: block;
    }
</style>

<script>
    export default {
        data() {
            return {
                //图书详细信息
                formData: {},
                formData2: {},
                books: {
                    title: '',
                    author: '',
                    image: '',
                    image_text: '',
                    publisher: '',
                    isbn: '',
                    price: '',
                    introduction: '',
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
                    image: [
                        { required: true, message: '上传书本封面', trigger: 'change' }
                    ],
                    image_text: [
                        { required: true, message: '上传书本介绍长图', trigger: 'change' }
                    ],
                    introduction: [
                        { required: true, message: '请输入图书简介', trigger: 'change' }
                    ],
                    isbn: [
                        { required: true, message: '请输入ISBN', trigger: 'change' }
                    ],
                }
            }
        },
        methods: {
            //添加图书
            addBook(formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.$confirm('保存图书信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            //将上传的数据和图片数据转
                            Object.keys(self.books).map(key => {
                                self.formData.append(key, self.books[key]);
                            });
                            self.formData.append('file2', self.formData2.get('file2'))
                            axios.post('/api/admin/buy/book/store', self.formData).then(response => {
                                var data = response.data;
                                if (data.code === 200) {
                                    self.$message.success(data.message);
                                    self.$router.push('/admin/buy/book/list')
                                } else {
                                    self.$message.error(data.message);
                                }
                            }).catch(function (error) {

                            });
                        }).catch(() => {
                            self.$message.info('已取消保存');
                        });
                    } else {
                        return false;
                    }
                });
            },
            //重置form表单
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
            beforeAvatarUpload2(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
            upload(file){
                let formData = new FormData();
                this.books.image = file.url;
                formData.append('file',file.raw);
                this.formData = formData;
            },
            upload2(file){
                let formData = new FormData();
                this.books.image_text = file.url;
                formData.append('file2',file.raw);
                this.formData2 = formData;
            },
        },
        mounted:function() {

        }
    }
</script>
