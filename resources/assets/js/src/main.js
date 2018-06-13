// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import router from './router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App'
import store from './store'  // Vuex 配置

import VueQuillEditor from 'vue-quill-editor'

window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

Vue.use(ElementUI);
// mount with global
Vue.use(VueQuillEditor);

/* eslint-disable no-new */
//实例化VUE
new Vue({
    el: '#app',
    router,
    store,
    render: h => h(App),
});

//全局接收错误
axios.interceptors.response.use(response => response, error => {
    if (error.response.status === 401) {
        Vue.prototype.$message.error('登录过期, 请重新登录系统');
        window.location.href = "\\";
    }
    if (error.response.status > 401 && error.response.status <= 500) {

        if (error.response.status === 403){
            Vue.prototype.$message.error('(×_×) 啊哦~ 你没有此权限哦~~');
            router.back(-1);
        } else if (typeof error.response.data.message == 'string') {
            Vue.prototype.$message.error(error.response.data.message);
        } else {
            Vue.prototype.$message.error('(×_×) 啊哦~ 服务器错误,有人要被扣奖金了~~');
        }
    }
    const dispatch = new Error('Error');
    dispatch.response = error.response;
    throw dispatch;
});
