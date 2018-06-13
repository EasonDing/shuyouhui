import Vue from 'vue'
import Router from 'vue-router'

// Containers
import Full from '../containers/Full.vue'

// Views
import Index from '../views/Index.vue'
import Alerts from '../views/Alerts.vue'
// admin
import AdminIndex from '../views/admin/Index.vue'
import AdminFinance from '../views/admin/Finance.vue'
import AdminBook from '../views/admin/Book.vue'
import AdminBookAdd from '../views/admin/BookAdd.vue'
import AdminBookEdit from '../views/admin/BookEdit.vue'
import BuyBookList from '../views/admin/admin/buyBook/List.vue'
import BuyBookAdd from '../views/admin/admin/buyBook/Add.vue'
import BuyBookEdit from '../views/admin/admin/buyBook/Edit.vue'
import BuyAwardList from '../views/admin/admin/buyBook/AwardList.vue'
import AdminMessage from '../views/admin/Message.vue'
import AdminOrder from '../views/admin/Order.vue'
import AdminUser from '../views/admin/User.vue'
import AdminGroup from  '../views/admin/Group.vue'
import AdminGroupAdmin from  '../views/admin/GroupAdmin.vue'
import AdminIndexNewUser from  '../views/admin/IndexNewUser.vue'
import AdminIndexNewGroup from  '../views/admin/IndexNewGroup.vue'
import AdminIndexRechargeMoney from  '../views/admin/IndexRechargeMoney.vue'
import AdminIndexTradingMoney from  '../views/admin/IndexTradingMoney.vue'
//bar
import BarIndex from '../views/bar/Index.vue'
import BarBook from '../views/bar/Book.vue'
import BarBookAdd from '../views/bar/BookAdd.vue'
import BarBookEdit from '../views/bar/BookEdit.vue'
import BarBookSelectAdd from '../views/bar/BookSelectAdd.vue'
import BarBookSelectEdit from '../views/bar/BookSelectEdit.vue'
import BarMessage from '../views/bar/Message.vue'
import BarOrder from '../views/bar/Order.vue'
import BarGroupUser from '../views/bar/GroupUser.vue'
import BarFinanceMoneyOut from '../views/bar/FinanceMoneyOut.vue'
import BarGroup from '../views/bar/Group.vue'
import BarBlog from '../views/bar/Blog.vue'
import BarBlogComment from '../views/bar/BlogComment.vue'
import BarBlogAdd from '../views/bar/BlogAdd.vue'
import BarBlogEdit from '../views/bar/BlogEdit.vue'
import BarColumn from '../views/bar/Column.vue'
import BarColumnAdd from  '../views/bar/ColumnAdd.vue'
import BarColumnEdit from  '../views/bar/ColumnEdit.vue'
import BarBanner from  '../views/bar/Banner.vue'
import BarBannerAdd from  '../views/bar/BannerAdd.vue'
import BarBannerEdit from  '../views/bar/BannerEdit.vue'
import BarIndexNewUser from  '../views/bar/IndexNewUser.vue'
import BarIndexActiveUser from  '../views/bar/IndexActiveUser.vue'
import BarIndexNewOrder from  '../views/bar/IndexNewOrder.vue'
import BarIndexRevenue from  '../views/bar/FinanceRevenue.vue'
import BarIndexExpenditure from  '../views/bar/FinanceExpenditure.vue'
import BarFinance from '../views/bar/Finance.vue'
import BarFinanceBindWechat from  '../views/bar/FinanceBindWechat.vue'

Vue.use(Router);

