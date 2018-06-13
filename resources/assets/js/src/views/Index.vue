<template>
    <div class="animated fadeIn"></div>
</template>

<script>
    export default {
        data() {
            return {}
        },
        methods: {},
        mounted: function () {
            //判断用户角色、管理员或者吧主
            axios.get('/api/role').then(response => {
                var data = response.data.data;
                if (data.name == 'admin') {
                    this.$router.push('/admin/index');
                } else if (data.name == 'bar') {
                    this.$router.push('/bar/index');
                } else {
                    this.$message.error('该账号存在异常！请联系管理员');
                }
            });
        }
    }
</script>
