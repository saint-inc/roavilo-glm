<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use App\Models\Deal;
use App\Models\Region;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 城市与行政区（全国约 50 个主要城市 + 真实区县，对标点评平台城市覆盖）
        $this->call([CityRegionSeeder::class]);
        $cities = City::all()->keyBy('slug');

        // 分类（含二级分类，演示父子层级：美食与丽人下各挂 1 个子分类）
        $categories = [];
        $catNames = ['美食', '休闲娱乐', '丽人', '结婚', '亲子', '运动健身', '酒店', '家装', '学习培训', '生活服务', '旅游', '医疗健康'];
        foreach ($catNames as $i => $name) {
            $categories[$name] = Category::create(['name' => $name, 'sort' => $i]);
        }
        // 二级分类 demo（parent_id 指向美食/丽人）
        $categories['本帮江浙菜'] = Category::create(['name' => '本帮江浙菜', 'parent_id' => $categories['美食']->id, 'sort' => 0]);
        $categories['美发'] = Category::create(['name' => '美发', 'parent_id' => $categories['丽人']->id, 'sort' => 0]);

        // 测试用户（第一个同时是管理员和店主，便于测试后台与商户中心）
        $user = User::firstOrCreate(
            ['email' => 'test@roavilo-glm.com'],
            ['name' => '测试用户', 'password' => 'password123', 'bio' => 'roavilo-glm 测试账号', 'is_admin' => true]
        );
        $users = [$user];
        // 普通用户：奇数位带头像/签名（完整资料），偶数位全空（最小资料），便于调试资料页两种展示
        $normalUsers = [
            ['美食家小王', true], ['吃货小李', false], ['旅行达人', true], ['生活家阿珍', false],
            ['周末探店君', true], ['甜品控', false], ['健身狂人', true], ['亲子宝妈', false],
            ['城市漫步者', false],
        ];
        foreach ($normalUsers as $i => [$name, $full]) {
            $users[] = User::firstOrCreate(
                ['email' => 'user'.($i + 1).'@roavilo-glm.com'],
                $full
                    ? ['name' => $name, 'password' => 'password123', 'avatar' => 'demo/avatar-'.($i + 1).'.png', 'bio' => '爱生活，爱分享消费体验。']
                    : ['name' => $name, 'password' => 'password123']
            );
        }

        // 商户
        $shopSeed = [
            ['外滩本帮菜馆', '美食', '黄浦区', 128, 4.8, '经典本帮菜，浓油赤酱，环境优雅，适合家庭聚餐。'],
            ['弄堂小笼包', '美食', '黄浦区', 35, 4.6, '皮薄馅大汤汁足，排队也要吃的小笼包。'],
            ['静安烤肉专门店', '美食', '静安区', 168, 4.5, '日式炭火烤肉，和牛品质出色。'],
            ['徐汇川菜馆', '美食', '徐汇区', 88, 4.3, '地道川味，水煮鱼和毛血旺必点。'],
            ['浦东粤式茶楼', '美食', '浦东新区', 110, 4.7, '正宗粤式早茶，虾饺烧卖一绝。'],
            ['长宁日料居酒屋', '美食', '长宁区', 145, 4.4, '深夜食堂氛围，串烧和清酒很棒。'],
            ['星空桌游吧', '休闲娱乐', '徐汇区', 60, 4.2, '百款桌游任选，适合朋友聚会。'],
            ['悦己美容SPA', '丽人', '静安区', 298, 4.6, '专业美容师，进口护理产品。'],
            ['燃力健身房', '运动健身', '浦东新区', 200, 4.1, '器械齐全，私教课程专业。'],
            ['云顶江景酒店', '酒店', '黄浦区', 580, 4.9, '外滩江景房，配套齐全。'],
            ['杨浦亲子乐园', '亲子', '杨浦区', 80, 4.5, '室内儿童乐园，安全卫生。'],
            ['印象婚纱摄影', '结婚', '长宁区', 3999, 4.7, '一对一服务，多套主题场景。'],
            // 3 家北京商户（验证多城市数据隔离：北京首页/分类可见，上海不可见）
            ['簋街小龙虾', '美食', '东城区', 120, 4.5, '簋街老字号麻辣小龙虾，夜宵人气王。'],
            ['三里屯精酿酒馆', '休闲娱乐', '朝阳区', 150, 4.4, '百款精酿生啤，氛围轻松。'],
            ['五道口咖啡馆', '美食', '海淀区', 55, 4.6, '学院氛围精品咖啡，手冲出色。'],
        ];
        $shops = [];
        // 各城市行政区缓存（避免重名区县跨城混淆，如多地都有"朝阳区"）
        $regionCache = [];
        foreach ($shopSeed as $i => [$name, $cat, $region, $price, $rating, $desc]) {
            // 第 13 条起为北京商户（东城区/朝阳区/海淀区），其余为上海
            $isBeijing = $i >= 12;
            $cityId = $isBeijing ? $cities['beijing']->id : $cities['shanghai']->id;
            $regionKey = $cityId.'-'.$region;
            $regionCache[$regionKey] ??= Region::where('city_id', $cityId)->where('name', $region)->first();
            // 状态分布：前 10 家营业中，第 11 家待审核（演示后台审核流），第 12 家已下架，北京 3 家营业中
            $shopStatus = $i === 10 ? 2 : ($i === 11 ? 0 : 1);
            $shops[] = Shop::create([
                'city_id' => $cityId,
                'category_id' => $categories[$cat]->id,
                'region_id' => $regionCache[$regionKey]->id,
                // 第一家店铺绑定测试用户为店主（便于演示商户中心全功能）
                'owner_id' => $i === 0 ? $user->id : null,
                'name' => $name,
                'slug' => 'shop-'.($i + 1),
                'address' => ($isBeijing ? '北京市' : '上海市').$region.'南京东路'.(100 + $i * 7).'号',
                'phone' => '021-'.(60000000 + $i * 1111),
                'avg_price' => $price,
                'rating' => $rating,
                'rating_count' => 0,
                'description' => $desc,
                // 营业时间状态差异：一家 24 小时营业，一家未设置
                'business_hours' => $i === 3 ? '00:00 - 24:00（全天营业）' : ($i === 9 ? null : '10:00 - 22:00'),
                'tags' => [$cat, '环境好', '服务佳'],
                'view_count' => rand(100, 9999),
                'status' => $shopStatus,
            ]);
        }

        // 点评
        // 状态覆盖目标：全库点评的星级按 5/4/3/2/1 均匀分布，
        // 保证商户详情页"好评(4-5)/中评(3)/差评(1-2)"筛选与星级分布条都有数据可调。
        $contents = [
            5 => '环境很好，服务态度也不错，值得推荐给朋友们，下次还会再来。',
            4 => '味道非常棒，分量足，性价比很高，就是周末人有点多需要排队。',
            3 => '整体体验中规中矩，价格可以接受，但离"惊艳"还有距离。',
            2 => '等位时间太长，上菜慢，口味也一般，不太推荐赶时间的朋友。',
            1 => '体验很差，服务态度冷淡，菜品与宣传严重不符，不会再来。',
        ];
        // 星级分配序列：循环 5,4,5,3,4,5,2,4,3,1 → 保证高中低分都有足够样本
        $ratingSequence = [5, 4, 5, 3, 4, 5, 2, 4, 3, 1, 5, 4];
        $seqIndex = 0;
        foreach ($shops as $shop) {
            $count = rand(2, 4);
            shuffle($users);
            for ($j = 0; $j < $count; $j++) {
                $rating = $ratingSequence[$seqIndex % count($ratingSequence)];
                $seqIndex++;
                Review::create([
                    'shop_id' => $shop->id,
                    'user_id' => $users[$j % count($users)]->id,
                    'rating' => $rating,
                    'content' => $contents[$rating],
                    // 低分点评不填消费金额（演示选填字段两种状态）
                    'cost' => $rating >= 3 ? $shop->avg_price : null,
                    'like_count' => rand(0, 50),
                ]);
            }
            $agg = Review::where('shop_id', $shop->id)->selectRaw('COUNT(*) c, AVG(rating) r')->first();
            $shop->update(['rating_count' => $agg->c, 'rating' => round($agg->r, 1)]);
        }

        // 团购（前 10 家商户各一条，保证 demo 数量）
        // 状态覆盖：上架在售 / 已下架 / 已过期 / 未开始 / 已售罄
        $demoDeals = [];
        foreach (array_slice($shops, 0, 10) as $i => $shop) {
            // 第 7 条下架、第 8 条已过期、第 9 条未开始、第 10 条售罄，其余正常在售
            $dealStatus = match ($i) {
                6 => 0,       // 已下架
                7, 8, 9 => 1, // 过期/未开始/售罄仍为"上架"状态，用时间与库存表达
                default => 1,
            };
            $demoDeals[] = Deal::create([
                'shop_id' => $shop->id,
                'title' => $shop->name.' · 招牌双人套餐',
                'description' => '含招牌菜 2 份 + 饮品 2 杯，周末节假日通用，免预约。',
                'original_price' => $shop->avg_price * 2,
                'price' => round($shop->avg_price * 2 * 0.7, 2),
                // 第 10 条库存为 0（售罄态），其余 100
                'stock' => $i === 9 ? 0 : 100,
                'sold_count' => $i === 9 ? 100 : rand(10, 500),
                // 时间状态：第 8 条已过期（昨天结束），第 9 条未开始（明天开始）
                'starts_at' => $i === 9 ? now()->addDay() : now()->subDays(10),
                'ends_at' => $i === 8 ? now()->subDay() : now()->addDays(30),
                'status' => $dealStatus,
            ]);
        }

        // 商户相册图片（占位路径，覆盖前 5 家商户各 2 张，共 10 条）
        foreach (array_slice($shops, 0, 5) as $si => $shop) {
            foreach ([1, 2] as $k) {
                \App\Models\ShopImage::create([
                    'shop_id' => $shop->id,
                    'path' => 'demo/shop-'.$shop->id.'-'.$k.'.jpg',
                    'caption' => $shop->name.' 环境图 '.$k,
                    'sort' => $k,
                ]);
            }
            unset($si);
        }

        // 点评回复（从已有点评中取样，模拟用户互评与商家回复）
        $sampleReviews = Review::take(10)->get();
        $replyTexts = ['同意！招牌菜确实好吃。', '请问人均大概多少呀？', '收藏了，周末就去试试。', '看起来环境不错！', '感谢分享～'];
        foreach ($sampleReviews as $ri => $review) {
            $review->replies()->create([
                // 交替以其他用户 / 店主身份回复
                'user_id' => $ri % 2 === 0
                    ? $users[($ri + 1) % count($users)]->id
                    : $shops[0]->owner_id,
                'content' => $ri % 2 === 0
                    ? $replyTexts[$ri % count($replyTexts)]
                    : '【商家回复】感谢您的光临，期待您再次惠顾！',
            ]);
            $review->increment('reply_count');
        }

        // 点赞（每条取样点评由 1-2 个其他用户点赞，共 10+ 条记录）
        $likeCount = 0;
        foreach ($sampleReviews as $ri => $review) {
            foreach ([1, 2] as $k) {
                if ($likeCount >= 12) {
                    break 2;
                }
                $liker = $users[($ri + $k) % count($users)];
                if ($liker->id !== $review->user_id) {
                    $review->likes()->create(['user_id' => $liker->id]);
                    $review->increment('like_count');
                    $likeCount++;
                }
            }
        }

        // 收藏（前 10 家商户各被一位用户收藏）
        foreach (array_slice($shops, 0, 10) as $si => $shop) {
            \App\Models\Favorite::create([
                'user_id' => $users[$si % count($users)]->id,
                'shop_id' => $shop->id,
            ]);
        }

        // 团购订单（10 条，覆盖待支付/已支付/已使用三种状态）
        foreach ($demoDeals as $di => $deal) {
            $buyer = $users[($di + 2) % count($users)];
            $status = $di < 5 ? 1 : ($di < 8 ? 0 : 2); // 5 已支付 / 3 待支付 / 2 已使用
            $orderData = [
                'order_no' => 'RD'.now()->format('YmdHis').str_pad((string) ($di + 1), 4, '0', STR_PAD_LEFT),
                'user_id' => $buyer->id,
                'deal_id' => $deal->id,
                'quantity' => rand(1, 3),
                'total_amount' => $deal->price * rand(1, 2),
                'status' => $status,
                'paid_at' => $status >= 1 ? now()->subDays(rand(1, 5)) : null,
                'used_at' => $status === 2 ? now()->subDays(1) : null,
            ];
            // 已支付/已使用订单生成核销码（已使用的码已消费）
            if ($status >= 1) {
                $orderData['verify_code'] = strtoupper(\Illuminate\Support\Str::random(12));
            }
            \App\Models\DealOrder::create($orderData);
        }

        // 意见反馈（10 条，覆盖全部类型与登录/游客两种来源）
        $feedbackSeed = [
            ['suggest', '建议增加收藏夹分组', '收藏的商户越来越多，希望能按美食、娱乐等分组管理。'],
            ['suggest', '希望支持暗黑模式', '晚上刷点评时白色背景太刺眼，建议适配深色主题。'],
            ['complaint', '某商户团购券无法使用', '到店后商户表示不认识团购券，请平台核实处理。'],
            ['complaint', '疑似刷好评', '某商户点评内容高度雷同，疑似刷评，建议核查。'],
            ['bug', '搜索联想框遮挡', '手机端搜索联想下拉框会被底部导航遮挡一部分。'],
            ['bug', '头像上传偶发失败', '上传 1.5MB 头像偶尔提示失败，重试后成功。'],
            ['other', '如何注销账号', '想注销账号并删除个人数据，请问流程是怎样的？'],
            ['other', '商户信息修改申请', '店铺搬迁了新地址，想更新商户页面的地址信息。'],
            ['suggest', '希望上线城市增加', '我是苏州用户，期待 roavilo-glm 尽快开通苏州站。'],
            ['other', '商务合作咨询', '我们是连锁品牌，想咨询批量入驻和推广事宜。'],
        ];
        foreach ($feedbackSeed as $fi => [$type, $title, $content]) {
            \App\Models\Feedback::create([
                // 前半为登录用户（带邮箱），后半为游客（带手机号）
                'user_id' => $fi < 5 ? $users[$fi % count($users)]->id : null,
                'name' => $fi < 5 ? $users[$fi % count($users)]->name : '游客'.(100 + $fi),
                'contact' => $fi < 5 ? $users[$fi % count($users)]->email : '138'.str_pad((string) (10000000 + $fi * 1111), 8, '0', STR_PAD_LEFT),
                'type' => $type,
                'title' => $title,
                'content' => $content,
                'status' => $fi < 2 ? 1 : 0, // 前两条标记已处理
            ]);
        }

        $this->command->info('Seed 完成：测试账号 test@roavilo-glm.com / password123');

        // 资讯种子数据（平台动态/媒体报道/消费指南）
        $newsSeed = [
            ['news', 'roavilo-glm 正式上线，首批开通上海站', "roavilo-glm 本地生活服务平台正式上线。\n首期开通上海站，覆盖美食、休闲娱乐、丽人、酒店等 12 大品类。\n用户可发布真实点评、抢购商户团购券，欢迎体验并反馈意见。"],
            ['news', '商户中心数据看板全新上线', "本次更新为商户中心新增数据看板。\n店主可实时查看浏览量、评分趋势、团购销量与订单核销情况。\n团购管理支持上下架快捷切换，运营效率大幅提升。"],
            ['news', '核销码功能上线，到店消费更便捷', "用户购买团购并支付成功后，订单将自动生成 12 位核销码。\n到店出示核销码，商户在商户中心一键核销，全程无需纸质凭证。"],
            ['media', '本地生活赛道新玩家：roavilo-glm 以真实点评切入', "据行业媒体报道，新平台 roavilo-glm 近日正式运营。\n该平台主打一人一店一评的真实点评机制，杜绝水军刷评。\n分析认为，内容真实性将成为本地生活平台的差异化竞争点。", '科技消费日报'],
            ['media', 'roavilo-glm 发布商户诚信公约', "roavilo-glm 发布商户诚信公约，对虚假宣传、刷单刷评等行为作出明确约束。\n平台将建立违规处理机制，保障消费者权益。", '新商业观察'],
            ['guide', '如何写出一条有用的消费点评', "一条好的点评可以帮助其他消费者做出更好的决策。\n建议包含：到店时间与排队情况、招牌菜品口味、环境与服务、人均消费。\n配上真实照片会更直观，但请勿泄露他人隐私。"],
            ['guide', '团购券使用全攻略', "购买团购后请留意有效期，建议提前致电商户预约。\n到店出示核销码完成核销后订单即为已使用状态。\n未核销的已支付订单可联系客服申请退款。"],
            ['guide', '三步找到心仪好店', "第一步：切换到你所在的城市，按分类浏览或关键词搜索。\n第二步：利用评分、人均、区域筛选缩小范围，参考评分分布与真实点评。\n第三步：收藏心仪商户，购买团购前仔细阅读使用规则。"],
            ['guide', '退改规则速览：什么情况可以退款', "未支付订单无需处理，超时自动关闭。\n已支付但未核销的团购券，可在有效期内联系客服申请退款。\n已核销订单属于已完成消费，原则上不支持退款，特殊情形以客服裁定为准。"],
            ['news', '客服中心意见反馈通道升级', "本次更新后，游客也可通过客服中心提交意见反馈。\n登录用户提交的反馈将自动关联账号与邮箱，处理进度可在后续版本中查询。\n我们会在 1-3 个工作日内处理每一条反馈。"],
        ];
        foreach ($newsSeed as $i => $row) {
            [$category, $title, $content] = $row;
            $source = $row[3] ?? null;
            \App\Models\News::create([
                'category' => $category,
                'title' => $title,
                'content' => $content,
                'source' => $source,
                'view_count' => rand(100, 5000),
                // 最后一篇未发布（演示 is_published=false：列表不显示、详情 404）
                'is_published' => $i !== count($newsSeed) - 1,
                'created_at' => now()->subDays(count($newsSeed) - $i),
            ]);
        }
        $this->command->info('资讯 Seed 完成：'.count($newsSeed).' 篇');

        // ------------------------------------------------------------------
        // 统一标记 demo 数据：本次 seeder 产出的全部记录 is_demo = 1
        // 约定：业务代码不读写 is_demo；上线前按 is_demo=1 批量清理并删除字段。
        // Seeder 运行于空库/演示库，全表标记即覆盖本次全部产出。
        // ------------------------------------------------------------------
        $demoTables = [
            'users', 'cities', 'categories', 'regions', 'shops', 'shop_images',
            'reviews', 'review_replies', 'review_likes', 'favorites',
            'deals', 'deal_orders', 'feedbacks', 'news',
        ];
        foreach ($demoTables as $demoTable) {
            \DB::table($demoTable)->update(['is_demo' => true]);
        }
        $this->command->info('Demo 标记完成：'.count($demoTables).' 张表全部记录 is_demo=1');
    }
}
