<template>
    <el-aside width="220px">
        <el-menu
                router
                :unique-opened="true"
                default-active="2"
                class="el-menu-vertical-demo">
            <template v-for="item in menu">
                <!--判断是否有子菜单 进行循环-->
                <el-submenu v-if="item.submodule" :index="item.text">
                    <template slot="title">
                        <i :class="item.icon"></i>
                        <span>{{ item.text }}</span>
                    </template>
                    <el-menu-item v-for="menu_item in item.children" :key="menu_item.path" :index="menu_item.path">
                        {{ menu_item.text }}
                    </el-menu-item>
                </el-submenu>

                <el-menu-item v-else :index="item.path">
                    <i :class="item.icon"></i>
                    <span slot="title">{{ item.text }}</span>
                </el-menu-item>
            </template>
        </el-menu>
    </el-aside>
</template>

<script>
    export default {
        data() {
            return {
                menu: [],
                loading: {
                    "text": "菜单加载中...",
                    "target": ".sidebar-nav"
                }
            }
        },
        methods: {
            getMenu: function () {
//                let loadingInstance = this.$loading(this.loading)
                axios.get('/api/system_menu').then(response => {
                    this.menu = response.data.data;
//                    loadingInstance.close()
                }).catch(function (error) {
                    console.log(error);
                });
            },
        },
        mounted: function () {
            this.getMenu()
        }
    }
</script>
