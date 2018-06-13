<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <el-button type="primary" @click="openMessageDialog('allUser', '全体成员')">推送全员消息</el-button>
                <el-button type="primary" @click="openMessageDialog('allBar', '全体吧主')">推送吧主消息</el-button>
                <div style="float: right;">
                    <el-form>
                        <el-select v-model="type" @change="getUsers" placeholder="用户身份" style="width: 115px;">
                            <el-option
                                    v-for="item in options"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
                        <el-select v-model="sex" @change="getUsers" placeholder="性别" style="width: 100px;">
                            <el-option
                                    v-for="item in options2"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
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
                        <el-input clearable @change="getUsers" style="width: 200px;" v-model="keyword" placeholder="输入用户昵称或手机号"></el-input>
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
                        label="头像">
                    <template slot-scope="scope">
                        <el-popover trigger="hover" placement="right-end" width="315">
                            <el-row>
                                <el-col :span="12">
                                    <el-card>
                                        <img style="width: 100px; height: 100px;" :src="scope.row.UserLogo" :alt="null" class="image">
                                    </el-card>
                                </el-col>
                                <el-col :span="12">
                                    <div style="padding: 14px;">
                                        <span>{{ scope.row.signature }}</span>
                                        <div class="bottom clearfix">
                                            <time class="time">{{ scope.row.city }}</time>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>

                            <div slot="reference" class="avatar">
                                <img :src="scope.row.UserLogo" :alt="scope.row.UserLogo" class="img-avatar"
                                     style="margin: 10px 0; width: 36px; height: 36px;">
                                <span v-if="scope.row.subscribe" class="avatar-status badge-success"></span>
                                <!--绿色小点-->
                                <span v-else class="avatar-status badge-default" style="margin: 10px 0;"></span>
                            </div>
                        </el-popover>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="username"
                        :show-overflow-tooltip="true"
                        label="昵称">
                </el-table-column>
                <el-table-column
                        prop="identity"
                        label="身份"
                >
                </el-table-column>
                <el-table-column
                        prop="sex"
                        label="性别"
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
                        width="180"
                >
                </el-table-column>
                <el-table-column
                        width="220"
                        prop="updateTime"
                        label="注册时间"
                >
                </el-table-column>

                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button type="primary" size="small" @click="openMessageDialog('user', scope.row)">发消息</el-button>
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
                :visible.sync="messageDialogVisible">
            <el-form :model="messageForm" ref="messageForm" class="user-manage-from">
                <el-form-item
                        prop="username">
                    <el-input v-model="messageForm.username" auto-complete="off" placeholder="接收者" disabled></el-input>
                </el-form-item>
                <el-form-item
                        prop="title"
                        :rules="[{ required: true, message: '标题不能为空'}]">
                    <el-input v-model="messageForm.title" auto-complete="off" placeholder="标题"></el-input>
                </el-form-item>
                <el-form-item
                        prop="content"
                        :rules="[{ required: true, message: '内容不能为空'}]">
                    <el-input v-model="messageForm.content" type="textarea" :rows="10"  auto-complete="off" placeholder="在此输入消息内容"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="resetMessageForm('messageForm')">取 消</el-button>
                <el-button type="primary" :loading="buttonLoading" @click="sendMessage('messageForm')">发送</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<style>

</style>

<script>
    export default {
        data() {
            return {
                loading: true,//加载动画
                users: [],//用户列表数据

                //搜索关键字
                keyword: '',
                date: [],
                //用户类型
                type: '',
                sex: '',

                //分页相关属性
                total: 0,
                page: 1,
                pagesize: 20,

                //发送消息
                messageDialogVisible: false,
                messageForm:{},
                messageUrl: '',
                buttonLoading: false,

                //用户身份
                options: [
                    {
                        value: "0",
                        label: '全部'
                    },
                    {
                        value: "1",
                        label: '吧主'
                    },
                    {
                        value: "2",
                        label: '普通用户'
                    }
                ],
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
            //获取所有用户
            getUsers: function () {
                const self = this;
                self.loading = true
                axios.get('/api/admin/user/index',{
                    params: {
                        page: self.page,
                        search: self.keyword,
                        date: self.date,
                        orderBy: 'updateTime',
                        sortedBy: 'desc',
                    }
                }).then(response => {
                    var data = response.data.data.data;
                    var pagination = response.data.data.meta.pagination;

                    self.pagesize = pagination.per_page;
                    self.total = pagination.total;
                    self.users = data;
                    self.loading = false
                });
            },
            //翻页事件
            paginate:function(val) {
                this.page = val;
                this.getUsers();
            },
            //推送消息 allUser:全员消息 allBar:吧主消息 user:私人
            openMessageDialog:function (messageType, messageData) {
                this.messageDialogVisible = true;
                if (messageType === 'user'){
                    this.messageForm = messageData;
                    this.messageUrl = '/api/admin/message/store';
                }else if (messageType === 'allUser'){
                    //需要先清空、防止发送个人消息后数据还存在
                    this.messageForm.username = messageData;
                    this.messageUrl = '/api/admin/message/all/user';
                }else if (messageType === 'allBar'){
                    this.messageForm.username = messageData;
                    this.messageUrl = '/api/admin/message/all/bar';

                }
            },
            //发送消息
            sendMessage:function (formName) {
                const self = this;
                self.$refs[formName].validate((valid) => {
                    if (valid) {
                        self.buttonLoading = true;
                        axios.post(self.messageUrl ,self.messageForm).then(response => {
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
                            self.messageDialogVisible = false;
                            self.buttonLoading = false;
                        });
                    } else {
                        self.$message.error('信息未填写完整哦!');
                        return false;
                    }
                });
            },
            //重置form表单
            resetMessageForm:function (formName) {
                this.messageDialogVisible = false;
                this.$refs[formName].resetFields();
            },
        },
        //进入页面会自动执行该方法
        mounted: function () {
            this.getUsers();
        }
    }
</script>