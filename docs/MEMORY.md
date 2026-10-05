# roavilo-glm 项目记忆

开源准备（2026-10-05）：采用 MIT，已补充项目 README、LICENSE、贡献指南、第三方声明和安全报告说明；本地环境信息已泛化。GitHub 可见性以仓库实际设置为准，公开前应确认已有提交历史可对外发布。验证：Composer 清单校验通过；全新 SQLite 迁移及 Seeder 通过；隔离副本中 3 项测试、6 项断言通过（仅跳过下载缓慢的 Pint 格式工具，保留正式项目依赖）；league/commonmark 升级至 2.10.3，Composer 审计未发现已知漏洞。

当前开发配置（2026-10-05）：Git 仓库为 `https://github.com/saint-inc/roavilo-glm.git`；本地目录为 `<项目路径>/roavilo-glm`；本地数据库为 `roavilo-glm`，各开发者通过本地 `.env` 设置数据库凭据。网站品牌为 `roavilo-glm`，网站域名已更新为 `roavilo-glm.com`（含各子域）。目录改名后需执行 `php artisan storage:link --force` 重建存储软链接，并清理配置与视图缓存。

更新日期：2026-09-06

最新全站基线：本地 MySQL `roavilo-glm` 为 **11 条迁移、22 张表**；`php artisan route:list` 当前为 **83 条路由**（含 5 条旧 URL 301 兼容跳转与 catch-all 城市路由）。**is_demo 机制已生效**：14 张业务表均有 `is_demo`（1=demo/0=真实，带索引），Seeder 全量产出并统一标记为 demo，每表至少 10 条（reviews 38、review_likes 12、categories 12、shops 12 为超额）；`deal_orders` 的 demo 数据覆盖待支付/已支付/已使用三种状态（已支付含核销码），`feedbacks` 覆盖登录/游客两类来源。**应用代码（app/routes/resources）零 is_demo 引用**（grep 已验证），该字段只存在于迁移与 Seeder；上线清理口径为按依赖顺序 `DELETE WHERE is_demo=1` 再回滚 `000008` 迁移删列。注意：`migrate:fresh --seed` 会清空全部数据并重建 demo 集；此后人工产生的订单/收藏/反馈默认 is_demo=0，属于"真实数据"口径，不会被 demo 清理误删。核销闭环（发布团购 → 下单 → 支付生成核销码 → 商户核销，订单状态 0→1→2）在 2026-09-06 上一数据库实例实测通过；demo 订单已含已支付订单（带核销码），可直接用于核销流程演示。

最新架构边界：roavilo-glm 是**单 Laravel 13 应用 + 主域（www.roavilo-glm.com）+ 多子域别名 + 响应式三端**架构。子域映射（2026-09-06 调整）：`merchant.roavilo-glm.com`（对应 e.dianping.com）→ `/merchant`、`zhaopin.roavilo-glm.com`（对应 hr/zhaopin.dianping.com）→ `/jobs`、`h5.roavilo-glm.com`（对应 h5.dianping.com）→ `/app`、`union.roavilo-glm.com`（对应 union.dianping.com）→ `/union-gateway` JSON 网关响应；实现方式为 Nginx 子域反代透传 Host + `public/index.php` 在请求创建前重写 REQUEST_URI 前缀（注意：不能用全局中间件重写——路由在中间件前已解析，实测无效）。重写豁免：静态资源（css/js/storage/build/favicon/robots/up）、认证页（login/register/logout/forgot-password/reset-password，否则子域未登录跳转会 404）、已带前缀路径。生产必须设置 `SESSION_DOMAIN=.roavilo-glm.com` 共享登录态（.env.example 已注释）。m./t./kf. 三个反代子域不变。前台、个人中心、商户中心、管理后台共用 `layouts/app.blade.php` 与 `public/css/app.css`；管理后台另有独立 `layouts/admin.blade.php`。主站原路径（/merchant、/jobs、/app）与子域双入口并存。

