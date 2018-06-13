<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="openCreateUserDialog()">创建账号</el-button>
                <div style="float: right;">
                    <el-form>
                        <el-date-picker
                                v-model="date"
                                type="daterange"
                                align="right"
                                unlink-panels
                                range-separator="-"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                @change="getUsers"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                        <el-input @change="getUsers" clearable style="width: 260px;" v-model="keyword" placeholder="输入书吧名称或手机号"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="users">
                <el-table-column
                        prop="group_name"
                        label="书吧名称"
                >
                </el-table-column>
                <el-table-column
                        prop="name"
                        label="吧主"
                >
                </el-table-column>
                <el-table-column
                        prop="username"
                        label="登录账号"
                >
                </el-table-column>
                <el-table-column
                        prop="phone"
                        label="手机号码"
                >
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="注册时间"
                        width="220">
                </el-table-column>

                <el-table-column label="操作" width="180">
                    <template slot-scope="scope">
                        <el-button type="danger" size="small" @click="destroyUser(scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>

        <!--创建吧主帐号-->
        <el-dialog
                title="创建吧主管理员"
                width="35%"
                :modal-append-to-body="false"
                :visible.sync="createUserDialogVisible">
            <el-form :model="createUserDialogForm" ref="createUserDialogForm" :labelWidth="createUserFormLabelWidth">
                <el-form-item
                        label="帐号名称"
                        prop="username"
                        :rules="[
                            { required: true, message: '帐号名称不能为空'},
                            { min: 3, max: 15, message: '长度在 3 到 15 个字符'}]">
                    <el-input v-model="createUserDialogForm.username" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item
                        label="帐户密码"
                        prop="password"
                        :rules="[
                            { required: true, message: '帐户密码不能为空'},
                            { min: 6, max: 16, message: '长度在 6 到 16 个字符'}]">
                    <el-input type="password" v-model="createUserDialogForm.password" auto-complete="off"></el-input>
                </el-form-item>
                <el-form-item
                        label="选择书吧"
                        prop="group_id"
                        :rules="[{ required: true, message: '请选择书吧'}]">
                    <el-select v-model="createUserDialogForm.group_id" placeholder="选择书吧">
                        <el-option
                                v-for="item in notAdminGroups"
                                :key="item.groupId"
                                :label="item.groupName"
                                :value="item.groupId">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item
                        labelWidth="0"
                        style="text-align: center;">
                    <el-button @click="createUserDialogVisible = false">取 消</el-button>
                    <el-button :loading="createUserButtonLoading" type="primary" @click="createUser('createUserDialogForm')">创建</el-button>
                </el-form-item>
            </el-form>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: true,
                //表格数据
                users: [],
                //创建帐号对话框是否显示
                createUserDialogVisible: false,
                createUserButtonLoading: false,
                createUserFormLabelWidth: '120px',
                //对应select框的显示内容
                createUserDialogForm: {
                    group_id: ''
                },
                notAdminGroups: [],//没有管理员账号的书吧列表

                //搜索关键字
                keyword: '',
                date: [],

                total: 0,
                page: 1,
                pagesize: 20,

                pickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
            }
        },
        methods: {
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getUsers();
            },
            //获取书吧管理员列表
            getUsers: function () {
                const self = this;
                self.loading = true;
                axios.get('/api/admin/admin/index', {
                    params: {
                        page: self.page,
                        search: self.keyword,
                        date: self.date,
                        orderBy: 'created_at',
                        sortedBy: 'desc',
                    }
                }).then(response => {
                    var data = response.data.data.data;
                    var pagination = response.data.data.meta.pagination;

                    self.pagesize = pagination.per_page;
                    self.total = pagination.total;
                    self.users = data;
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            //创建账号对话框
            openCreateUserDialog: function () {
                this.createUserDialogVisible = true;
                this.getGroups()
            },
            //创建吧主管理帐号
            createUser: function (createUserForm) {
                const self = this;
                self.$refs[createUserForm].validate((valid) => {
                    if (valid) {
                        self.createUserButtonLoading = true;
                        axios.post('/api/admin/admin/store', self.createUserDialogForm).then(response => {
                            self.$message.success(response.data.message);
                            self.resetCreateUserForm(createUserForm);
                            self.getUsers()
                        }).catch(function (error) {
                            self.createUserButtonLoading = false;
                        });
                    } else {
                        self.$message.error('信息未填写完整哦！');
                        return false;
                    }
                });
            },
            //删除书吧管理员账号
            destroyUser: function (row) {
                this.$confirm('删除此书吧管理员账号, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/admin/admin/destroy/' + row.id).then(response => {
                        this.$message.success(response.data.message);
                        this.getUsers();
                    });
                }).catch(() => {
                    this.$message.info('已取消删除');
                });
            },
            //关闭并重置表单数据
            resetCreateUserForm:function (createUserForm) {
                this.createUserButtonLoading = false;
                this.createUserDialogVisible = false;
                this.$refs[createUserForm].resetFields();
            },
            //无管理员账号的书吧
            getGroups: function () {
                axios.get('/api/admin/group/groups').then(response => {
                    this.notAdminGroups = response.data.data
                });
            },
        },
        mounted: function () {
            this.getUsers();
        }
    }
</script>