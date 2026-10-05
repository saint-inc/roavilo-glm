# roavilo-glm 部署指南（CentOS Stream 8 + Nginx + PHP-FPM + MySQL）

> 适用环境：CentOS Stream 8 / 2C4G 以上 / 已备案域名 roavilo-glm.com

## 本地与生产环境名称

Git 仓库：`https://github.com/saint-inc/roavilo-glm.git`。当前本地项目目录为 `<项目路径>/roavilo-glm`，MySQL 数据库名为 `roavilo-glm`，各开发者通过本地 `.env` 设置数据库凭据。

以下生产部署示例使用 `/var/www/roavilo-glm` 和数据库 `roavilo-glm`，与现有 Nginx 配置一致。克隆命令末尾的 `roavilo-glm` 显式指定服务器目录名。生产环境的实际库名应在 `.env` 的 `DB_DATABASE` 中配置；若使用 `roavilo-glm`，创建库时需用反引号包裹名称，例如：

```sql
CREATE DATABASE `roavilo-glm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 一、安装依赖

```bash
# 启用 Remi 源安装 PHP 8.4.1+
dnf install -y epel-release
dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm
dnf module reset php && dnf module enable php:remi-8.4 -y

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
mysql -uroot -p -e 'CREATE DATABASE `roavilo-glm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
```

## 三、部署代码

```bash
mkdir -p /var/www && cd /var/www
# 上传代码（git clone 或 scp/rsync）
git clone https://github.com/saint-inc/roavilo-glm.git roavilo-glm
cd roavilo-glm

# 安装依赖（生产环境不装 dev 依赖）
composer install --no-dev --optimize-autoloader --no-interaction

# 配置环境变量
cp .env.example .env
# 编辑 .env：数据库连接、APP_URL=https://www.roavilo-glm.com、APP_ENV=production、APP_DEBUG=false
php artisan key:generate

# 建表 + 初始数据 + 存储软链
php artisan migrate --force
# 正式运营请独立创建管理员与基础城市数据，不导入含默认管理员的演示 Seeder
php artisan storage:link

# 目录权限（php-fpm 运行用户为 apache 或 nginx，视 dnf 安装结果而定）
chown -R apache:apache /var/www/roavilo-glm/storage /var/www/roavilo-glm/bootstrap/cache
chmod -R 775 /var/www/roavilo-glm/storage /var/www/roavilo-glm/bootstrap/cache

# 生产优化
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## 四、配置 Nginx

```bash
cp deploy/nginx/roavilo-glm.conf /etc/nginx/conf.d/
nginx -t && systemctl reload nginx
```

证书申请（免费 Let's Encrypt）：

```bash
dnf install -y certbot python3-certbot-nginx
certbot --nginx -d roavilo-glm.com -d www.roavilo-glm.com -d m.roavilo-glm.com -d t.roavilo-glm.com -d kf.roavilo-glm.com -d merchant.roavilo-glm.com -d zhaopin.roavilo-glm.com -d h5.roavilo-glm.com -d union.roavilo-glm.com
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
chcon -R -t httpd_sys_content_t /var/www/roavilo-glm
```

## 七、常用运维命令

```bash
php artisan queue:work &        # 队列（如后续接异步任务）
tail -f storage/logs/laravel.log   # 查看应用日志
tail -f /var/log/nginx/roavilo-glm.error.log   # 查看 Nginx 错误
systemctl restart php-fpm nginx  # 重启服务
```

## 八、二级域名规划

| 域名 | 用途 | 状态 |
|---|---|---|
| www.roavilo-glm.com | PC 主站（响应式覆盖 iPad/手机） | ✅ 已支持 |
| m.roavilo-glm.com | 移动端入口 | ✅ 已支持（同应用） |
| t.roavilo-glm.com | 团购子域（对应 t.dianping.com） | ✅ 已支持（反代 /t） |
| kf.roavilo-glm.com | 客服中心（对应 kf.dianping.com） | ✅ 已支持（反代 /kf） |
| merchant.roavilo-glm.com | 商户中心（对应 e.dianping.com） | ✅ 已支持（子域重写 → /merchant） |
| zhaopin.roavilo-glm.com | 招聘（对应 hr/zhaopin.dianping.com） | ✅ 已支持（子域重写 → /jobs） |
| h5.roavilo-glm.com | H5/App 下载页（对应 h5.dianping.com） | ✅ 已支持（子域重写 → /app） |
| union.roavilo-glm.com | API 网关（对应 union.dianping.com） | ✅ 已支持（JSON 网关响应） |
| admin.roavilo-glm.com | 管理后台独立域名 | 预留（当前 /admin 路径） |

**注意**：启用子域登录态共享需在 `.env` 设置 `SESSION_DOMAIN=.roavilo-glm.com`；DNS 解析表中对应主机记录（@/www/m/t/kf/merchant/zhaopin/h5/union）均需添加 A 记录指向服务器 IP；证书申请需覆盖全部子域。
