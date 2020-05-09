<p align="center">
    <img src="https://res.cloudinary.com/betterde/image/upload/v1588045292/github/projects/ecms/signin-view-no-border.png" width="100%">
</p>
<p align="center">
    <img src="https://res.cloudinary.com/betterde/image/upload/v1588045796/github/projects/ecms/dashboard.png" width="100%">
</p>

![ECMS](https://github.com/betterde/ecms/workflows/ECMS/badge.svg)

## ECMS

一个面向代购的简单电商管理系统。前端采用 [Vue Cli 3](https://cli.vuejs.org/)搭建的开发环境，UI 使用的是 [Element UI](https://element.eleme.io/)，统计图表使用 [Antv G2](https://g2.antv.vision/en/)，后端 API 采用 Laravel 实现。

- [x] 数据分析
- [x] 订单管理
- [x] 商品信息管理
- [x] 库存管理
- [x] 客户信息管理
- [x] 采购订单导出 Excel
- [] 代理商管理
- [] 支付系统

## API

```bash
composer install --optimize-autoloader --no-dev # 安装项目依赖

cp .env.example .env # 复制一份配置文件模板，然后根据实际环境修改配置文件

php artisan config:cache # 优化配置加载

php artisan route:cache # 优化路由加载

php artisan migrate # 迁移数据表信息

php artisan jwt:secret # 生成 JWT Secret
```

如果需要将 SPA 和 API 分开部署，需要注释 `routes/web.php` 中的路由：
```php
Route::view('{path?}', 'index')->where('path', '[\/\w\.-]*');
```

## Web

这里并没有采用 Laravel 自带的 Vue 脚手架，也是便于将前端和 Laravel 进行拆分。

```bash
yarn # 安装依赖
yarn build # 打包前端资源
```

如果需要将 Vue SPA 单独部署，只需要将打包后的 `resources/views/index.blade.php` 拷贝到前端资源目录中，并改名为 `index.html`，再将 `public` 目录下生成的 `js,css,fonts` 等目录同时拷贝到前端资源目录中。目录结构如下：
```
.
├── css
├── favicon-128.png
├── favicon-16.png
├── favicon-32.png
├── favicon-48.png
├── favicon-64.png
├── favicon.ico
├── fonts
├── index.html
└── js
```

## Nginx

```
server {
    listen 80;
    server_name ecms.example.com;
    root /example.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
以下是前后端完全分离的 Nginx 配置文件：

```
server {
    listen 80;
    server_name ecms.example.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_tokens off;
    server_name ecms.example.com;
    ssl_certificate /etc/nginx/cert/fullchain.cer;
    ssl_certificate_key /etc/nginx/cert/example.com.key;

    # Recommendations from https://raymii.org/s/tutorials/Strong_SSL_Security_On_nginx.html
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_ciphers '!aNULL:kECDH+AESGCM:ECDH+AESGCM:RSA+AESGCM:kECDH+AES:ECDH+AES:RSA+AES:';
    ssl_prefer_server_ciphers on;
    ssl_session_cache shared:SSL:10m;

    # disable any limits to avoid HTTP 413 for large image uploads
    client_max_body_size 0;

    # required to avoid HTTP 411: see Issue #1486 (https://github.com/docker/docker/issues/1486)
    chunked_transfer_encoding on;

    add_header Strict-Transport-Security max-age=15768000;

    # OCSP Stapling ---
    # fetch OCSP records from URL in ssl_certificate and cache them
    ssl_stapling on;
    ssl_stapling_verify on;

    ssl_trusted_certificate /etc/nginx/cert/ca-bundle.trust.crt;

    location / {
        index index.html;
        root /web/sites/php/ecms/spa;
        try_files $uri $uri/ /index.html;
    }

    location /api {
        index index.php;
        root /web/sites/php/ecms/api/public;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        root /var/local/web/sites/php/ecms/api/public;
        fastcgi_pass php-fpm:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
> 这个配置文件是基于 Docker 部署的，如果直接在服务器上部署，可以将f `astcgi_pass` 修改为响应的 PHP-FPM 监听地址或 unxi

## 赞助商

[![jetbrains](https://res.cloudinary.com/betterde/image/upload/v1588046151/github/sponsor/jetbrains.svg)](https://www.jetbrains.com/?from=ecms)
