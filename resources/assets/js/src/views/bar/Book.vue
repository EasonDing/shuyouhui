<template>
    <div class="animated fadeIn">
        <el-tabs v-model="activeName" @tab-click="handleClick" v-loading="loading" element-loading-text="拼命加载中">
            <el-tab-pane label="贝壳图书" name="first">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <el-button type="success" @click="selectBook()">添加新书</el-button>
                        <div style="float: right;">
                            <el-form  :model="filters">
                                <el-date-picker
                                        v-model="filters.value2"
                                        type="daterange"
                                        align="right"
                                        unlink-panels
                                        range-separator="-"
                                        start-placeholder="开始日期"
                                        end-placeholder="结束日期"
                                        @change="getTimeData"
                                        :picker-options="pickerOptions2">
                                </el-date-picker>
                                <el-input @change="change(1)" style="width: 260px;" v-model="filters.input" placeholder="输入书名"></el-input>
                                <el-button type="primary" @click="getBarBooks()">搜索</el-button>
                            </el-form>
                        </div>
                    </div>

                    <div v-if="books1 == 0" style="text-align: center;"><span style="color: #5e7382;">暂无数据</span></div>
                    <template v-for="book in books1">
                        <el-row :span="24">
                            <el-col :span="6" style="width: 500px;">
                                <div class="picture-box">
                                    <img class="bookrack-img-bookimg" :src="book.image"/>
                                    <ul class="bookrack-ul-bookinfo" type="none">
                                        <li>书名：{{ book.title }}</li>
                                        <li>作者：{{ book.author}}</li>
                                        <li>简介：{{ book.summary }}</li>
                                        <li class="price">价格：{{ book.price ? book.price : 0}}元</li>
                                    </ul>
                                </div>
                            </el-col>
                            <el-col :span="5" style="height: 100px; line-height: 100px;">
                                上传日期：{{ book.created_at }}
                            </el-col>
                            <el-col :span="3" style="float: right; height: 100px; line-height: 100px;">
                                <el-button type="info" size="small" @click="editSelect(book)">编辑</el-button>
                                <el-button type="danger" size="small" @click="destroyBook(book)">移除</el-button>
                            </el-col>
                        </el-row>
                    </template>
                    <!--分页-->
                    <el-col :span="24" class="paginate" v-if="total > 0">
                        <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
                    </el-col>
                </el-card>
            </el-tab-pane>

            <!--上传图书-->
            <el-tab-pane label="上传图书" name="second">
                <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
                    <div slot="header" class="clearfix">
                        <!--<el-button type="success" @click="add()">添加新书</el-button>-->
                        <div style="float: right;">
                            <el-form  :model="filters">
                                <el-date-picker
                                        v-model="filters.value2"
                                        type="daterange"
                                        align="right"
                                        unlink-panels
                                        range-separator="-"
                                        start-placeholder="开始日期"
                                        end-placeholder="结束日期"
                                        @change="getTimeBook"
                                        :picker-options="pickerOptions2">
                                </el-date-picker>
                                <el-input @change="change(2)" style="width: 260px;" v-model="filters.input" placeholder="输入书名"></el-input>
                                <el-button type="primary" @click="getBook()">搜索</el-button>
                            </el-form>
                        </div>
                    </div>

                    <div v-if="books2 == 0" style="text-align: center;"><span style="color: #5e7382;">暂无数据</span></div>
                    <template v-for="book in books2">
                        <el-row :span="24">
                            <el-col :span="6" style="width: 500px;">
                                <div class="picture-box">
                                    <img class="bookrack-img-bookimg" :src=" book.book ? book.book.image : '/images/DefaultLogo.jpg'"/>
                                    <ul class="bookrack-ul-bookinfo" type="none">
                                        <li>书名：{{ book.book ? book.book.title : '' }}</li>
                                        <li>作者：{{ book.book ? book.book.author : ''}}</li>
                                        <li>简介：{{ book.book ? book.book.summary : ''}}</li>
                                        <li class="price">价格：{{ book.book ? book.book.price : 0}}元</li>
                                    </ul>
                                </div>
                            </el-col>
                            <el-col :span="5" style="height: 100px; line-height: 100px;">
                                上传日期：{{ book.updateTime }}
                            </el-col>
                            <el-col :span="3" style="float: right; height: 100px; line-height: 100px;">
                                <!--<el-button type="info" size="small" @click="edit(book)">编辑</el-button>-->
                                <!--<el-button type="danger" size="small" @click="del(book)">删除</el-button>-->
                            </el-col>
                        </el-row>
                    </template>
                    <!--分页-->
                    <el-col :span="24" class="paginate" v-if="total > 0">
                        <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate2" :page-size="pagesize"></el-pagination>
                    </el-col>
                </el-card>
            </el-tab-pane>
        </el-tabs>

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
                books1: [],//书本列表信息
                books2: [],//书本列表信息

                activeName: 'first',//控制Tabs默认显示页

                //分页数据
                total: 0,
                page: 1,
                pagesize: 20,

                //筛选条件
                filters: {
                    value: '',
                    input: '',
                    value2: '',
                },

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
            //贝壳图书列表
            getBarBooks: function () {
                this.loading = true
                axios.post('/api/bar/book/index?page=' + this.page, this.filters).then(response => {
                    this.pagesize = response.data.data.per_page
                    this.total = response.data.data.total
                    this.books1 = response.data.data.data
                    this.loading = false
                }).catch(function (error) {
                    console.log(error)
                });
            },
            //编辑贝壳图书 吧主只能修改推荐语
            editSelect:function (row) {
                this.$router.push('/bar/book/select/'+ row.id +'/edit/');
            },
            //上传图书列表
            getBook:function () {
                this.loading = true
                axios.post('/api/t_book?page=' + this.page, this.filters).then(response => {
                    this.pagesize = response.data.data.per_page
                    this.total = response.data.data.total
                    this.books2 = response.data.data.data
                    this.loading = false
                }).catch(function (error) {
                    console.log(error)
                });
            },
            //时间区间 贝壳图书
            getTimeData:function () {
                var startTime = new Date(this.filters.value2[0]).getTime()
                var endTime = new Date(this.filters.value2[1]).getTime()
                this.filters.startTime = startTime/1000
                this.filters.endTime = endTime/1000

                this.getBarBooks();
            },
            //时间区间 上传图书
            getTimeBook:function () {
                var startTime = new Date(this.filters.value2[0]).getTime();
                var endTime = new Date(this.filters.value2[1]).getTime();
                this.filters.startTime = startTime/1000;
                this.filters.endTime = endTime/1000;

                this.getBook();
            },
            //翻页事件
            paginate:function(val) {
                this.page = val;
                this.getBarBooks()
            },
            paginate2:function(val) {
                this.page = val;
                this.getBook();
            },
            //吧主上传图书
            add:function () {
                this.$router.push('/bar/book/add');
            },
            //对上传的图书进行编辑
            edit:function (row) {
                this.$router.push('/bar/book/'+ row.codeid +'/edit/');
            },
            //删除 上传图书
            del: function (row) {
                this.$confirm('此操作将删除该图书, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/t_book/'+row.codeid+'/destroy').then(response => {
                        if(response.data.code === 500){
                            this.$notify.success({
                                title: '系统提示',
                                message: '删除成功',
                            });
                            this.getBook()
                        }else if (response.data.code === 501){
                            this.$notify.success({
                                title: '系统提示',
                                message: '该书已产生流转行为，不可删除',
                            });
                        } else{
                            this.$notify.error({
                                title: '系统提示！',
                                message: '删除失败'
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
            selectBook:function () {
                this.$router.push('/bar/book/select');
            },
            //对贝壳图书选择的书进行编辑
            editShell:function (row) {
                this.$router.push('/bar/book/'+ row.id +'/select');
            },
            //将从贝壳图书选取的书移除
            destroyBook:function (row) {
                this.$confirm('从书架中移除, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    axios.post('/api/bar/book/destroy', row).then(response => {
                        if (response.data.code == 200) {
                            this.$notify.success({
                                title: '系统提示',
                                message: '移除成功',
                            });
                            this.getBarBooks()
                        } else {
                            this.$notify.error({
                                title: '系统提示',
                                message: '移除失败'
                            })
                        }
                    }).catch(function (error) {
                        console.log(error)
                    });
                }).catch(() => {
                    this.$message({
                        type: 'info',
                        message: '已取消移除'
                    });

                });
            },
            //Tabs标签切换回调
            handleClick(tab, event){
                if (tab.name === "first"){
                    this.getBarBooks();
                }else {
                    this.getBook();
                }
            },
            //当搜索框值为空时重新调用列表数据
            change:function ($tab) {
                if (this.filters.input == ""){
                    //判断是贝壳图书页面还是上传图书页面，进行刷新数据
                    if ($tab === 1){
                        this.getBarBooks();
                    }else {
                        this.getBook();
                    }
                }
            },
        },
        mounted: function () {
//            if (this.$route.params.status == "first"){
                this.getBarBooks();
//            }else {
//                this.activeName = "second";
//                this.getBook();
//            }
        },
    }
</script>