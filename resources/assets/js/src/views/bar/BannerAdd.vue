<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <span style="line-height: 36px">图文信息</span>
            </div>
            <el-form :model="banner" :rules="rules" ref="banner" label-width="100px" class="demo-banner">
                <el-form-item label="图文标题" prop="title">
                    <el-input v-model="banner.title" class="new-book-input"></el-input>
                </el-form-item>
                <!--图片上传-->
                <el-form-item label="封面" prop="image">
                    <el-upload
                            class="avatar-uploader2"
                            :show-file-list="false"
                            :auto-upload="false"
                            action=""
                            accept="image/*"
                            :on-change="uploadImage"
                            :before-upload="beforeAvatarUpload">
                        <img v-if="banner.image" :src="banner.image" class="avatar3">
                        <i v-else style="line-height: 140px;" class="el-icon-plus avatar-uploader2-icon"></i>
                    </el-upload>
                </el-form-item>
                <!--富文本编辑器-->
                <el-form-item label="图文详情" prop="content">
                    <quill-editor style="height: 350px; margin-bottom: 90px"
                                  ref="myTextEditor"
                                  v-model="banner.content"
                                  :config="editorOption"
                    ></quill-editor>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="AddBanner('banner')">保存</el-button>
                    <el-button @click="resetForm('banner')">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<style>
    .avatar-uploader2 .el-upload {
        border: 1px dashed #d9d9d9;
        border-radius: 6px;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .avatar-uploader2 .el-upload:hover {
        border-color: #20a0ff;
    }
    .avatar-uploader2-icon {
        font-size: 28px;
        color: #8c939d;
        width: 200px;
        height: 140px;
        line-height: 140px;
        text-align: center;
    }
    .avatar3 {
        width: 200px;
        height: 140px;
        display: block;
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
                formData: {},
                banner: {
                    image: '',
                    title: '',
                    content: ''
                },
                rules: {
                    title: [
                        { required: true, message: '请输入书名', trigger: 'change' },
                        { min: 1, max: 15, message: '长度在 1 到 15 个字符', trigger: 'change' }
                    ],
                    image: [
                        { required: true, message: '上传banner封面', trigger: 'change' }
                    ],
                    content: [
                        { required: true, message: '请输入banner详情', trigger: 'change' }
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
            //quill编辑器
            onEditorChange({editor, html, text}) {
                this.content = html;
            },
            AddBanner(formName) {
                const self = this;
                //表单验证
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.$confirm('保存 Banner 信息, 是否继续?', '提示', {
                            confirmButtonText: '确定',
                            cancelButtonText: '取消',
                            type: 'warning'
                        }).then(() => {
                            Object.keys(self.banner).map(key => {
                                self.formData.append(key, self.banner[key]);
                            });
                            axios.post('/api/bar/banner/store', self.formData).then(response => {
                                var data = response.data;
                                if (data.code === 200) {
                                    self.$message.success(data.message);
                                    self.$router.push('/bar/banner')
                                } else {
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
            uploadImage(file){
                let formData = new FormData();
                this.banner.image = file.url;
                formData.append('file',file.raw);
                this.formData = formData;
            },
        },
        mounted: function () {

        }
    }
</script>