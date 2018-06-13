<template>
    <el-container>
        <!--顶部-->
        <el-header class="full-header">
            <Header></Header>
        </el-header>
        <el-container>
            <!--左侧菜单-->
            <Sidebar></Sidebar>
            <el-container>
                <el-header class="full-header2">
                    <Breadcrumb></Breadcrumb>
                </el-header>
                <!--主页面-->
                <el-main class="full-main">
                    <router-view></router-view>
                </el-main>
                <!--底部-->
                <Footer></Footer>
            </el-container>
        </el-container>
    </el-container>
</template>

<style>
    .full-header2 {
        background: #ffffff;
        height: 35px !important;
        display: flex;
        justify-content: center;
        flex-direction: column;
    }

    .full-main {
        background: #ffffff;
    }
</style>

<script>
    import Sidebar from '../components/Sidebar'
    import Footer from '../components/Footer.vue'
    import Header from '../components/Header.vue'
    import Breadcrumb from '../components/Breadcrumb.vue'

    export default {
        components: {
            Sidebar,
            Footer,
            Header,
            Breadcrumb,
        },
        computed: {
            name() {
                return this.$route.name
            },

            list() {
                return this.$route.matched
            }
        },
    }
</script>
