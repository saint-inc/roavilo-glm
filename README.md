# roavilo-glm

基于 Laravel 13 的开源本地生活服务平台，包含城市浏览、商户搜索、评分点评、团购订单、商户中心和管理后台。PC、平板和手机共用响应式界面，采用原创视觉设计。

仓库：[saint-inc/roavilo-glm](https://github.com/saint-inc/roavilo-glm)。项目采用 [MIT 许可证](LICENSE)。

## 功能与当前状态

- 城市切换、分类与区域筛选、关键词搜索、商户详情。
- 用户注册登录、个人资料、收藏、点评、点赞和商户回复。
- 团购下单、演示支付、核销码和商户核销。
- 店铺入驻、店铺及团购管理、管理员审核与基础数据管理。
- 资讯、客服反馈，以及商户、招聘、H5 和网关子域入口。

支付流程目前为演示实现，尚未接入真实支付渠道。地图、短信和原生 Android/iOS App 尚未实现。本项目与 dianping 无隶属关系，不包含或授权其品牌、素材及私有数据。

## 环境要求

- PHP 8.4.1 或更高版本（当前锁定的 Symfony 依赖要求），Composer 2。
- PHP 扩展：PDO、SQLite 或 MySQL 驱动、mbstring、XML、ctype、fileinfo、GD 等；实际要求以 `composer check-platform-reqs` 为准。
- 默认使用 SQLite，可切换 MySQL 8。
- 当前页面直接使用 `public/css/app.css`，启动网站不需要 Node.js；使用保留的 Vite 工具时需安装兼容项目依赖的 Node.js。

## 本地启动

以下步骤用于全新开发数据库：

```bash
git clone https://github.com/saint-inc/roavilo-glm.git
cd roavilo-glm
composer install
cp .env.example .env
php artisan key:generate
```

默认 `.env.example` 使用 SQLite：

```bash
touch database/database.sqlite
php artisan migrate --seed
php artisan storage:link
php artisan serve --host=127.0.0.1 --port=8000
```

打开 `http://127.0.0.1:8000`，首页会跳转到当前城市。Seeder 会生成演示店铺、资讯和账号，部分演示图片仅提供路径占位；不要在已有业务数据库中重复执行 Seeder。

演示管理员兼店主：`test@roavilo-glm.com`，密码 `password123`。该账号仅用于本地演示，生产部署不应导入默认演示管理员。

### 使用 MySQL

先创建库：

```sql
CREATE DATABASE `roavilo-glm` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

在本地 `.env` 中配置自己的凭据：

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roavilo-glm
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

然后执行 `php artisan config:clear`，再运行上述迁移和启动命令。`.env`、数据库文件、上传文件和运行日志不应提交到仓库。

## 验证与贡献

```bash
composer validate --strict
php artisan test
```

测试使用内存 SQLite，与本地业务数据库隔离。贡献流程见 [CONTRIBUTING.md](CONTRIBUTING.md)，漏洞报告见 [SECURITY.md](SECURITY.md)。

## 部署与文档

生产目录示例为 `/var/www/roavilo-glm`；主域为 `roavilo-glm.com`，各子域沿用相同根域。上线需设置 `APP_ENV=production`、`APP_DEBUG=false`、自己的数据库凭据，以及实际的 `APP_URL`。跨子域共享登录时设置 `SESSION_DOMAIN=.roavilo-glm.com`。

- [项目架构和部署说明](docs/项目架构和部署说明.md)
- [部署指南](docs/部署指南-CentOS8.md)（历史 CentOS 8 示例，尚未在真实服务器验证；PHP 版本以本 README 为准）
- [数据库表说明](docs/数据库表说明.md)
- [功能测试清单](docs/功能测试清单.md)
- [域名路径映射](docs/roavilo-glm与dianping域名路径功能映射.md)

## 许可证与第三方内容

仓库原创代码与文档按 MIT 许可证发布；Laravel 衍生部分保留原版权声明。依赖和第三方内容保留各自许可证，详见 [THIRD_PARTY_NOTICES.md](THIRD_PARTY_NOTICES.md)。网站用户上传内容、品牌商标和外部服务不因代码开源而自动获得 MIT 授权。
