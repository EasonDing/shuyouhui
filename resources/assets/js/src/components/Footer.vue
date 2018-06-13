<template>
    <el-footer class="full-footer">
        <el-row>
            <el-col :span="12"><span>贝壳书友会 2017-09-06</span></el-col>
            <el-col class="copyright" :span="12"> 贝壳书友会 @版权所有</el-col>
        </el-row>
    </el-footer>
</template>

<style>
    .full-footer {
        background: #eceff1;
        height: 45px !important;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }

    .full-footer .copyright {
        text-align: right;
    }
</style>