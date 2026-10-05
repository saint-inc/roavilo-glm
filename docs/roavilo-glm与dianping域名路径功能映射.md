# roavilo-glm 与 dianping 域名、子域名、路径功能映射

更新日期：2026-09-06

本文是 dianping.com 公开入口与 roavilo-glm 规范 URL 的统一映射表，用于路由设计、Nginx、链接检查与验收。dianping 的 www/m/h5 主站存在反爬或 JS 壳，无法逐页抓取；映射依据为 kf/t 子域可访问内容、页面 footer 链接清单与公开 URL 模式。dianping 动态路径、登录态路径可能随时变化，不能把某次浏览结果当成永久接口。

映射状态说明：

- **一一对应**：path 结构、层级与参数语义相同，只替换根域（例：`www.dianping.com/shanghai/ch10` → `www.roavilo-glm.com/shanghai/ch1`，`ch` 后数字为 roavilo-glm 自有分类 ID）。
- **规范化**：roavilo-glm 将旧入口统一到稳定路径，旧路径单跳 301。
- **roavilo-glm 自有**：dianping 无同 path 原型（或无法核实），roavilo-glm 自行设计，不得声明为"对标实现"。
- **外部服务**：支付、短信、地图等只映射业务责任，不伪造第三方域名。
- **规划缺口**：dianping 已确认存在对应入口，roavilo-glm 未实现。

品牌替换只发生在页面可见内容中；URL path 是地址契约，不做品牌化改写。

## 1. dianping 子域与 roavilo-glm 对应

| dianping 入口 | 公开行为/性质 | roavilo-glm 规范入口 | 映射状态 | 承载功能 |
| --- | --- | --- | --- | --- |
| `www.dianping.com` | PC 消费者主站 | `www.roavilo-glm.com` | 一一对应 | 城市首页、分类列表、搜索、商户、团购、资讯、站点页（响应式同时覆盖 iPad/手机） |
| `m.dianping.com` | 移动站 | `m.roavilo-glm.com`（Nginx 反代主应用） | 一一对应 | 与主站同应用；≤768px 显示底部 Tab 栏 |
| `t.dianping.com` | 团购子域 | `t.roavilo-glm.com`（反代 `/t`；`/deal/*` 直达） | 一一对应 | 团购列表 `/t`、详情 `/deal/{id}` |
| `kf.dianping.com` | 客服中心（会员/商户热线+FAQ） | `kf.roavilo-glm.com`（反代 `/kf`） | 一一对应 | 双热线、FAQ、意见反馈（feedbacks 表） |
| `e.dianping.com` | 商户自助平台 | `merchant.roavilo-glm.com` | **独立子域** | 商户中心全功能：子域路径经 `public/index.php` 子域重写映射到 `/merchant` 前缀（`/`→`/merchant`、`/shops/1/dashboard`→`/merchant/shops/1/dashboard`），登录/注册等认证页与静态资源不重写 |
| `hr.dianping.com` / `zhaopin.dianping.com` | 招聘站 | `zhaopin.roavilo-glm.com` | **独立子域** | 子域路径重写到 `/jobs`（职位列表与投递） |
| `h5.dianping.com` | H5/App 下载页 | `h5.roavilo-glm.com` | **独立子域** | 子域路径重写到 `/app`（App 下载页，iOS/Android 预留） |
| `union.dianping.com` | API 网关（返回 JSON 欢迎响应，无页面） | `union.roavilo-glm.com` | **独立子域** | 子域任意路径统一落到 `/union-gateway` 路由，返回 `{"code":200,"msg":"WelCome roavilo-glm union gateway!"}` |

子域实现机制（2026-09-06 调整）：Nginx 各子域 server 块反代 `www.roavilo-glm.com` 并透传 Host；`public/index.php` 在请求创建前按 Host 重写 REQUEST_URI 前缀（merchant→/merchant、zhaopin→/jobs、h5→/app、union→/union-gateway），豁免静态资源（css/js/storage 等）、认证页（login/register/logout/forgot-password/reset-password）与已带前缀路径。生产需设置 `SESSION_DOMAIN=.roavilo-glm.com` 使登录态跨子域共享（.env.example 已注释说明）。主站原路径（/merchant、/jobs、/app）继续可用，与子域双入口并存。

## 2. 主站关键 path 映射

| dianping path 模式 | 功能 | roavilo-glm 对应 path | 状态 |
| --- | --- | --- | --- |
| `/` | 首页 | `/`（302 → 当前城市首页） | ✅ |
| `/{城市拼音}`（如 `/shanghai`） | 城市首页 | `/{城市拼音}`（10 城） | ✅ |
| `/{城市}/ch{分类ID}` | 分类商户列表 | `/{城市}/ch{分类ID}` | ✅ |
| `/search/keyword/{城市ID}/0_关键词` | 关键词搜索 | 同结构；关键词亦支持 `?keyword=` | ✅ |
| `/shop/{商户ID}` | 商户详情 | `/shop/{id}`（rf/r_sort 点评筛选排序） | ✅ |
| `/{城市}/ch{分类}/g{区域ID}` | 区域筛选列表 | `/{城市}/ch{id}/g{区域ID}`（如 /shanghai/ch1/g13 黄浦区） | ✅ |
| `/shop/{id}/fav`（收藏动作） | 收藏商户 | `POST /shop/{id}/fav` | ✅ |
| `/deal/{团购ID}` | 团购详情 | `/deal/{id}` | ✅ |
| footer：关于我们/联系我们/帮助中心/客服中心/商户入驻/推广服务/商户诚信公约/最新资讯/人才招聘/用户协议/隐私政策/应用下载 | 站点链接 | `/about` `/contact` `/help` `/kf` `/merchant/create` `/promote` `/merchant-convention` `/news` `/jobs` `/agreement` `/privacy` `/app` | ✅ 全部 200 |

## 3. 旧版 URL 兼容跳转（单跳 301）

| 旧 path | 跳转目标 |
| --- | --- |
| `/shops` | `/{当前城市拼音}` |
| `/shops/{id}` | `/shop/{id}` |
| `/city/{slug}` | `/{slug}` |
| `/deals` | `/t` |
| `/deals/{id}` | `/deal/{id}` |

## 4. roavilo-glm 自有页面（无 dianping 同 path 原型）

| path | 功能 | 说明 |
| --- | --- | --- |
| `/city` | 城市选择页 | 热门+全部城市网格 |
| `/login` `/register` `/logout` `/forgot-password` `/reset-password/{token}` | 认证 | dianping 登录为弹层+扫码，roavilo-glm 为独立页 |
| `/user` `/user/reviews` `/favorites` `/orders` `/user/profile` | 个人中心 | dianping 个人中心结构未核实（登录态反爬） |
| `/merchant` `/merchant/shops/{shop}/*` | 商户中心 | 对标 e 子域功能，path 为 roavilo-glm 自有 |
| `/admin/*` | 管理后台 | 平台内部功能 |
| `/api/search/suggest` | 搜索联想 JSON | 内部接口 |
| `/news/{id}` | 资讯详情 | path 模式通用，非核实对标 |

## 5. 规划缺口（dianping 有、roavilo-glm 未实现）

- 地图选点/POI 展示（需地图 API key）。
- 推荐菜/菜单模块、视频点评、社区笔记/榜单。
- 会员 VIP 等级与积分体系。
- 扫码登录、短信验证码登录。
- 真实支付（微信/支付宝）、退款流程界面。

以上缺口未完成前，不得在验收或对外描述中宣称"覆盖 dianping 全部功能"。
