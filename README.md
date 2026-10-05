# roavilo-glm

**一个基于 Laravel 的点评类开源项目。**

roavilo-glm 围绕「找店、看点评、写点评、购买团购」构建本地生活服务平台，提供消费者前台、个人中心、商户中心和管理后台。项目采用服务端渲染与响应式布局，支持电脑、平板和手机访问，可用于学习 Laravel、开发城市商户点评网站，或作为同类项目的二次开发基础。

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Laravel 13](https://img.shields.io/badge/Laravel-13-FF2D20.svg)](composer.json)
[![PHP 8.4.1+](https://img.shields.io/badge/PHP-8.4.1%2B-777BB4.svg)](composer.lock)

[功能介绍](#功能介绍) · [快速开始](#快速开始) · [MySQL 配置](#mysql-配置) · [部署说明](#部署说明) · [参与贡献](#参与贡献)

## 项目介绍

项目以城市和商户为核心，将商户分类、消费点评、用户收藏、团购订单与商户运营串联起来。消费者可以发现店铺并分享体验，商户可以维护店铺、发布团购和处理点评，管理员可以管理平台基础数据及审核内容。

项目使用原创界面设计，包含可运行的演示数据和业务流程，实际支付、地图及短信服务需另行接入。

## 功能介绍

| 模块 | 已实现功能 |
| --- | --- |
| 城市与商户浏览 | 城市切换与记忆、城市首页、分类及区域筛选、关键词搜索、搜索联想、商户详情 |
| 评分与点评 | 星级评分、消费点评、评分分布、点评筛选、点评点赞和回复 |
| 用户与个人中心 | 注册登录、找回密码、个人资料、头像、商户收藏、我的点评、我的订单 |
| 团购与订单 | 团购列表和详情、下单、模拟支付、订单详情、核销码、商户核销 |
| 商户中心 | 店铺入驻、经营看板、店铺资料维护、点评回复、团购发布及上下架、订单核销 |
| 管理后台 | 店铺审核、用户及管理员权限管理、点评管理、分类管理、城市管理 |
| 站点内容 | 资讯列表与详情、帮助中心、客服反馈、招聘信息、联系及协议页面 |
| 多端与子域入口 | PC／平板／手机响应式布局，移动、团购、客服、商户、招聘、H5 与网关子域配置 |

用户、店主和管理员共用一套账号体系：普通用户入驻店铺后可以使用商户中心，管理员通过账号权限进入后台。

### 当前实现范围

- 团购支付采用模拟流程，尚未接入微信支付、支付宝等真实支付渠道。
- 找回密码使用 Laravel 邮件服务；默认邮件驱动为 `log`，实际发送需要配置邮件服务。
- 地图、短信和原生 Android／iOS App 尚未实现；H5 入口目前为 App 介绍页面。
- 网关子域目前提供 JSON 欢迎响应，尚未提供完整开放 API。
- 部分演示图片为路径占位，需要自行补充图片或通过已有上传功能维护。

## 技术栈

| 层级 | 技术 |
| --- | --- |
| 后端框架 | Laravel 13、PHP |
| 页面渲染 | Blade 模板 |
| 前端界面 | 原生 CSS、JavaScript、响应式布局 |
| 数据库 | SQLite／MySQL |
| 会话、缓存与队列 | Laravel 驱动，默认使用数据库 |
| 测试 | PHPUnit、内存 SQLite |
| 部署 | Nginx、PHP-FPM |

页面样式直接使用 `public/css/app.css`，常规启动无需安装 Node.js。仓库保留了 Vite 配置，后续使用该构建流程时再安装前端依赖。

## 环境要求

- **PHP 8.4.1 或更高版本**：当前 `composer.lock` 中的 Symfony 依赖要求此版本。
- **Composer 2**。
- **SQLite**，或 **MySQL 8**。
- PHP 扩展包括 PDO、对应数据库驱动、mbstring、XML、ctype、fileinfo 等；图片处理还需 GD。完整依赖要求可在安装后通过 `composer check-platform-reqs` 检查。

## 快速开始

以下步骤用于全新本地开发环境，默认使用 SQLite。

### 1. 获取代码并安装依赖

```bash
git clone https://github.com/saint-inc/roavilo-glm.git
cd roavilo-glm
composer install
```

### 2. 配置环境

```bash
cp .env.example .env
php artisan key:generate
```

`.env.example` 默认使用 `DB_CONNECTION=sqlite`，应用地址为 `http://localhost:8000`。如需使用 MySQL，请先完成下方的 [MySQL 配置](#mysql-配置)。

### 3. 初始化数据库和存储

```bash
# SQLite 环境需要创建数据库文件；MySQL 环境跳过此行
touch database/database.sqlite

php artisan migrate --seed
php artisan storage:link
```

Seeder 会生成城市、分类、商户、点评、团购、订单和资讯等演示数据，并创建演示账号。请仅在新建的开发数据库中执行；在已有业务数据库中重复导入会造成重复数据，且 Seeder 会将相关表中的记录统一标记为演示数据。

### 4. 启动项目

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

访问 **http://127.0.0.1:8000**，首页会跳转到当前城市，例如 `/shanghai`。

### 演示账号

| 身份 | 邮箱 | 密码 |
| --- | --- | --- |
| 管理员兼店主 | `test@roavilo-glm.com` | `password123` |
| 普通用户 | `user1@roavilo-glm.com` | `password123` |

管理员账号可以体验个人中心、商户中心和管理后台。演示账号仅供本地开发体验，正式部署请使用独立账号和密码。

## MySQL 配置

使用 MySQL 时，在执行数据库初始化前创建数据库：

```sql
CREATE DATABASE `roavilo-glm`
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

编辑 `.env`，填写自己的数据库用户名和密码：

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roavilo-glm
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

执行 `php artisan config:clear`，随后继续快速开始中的迁移、存储链接和启动步骤。MySQL 环境无需创建 SQLite 文件。

## 常用入口

| 页面 | 本地路径 |
| --- | --- |
| 城市首页 | `/shanghai` |
| 城市选择 | `/city` |
| 商户详情 | `/shop/{id}` |
| 团购列表 | `/t` |
| 个人中心 | `/user` |
| 商户中心 | `/merchant` |
| 管理后台 | `/admin` |
| 资讯 | `/news` |
| 客服与反馈 | `/kf` |

`{id}` 使用数据库中实际存在的记录 ID；个人中心、商户中心和管理后台会按登录身份与权限限制访问。

## 项目结构

```text
app/
├── Http/Controllers/      # 前台、用户、商户和后台业务控制器
├── Http/Middleware/       # 管理员权限等中间件
├── Models/                # 商户、点评、团购和订单等模型
└── Services/              # 城市解析等服务
config/                    # 应用配置
database/
├── migrations/            # 数据库迁移
└── seeders/               # 城市及业务演示数据
resources/views/           # Blade 页面模板
public/css/                # 页面样式
routes/web.php             # Web 路由
deploy/nginx/              # Nginx 部署示例
tests/                     # 自动化测试
docs/                      # 架构、数据库和功能说明
```

## 开发与测试

```bash
# 检查 Composer 清单及当前 PHP 环境
composer validate --strict
composer check-platform-reqs

# 运行测试（需要 composer install 安装开发依赖）
php artisan test
```

测试默认使用内存 SQLite。仓库包含 GitHub Actions 基础测试配置，目前测试覆盖首页跳转和城市页面等基础行为；更完整的业务测试欢迎通过 Pull Request 补充。

## 部署说明

生产目录示例为 `/var/www/roavilo-glm`，Nginx 站点根目录应指向其中的 `public/`。应用可使用自己的域名；现有子域映射以 `roavilo-glm.com` 为例，更换根域时需同步调整 `public/index.php` 和 Nginx 配置。

生产环境配置示例：

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://www.roavilo-glm.com
SESSION_DOMAIN=.roavilo-glm.com
```

数据库、邮件及第三方服务凭据请在服务器 `.env` 中配置。正式部署通常执行 `composer install --no-dev --optimize-autoloader` 和 `php artisan migrate --force`，基础城市、分类与管理员账号需单独初始化，不导入带有默认管理员的演示 Seeder。部署时需配置 DNS、HTTPS，以及 `storage`、`bootstrap/cache` 的写入权限；使用上传功能时需创建存储链接。

Nginx 配置见 [deploy/nginx/roavilo-glm.conf](deploy/nginx/roavilo-glm.conf)。[CentOS 8 部署指南](docs/部署指南-CentOS8.md)为历史环境示例，尚未在真实服务器验证；当前 PHP 要求以本 README 和依赖锁定文件为准。

## 项目文档

- [项目架构和部署说明](docs/项目架构和部署说明.md)
- [数据库表说明](docs/数据库表说明.md)
- [功能测试清单](docs/功能测试清单.md)
- [域名与路径功能映射](docs/域名与路径功能映射.md)
- [页面样式与交互验收规范](docs/全站三端页面样式与交互验收规范.md)

## 参与贡献

欢迎通过 [Issues](https://github.com/saint-inc/roavilo-glm/issues) 报告问题、提出建议，或提交 Pull Request 改进功能、测试及文档。

提交问题时请描述运行环境、复现步骤和实际结果。贡献前请阅读 [贡献指南](CONTRIBUTING.md)，漏洞报告请遵循 [安全报告说明](SECURITY.md)。

## 开源许可证

本项目原创代码与文档采用 [MIT 许可证](LICENSE)，使用、修改及分发时请保留版权和许可证声明。Laravel 衍生部分保留原版权声明，第三方依赖及内容遵循各自授权，详见 [第三方声明](THIRD_PARTY_NOTICES.md)。用户上传内容和第三方商标不因项目开源而自动获得授权。
