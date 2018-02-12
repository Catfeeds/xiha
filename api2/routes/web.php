<?php

/*
   |--------------------------------------------------------------------------
   | Application Routes
   |--------------------------------------------------------------------------
   |
   | Here is where you can register all of the routes for an application.
   | It is  breeze. Simply tell Lumen the URIs it should respond to
   | and give it the Closure to call when that URI is requested.
   |
 */
// header("Cache-Control: no-cache, must-revalidate");

// 嘻哈学车v3版接口公共部分,v3根目录
$app->group(['namespace' => 'v3', 'prefix' => 'v3'], function () use ($app) {
    $app->post('pay/notify/{method}', ['uses' => 'PayController@notify']); // 支付异步回调通知
    $app->post('pay/biz/appoint', ['uses' => 'PayController@appointBiz']); // 预约计时付款通知
    $app->post('pay/biz/signup', ['uses' => 'PayController@signupBiz']); // 报名班制或驾校付款通知
});

// 嘻哈学车学员端v3
$app->group(['namespace'=>'v3\student','prefix'=>'v3/student'], function () use ($app) {

    // 转发所有的options请求
    $app->options('[/{a}[/{b}[/{c}[/{d}]]]]', ['uses' => 'RequestController@options']);
    /***********************************首页开始************************************/
    // 获取城市列表
    $app->get('citylist', ['uses'=>'IndexController@getCityList']);
    // 根据城市名称获取城市信息
    $app->get('city', ['uses'=>'IndexController@getCityInfoByName']);
    // 获取app升级信息
    $app->get('app/update', ['uses'=>'IndexController@getAppVersionInfo']);
    // 获取广告
    $app->get('ads/bannerlist', ['uses'=>'IndexController@getAdsBannerList']);
    // 获取首页推荐教练
    $app->get('recommendcoachlist', ['uses'=>'SignupController@getIndexRecommendCoachList']);
    // 获取首页学车套餐信息
    $app->get('homelist', ['uses'=>'SignupController@getIndexShiftsList']);
    // 获取学车套餐列表
    $app->get('shiftslist', ['uses'=>'SignupController@getPackageShiftsList']);
    // 获取学车套餐详情
    $app->get('shifts/detail', ['uses'=>'SignupController@getPachageShiftsDetail']);
    // 获取首页推荐驾校
    $app->get('recommendschoollist', ['uses'=>'SignupController@getIndexRecommendSchoolList']);
    // 获取首页的图标和文字
    $app->get('ads/iconlist', ['uses'=>'IndexController@getIndexIcon']);
    /***********************************首页结束************************************/

    /***********************************驾考开始************************************/
    // 2.驾考
    $app->get('exam/setting', [
        'uses'=>'DriveController@getQuestionSetting'
    ]); // 题库设置(web)

    $app->get('exam/getsettinglist', [
        'uses'=>'DriveController@getQuestionLicenseList'
    ]); // 获取题库牌照信息(移动端)

    $app->get('exam/questioncount', [
        'uses'=>'DriveController@getQuestionCount'
    ]); // 获取题数(移动端)

    $app->get('exam/questionIds', [
        'uses'=>'DriveController@getQuestionIdsList'
    ]); // 获取章节|顺序|专项|随机|模拟练习的题目id

    $app->get('exam/collectionids', [
        'uses'=>'DriveController@getCollectionIdsList'
    ]); // 获取错误收藏ID

    $app->post('exam/questions', [
        'uses'=>'DriveController@getQuestionsList'
    ]); // 获取题目详情列表
    $app->get('exam/questions', [
        'uses'=>'DriveController@getQuestionsList'
    ]); // 获取题目详情列表

    $app->get('exam/chapters', [
        'uses'=>'DriveController@getChaptersList'
    ]); // 获取章节列表

    $app->get('exam/special', [
        'uses'=>'DriveController@getSpecialList'
    ]); // 获取专项列表

    $app->get('exam/examrecords', [
        'uses'=>'DriveController@getMyExamRecords'
    ]); // 获取我的考试记录

    $app->get('exam/submit', [
        'uses'=>'DriveController@submitMyExamRecords'
    ]); // 提交我的考试成绩记录

    $app->get('exam/userQuestionIds', [
        'uses'=>'DriveController@getUserQuestionIds'
    ]); // 获取我的错题|收藏的题目ID

    $app->post('exam/collection', [
        'uses'=>'DriveController@submitCollectionExam'
    ]); // 添加收藏的题目

    $app->post('exam/error', [
        'uses'=>'DriveController@submitErrorExam'
    ]); // 添加错误的题目

    $app->get('exam/wrongcollection', [
        'uses'=>'DriveController@getErrorCollectionList'
    ]); // 获取我的错题|收藏列表

    $app->get('exam/cancel', [
        'uses'=>'DriveController@cancelErrorCollection'
    ]); // 取消收藏题目

    $app->get('exam/video', [
        'uses'=>'DriveController@getVideoList'
    ]); // 获取学车视频列表

    $app->get('exam/videodetail', [
        'uses'=>'DriveController@getVideoDetail'
    ]); // 获取学车视频详情

    $app->post('exam/videocomments', [
        'uses'=>'DriveController@submitVideoComments'
    ]); // 添加学车视频的评论

    $app->post('exam/like', [
        'uses'=>'DriveController@submitLike'
    ]); // 给学车视频的评论点赞

    $app->get('exam/views', [
        'uses'=>'DriveController@increaseViews'
    ]); // 添加视频浏览量

    $app->get('exam/commentslist', [
        'uses'=>'DriveController@getUserQuestionsList'
    ]); // 获取题目的评论列表

    $app->post('exam/submitcomments', [
        'uses'=>'DriveController@submitQuestionComments'
    ]); // 添加题目的评论

    $app->post('exam/likecomment', [
        'uses'=>'DriveController@submitQuestionLike'
    ]); // 给学车视频的评论点赞

    $app->get('exam/ranking/{car_type}/{course}', ['middleware'=>'auth','uses'=>'ExamController@ranking']);//成绩排行

    /***********************************驾考结束************************************/

    /************************************预约开始************************************/
    // 获取驾校详情
    $app->get('signup/school/schooldetail', ['uses'=>'SignupController@getSchoolMessage']);
    // 获取教练列表
    $app->get('coach/index', ['uses'=>'CoachController@index']);
    // 获取教练名片
    $app->get('coach/detail', ['uses'=>'CoachController@getCoachDetail']);
    //　获取教练详情页－电子教练服务
    $app->get('coach/elecoach/service', ['uses'=>'CoachController@getElecoach']);
    // 获取教练详情页－驾考服务
    $app->get('coach/shifts/service', ['uses'=>'CoachController@getShifts']);
    // 获取教练全部评论
    $app->get('comment/more', ['uses'=>'CommentController@getMoreComments']);
    // 获取教练设置的班制列表
    $app->get('coach/coachshiftslist', ['middleware'=>'auth','uses'=>'ShiftsController@getCoachShiftsList']);
    // 报名班制订单提交
    $app->get('order/{order_type}/submit', ['middleware'=>'auth','uses'=>'OrderController@submitOrder']);

    // 取消预约订单
    $app->post('order/cancel', ['middleware'=>'auth','uses'=>'OrderController@cancelOrder']);
    // 评价预约订单
    $app->post('order/comment', ['middleware'=>'auth','uses'=>'OrderController@commentOrder']);
    // 获取驾校班制列表
    $app->get('school/shiftslist/{school_id}', ['uses'=>'SignupController@getSchoolShiftsById']);
    /************************************预约结束***********************************/

    /************************************订单开始***********************************/
    // 获取支付方式
    $app->get('order/paylist', ['uses'=>'OrderController@getPayMethods']);
    // 订单支付总入口
    $app->post('order/pay', ['middleware' => 'auth','uses'=>'OrderController@payOrder']);
    /************************************订单结束***********************************/

    /************************************我的开始***********************************/
    // 新用户注册
    $app->post('ucenter/register', ['uses'=>'UserController@register']);
    // 获取验证码-注册
    $app->get('ucenter/smscode/student/reg', ['middleware'=>'entrance','uses'=>'UserController@regSmsCode']);
    // 获取验证码-忘记密码
    $app->get('ucenter/smscode/student/forgetpass', ['middleware'=>'entrance','uses'=>'UserController@forgotSmsCode']);
    // 手机密码登录
    $app->post('ucenter/login', ['uses'=>'UserController@login']);
    // 第三方登录
    $app->post('ucenter/thirdlogin/{user_type}', ['uses'=>'UserController@thirdLogin']);
    // 个人中心首页
    $app->get('ucenter/my/index', ['middleware'=>'auth','uses'=>'UserController@myIndex']);
    // 我的个人资料
    $app->get('ucenter/profile', ['middleware'=>'auth','uses'=>'UserController@profile']);
    // 修改个人资料
    $app->post('ucenter/upprofile', ['middleware'=>'auth','uses'=>'UserController@updateProfile']);
    // 学车报告
    $app->get('ecoach/report', ['middleware'=>'auth','uses'=>'EcoachController@report']);
    // 电子教练
    $app->get('ecoach/examresults', ['middleware'=>'auth','uses'=>'EcoachController@examresults']);
    // 获取科目列表
    $app->get('ucenter/lessonItems', ['uses'=>'UserController@lessonItems']);
    // 获取牌照类型列表
    $app->get('exam/license/list', ['uses'=> 'ExamController@licenseList']);
    // 获取题库题目总数
    $app->get('exam/sum', ['uses'=>'ExamController@sum']);
    // 我的驾校
    $app->get('ucenter/my_school', ['middleware'=>'auth','uses'=>'SignupController@getMySchool']);
    // 获取我的预约计时订单
    $app->get('order/appointlist', ['middleware'=>'auth','uses'=>'OrderController@getMyAppointOrdersList']);
    // 获取我的报名班制订单
    $app->get('order/signuplist', ['middleware'=>'auth','uses'=>'OrderController@getMySignupOrdersList']);
    // 消息中心
    $app->get('ucenter/message/list', ['middleware'=>'auth','uses'=>'UserController@getMessageList']);
    // 读消息
    $app->post('ucenter/message/read', ['middleware'=>'auth','uses'=>'UserController@readMessage']);
    // 分享
    $app->get('ucenter/share/app', ['uses'=>'UserController@shareApp']);
    // 设置-意见反馈
    $app->post('ucenter/feedback', ['middleware'=>'auth','uses'=>'UserController@feedback']);
    // 刷新用户token
    $app->post('ucenter/refresh', ['middleware'=>'auth:no','uses'=>'UserController@refresh']);
    // 修改密码
    $app->post('ucenter/changepass', ['middleware'=>'auth', 'uses'=>'UserController@changepass']);
    // 获取我的二维码
    $app->get('ucenter/myqrcode', ['middleware'=>'auth', 'uses'=>'UserController@getMyQrcode']);
    // 获取订单取消原因
    $app->get('order/cancelreason/{type}', ['uses'=>'OrderController@getCancelReason']);
    // 获取题库下载地址
    $app->get('ucenter/downloadtiku/{version}', ['uses'=>'UserController@tikuBase']);
    /************************************我的结束**********************************/
    $app->get('demo/apisafety', ['uses'=>'DemoController@apiSafety']);
    $app->get('demo/getTikuVersion', ['uses'=>'DemoController@getTikuVersion']);
});

