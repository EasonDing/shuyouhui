## 书友会后台项目

基于 Laravel + VUE + CoreUI + Element.

### 开发环境配置

1. 克隆代码到你的本地 PHP 开发环境中。并安装 composer 依赖
```
composer install
```

2. 配置 .env 文件中的数据库连接信息, 并创建数据表
```
修改.env CACHE_DRIVER =array

php artisan migrate
php artisan db:seed
```

3. 生成 Key
```
php artisan key:generate
```

4. 安装 passport
```
php artisan passport:install
```


### 前端开发配置

```
npm install
npm run dev
```

### 运行文档

```
npm i -g docute-cli
docute ./docs
```