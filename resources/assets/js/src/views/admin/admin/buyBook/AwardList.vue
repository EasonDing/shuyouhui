<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <div style="float: right;">
                    <el-form>
                        <!--<el-date-picker-->
                                <!--v-model="date"-->
                                <!--type="daterange"-->
                                <!--align="right"-->
                                <!--unlink-panels-->
                                <!--range-separator="-"-->
                                <!--start-placeholder="开始日期"-->
                                <!--end-placeholder="结束日期"-->
                                <!--@change="getList"-->
                                <!--value-format="yyyy-MM-dd"-->
                                <!--:picker-options="pickerOptions">-->
                        <!--</el-date-picker>-->
                        <!--<el-input @change="getList" clearable style="width: 260px;" v-model="keyword" placeholder="输入书名"></el-input>-->
                        <!--<el-button type="primary">搜索</el-button>-->
                    </el-form>
                </div>
            </div>

            <div v-if="dataList.length < 1" style="text-align: center;"><span style="color: #5e7382;">暂无数据</span></div>
            <el-table
                    v-else
                    v-loading="loading"
                    element-loading-text="拼命加载中..."
                    stripe
                    :data="dataList">
                <el-table-column
                        prop="number"
                        label="订单号"
                        width="180">
                </el-table-column>
                <el-table-column
                        width="350"
                        label="书名">
                    <template slot-scope="scope">
                        <div v-if="scope.row.buy_book">
                            <img :src="'/storage/' + scope.row.buy_book.image" style="width: 80px;height: 100px; float:left; padding: 7px;"/>
                            <ul class="order-ul-goodinfo" type="none">
                                <li>{{ scope.row.buy_book.title }}</li>
                                <li>{{ scope.row.buy_book.author }}</li>
                                <li>{{ scope.row.buy_book.introduction }}</li>
                            </ul>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column
                        prop="buy_user.username"
                        label="用户名"
                >
                </el-table-column>
                <el-table-column
                        prop="phone"
                        label="手机号"
                        width="120">
                </el-table-column>
                <el-table-column
                        prop="address"
                        label="详细地址"
                        width="330"
                >
                </el-table-column>
                <el-table-column
                        prop="created_at"
                        label="订单日期"
                        width="220">
                </el-table-column>

                <!--<el-table-column label="操作" width="120">-->
                    <!--<template slot-scope="scope">-->
                        <!--<el-button type="danger" size="small" @click="destroyOrder(scope.row)">删除</el-button>-->
                    <!--</template>-->
                <!--</el-table-column>-->
            </el-table>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
    </div>
</template>

<style>
    .bookrack-img-bookimg{
        width: 80px;
        height: 100px;
        float:left;
    }
    .bookrack-ul-bookinfo{
        float: left;
        padding:0;
        margin:0 0 0 10px;

    }
    .bookrack-ul-bookinfo li{
        width: 300px;
        height: 20px;
        margin: 2px 0 0 5px;
        overflow : hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .bookrack-ul-bookinfo .price{
        color: red;
    }

</style>

<script>
    export default{
        data(){
            return {
                loading: true,
                dataList: [],//书本列表信息

                //分页数据
                total: 0,
                page: 1,
                pagesize: 20,

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
            paginate:function(val) {
                this.page = val;
                this.getList();
            },
            //获取书本列表
            getList: function () {
                const self = this;
                self.loading = true
                axios.get('/api/admin/buy/order/index', {
                    params: {
                        page: self.page,
                    }
                }).then(response => {
                    var data = response.data.data;

                    self.pagesize = data.per_page;
                    self.total = data.total;
                    self.dataList = data.data;
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            //添加图书
            add:function () {
                this.$router.push('/admin/buy/book/add');
            },
            //编辑图书
            edit:function (row) {
                this.$router.push('/admin/buy/book/' + row.id + '/edit');
            },
            //删除图书
            destroy: function (row) {
                const self = this;
                self.$confirm('删除此书, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.get('/api/admin/buy/book/destroy/' + row.id ).then(response => {
                        var data = response.data;
                        self.$message.success(data.message);
                        self.getList()
                    });
                }).catch(() => {
                    self.$message.info('已取消删除');
                });
            },
        },
        mounted: function () {
            this.getList()
        },
    }
</script>