// 嘻哈学车教练端v2
$app->group(['namespace' => 'v2', 'prefix' => 'v2'], function () use ($app) {

    // 计时设置
    $app->get('coach/template/timetemp', [
        'middleware' => 'auth', 'uses' => 'CoachController@getCoachTimeTemplate'
    ]); // 获取教练时间模板
    $app->post('coach/template/handletimetemp', [
        'middleware' => 'auth', 'uses' => 'CoachController@handleCoachTimeTemplate'
    ]); // 添加|编辑时间模板
    $app->delete('coach/template/deltimetemp', [
        'middleware' => 'auth', 'uses' => 'CoachController@deleteCoachTimeTemplate'
    ]); // 删除时间模板


    // 学员
    $app->get('coach/coachuserlist', [
        'middleware' => 'auth', 'uses' => 'StudentController@getCoachUserList'
    ]); // 获取教练导入|添加的学员
});

// api2目录下的嘻哈学车接口v1版本
$app->group(['namespace' => 'v1', 'prefix' => 'v1'], function () use ($app) {

    // 首页
    $app->get(
        'citylist',
        [
            'uses'       => 'IndexController@getCityList'
        ]
    );  // 城市列表(包含热门城市)
    $app->get(
        'city',
        [
            'uses'       => 'IndexController@getCityInfoByName'
        ]
    ); // 获取首页推荐教练列表
    $app->get(
        'recommendcoachlist',
        [
            'uses'       => 'SignupController@getIndexRecommendCoachList'
        ]
    ); // 获取首页推荐教练列表
    $app->get(
        'recommendschoollist',
        [
            'uses'       => 'SignupController@getIndexRecommendSchoolList'
        ]
    ); // 获取首页推荐驾校列表
    $app->get(
        'homelist',
        [
            'uses'       => 'SignupController@getIndexShiftsList'
        ]
    ); // 获取学员端首页列表
    $app->get(
        'shiftslist',
        [
            'uses'       => 'SignupController@getPackageShiftsList'
        ]
    ); // 获取学员端学车套餐列表
    $app->get(
        'signup/shifts/detail',
        [
            'uses'       => 'SignupController@getPackageShiftsDetail'
        ]
    ); // 获取学车套餐的详情
    $app->get(
        'app/update',
        [
            'uses'       => 'IndexController@getAppVersionInfo'
        ]
    ); // 获取app升级信息
    $app->get(
        'ads/bannerlist',
        [
            'uses'       => 'IndexController@getAdsBannerList'
        ]
    ); // 获取首页banner轮播图（给出是否显示嘻哈券领取入口）
    $app->get(
        'ads/iconlist',
        [
            'uses'       => 'IndexController@getIndexIcon'
        ]
    ); // 获取首页图标和文字

    // 题库
    $app->get(
        'exam/sum',
        [
            'uses'       => 'ExamController@sum'
        ]
    ); // 牌照，科目(一，四)对应的题目总数
    $app->get(
        'exam/license/list',
        [
            'uses'       => 'ExamController@licenseList'
        ]
    ); // 牌照类型列表
    $app->get(
        'exam/videolist',
        [
            'uses'       => 'ExamController@getSubjectsInfo'
        ]
    ); // 牌照类型列表
    $app->get(
        'exam/lightlist',
        [
            'uses'       => 'ExamController@getLightsList'
        ]
    ); // 灯光列表

    // 报名
    $app->get(
        'signup/schoollist',
        [
            'uses'       => 'SignupController@getSchoolList'
        ]
    ); // 驾校列表
    $app->get(
        'signup/coach/detail',
        [
            'uses'       => 'SignupController@getCoachDetail'
        ]
    ); // 获取教练详情
    $app->get(
        'signup/trainlist',
        [
            'uses'       => 'SignupController@getTrainList'
        ]
    ); // 获取更多报名点
    $app->get(
        'comment/more',
        [
            'uses'       => 'CommentController@getMoreComments'
        ]
    ); // 获取教练名片,驾校评论或者帖子评论的更多评论
    $app->get(
        'signup/coachlist',
        [
            'uses'       => 'SignupController@getCoachList'
        ]
    ); // 教练列表
    $app->get(
        'signup/school/detail',
        [
            'uses'       => 'SignupController@getSchoolInfo'
        ]
    ); // 驾校详情
    $app->get(
        'signup/school/schooldetail',
        [
            'uses'       => 'SignupController@getSchoolMessage'
        ]
    ); // 驾校详情（新）
    $app->get(
        'signup/school/shiftslist',
        [
            'uses'       => 'SignupController@getShiftsList'
        ]
    ); // 获取驾校下的班制列表
    $app->get(
        'signup/school/shiftsdetail',
        [
            'uses'       => 'SignupController@getSchoolShiftsDetail'
        ]
    ); // 获取驾校班制详情
    $app->get(
        'signup/school/coachlist',
        [
            'uses'       => 'SignupController@getCoachListInSchool'
        ]
    ); // 获取驾校下的教练列表
    $app->get(
        'signup/school/shareschoolshifts',
        [
            'uses'       => 'SignupController@shareShifts'
        ]
    ); // 分享报名班制
    $app->get(
        'signup/school/sharepackageshifts',
        [
            'uses'       => 'SignupController@sharePackageShifts'
        ]
    ); // 分享学车套餐
    $app->get(
        'option/list',
        [
            'uses'       => 'SystemController@getOptionList'
        ]
    ); // 获取搜索教练时的选项列表

    // 用户中心
    $app->get(
        'ucenter/smscode/{user_type}/{operation}',
        [
            'middleware' => 'entrance',
            'uses'       => 'UserController@getSmsCode'
        ]
    ); // 获取短信验证码
    $app->post(
        'ucenter/register/{user_type}',
        [
            'uses'       => 'UserController@register'
        ]
    ); // 注册新用户接口
    $app->post(
        'ucenter/login/{user_type}',
        [
            'uses'       => 'UserController@login'
        ]
    ); // 用户登陆
    $app->post(
        'ucenter/thirdlogin/{user_type}',
        [
            'uses'       => 'UserController@thirdLogin'
        ]
    ); // 微信，QQ第三方登陆
    $app->post(
        'ucenter/userthirdlogin/{user_type}',
        [
            'uses'       => 'UserController@userThirdLogin'
        ]
    ); // 微信，QQ第三方登陆 ( coach )
    $app->post(
        'ucenter/refresh',
        [
            'middleware' => 'auth:no',
            'uses'       => 'UserController@refresh'
        ]
    ); // 刷新用户token
    $app->get(
        'ucenter/my/index',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@myIndex'
        ]
    ); // 我的首页
    $app->get(
        'ucenter/profile/{user_type}',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@profile'
        ]
    ); // 我的个人资料
    $app->post(
        'ucenter/profile',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@updateProfile'
        ]
    ); // 更改个人资料
    $app->post(
        'ucenter/profile/coach',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@updateCoachProfile'
        ]
    ); // 更改教练个人资料
    $app->post(
        'ucenter/coach/car',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@updateCoachCarInfo'
        ]
    ); // 更改教练车辆信息
    $app->get(
        'ucenter/qrcode/{user_type}',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@getUserQrcode'
        ]
    ); // 获取用户的二维码信息
    $app->get(
        'ucenter/share/app',
        [
            'uses'       => 'UserController@shareApp'
        ]
    ); // 分享嘻哈学车App
    $app->get(
        'ucenter/share/signup',
        [
            'uses'       => 'UserController@shareSignup'
        ]
    ); // 分享报名成功后的页面
    $app->get(
        'ucenter/couponlist',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@getMyCouponList'
        ]
    ); // 我的优惠券
    $app->post(
        'coupon/exchange',
        [
            'uses'       => 'UserController@exchangeCoupon'
        ]
    ); // 优惠券兑换,领取

    $app->get(
        'ucenter/message/list',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@getMessageList'
        ]
    ); // 获取消息列表
    $app->post(
        'ucenter/message/read',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@readMessage'
        ]
    ); // 读消息
    $app->delete(
        'ucenter/message',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@deleteMessage'
        ]
    ); // 删除消息
    $app->get(
        'ucenter/my_coach',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@getMyCoach'
        ]
    ); // 获取我的教练
    $app->get(
        'ucenter/my_school',
        [
            'middleware' => 'auth',
            'uses'       => 'SignupController@getMySchool'
        ]
    ); // 获取我的驾校
    $app->post(
        'ucenter/bind_coach',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@bindCoach'
        ]
    ); // 申请绑定教练
    $app->post(
        'ucenter/unbind_coach',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@unbindCoach'
        ]
    ); // 解除绑定教练
    $app->post(
        'ucenter/feedback',
        [
            'uses'       => 'UserController@feedback'
        ]
    );
    $app->get(
        'ucenter/examrecords',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@getMyExamRecords'
        ]
    );
    $app->post(
        'ucenter/changepass',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@changepass'
        ]
    ); // 修改密码
    $app->post(
        'ucenter/forgetpass',
        [
            'uses'       => 'UserController@forgetpass'
        ]
    ); // 忘记密码是在非登录状态，通过验证码找回密码
    $app->get(
        'ucenter/qrcode',
        [
            'middleware' => 'auth',
            'uses'       => 'UserController@qrcode'
        ]
    ); // 获取二维码

    // 号外
    $app->get(
        'news',
        [
            'uses'       => 'NewsController@getIndexArticleList'
        ]
    );   //  获取嘻哈号外首页的分类列表
    $app->get(
        'category',
        [
            'uses'       => 'NewsController@getArticleList'
        ]
    );   //  获取嘻哈号外分类下的文章列表
    $app->get(
        'article/detail',
        [
            'uses'       => 'NewsController@getArticleDetail'
        ]
    );   //  获取嘻哈号外的文章内容
    $app->options(
        'article/detail',
        [
            'uses'       => 'NewsController@optionsArticleDetail'
        ]
    ); // 避免请求文章详情时多发出的一个options请求
    $app->get(
        'question/detail',
        [
            'uses'       => 'NewsController@getQuestionDetail'
        ]
    );   //  获取嘻哈号外问题（帖子）内容
    $app->get(
        'news/graduate',
        [
            'uses'       => 'NewsController@getGraduateInfo'
        ]
    );   //  获取嘻哈号外的文章内容
    $app->post(
        'article/disorlike',
        [
            'middleware' => 'auth',
            'uses'       => 'NewsController@getArticledisOrLike'
        ]
    );   //  获取文章点赞，取消点赞和查询点赞

    // 订单
    $app->get(
        'order/signuplist',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@getMySignupOrdersList'
        ]
    );   // 获取我的报名班制订单列表
    $app->get(
        'order/appointlist',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@getMyAppointOrdersList'
        ]
    );   // 获取我的预约计时订单列表
    $app->post(
        'order/{order_type}/submit',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@submitOrder'
        ]
    );   //  提交报名班制，计时培训的订单
    $app->post(
        'order/pay',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@payOrder'
        ]
    );   // 支付总入口
    $app->post(
        'order/notify/wechatpay',
        [
            'uses'       => 'WechatpayController@notify'
        ]
    );   //  微信支付异步回调
    $app->post(
        'order/notify/alipay',
        [
            'uses'       => 'AlipayController@notify'
        ]
    );   //  支付宝支付异步回调
    $app->post(
        'order/notify/unionpay',
        [
            'uses'       => 'UnionpayController@notify'
        ]
    );   //  银联支付异步回调
    $app->post(
        'order/{order_type}/refund',
        [
            'middleware' => 'entrance',
            'uses'       => 'OrderController@refund'
        ]
    );
    $app->post(
        'order/{order_type}/query',
        [
            'middleware' => 'entrance',
            'uses'       => 'OrderController@query',
        ]
    );
    $app->post(
        'order/{order_type}/queryRefund',
        [
            'middleware' => 'entrance',
            'uses'       => 'OrderController@queryRefund',
        ]
    );
    $app->get(
        'order/paylist',
        [
            'uses'       => 'OrderController@getPayMethods'
        ]
    );   //  获取支付列表

    $app->get(
        'coach/timelist',
        [
            'middleware' => 'auth',
            'uses'       => 'CoachController@getCoachTimeList'
        ]
    ); // 获取教练时间设置
    $app->get(
        'coach/datelist',
        [
            'middleware' => 'auth',
            'uses'       => 'CoachController@getCoachDateList'
        ]
    ); // 获取教练从今天开始的日期设置

    $app->get(
        'order/cancelreason/{type}',
        [
            'uses'       => 'OrderController@getCancelReason'
        ]
    ); // 取消原因列表
    $app->post(
        'order/cancel',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@cancelOrder'
        ]
    ); // 取消订单(包括两种订单，分别是预约学车和报名驾校)
    $app->post(
        'order/comment',
        [
            'middleware' => 'auth',
            'uses'       => 'OrderController@commentOrder'
        ]
    ); // 评论(包括两种订单，分别是预约学车和报名驾校)
    $app->post(
        'order/timeout',
        [
            'uses'       => 'OrderController@timeoutAppointTimeOrder'
        ]
    ); // 预约计时订单超时，仅供内部(swoole server)调用 (取消和关闭)
    $app->post(
        'message/send',
        [
            'uses'      => 'OrderController@sendMessage'
        ]
    ); // 发送队列中的消息

    // 教练
    // $app->get(
    //     'coach/coachorderinfo',
    //     [
    //         'middleware' => 'auth',
    //         'uses'      => 'CoachinfoController@getCoachOrdersList'
    //     ]
    // ); // 获取预约计时(属对应教练) | 报名班制(教练设置)的部门是信息


    // 学员考试安排
    $app->get(
        'coach/examarrangmentlist',
        [
            'middleware'=> 'auth',
            'uses'      => 'CoachIndexController@getExamArrangementList'
        ]
    ); // 获取考试安排列表
    $app->post(
        'coach/operatestudentexam',
        [
            'middleware'=> 'auth',
            'uses'      => 'CoachIndexController@handleStudentExam'
        ]
    ); // 添加|编辑学员的考试安排
    $app->delete(
        'coach/delstudentexam',
        [
            'middleware'=> 'auth',
            'uses'      => 'CoachIndexController@deleteStuExamArrangment'
        ]
    ); // 删除学员的考试安排


    // 教练班制的设置
    $app->get(
        'coach/coachshiftslist',
        [
            'middleware'=> 'auth',
            'uses'      => 'ShiftsController@getCoachShiftsList'
        ]
    ); // 获取教练设置的班制列表
    $app->get(
        'coach/coachshiftsdetail',
        [
            'middleware'=> 'auth',
            'uses'      => 'ShiftsController@getCoachShiftsDetail'
        ]
    ); // 获取教练的班制详情
    $app->post(
        'coach/handleshifts',
        [
            'middleware'=> 'auth',
            'uses'      => 'ShiftsController@handleCoachShifts'
        ]
    ); // 添加|编辑教练的班制
    $app->delete(
        'coach/delcoachshifts',
        [
            'middleware' => 'auth',
            'uses'       => 'ShiftsController@deleteCoachShifts'
        ]
    ); // 删除教练班制


    // 优惠券(教练)
    $app->get(
        'coach/couponlist',
        [
            'middleware'=> 'auth',
            'uses'      => 'CouponController@getCoachCouponList'
        ]
    ); // 获取教练的优惠券列表
    $app->post(
        'coupon/couponcodelist',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@getCouponCodeList'
        ]
    ); // 生成兑换码
    $app->post(
        'coupon/handlecoupon',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@handleCoachCoupon'
        ]
    ); // 教练添加或者编辑优惠券
    $app->get(
        'coupon/couponinfo',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@getCouponInfo'
        ]
    ); // 获取优惠券信息
    $app->delete(
        'coupon/delcoupon',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@deleteCoupon'
        ]
    ); // 教练删除优惠券
    $app->post(
        'coupon/couponstatus',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@setCouponStatus'
        ]
    ); // 设置教练的优惠券发布状态
    $app->get(
        'coupon/usercouponlist',
        [
            'middleware' => 'auth',
            'uses'       => 'CouponController@getUserCouponList'
        ]
    ); // 获取领券人列表
    $app->get(
        'coupon/provincelist',
        [
            'uses'       => 'CouponController@getProvinceList'
        ]
    ); // 获取省的相关信息
    $app->get(
        'coupon/citylist',
        [
            'uses'       => 'CouponController@getCityList'
        ]
    ); // 获取市的相关信息
    $app->get(
        'coupon/arealist',
        [
            'uses'       => 'CouponController@getAreaList'
        ]
    ); // 获取地区的相关信息


    // 模拟成绩
    $app->get(
        'coach/examrecords',
        [
            'middleware' => 'auth',
            'uses'       => 'CoachIndexController@getStudentExamRecords'
        ]
    ); // 获取与学员的模拟成绩(与教练是绑定关系)



    /**
     * 以下是一些非api接口的工具
     */
    $app->post(
        'toolkit/batchcoach',
        [
            'uses'       => 'CommandController@batchCoach'
        ]
    ); // 批量注册教练

    /**
     * Demo控制器
     */
    $app->get(
        'demo/sign',
        [
            'uses'       => 'DemoController@apiSafety'
        ]
    ); // 验证接口安全性
    $app->post(
        'demo/sms',
        [
            'uses'       => 'DemoController@sms'
        ]
    );
    $app->get('demo/qrcode', ['uses' => 'DemoController@qrcode']);

    /**
     *----------------------------------------------------------------------
     * 嘻哈钱包 XihapayController
     *----------------------------------------------------------------------
     *
     * 第三方支付账户绑定
     * 银行账户绑定
     * 支付绑定查询
     * 余额查询
     * 提现申请
     * 提现执行
     * 提现进度查询
     * 对账单下载
     *
     */
    $app->post(
        'xihapay/promotion/transfer',
        [
            'middleware' => 'entrance, auth',
            'uses'       => 'XihapayController@promotionTransfer',
        ]
    );
    $app->post(
        'xihapay/promotion/transfer/query',
        [
            'middleware' => 'entrance, auth',
            'uses'       => 'XihapayController@queryTransfer',
        ]
    );
    $app->post(
        'xihapay/billdownload',
        [
            'middleware' => 'entrance, auth',
            'uses'       => 'XihapayController@billDownload',
        ]
    );

    /**
     *----------------------------------------------------------------------
     * 电子教练模拟考试记录
     *----------------------------------------------------------------------
     *
     * 1 上传记录
     * 2 按日期，身份证号获取当天的模拟考试记录
     */
    $app->post(
        'ecoach/uploadexam',
        [
            'uses'       => 'EcoachController@uploadexam'
        ]
    );
    $app->get(
        'ecoach/examresults',
        [
            'middleware' => 'auth',
            'uses'       => 'EcoachController@examresults'
        ]
    );
    $app->post(
        'ecoach/uploadtrain',
        [
            'uses'       => 'EcoachController@uploadtrain'
        ]
    );
    $app->get(
        'ecoach/report',
        [
            'middleware' => 'auth',
            'uses'       => 'EcoachController@report'
        ]
    );
    $app->get(
        'ecoach/report/dev',
        [
            'middleware' => 'auth',
            'uses'       => 'EcoachController@reportDev'
        ]
    );
});
