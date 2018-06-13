<template>
    <div class="animated fadeIn">
        <el-card class="box-card" v-loading="loading" element-loading-text="拼命加载中">
            <div slot="header" class="clearfix">
                <div style="float: right;">
                    <el-form :model="filters">
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
                        <el-input @change="change()" style="width: 260px;" v-model="filters.input" placeholder="输入书名"></el-input>
                        <el-button type="primary" @click="getBarBooks()">搜索</el-button>
                    </el-form>
                </div>
            </div>

            <template v-for="book in books">
                <el-row :span="24">
                    <el-col :span="6" style="width: 500px;">
                    <div class="picture-box">
                        <img class="t-bookrack-img-bookimg" :src="book.image"/>
                        <ul class="t-bookrack-ul-bookinfo" type="none">
                            <li>书名：{{ book.title }}</li>
                            <li>作者：{{ book.author}}</li>
                            <li>简介：{{ book.summary }}</li>
                            <li class="price">价格：{{ book.price ? book.price : 0}}元</li>
                        </ul>
                    </div>
                    </el-col>
                    <el-col :span="5" style="height: 100px; line-height: 100px;">
                        上传时间：{{ book.created_at }}
                    </el-col>
                    <el-col :span="3" style="float: right; height: 100px; line-height: 100px;">
                        <el-button type="info" size="small" @click="addStorage(book)">添加至书架</el-button>
                    </el-col>
                </el-row>
            </template>
            <div v-if="total == 0" style="text-align: center;">暂无数据</div>

            <!--添加入库-->
            <el-dialog
                    title="推荐语"
                    :modal-append-to-body="false"
                    size="small"
                    :visible.sync="dialogFormVisible">
                <el-form :model="dialogForm" ref="dialogForm" class="user-manage-from">
                    <el-form-item
                            prop="summary">
                        <el-input v-model="dialogForm.summary" type="textarea" :rows="10"  auto-complete="off" placeholder="写点对此书的评价吧!  不写则使用默认推荐语" class="user-manage-textarea"></el-input>
                    </el-form-item>
                </el-form>
                <div slot="footer" class="dialog-footer">
                    <el-button @click="resetForm()">取 消</el-button>
                    <el-button type="primary" @click="addBook('dialogForm')">保存</el-button>
                </div>
            </el-dialog>
            <!--分页-->
            <el-col :span="24" class="paginate" v-if="total > 0">
                <el-pagination background layout="total, prev, pager, next" :total="total" @current-change="paginate" :page-size="pagesize"></el-pagination>
            </el-col>
        </el-card>
    </div>
</template>

<style>
    .t-bookrack-img-bookimg{
        width: 80px;
        height: 100px;
        float:left;
    }
    .t-bookrack-ul-bookinfo{
        float: left;
        padding:0;
        margin:0 0 0 10px;

    }
    .t-bookrack-ul-bookinfo li{
        width: 300px;
        height: 20px;
        margin: 2px 0 0 5px;
        overflow : hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .t-bookrack-ul-bookinfo .price{
        color: red;
    }

</style>

<script>
    export default{
        data(){
            return {
                loading: true,
                books: [],//书本列表信息

                //分页数据
                total: 0,
                page: 1,
                pagesize: 20,

                //筛选条件
                filters: {
                    input: '',
                    value2: '',
                },

                //添加入库
                dialogFormVisible: false,
                dialogForm:{
                    bookId: '',//选择入库图书 的ID
                    summary: '',//推荐语
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
            //获取书吧 未添加的贝壳图书
            getBarBooks: function () {
                this.loading = true
                axios.post('/api/bar/book?page=' + this.page, this.filters).then(response => {
                    this.pagesize = response.data.data.per_page
                    this.total = response.data.data.total
                    this.books = response.data.data.data;
                    this.loading = false;
                }).catch(function (error) {
                    console.log(error);
                });
            },
            //时间区间
            getTime:function () {
                var startTime = new Date(this.filters.value2[0]).getTime()
                var endTime = new Date(this.filters.value2[1]).getTime()
                this.filters.startTime = startTime/1000
                this.filters.endTime = endTime/1000

                this.getBarBooks()
            },
            //翻页事件
            paginate:function(val) {
                this.page = val;
                this.getBarBooks();
            },
            //添加入库
            addStorage:function (row) {
                this.dialogForm = row;//图书ID
                this.dialogForm.summary = '';
                this.dialogFormVisible = true;

            },
            //dialog弹框提交
            addBook:function (formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        axios.post('/api/bar/book/store' , this.dialogForm).then(response => {
                            if (response.data.code === 200){
                                this.$notify.success({
                                    title: '系统通知',
                                    message: '已入书架'
                                });
                            }else if (response.data.code === 501){
                                this.$notify.success({
                                    title: '系统通知',
                                    message: '书架中已存在此书'
                                });
                            }else {
                                this.$notify.success({
                                    title: '系统通知',
                                    message: '导入书架失败'
                                });
                            }

                            this.dialogFormVisible = false;
                            this.getBarBooks();
                        }).catch(function (error) {
                            console.log(error);
                        });
                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            //关闭dialog弹框
            resetForm:function () {
                this.dialogFormVisible = false;
            },
            //当搜索框值为空时重新调用列表数据
            change: function () {
                if (this.filters.input == ""){
                    this.getBarBooks();
                }
            },
        },
        mounted: function () {
            this.getBarBooks()
        },
    }
</script>