最新账户模型：与 Moonker 的双账户隔离不同，roavilo-glm 采用**单账号复用**——`users.is_admin` 区分管理员，`shops.owner_id` 标记店主身份，普通用户入驻即成为店主，无需第二套 Guard。越权保护统一由控制器内 `authorizeShop()`（店主校验）与 `EnsureUserIsAdmin` 中间件（`is_admin` 校验）承担，均已实测：非店主访问店铺看板 403、普通用户访问 /admin 403。

最新 URL 对标结论：dianping.com 公开 URL 模式中 roavilo-glm 已按相同 path 结构落地 `/`（→当前城市）、`/{城市拼音}`、`/{城市}/ch{分类ID}`、`/search/keyword/{城市ID}/0_关键词`、`/shop/{id}`、`/shop/{id}/fav`、`/deal/{id}`、`/t`。dianping 的 www/m/h5 主站存在反爬或 JS 壳，无法逐页抓取；已通过 kf/t 子域可访问内容与 footer 链接清单完成对标，无法核实私有路径的页面不得声称为"已对照 dianping 原样实现"。roavilo-glm 自有补充页面（/city、/user、/merchant、/admin、/news、/jobs、/contact 等）没有 dianping 同 path 原型，已在域名映射文档中单独标注。

最新视觉边界（重要，长期有效）：dianping 的页面视觉设计、图标、素材受版权保护，**roavilo-glm 不做 1:1 像素级复刻**。已交付实现为"布局结构、信息层级、交互模式、响应式断点、登录态差异逻辑对标 + roavilo-glm 原创视觉（橙色主题）"。任何"与 dianping 完全一致的像素/颜色/素材"要求都属于该版权边界之外；如需调整，只能以原创设计继续演进。

最新踩坑记录（新代码必须规避）：Laravel 13 已移除 `Blueprint::unsignedDecimal`（用 `decimal()->unsigned()`）与控制器构造器 `middleware()`（用路由中间件组）；MySQL `ONLY_FULL_GROUP_BY` 下对带排序的关联做聚合会报 1140（聚合查询需绕开关系）；本机 MySQL 必须用 `127.0.0.1` 连接（localhost 走 socket 失败）；`feedback` 单复数同形，模型必须显式 `protected $table = 'feedbacks'`；foreach 解构不能带默认值；catch-all 城市路由 `/{city}` 必须放路由文件末尾且控制器内对未知 slug 显式 `abort(404)`，否则未知路径变 500。

已验证的登录态差异矩阵（2026-09-06 实测）：顶栏（未登录"登录/注册" ↔ 登录后头像+下拉菜单 8 入口，纯 CSS `focus-within`）、提示条（引导登录条 ↔ 个性化欢迎条）、移动端底部 Tab 第 4 格（🔑登录 ↔ 👤我的），DOM 断言全部通过。非登录访问受限页跳登录、越权操作 403。

## 等待外部账号/资质

- 真实支付：微信支付/支付宝商户号（现为模拟支付，`OrderController::pay` 预留替换点）。
- 短信验证码登录：短信服务商账号（现为邮箱+密码登录）。
- 地图选点/POI：高德或百度地图 API key（商户详情页地址模块预留）。
- 邮件发送：SMTP 账号（找回密码当前在页面直接展示重置链接，生产必须改为邮件下发）。
- 对象存储：商户相册图片（`shop_images` 表已建，上传界面未接）。

## 下一阶段优先级

1. 补自动化测试（当前为 curl 手工回归，无 PHPUnit 覆盖）；dev 依赖因网络问题暂用 `--no-dev` 安装，需补装后建立测试基线。
2. 地图、支付、短信、邮件按账号到位顺序逐个接入，并各自建立 Sandbox 验收记录。
3. 推荐菜/菜单模块、会员积分体系、社区笔记（dianping 有、roavilo-glm 未建的产品模块）。
4. CentOS Stream 8 实机部署演练（Nginx 配置与部署指南已就绪，未在真实服务器执行过）。

详细进度见同目录《开发进度.md》《功能测试清单.md》。
