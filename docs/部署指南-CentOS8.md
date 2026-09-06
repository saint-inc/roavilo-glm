# roavilo 部署指南（CentOS Stream 8 + Nginx + PHP-FPM + MySQL）

> 适用环境：CentOS Stream 8 / 2C4G 以上 / 已备案域名 roavilo.com

## 一、安装依赖

```bash
# 启用 Remi 源安装 PHP 8.3+
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf module reset php && dnf module enable php:remi-8.3 -y

# 安装 PHP 及扩展
dnf install -y php php-fpm php-mysqlnd php-mbstring php-xml php-json php-gd php-curl php-zip php-intl php-bcmath

# 安装 Nginx 与 MySQL 8
dnf install -y nginx mysql-server

# 启动并设置开机自启
systemctl enable --now php-fpm nginx mysqld
```

## 二、初始化数据库

```bash
mysql_secure_installation
mysql -uroot -p -e "CREATE DATABASE roavilo DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## 三、部署代码

```bash
mkdir -p /var/www && cd /var/www
# 上传代码（git clone 或 scp/rsync）
git clone <你的仓库地址> roavilo
cd roavilo

# 安装依赖（生产环境不装 dev 依赖）
composer install --no-dev --optimize-autoloader --no-interaction

# 配置环境变量
cp .env.example .env
# 编辑 .env：数据库连接、APP_URL=https://www.roavilo.com、APP_ENV=production、APP_DEBUG=false
php artisan key:generate

# 建表 + 初始数据 + 存储软链
php artisan migrate --seed --force
php artisan storage:link

# 目录权限（php-fpm 运行用户为 apache 或 nginx，视 dnf 安装结果而定）
chown -R apache:apache /var/www/roavilo/storage /var/www/roavilo/bootstrap/cache
chmod -R 775 /var/www/roavilo/storage /var/www/roavilo/bootstrap/cache

# 生产优化
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 四、配置 Nginx

```bash
cp deploy/nginx/roavilo.conf /etc/nginx/conf.d/
nginx -t && systemctl reload nginx
```

证书申请（免费 Let's Encrypt）：

```bash
dnf install -y certbot python3-certbot-nginx
certbot --nginx -d roavilo.com -d www.roavilo.com -d m.roavilo.com -d t.roavilo.com -d kf.roavilo.com -d merchant.roavilo.com -d zhaopin.roavilo.com -d h5.roavilo.com -d union.roavilo.com
```

## 五、DNS 解析

在域名控制台添加 A 记录：

| 主机记录 | 类型 | 记录值 |
|---|---|---|
| @ / www / m / kf | A | 服务器公网 IP |

## 六、防火墙与 SELinux

```bash
firewall-cmd --permanent --add-service=http --add-service=https
firewall-cmd --reload

# SELinux 放行 nginx 访问项目目录与网络
setsebool -P httpd_can_network_connect 1
chcon -R -t httpd_sys_content_t /var/www/roavilo
```

## 七、常用运维命令

```bash
php artisan queue:work &        # 队列（如后续接异步任务）
tail -f storage/logs/laravel.log   # 查看应用日志
tail -f /var/log/nginx/roavilo.error.log   # 查看 Nginx 错误
systemctl restart php-fpm nginx  # 重启服务
```

## 八、二级域名规划

| 域名 | 用途 | 状态 |
|---|---|---|
| www.roavilo.com | PC 主站（响应式覆盖 iPad/手机） | ✅ 已支持 |
| m.roavilo.com | 移动端入口 | ✅ 已支持（同应用） |
| t.roavilo.com | 团购子域（对应 t.dianping.com） | ✅ 已支持（反代 /t） |
| kf.roavilo.com | 客服中心（对应 kf.dianping.com） | ✅ 已支持（反代 /kf） |
| merchant.roavilo.com | 商户中心（对应 e.dianping.com） | ✅ 已支持（子域重写 → /merchant） |
| zhaopin.roavilo.com | 招聘（对应 hr/zhaopin.dianping.com） | ✅ 已支持（子域重写 → /jobs） |
| h5.roavilo.com | H5/App 下载页（对应 h5.dianping.com） | ✅ 已支持（子域重写 → /app） |
| union.roavilo.com | API 网关（对应 union.dianping.com） | ✅ 已支持（JSON 网关响应） |
| admin.roavilo.com | 管理后台独立域名 | 预留（当前 /admin 路径） |

**注意**：启用子域登录态共享需在 `.env` 设置 `SESSION_DOMAIN=.roavilo.com`；DNS 解析表中对应主机记录（@/www/m/t/kf/merchant/zhaopin/h5/union）均需添加 A 记录指向服务器 IP；证书申请需覆盖全部子域。
