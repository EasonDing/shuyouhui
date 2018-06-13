<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span class="extra-large">图书管理</span>
            </div>
            <el-form :model="book" :rules="rules" ref="book" label-width="100px" class="demo-book">
                <el-form-item label="书名" prop="title">
                    <el-input v-model="book.title" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="作者" prop="author" >
                    <el-input v-model="book.author" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="出版社" prop="publisher">
                    <el-input v-model="book.publisher" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="ISBN" prop="isbn">
                    <el-input v-model="book.isbn" class="new-book-input"></el-input>
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
                        <img v-if="book.image" :src="book.image" class="avatar2">
                        <i v-else style="line-height: 178px;" class="el-icon-plus avatar-uploader-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item
                        label="价格"
                        prop="price"
                        :rules="[
                        { required: true, message: '价格不能为空'},
                        { type: 'number', message: '价格必须为数字值'}]">
                    <el-input type="number" v-model.number="book.price" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item label="推荐语" prop="summary">
                    <el-input type="textarea" v-model="book.summary" class="new-book-input"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="addBook('book')">保存</el-button>
                    <el-button @click="resetForm('book')">重置</el-button>
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
                //是否更新图片
                isUpdateImage: 0,
                formData: {},
                //图书数据
                book: {
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
                    image: [
                        { required: true, message: '上传书本封面', trigger: 'change' }
                    ],
                    summary: [
                        { required: true, message: '请输入推荐语', trigger: 'change' }
                    ],
                    isbn: [
                        { required: true, message: '请输入ISBN', trigger: 'change' }
                    ],
                }
            }
        },
        methods: {
            getBook:function () {
                const self = this;
                axios.get('/api/admin/book/edit/'+ self.$route.params.id).then(response => {
                    var data = response.data;
                    if (data.code === 200) {
                        self.book = data.data;
                    } else {
                        self.$message.error(data.message);
                        self.$router.push('/admin/book')
                    }
                }).catch(function (error) {
                    self.$message.error('服务器错误!');
                });
            },
            //验证 提交
            addBook(formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.$confirm('保存图书信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            var data = self.book;
                            //如果有更新图片再转 map
                            if (self.isUpdateImage){
                                Object.keys(self.book).map(key => {
                                    self.formData.append(key, self.book[key]);
                                });
                                data = self.formData;
                            }
                            axios.post('/api/admin/book/update/' + self.book.id, data).then(response => {
                                var data = response.data;
                                if(data.code === 200){
                                    self.$message.success(data.message);
                                    self.$router.push('/admin/book');
                                }else{
                                    self.$message.error(data.message);
                                }
                            }).catch(function (error) {
                                self.$message.error('服务器错误!');
                            });
                        }).catch(() => {
                            self.$message.info('已取消保存');
                        });
                    } else {
                        return false;
                    }
                });
            },
            //重置表单数据
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
            upload(file){
                let formData = new FormData();
                this.book.image = file.url;
                formData.append('file',file.raw);
                this.formData = formData;
                this.isUpdateImage = 1;
            },
        },
        mounted:function () {
            this.getBook()
        }
    }
</script>
