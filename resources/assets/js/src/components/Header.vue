<template>
    <el-row>
        <el-col :span="12" class="logo">贝壳书友会</el-col>
        <el-col :span="12" class="userInfo">
            <el-badge is-dot class="item">
                <el-dropdown :show-timeout="0" :hide-timeout="0">
                    <span class="el-dropdown-link">通知中心<i class="el-icon-arrow-down el-icon--right"></i></span>
                    <el-dropdown-menu slot="dropdown">
                        <el-badge is-dot class="item">
                            <el-dropdown-item>系统消息</el-dropdown-item>
                        </el-badge>
                        <el-badge is-dot class="item">
                            <el-dropdown-item>私信</el-dropdown-item>
                        </el-badge>
                    </el-dropdown-menu>
                </el-dropdown>
            </el-badge>
            <img :src="this.user.face" class="imoldPassword userLogo">
            <el-dropdown :show-timeout="0" :hide-timeout="0" @command="handleCommand">
                <span class="el-dropdown-link">{{ this.user.name }}<i class="el-icon-arrow-down el-icon--right"></i></span>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item command="changePasswordDialogVisible">修改密码</el-dropdown-item>
                    <el-dropdown-item command="logout">退出</el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
            <!--注销登录-->
            <form id="logout-form" action="/logout" method="POST" style="display: none;">
                <input type="hidden" name="_token" id="_token" value="">
            </form>
        </el-col>
        <!--修改密码-->
        <el-dialog
                title="修改密码"
                :visible.sync="changePasswordDialogVisible"
                width="30%"
                center>
            <el-form :model="changePasswordForm" status-icon :rules="rules2" ref="changePasswordForm" label-width="80px" class="demo-ruleForm">
                <el-form-item label="原密码" prop="oldPassword">
                    <el-input type="password" v-model="changePasswordForm.oldPassword"></el-input>
                </el-form-item>
                <el-form-item label="新密码" prop="password">
                    <el-input type="password" v-model="changePasswordForm.password" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item label="确认密码" prop="checkPassword">
                    <el-input type="password" v-model="changePasswordForm.checkPassword" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item
                        labelWidth="0"
                        style="text-align: center;">
                    <el-button type="primary" @click="revisePassword('changePasswordForm')">提交</el-button>
                    <el-button @click="resetForm('changePasswordForm')">重置</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </el-row>
</template>

<style>
    .full-header {
        height: 55px !important;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }

    .full-header .userInfo {
        text-align: right;
    }

    .full-header .userLogo {
        border-radius: 50%;
        width: 35px;
        height: 35px;
        margin: 0 10px;
    }

    .full-header .logo {
        height: 35px;
        line-height: 35px;
    }
</style>

<script>
    export default {
        data () {
            var checkoldPassword = (rule, value, callback) => {
                if (!value) {
                    return callback(new Error('原密码不能为空'));
                }else {
                    callback();
                }

            };
            var validatePass = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请输入新密码'));
                } else {
                    if (this.changePasswordForm.checkPassword !== '') {
                        this.$refs.changePasswordForm.validateField('checkPassword');
                    }
                    callback();
                }
            };
            var validatePass2 = (rule, value, callback) => {
                if (value === '') {
                    callback(new Error('请再次输入新密码'));
                } else if (value !== this.changePasswordForm.password) {
                    callback(new Error('两次输入密码不一致!'));
                } else {
                    callback();
                }
            };

            return {
                changePasswordDialogVisible: false,
                changePasswordForm: {
                    password: '',
                    checkPassword: '',
                    oldPassword: ''
                },
                rules2: {
                    password: [
                        { validator: validatePass, trigger: 'blur' }
                    ],
                    checkPassword: [
                        { validator: validatePass2, trigger: 'blur' }
                    ],
                    oldPassword: [
                        { validator: checkoldPassword, trigger: 'blur' }
                    ]
                },
                user:{}
            };
        },
        methods: {
            //下拉菜单单击事件
            handleCommand(command) {
                if (command == 'logout') {
                    document.getElementById('_token').value = window.Laravel.csrfToken;
                    document.getElementById('logout-form').submit();
                }else if (command == 'changePasswordDialogVisible'){
                    this.changePasswordDialogVisible = true;
                }
            },
            //修改密码
            revisePassword(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post('/api/admin/admin/change/password', this.changePasswordForm).then(response => {
                            var data = response.data;
                            this.$message.success(data.message);
                            this.changePasswordDialogVisible = false;
                            this.resetForm(formName);
                        });
                    } else {
                        return false;
                    }
                });
            },
            //重置表单
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
        },
        mounted: function () {
            axios.get('/api/admin/user').then(response => {
                this.user = response.data.data;
            });
        },
    }
</script>