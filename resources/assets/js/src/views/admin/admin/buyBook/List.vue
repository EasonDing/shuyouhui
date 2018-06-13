<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <el-button type="success" @click="add()">添加新书</el-button>
                <el-button type="success" @click="awardList()">获奖名单</el-button>
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
                                @change="getList"
                                value-format="yyyy-MM-dd"
                                :picker-options="pickerOptions">
                        </el-date-picker>
                        <el-input @change="getList" clearable style="width: 260px;" v-model="keyword" placeholder="输入书名"></el-input>
                        <el-button type="primary">搜索</el-button>
                    </el-form>
                </div>
            </div>

            <div v-if="dataList == 0" style="text-align: center;"><span style="color: #5e7382;">暂无数据</span></div>
            <template v-for="book in dataList">
                <el-row :span="24">
                    <el-col :span="6" style="width: 500px;">
                        <div class="picture-box">
                            <img class="bookrack-img-bookimg" :src="book.image"/>
                            <ul class="bookrack-ul-bookinfo" type="none">
                                <li>书名：{{ book.title }}</li>
                                <li>作者：{{ book.author}}</li>
                                <li>简介：{{ book.introduction }}</li>
                                <li class="price">价格：{{ book.price ? book.price : 0}}元</li>
                            </ul>
                        </div>
                    </el-col>
                    <el-col :span="5" style="height: 100px; line-height: 100px;">
                        上传日期：{{ book.created_at }}
                    </el-col>
                    <el-col :span="3" style="float: right; height: 100px; line-height: 100px;">
                        <el-button type="primary" size="small" @click="edit(book)">编辑</el-button>
                        <el-button type="danger" size="small" @click="destroy(book)">删除</el-button>
                    </el-col>
                </el-row>
            </template>
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
                axios.get('/api/admin/buy/book/index', {
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
                    self.dataList = data;
                    self.loading = false;
                }).catch(function (error) {
                    self.loading = false;
                });
            },
            //添加图书
            add:function () {
                this.$router.push('/admin/buy/book/add');
            },
            //中奖名单
            awardList: function () {
                this.$router.push('/admin/buy/award/list');
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