export default new Router({
    mode: 'hash',
    linkActiveClass: 'open active',
    scrollBehavior: () => ({y: 0}),
    routes: [
        {
            path: '/',
            name: '主页',
            redirect: '/index',
            component: Full,
            children: [
                {
                    path: 'index',
                    component: Index,
                },
                //管理员
                {
                    path: 'admin/index',
                    name: '主页',
                    component: AdminIndex,
                },
                {
                    path: 'admin/new/user',
                    name: '今日新增用户',
                    component: AdminIndexNewUser,
                },
                {
                    path: 'admin/new/group',
                    name: '今日新增书吧',
                    component: AdminIndexNewGroup,
                },
                {
                    path: 'admin/group',
                    name: '所有书吧',
                    component: AdminGroup,
                },
                {
                    path: 'admin/group/admin',
                    name: '书吧管理员列表',
                    component: AdminGroupAdmin,
                },
                {
                    path: 'admin/book',
                    name: '书架列表',
                    component: AdminBook,
                },
                {
                    path: 'admin/book/add',
                    name: '书架列表 / 添加图书',
                    component: AdminBookAdd,
                },
                {
                    path: 'admin/book/:id/edit',
                    name: '书架列表 / 编辑图书',
                    component: AdminBookEdit,
                },
                {
                    path: 'admin/buy/book/list',
                    name: '0元购书 / 图书列表',
                    component: BuyBookList,
                },
                {
                    path: 'admin/buy/book/add',
                    name: '0元购书 / 添加图书',
                    component: BuyBookAdd,
                },
                {
                    path: 'admin/buy/book/:id/edit',
                    name: '0元购书 / 编辑图书',
                    component: BuyBookEdit,
                },
                {
                    path: 'admin/buy/award/list',
                    name: '0元购书 / 中奖名单',
                    component: BuyAwardList,
                },
                {
                    path: 'admin/user',
                    name: '用户管理 / 用户列表',
                    component: AdminUser,
                },
                {
                    path: 'admin/message',
                    name: '消息管理 / 历史消息',
                    component: AdminMessage,
                },
                {
                    path: 'admin/order',
                    name: '订单管理 / 订单列表',
                    component: AdminOrder,
                },
                {
                    path: 'admin/finance',
                    name: '财务管理 / 财务详情',
                    component: AdminFinance,
                },
                {
                    path: 'admin/recharge/money',
                    name: '今日充值金额',
                    component: AdminIndexRechargeMoney,
                },
                {
                    path: 'admin/trading/money',
                    name: '今日交易金额',
                    component: AdminIndexTradingMoney,
                },
                {
                    path: 'alerts',
                    name: '通知中心',
                    component: Alerts,
                },
                //吧主
                {
                    path: 'bar/index',
                    name: '主页 ',
                    component: BarIndex,
                },
                {
                    path: 'bar/new/user',
                    name: '今日新增用户 ',
                    component: BarIndexNewUser,
                },
                {
                    path: 'bar/active/user',
                    name: '今日活跃用户',
                    component: BarIndexActiveUser,
                },
                {
                    path: 'bar/new/order',
                    name: '今日新增订单',
                    component: BarIndexNewOrder,
                },
                {
                    path: 'bar/group',
                    name: '书吧管理 / 书吧信息',
                    component: BarGroup,
                },
                {
                    path: 'bar/group/user',
                    name: '书吧管理 / 用户列表',
                    component: BarGroupUser,
                },
                {
                    path: 'bar/column',
                    name: '书吧管理 / 专栏列表',
                    component: BarColumn,
                },
                {
                    path: 'bar/column/add',
                    name: '书吧管理 / 专栏列表 / 添加专栏',
                    component: BarColumnAdd,
                },
                {
                    path: 'bar/column/:id/edit',
                    name: '书吧管理 / 专栏列表 / 编辑专栏',
                    component: BarColumnEdit,
                },
                {
                    path: 'bar/book',
                    name: '书架管理 / 书架列表 ',
                    component: BarBook,
                },
                {
                    path: 'bar/book/add',
                    name: '书架管理 / 书架列表 / 添加图书 ',
                    component: BarBookAdd,
                },
                {
                    path: 'bar/book/:id/edit',
                    name: '书架管理 / 书架列表 / 编辑图书 ',
                    component: BarBookEdit,
                },
                {
                    path: 'bar/book/select',
                    name: '书架管理 / 书架列表 / 选择图书 ',
                    component: BarBookSelectAdd,
                },
                {
                    path: 'bar/book/select/:id/edit',
                    name: '书架管理 / 书架列表 / 编辑图书  ',
                    component: BarBookSelectEdit,
                },
                {
                    path: 'bar/message',
                    name: '消息管理 / 历史消息 ',
                    component: BarMessage,
                },
                {
                    path: 'bar/order',
                    name: '订单管理 ',
                    component: BarOrder,
                },
                {
                    path: 'bar/finance',
                    name: '财务管理 ',
                    component: BarFinance,
                },
                {
                    path: 'bar/revenue',
                    name: '今日收入 ',
                    component: BarIndexRevenue,
                },
                {
                    path: 'bar/expenditure',
                    name: '今日支出 ',
                    component: BarIndexExpenditure,
                },
                {
                    path: 'bar/bind/wechat',
                    name: '财务管理 / 微信绑定 ',
                    component: BarFinanceBindWechat,
                },
                {
                    path: 'bar/money/out',
                    name: '财务管理 / 收入提现',
                    component: BarFinanceMoneyOut,
                },
                {
                    path: 'bar/blog',
                    name: '书吧管理 / 帖子列表',
                    component: BarBlog,
                },
                {
                    path: 'bar/blog/add',
                    name: '书吧管理 / 帖子列表 / 添加帖子',
                    component: BarBlogAdd,
                },
                {
                    path: 'bar/blog/:id/edit',
                    name: '书吧管理 / 帖子列表 / 编辑帖子',
                    component: BarBlogEdit,
                },
                {
                    path: 'bar/blog/:id/comment',
                    name: '书吧管理 / 帖子列表 / 帖子评论',
                    component: BarBlogComment,
                },
                {
                    path: 'bar/banner',
                    name: '书吧banner',
                    component: BarBanner,
                },
                {
                    path: 'bar/banner/add',
                    name: 'banner管理 / 添加书吧banner',
                    component: BarBannerAdd,
                },
                {
                    path: 'bar/banner/:id/edit',
                    name: 'banner管理 / 编辑书吧banner',
                    component: BarBannerEdit,
                },
            ]
        }
    ]
})
