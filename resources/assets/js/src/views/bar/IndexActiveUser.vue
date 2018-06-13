<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <div style="float: right;">
                    <el-form :inline="true" :model="filters">
                        <el-select v-model="filters.sex" @change="getActiveUsers" placeholder="性别" style="width: 120px;">
                            <el-option
                                    v-for="item in options"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
                        <el-input clearable style="width: 260px;" v-model="filters.input" placeholder="输入用户昵称或者手机号"></el-input>
                        <el-button @click="getActiveUsers" type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中"
                    stripe
                    :data="tableData"
                    style="width: 100%">
                <el-table-column
                        label="头像">
                    <template slot-scope="scope">
                        <el-popover trigger="hover" placement="right-end" width="315">
                            <el-row>
                                <el-col :span="7" >
                                    <img class="t-user-img-userlogo" :src="scope.row.UserLogo" :alt="null">
                                </el-col>
                                <el-col :span="17">
                                    <ul class="t-user-ul-userinfo" type="none">
                                        <li>{{ scope.row.signature }}</li>
                                        <li>{{ scope.row.city }}</li>
                                    </ul>
                                </el-col>
                            </el-row>

                            <div slot="reference" class="avatar">
                                <img :src="scope.row.UserLogo" :alt="scope.row.UserLogo" class="img-avatar"
                                     style="margin: 10px 0;">
                                <span v-if="scope.row.subscribe" class="avatar-status badge-success"></span>
                                <!--绿色小点-->
                                <span v-else class="avatar-status badge-default" style="margin: 10px 0;"></span>
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="username"
                        label="昵称">
                </el-table-column>
                <el-table-column
                        prop="sex"
                        label="性别"
                        :formatter="formatSex"
                >
                </el-table-column>
                <el-table-column
                        prop="Birthday"
                        label="年龄"
                >
                </el-table-column>
                <el-table-column
                        prop="bindPhone"
                        label="手机号码"
                >
                </el-table-column>
                <el-table-column
                        width="220"
                        prop="updateTime"
                        label="最后登录时间"
                >
                </el-table-column>

                <el-table-column label="操作" width="130">
                    <template slot-scope="scope">
                        <el-button type="info" size="small" @click="openDialog(3, scope.row)">发消息</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
        <!--发送消息-->
        <el-dialog
                title="发送消息"
                :modal-append-to-body="false"
                size="small"
                :visible.sync="dialogFormVisible">
            <el-form :model="dialogForm" ref="dialogForm" class="user-manage-from">
                <el-form-item
                        prop="username"
                        :rules="[{ required: true, message: '用户名称不能为空'}]">
                    <el-input v-model="dialogForm.username" auto-complete="off" placeholder="接收者" disabled></el-input>
                </el-form-item>
                <el-form-item
                        prop="title"
                        :rules="[{ required: true, message: '标题不能为空'}]">
                    <el-input v-model="dialogForm.title" auto-complete="off" placeholder="标题" class="user-manage-textarea"></el-input>
                </el-form-item>
                <el-form-item
                        prop="content"
                        :rules="[{ required: true, message: '内容不能为空'}]">
                    <el-input v-model="dialogForm.content" type="textarea" :rows="10"  auto-complete="off" placeholder="在此输入消息内容" class="user-manage-textarea"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="resetForm('dialogForm')">取 消</el-button>
                <el-button :loading="buttonLoading" type="primary" @click="submitForm('dialogForm')">发送</el-button>
            </div>
        </el-dialog>
    </div>
</template>
<style>
    .t-user-img-userlogo{
        width: 80px;
        height: 80px;
        vertical-align: middle;
    }
    .t-user-ul-userinfo{
        padding:0;
        margin: 0 0 0 15px;
    }
    .t-user-ul-userinfo li{
        margin: 20px 0;
    }
</style>

<script>
    export default {
        data() {
            return {
                loading: true,//加载动画
                buttonLoading: false,
                tableData: [],//表格数据
                //筛选条件
                filters: {
                    sex: '',
                    input: '',
                },
                //下拉列表框值
                options: [
                    {
                        value: '0',
                        label: '全部'
                    },
                    {
                        value: "1",
                        label: '男'
                    },
                    {
                        value: "2",
                        label: '女'
                    }
                ],
                value: '',


                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,

                //发送消息
                dialogFormVisible: false,
                dialogForm:{},
            }
        },
        methods: {
            //转换性别
            formatSex: function (row, index) {
                if (row.sex == 1){
                    return '男'
                }
                if (row.sex == 2){
                    return '女'
                }
            },
            //获取所有用户
            getActiveUsers: function () {
                this.loading = true
                axios.post('/api/bar/index/active/user?page=' + this.page, this.filters).then(response => {
                    this.pagesize = response.data.data.per_page;
                    this.total = response.data.data.total;
                    this.tableData = response.data.data.data;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //翻页事件
            paginate:function(val) {
                this.page = val;
                this.getActiveUsers();
            },
            openDialog: function (type, row) {
                this.dialogFormVisible = true;
                this.dialogForm = row;

            },
            //发送消息
            submitForm: function (formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.buttonLoading = true;
                        axios.post('/api/bar/message/store', self.dialogForm).then(response => {
                            self.$message.success(response.data.message);
                            self.resetForm(formName);
                        }).catch(function (error) {
                            self.buttonLoading = false;
                        });
                    } else {
                        return false;
                    }
                });
            },
            //重置form表单数据
            resetForm:function (formName) {
                this.buttonLoading = false;
                this.dialogFormVisible = false;
                this.$refs[formName].resetFields();
            },
        },
        //进入页面会自动执行该方法
        mounted: function () {
            this.getActiveUsers();
        }
    }
</script>