<template>
    <div class="animated fadeIn">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <div style="float: right;">
                    <el-form>
                        <el-select v-model="groupType" @change="getGroups" placeholder="书吧类型" style="width: 120px;">
                            <el-option
                                    v-for="item in options"
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
                                @change="getGroups"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                        <el-input @change="getGroups" clearable style="width: 260px;" v-model="keyword" placeholder="输入书吧名称"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>
            <el-table
                    v-loading="loading"
                    element-loading-text="拼命加载中"
                    stripe
                    :data="groups">
                <el-table-column
                        prop="type"
                        label="类型"
                        width="70">
                </el-table-column>
                <el-table-column
                        :show-overflow-tooltip="true"
                        prop="groupName"
                        label="书吧名字">
                </el-table-column>
                <el-table-column
                        prop="barAdmin.username"
                        label="吧主"
                >
                </el-table-column>
                <el-table-column
                        prop="users"
                        label="书吧用户量"
                >
                </el-table-column>
                <el-table-column
                        prop="books"
                        label="书籍数量"
                >
                </el-table-column>
                <el-table-column
                        prop="barAdmin.phone"
                        label="手机号码"
                >
                </el-table-column>
                <el-table-column
                        prop="updateTime"
                        label="注册时间"
                        width="220"
                >
                </el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                            <el-button type="danger" size="small" @click="destroyGroup(scope.row)">删除书吧</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
    </div>
</template>

<style>

</style>

<script>
    export default {
        data() {
            return {
                //加载动画
                loading: true,
                //书吧列表
                groups: [],
                groupType: '',

                //分页参数
                total: 0,
                page: 1,
                pagesize: 20,

                options: [
                    {
                        value: null,
                        label: '全部'
                    },
                    {
                        value: "1",
                        label: '校园'
                    },
                    {
                        value: "2",
                        label: '兴趣'
                    },
                    {
                        value: "3",
                        label: '城市'
                    }
                    ,{
                        value: "4",
                        label: '其它'
                    }
                ],

                //搜索关键字
                keyword: '',
                date: [],

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
                this.getGroups()
            },
            //书吧列表
            getGroups: function () {
                const self = this;
                self.loading = true;
                axios.get('/api/admin/group/index', {
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
                    self.groups = data;
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            //删除书吧
            destroyGroup: function (row) {
                const self = this;
                self.$confirm('删除此书吧, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/admin/group/destroy/' + row.id).then(response => {
                        self.$message.success(response.data.message);
                        self.getGroups();
                    });
                }).catch(() => {
                    self.$message.info('已取消删除');
                });
            },
        },
        mounted: function () {
            this.getGroups();
        }
    }
</script>