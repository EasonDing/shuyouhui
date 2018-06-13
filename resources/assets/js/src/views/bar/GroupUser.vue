<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="openMessageDialog('allUser', '书吧全体全员')">推送全员消息</el-button>
                <div style="float: right;">
                    <el-form :model="filters">
                        <el-select v-model="filters.sex" @change="getUsers()" placeholder="性别" style="width: 120px;">
                            <el-option
                                    v-for="item in options2"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
                        <el-date-picker
                                v-model="filters.value2"
                                type="daterange"
                                align="right"
                                unlink-panels
                                range-separator="-"
                                start-placeholder="开始日期"
                                end-placeholder="结束日期"
                                @change="getTime"
                                :picker-options="pickerOptions2">
                        </el-date-picker>
                        <el-input @change="change()" style="width: 260px;" v-model="filters.input" placeholder="输入用户昵称或者手机号"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <!--stripe 斑马纹-->
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中"
                    stripe
                    :data="users">
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
                        :formatter="formatBirthday"
                >
                </el-table-column>
                <el-table-column
                        prop="bindPhone"
                        label="手机号码"
                >
                </el-table-column>
                <el-table-column
                        width="220"
                        prop="pivot.updateTime"
                        label="加入时间"
                >
                </el-table-column>

                <el-table-column label="操作" width="220">
                    <template slot-scope="scope">
                        <!--吧主禁用按钮-->
                        <el-button :disabled="scope.row.pivot.type ? true : false" type="danger" size="small" @click="destroyUser(scope.row)">删除</el-button>
                        <el-button :disabled="scope.row.pivot.type ? true : false" type="info" size="small" @click="openMessageDialog('user', scope.row)">发消息</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
        <!--发送消息弹出框-->
        <el-dialog
                title="发送消息"
                :modal-append-to-body="false"
                size="small"
                :visible.sync="messageDialogVisible">
            <el-form :model="messageForm" ref="messageForm" class="user-manage-from">
                <el-form-item
                        prop="username"
                        :rules="[{ required: true, message: '用户名称不能为空'}]">
                    <el-input v-model="messageForm.username" auto-complete="off" placeholder="接收者" disabled></el-input>
                </el-form-item>
                <el-form-item
                        prop="title"
                        :rules="[{ required: true, message: '标题不能为空'}]">
                    <el-input v-model="messageForm.title" auto-complete="off" placeholder="标题" class="user-manage-textarea"></el-input>
                </el-form-item>
                <el-form-item
                        prop="content"
                        :rules="[{ required: true, message: '内容不能为空'}]">
                    <el-input v-model="messageForm.content" type="textarea" :rows="10"  auto-complete="off" placeholder="在此输入消息内容" class="user-manage-textarea"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="messageDialogVisible=false">取 消</el-button>
                <el-button type="primary" :loading="buttonLoading" @click="sendMessage('messageForm')">发送</el-button>
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
                users: [],//用户列表数据
                //筛选条件 select框 必须 要有个默认值
                filters: {
                    input: '',
                    value2: '',
                    sex: '',
                },
                
                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,

                //发送消息 相关属性
                messageDialogVisible: false,
                messageForm:{},
                messageUrl: '',
                buttonLoading: false,

                //按钮禁用状态
                destroyButtonIsDisabled: false,
                sendMessageButtonIsDisabled: false,

                //用户性别
                options2: [
                    {
                        value: null,
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

                pickerOptions2: {
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
            paginate:function(val) {
                this.page = val;
                this.getUsers()
            },
            //获取书吧用户列表
            getUsers: function () {
                const self = this;
                self.loading = true;
                axios.post('/api/bar/group/users?page=' + self.page, self.filters).then(response => {
                    if (response.data.code === 200) {
                        var data = response.data.data.data;
                        self.pagesize = data.users.per_page;
                        self.total = data.users.total;
                        self.users = data.users.data;
                    } else {
                        self.$message.error('哈哈！有人要被扣奖金啦！');
                    }
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            //删除用户
            destroyUser: function (row) {
                this.$confirm('删除此用户, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/bar/group/destroy/' + row.userid + '/user').then(response => {
                        if (response.data.code === 200) {
                            this.$message.error('用户删除成功！');
                            this.getUsers()
                        } else {
                            this.$message.error('哈哈！有人要被扣奖金啦！');
                        }
                    }).catch(function (error) {
                        this.$message.error('服务器错误！');
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            //转换性别
            formatSex: function (row, index) {
                if (row.sex == 1){
                    return '男'
                }
                if (row.sex == 2){
                    return '女'
                }
            },
            //时间区间
            getTime:function () {
                var startTime = new Date(this.filters.value2[0]).getTime()
                var endTime = new Date(this.filters.value2[1]).getTime()
                this.filters.startTime = startTime/1000
                this.filters.endTime = endTime/1000

                this.getUsers()
            },
            //计算年龄 当前年份-生日年份
            formatBirthday:function (row, index) {
                if (row.Birthday.length > 0){
                    var myDate = new Date()
                    var now = myDate.getFullYear();
                    var old = row.Birthday.substring(0,4)
                    return now - old
                }
            },
            //打开发送消息对话框
            openMessageDialog:function (messageType, messageData) {
                this.messageDialogVisible = true;
                //user 为个人消息 allUser 为书吧全体成员
                if (messageType == 'user'){
                    this.messageForm = messageData;
                    this.messageUrl = '/api/bar/message/store';
                }else {
                    this.messageForm.username = messageData;
                    this.messageUrl = '/api/bar/message/all/user';
                }
            },
            //发送私人消息或全局消息
            sendMessage: function (formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.buttonLoading = true;
                        axios.post(self.messageUrl, self.messageForm).then(response => {
                            var data = response.data;
                            if (data.code === 200){
                                self.$message.success(data.message);
                                self.resetMessageForm(formName);
                            }else {
                                self.$message.error(data.message);
                            }

                            self.messageDialogVisible = false;
                            self.buttonLoading = false;
                        }).catch(function (error) {
                            self.buttonLoading = false;
                        });
                    } else {
                        self.$message.error('信息未填写完整哦!');
                        return false;
                    }
                });
            },
            //重围表单
            resetMessageForm:function (formName) {
                this.messageDialogVisible = false
                this.$refs[formName].resetFields();
            },
            //当搜索框值为空时重新调用列表数据
            change:function () {
                if (this.filters.input == ""){
                    this.getUsers();
                }
            },
        },
        //进入页面会自动执行该方法
        mounted: function () {
            this.getUsers();
        }
    }
</script>