<template>
    <div class="animated fadeIn column-edit">
        <el-card class="box-card">
            <el-form :model="column" :rules="rules" ref="column" label-width="100px">
                <el-form-item label="图文标题" prop="title">
                    <el-input v-model="column.title" class="input"></el-input>
                </el-form-item>
                <!--图片上传-->
                <el-form-item label="封面" prop="image">
                    <el-upload
                            class="image-upload"
                            :show-file-list="false"
                            :auto-upload="false"
                            action=""
                            accept="image/*"
                            :on-change="upload"
                            :before-upload="beforeAvatarUpload">
                        <img class="image" v-if="column.image" :src="column.image">
                        <i v-else class="el-icon-plus image-icon"></i>
                    </el-upload>
                </el-form-item>
                <el-form-item label="图文详情" prop="content">
                    <el-input
                            type="textarea"
                            :rows="10"
                            placeholder="请输入内容"
                            v-model="column.content">
                    </el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="updateColumn('column')">保存</el-button>
                    <el-button @click="resetForm('column')">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<style>
    .column-edit .input {
        width: 30%;
    }
    .column-edit .image-upload .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .column-edit .image .el-upload:hover {
        border-color: #20a0ff;
    }
    .column-edit .image-icon {
        font-size: 28px;
        color: #8c939d;
        width: 200px;
        height: 140px;
        line-height: 140px !important;
        text-align: center;
    }
    .column-edit .image-upload .image {
        width: 200px;
    }
</style>

<script>
    import { quillEditor } from 'vue-quill-editor';
    export default {
        data() {
            return {
                //编辑器
                content: '<p>请开始你的创作!</p>',
                editorOption: {
                    // something config
                },
                loading: false,
                //是否有更新图片
                isUpdateImage: 0,
                formData: {},
                column: {
                    title: '',
                    image: '',
                    content: ''
                },

                rules: {
                    title: [
                        { required: true, message: '请输入书名', trigger: 'change' },
                        { min: 1, max: 15, message: '长度在 1 到 15 个字符', trigger: 'change' }
                    ],
                    image: [
                        { required: true, message: '上传专栏封面', trigger: 'change' }
                    ],
                    content: [
                        { required: true, message: '请输入专栏详情', trigger: 'change' }
                    ]
                }
            }
        },
        components: {
            quillEditor
        },
        computed: {
            editor() {
                return this.$refs.myTextEditor.quillEditor;
            }
        },
        methods: {
            getColumn:function () {
                axios.get('/api/bar/column/edit/' + this.$route.params.id ).then(response => {
                    var data = response.data;
                    if(data.code === 200){
                        this.column = data.data;
                    }else{
                        this.$message.error(data.message);
                        this.$router.push('/bar/column');
                    }

                }).catch((error) => {
                    this.$message.error('服务器错误！');
                    this.$router.push('/bar/column');
                });
            },
            //quill编辑器
            onEditorChange({ editor, html, text }) {
                this.content = html;
            },
            //更新专栏
            updateColumn(column) {
                //验证
                this.$refs[column].validate((valid) => {
                    if (valid) {
                        this.$confirm('更新专栏信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            var data = this.column;
                            //如果有更新图片再转
                            if (this.isUpdateImage){
                                Object.keys(this.column).map(key => {
                                    this.formData.append(key, this.column[key]);
                                });
                                data = this.formData;
                            }
                            axios.post('/api/bar/column/update/' + this.column.id, data).then(response => {
                                var data = response.data;
                                if(data.code === 200){
                                    this.$message.success(data.message);
                                    this.$router.push('/bar/column');
                                }else{
                                    this.$message.error(data.message);
                                }
                            }).catch(function (error) {
                                console.log(error)
                            });
                        }).catch(() => {
                            this.$message.info('已取消保存');
                        });
                    } else {
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();

            },
            //上传图片之前的一些验证
            beforeAvatarUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isLt2M) {
                    this.$message.error('上传头像图片大小不能超过 2MB!');
                }
                return isLt2M;
            },
            upload(file){
                let formData = new FormData();
                this.column.image = file.url;
                formData.append('file',file.raw);
                this.formData = formData;
                this.isUpdateImage = 1;
            },
        },
        mounted: function () {
            this.getColumn();
        }
    }
</script>