<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\News;
use Illuminate\Http\Request;

/**
 * 页面控制器
 * 承担全站静态信息页与客服中心（对标点评平台 footer 全部链接，避免 404）
 * - /about      关于我们
 * - /help       帮助中心（FAQ）
 * - /kf         客服中心（热线/FAQ/意见反馈）
 * - /app        App 下载页
 * - /agreement  用户协议
 * - /privacy    隐私政策
 * - /merchant-convention 商户诚信公约
 * - /promote    推广服务介绍
 */
class PageController extends Controller
{
    /** 帮助中心 FAQ 数据（按分类组织，后续可迁数据库） */
    private const FAQS = [
        '账号相关' => [
            ['如何注册 roavilo 账号？', '点击页面右上角「注册」，使用邮箱即可完成注册，全程免费。'],
            ['忘记密码怎么办？', '在登录页点击「忘记密码」，输入注册邮箱即可获取重置链接（1 小时内有效）。'],
            ['如何修改个人资料？', '登录后进入「个人中心 → 个人资料」，可修改昵称、上传头像、编辑签名。'],
        ],
        '点评与商户' => [
            ['如何发布点评？', '进入商户详情页，在「写点评」区域选择星级、填写体验内容即可发布，每个商户每个账号限发布一条（可更新）。'],
            ['发现违规内容如何举报？', '可通过客服中心「意见反馈」提交投诉举报，我们将在 1-3 个工作日内处理。'],
            ['商户信息有误怎么办？', '可在该商户详情页提交反馈，或联系商户中心由店主自行更新。'],
        ],
        '团购与订单' => [
            ['团购券如何使用？', '支付成功后订单会生成 12 位核销码，到店出示给商户，商户输入核销码完成核销。'],
            ['如何申请退款？', '未核销的已支付订单可联系客服热线申请退款；已核销订单不支持退款。'],
            ['订单状态说明？', '待支付（需完成支付）→ 已支付（可到店核销）→ 已使用（核销完成）；另有已退款/已取消状态。'],
        ],
        '商户入驻' => [
            ['如何入驻 roavilo？', '登录后进入「商户中心 → 入驻新店铺」，填写店铺信息提交，管理员审核通过后即可上架展示。'],
            ['入驻需要什么资质？', '需要提供真实的店铺名称、地址与联系方式；营业执照等资质材料将在正式运营阶段要求上传。'],
        ],
    ];

    /**
     * 关于我们
     */
    public function about()
    {
        return view('pages.about');
    }

    /**
     * 帮助中心（FAQ 分类展示）
     */
    public function help()
    {
        return view('pages.help', ['faqs' => self::FAQS]);
    }

    /**
     * 客服中心（对标 kf.dianping.com 结构：热线 + FAQ + 意见反馈）
     */
    public function kf()
    {
        return view('pages.kf', ['faqs' => self::FAQS]);
    }

    /**
     * 提交意见反馈（登录用户自动关联账号，游客可填写联系方式）
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:'.implode(',', array_keys(Feedback::TYPE_LABELS)),
            'title' => 'required|string|max:100',
            'content' => 'required|string|min:5|max:2000',
            'name' => 'nullable|string|max:50',
            'contact' => 'nullable|string|max:100',
        ], [
            'content.min' => '反馈内容至少 5 个字',
            'type.in' => '请选择正确的反馈类型',
        ]);

        // 游客必须留联系方式，登录用户自动记录 user_id
        if (! $request->user() && empty($validated['contact'])) {
            return back()->withErrors(['contact' => '请填写联系方式，方便我们回复您']);
        }

        Feedback::create([
            'user_id' => $request->user()?->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'name' => $validated['name'] ?? ($request->user()?->name),
            'contact' => $validated['contact'] ?? ($request->user()?->email),
        ]);

        return back()->with('success', '反馈已提交，我们会在 1-3 个工作日内处理，感谢您的支持！');
    }

    /**
     * App 下载页（Android/iOS 预留，对标点评平台「应用下载」）
     */
    public function app()
    {
        return view('pages.app');
    }

    /**
     * 用户协议
     */
    public function agreement()
    {
        return view('pages.agreement');
    }

    /**
     * 隐私政策
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * 商户诚信公约
     */
    public function merchantConvention()
    {
        return view('pages.merchant-convention');
    }

    /**
     * 推广服务（商户广告投放介绍）
     */
    public function promote()
    {
        return view('pages.promote');
    }

    /**
     * 知识产权声明（对标 dianping footer《知识产权声明》）
     */
    public function copyright()
    {
        return view('pages.copyright');
    }

    /**
     * 网关欢迎响应（union.roavilo.com，对应 union.dianping.com 的 API 网关行为）
     */
    public function unionGateway()
    {
        return response()->json(['code' => 200, 'msg' => 'WelCome roavilo union gateway!']);
    }

    /**
     * 招聘页（对标 hr.dianping.com 人才招聘）
     */
    public function jobs()
    {
        // 招聘职位列表（静态数据，正式运营后可迁数据库）
        $jobs = [
            ['title' => '高级 PHP 开发工程师', 'dept' => '技术部', 'location' => '上海', 'type' => '全职',
             'req' => '3 年以上 PHP/Laravel 经验，熟悉 MySQL 优化与高并发架构，有本地生活类产品经验优先。'],
            ['title' => '前端开发工程师', 'dept' => '技术部', 'location' => '上海', 'type' => '全职',
             'req' => '熟练掌握 Vue/React，对响应式布局、移动端 H5 性能优化有实战经验。'],
            ['title' => '商户运营专员', 'dept' => '运营部', 'location' => '上海', 'type' => '全职',
             'req' => '负责本地商户的拓展与维护，有 O2O 平台商户运营经验者优先，抗压能力强。'],
            ['title' => '内容审核专员', 'dept' => '运营部', 'location' => '上海', 'type' => '全职',
             'req' => '负责点评内容审核与社区氛围维护，细心负责，熟悉本地生活内容生态。'],
            ['title' => 'UI 设计师', 'dept' => '设计部', 'location' => '上海', 'type' => '全职',
             'req' => '负责三端产品视觉与交互设计，作品集需包含移动端项目。'],
            ['title' => '产品经理（社区方向）', 'dept' => '产品部', 'location' => '上海', 'type' => '全职',
             'req' => '负责点评社区与用户激励体系的产品规划，数据分析能力强。'],
        ];

        return view('pages.jobs', compact('jobs'));
    }

    /**
     * 联系我们页
     */
    public function contact()
    {
        return view('pages.contact');
    }

    /**
     * 资讯列表页（支持分类筛选）
     */
    public function newsList(Request $request)
    {
        $query = News::published()->latest();

        // 分类筛选：news 平台动态 / media 媒体报道 / guide 消费指南
        if ($category = $request->query('category')) {
            $query->where('category', $category);
        }

        $news = $query->paginate(15)->withQueryString();

        return view('news.index', compact('news'));
    }

    /**
     * 资讯详情页
     */
    public function newsShow(News $news)
    {
        abort_unless($news->is_published, 404);
        $news->increment('view_count');

        // 相关推荐：同分类其他资讯
        $related = News::published()
            ->where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(5)
            ->get();

        return view('news.show', compact('news', 'related'));
    }
}
