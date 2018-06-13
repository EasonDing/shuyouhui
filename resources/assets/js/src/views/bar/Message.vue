<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="openDialog(1, '推送全员消息')">推送全员消息</el-button>
                <div style="float: right;">
                    <el-form  :model="filters">
                        <el-select v-model="filters.type" @change="getData()" placeholder="消息类型" style="width: 120px;">
                            <el-option
                                    v-for="item in options"
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value">
                            </el-option>
                        </el-select>
                        <el-input @change="change()" style="width: 260px;" v-model="filters.input" placeholder="输入用户名称"></el-input>
                        <el-button type="primary" @click="getData()">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="tableData">
                <el-table-column
                        prop="news_type"
                        label="类型"
                        :formatter="formatType">
                </el-table-column>
                <el-table-column
                        prop="username"
                        :show-overflow-tooltip="true"
                        label="收信人">
                </el-table-column>
                <el-table-column
                        prop="user.bindPhone"
                        width="140"
                        label="手机号">
                </el-table-column>
                <el-table-column
                        width="400"
                        prop="content"
                        :show-overflow-tooltip="true"
                        label="消息内容"
                >
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="发送时间"
                        width="220"
                >
                </el-table-column>

                <el-table-column label="操作" width="180">
                    <template slot-scope="scope">
                        <div style="height: 80px; line-height: 80px">
                        <el-button type="danger" size="small" @click="del(scope.row)">删除</el-button>
                        </div>
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
                        prop="subtitle"
                        :rules="[{ required: true, message: '标题不能为空'}]">
                    <el-input v-model="dialogForm.subtitle" auto-complete="off" placeholder="标题" class="user-manage-textarea"></el-input>
                </el-form-item>
                <el-form-item
                        prop="content"
                        :rules="[{ required: true, message: '内容不能为空'}]">
                    <el-input v-model="dialogForm.content" type="textarea" :rows="10"  auto-complete="off" placeholder="在此输入消息内容" class="user-manage-textarea"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="resetForm('dialogForm')">取 消</el-button>
                <el-button type="primary" @click="submitForm('dialogForm')">发送</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        name: 'userManage',
        data() {
            return {
                //加载动画
                loading: true,
                //表格数据
                tableData: [],

                //下拉列表框值
                options: [
                    {
                        value: '0',
                        label: '全部'
                    },
                    {
                        value: '1',
                        label: '公共'
                    },
                    {
                        value: '2',
                        label: '私人'
                    },
                ],

                //筛选条件
                filters: {
                    type: '',
                    input: '',
                },
                //分页参数
                total: 0,
                page: 1,
                pagesize: 20,

                //发送消息
                dialogFormVisible: false,
                dialogForm:{},
            }
        },
        methods: {
            //返回价格单位为元
            formatType: function (row, index) {
                if (row.news_type == 1) {
                    return '公共'
                } else if (row.news_type == 2) {
                    return '私人'
                } else {
                    return '未知'
                }
            },
            //翻页事件
            paginate: function (val) {
                this.page = val;
                this.getData();
            },
            //获取历史消息
            getData: function () {
                this.loading = true
                axios.post('/api/t_news?page=' + this.page, this.filters).then(response => {
                    this.pagesize = response.data.data.per_page
                    this.total = response.data.data.total
                    this.tableData = response.data.data.data
                    this.loading = false
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //删除消息
            del: function (row) {
                this.$confirm('此操作将永久删除该文件, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/t_news/' + row.id + '/destroy').then(response => {
                        if (response.data.data) {
                            this.$notify.success({
                                title: '系统提示',
                                message: '消息删除成功',
                            });
                            this.getData()
                        } else {
                            this.$notify.error({
                                title: '系统提示！',
                                message: '消息删除失败'
                            })
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消删除'
                    });
                });
            },
            openDialog:function (type, row) {
                this.dialogFormVisible = true
                if (type == 3){
                    this.dialogForm = row
                }else {
                    this.dialogForm.type = type
                    this.dialogForm.username = row
                }
            },
            //发送消息
            submitForm:function (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post('/api/t_push' ,this.dialogForm).then(response => {
                            if (response.data.data.result == "ok"){
                                this.$notify.success({
                                    title: '系统提示',
                                    message: '消息发送成功',
                                });
                            }
                            this.dialogFormVisible = false
                        }).catch(function (error) {
                            console.log(error)
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            //重置表单
            resetForm:function (formName) {
                this.dialogFormVisible = false
                this.$refs[formName].resetFields();
            },
            //当搜索框值为空时重新调用列表数据
            change:function () {
                if (this.filters.input == ""){
                    this.getData();
                }
            },
        },
        mounted: function () {
            this.getData();
        }
    }
</script>