<?php
/**
 * tpshop
 * ============================================================================
 * * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * 2015-11-21
 */
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------

    // 应用命名空间
    'app_namespace'          => 'app',
    // 应用调试模式
    'app_debug'              => true,
    // 应用Trace
    'app_trace'              => false,
    // 应用模式状态
    'app_status'             => '',
    // 是否支持多模块
    'app_multi_module'       => true,
    // 入口自动绑定模块
    'auto_bind_module'       => false,
    // 注册的根命名空间
    'root_namespace'         => [],
    // 扩展函数文件
    'extra_file_list'        => [THINK_PATH . 'helper' . EXT,APP_PATH.'function.php'],
    // 默认输出类型
    'default_return_type'    => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'    => 'html',
    // 默认JSONP格式返回的处理方法
    'default_jsonp_handler'  => 'jsonpReturn',
    // 默认JSONP处理方法
    'var_jsonp_handler'      => 'callback',
    // 默认时区
    'default_timezone'       => 'PRC',
    // 是否开启多语言
    'lang_switch_on'         => false,
    // 默认全局过滤方法 用逗号分隔多个
    'default_filter'         => 'strip_sql,htmlspecialchars',
    // 默认语言
    'default_lang'           => 'zh-cn',
    // 应用类库后缀
    'class_suffix'           => false,
    // 控制器类后缀
    'controller_suffix'      => false,

    // +----------------------------------------------------------------------
    // | 模块设置
    // +----------------------------------------------------------------------

    // 默认模块名
    'default_module'         => 'home',
    // 禁止访问模块
    'deny_module_list'       => ['common'],
    // 默认控制器名
    'default_controller'     => 'Index',
    // 默认操作名
    'default_action'         => 'index',
    // 默认验证器
    'default_validate'       => '',
    // 默认的空控制器名
    'empty_controller'       => 'Error',
    // 操作方法后缀
    'action_suffix'          => '',
    // 自动搜索控制器
    'controller_auto_search' => false,

    // +----------------------------------------------------------------------
    // | URL设置
    // +----------------------------------------------------------------------

    // PATHINFO变量名 用于兼容模式
    'var_pathinfo'           => 's',
    // 兼容PATH_INFO获取
    'pathinfo_fetch'         => ['ORIG_PATH_INFO', 'REDIRECT_PATH_INFO', 'REDIRECT_URL'],
    // pathinfo分隔符
    'pathinfo_depr'          => '/',
    // URL伪静态后缀
    'url_html_suffix'        => 'html',
    // URL普通方式参数 用于自动生成
    'url_common_param'       => false,
    // URL参数方式 0 按名称成对解析 1 按顺序解析
    'url_param_type'         => 0,
    // 是否开启路由
    'url_route_on'           => true,
    // 路由使用完整匹配
    'route_complete_match'   => false,
    // 路由配置文件（支持配置多个）
    'route_config_file'      => ['route'],
    // 是否强制使用路由
    'url_route_must'         => false,
    // 域名部署
    'url_domain_deploy'      => false,
    // 域名根，如thinkphp.cn
    'url_domain_root'        => '',
    // 是否自动转换URL中的控制器和操作名
    'url_convert'            => false,
    // 默认的访问控制器层
    'url_controller_layer'   => 'controller',
    // 表单请求类型伪装变量
    'var_method'             => '_method',
    // 表单ajax伪装变量
    'var_ajax'               => '_ajax',
    // 表单pjax伪装变量
    'var_pjax'               => '_pjax',
    // 是否开启请求缓存 true自动缓存 支持设置请求缓存规则
    'request_cache'          => false,
    // 请求缓存有效期
    'request_cache_expire'   => null,

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------

    'template'               => [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 模板路径
        'view_path'    => '',
        // 模板后缀
        'view_suffix'  => 'html',
        // 模板文件名分隔符
        'view_depr'    => DS,
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // 视图输出字符串内容替换
    'view_replace_str'       => [],
    // 默认跳转页面对应的模板文件
    'dispatch_success_tmpl'  => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',
    'dispatch_error_tmpl'    => THINK_PATH . 'tpl' . DS . 'dispatch_jump.tpl',

    // +----------------------------------------------------------------------
    // | 异常及错误设置
    // +----------------------------------------------------------------------

    // 异常页面的模板文件 
    'exception_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_exception.tpl',
    // errorpage 错误页面
    'error_tmpl'         => THINK_PATH . 'tpl' . DS . 'think_error.tpl',


    // 错误显示信息,非调试模式有效
    'error_message'          => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'         => true,
    // 异常处理handle类 留空使用 \think\exception\Handle
    'exception_handle'       => '',

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------

    'log'                    => [
        // 日志记录方式，内置 file socket 支持扩展
        'type'  => 'File',
        // 日志保存目录
        'path'  => LOG_PATH,
        // 日志记录级别
        'level' => [],
        // 日志开关  1 开启 0 关闭
        'switch' => 0,
    ],

    // +----------------------------------------------------------------------
    // | Trace设置 开启 app_trace 后 有效
    // +----------------------------------------------------------------------
    'trace'                  => [
        // 内置Html Console 支持扩展
        'type' => 'Html',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------

    'cache'                  => [
        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' => '33de53c9e0797772c92f81b596cc23ab',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
    /*
    'cache'                  => [
        // 驱动方式
        'type'   => 'redis',
        'host'       => '192.168.0.201', // 指定redis的地址
        'persistent' => true,
    ],
*/
    // +----------------------------------------------------------------------
    // | 会话设置
    // +----------------------------------------------------------------------

    'session'                => [
        'id'             => '',
        // SESSION_ID的提交变量,解决flash上传跨域
        'var_session_id' => '',
        // SESSION 前缀
        'prefix'         => 'think',
        // 驱动方式 支持redis memcache memcached
        'type'           => '',
        // 是否自动开启 SESSION
        'auto_start'     => true,
    ],

    // +----------------------------------------------------------------------
    // | Cookie设置
    // +----------------------------------------------------------------------
    'cookie'                 => [
        // cookie 名称前缀
        'prefix'    => '',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
    ],

    //分页配置
    'paginate'               => [
        'type'      => 'bootstrap',
        'var_page'  => 'page',
        'list_rows' => 15,
    ],
    // 密码加密串
    'AUTH_CODE' => "TPSHOP", //安装完毕之后不要改变，否则所有密码都会出错

    'ORDER_STATUS' => array(
        0 => '待确认',
        1 => '已确认',
        2 => '已收货',//已完成
        3 => '已取消',
        4 => '已评价',
        5 => '已关闭',
    ),
    'SHIPPING_STATUS' => array(
        0 => '未发货',
        1 => '已发货',
    	2 => '部分发货',
    ),
    'PAY_STATUS' => array(
        0 => '未支付',
        1 => '已支付',
        2 => '部分支付',
        3 => '已退款',
        4 => '拒绝退款'
    ),
    'SEX' => array(
        0 => '保密',
        1 => '男',
        2 => '女'
    ),
    'COUPON_TYPE' => array(
    	0 => '下单赠送',
        1 => '指定发放',
        2 => '免费领取',
        3 => '线下发放',
        4 => '新人优惠券'
    ),
	'PROM_TYPE' => array(
		0 => '默认',
		1 => '抢购',
		2 => '团购',
		3 => '优惠',
		4 => '预售',
		5 => '虚拟',
		6 => '拼团'
	),
	//用户消息类型
	'CATEGORY'=>['系统通知','物流通知', '优惠促销','商品提醒','我的资产','商城好店'],
	'TEAM_TYPE' => [0 => '分享团', 1 => '佣金团', 2 => '抽奖团'],
	'FREIGHT_TYPE' => [0 => '件数', 1 => '重量', 2 => '体积'],
    // 订单用户端显示状态
    'WAITPAY'=>' AND (pay_status=0 OR pay_status=2) AND order_status = 0 AND pay_code !="cod" ', //订单查询状态 待支付
    'WAITSEND'=>' AND (pay_status=1 OR pay_code="cod") AND shipping_status !=1 AND order_status in(0,1) ', //订单查询状态 待发货
    'WAITRECEIVE'=>' AND shipping_status=1 AND order_status = 1 ', //订单查询状态 待收货
    'WAITCCOMMENT'=> ' AND order_status=2 ', // 待评价 确认收货     //'FINISHED'=>'  AND order_status=1 ', //订单查询状态 已完成
    'FINISH'=> ' AND order_status = 4 ', // 已完成
    'CANCEL'=> ' AND order_status = 3 ', // 已取消
	'CANCELLED'=> 'AND order_status = 5 ',//已关闭
    'PAYED'=>' AND (order_status=2 OR (order_status=1 AND pay_status=1) ) ', //虚拟订单状态:已付款

    'ORDER_STATUS_DESC' => array(
        'WAITPAY' => '待支付',
        'WAITSEND'=>'待发货',
        'WAITRECEIVE'=>'待收货',
        'WAITCCOMMENT'=> '待评价',
        'CANCEL'=> '已取消',
        'CANCEL_REFUND'=> '已取消(已退款)',
        'FINISH'=> '已完成', //
    	'CANCELLED'=> '已关闭',
        'OTHER' => '未知',
    ),
    'RETURN_STATUS'=>array(
    		-2 => '服务单取消',//会员取消
    		-1 => '审核失败',//不同意
    		0  => '待审核',//卖家审核
    		1  => '审核通过',//同意
    		2  => '已发货',//买家发货
    		3  => '已收货',//卖家收货
    		4  => '维修/换货完成',//重新发货或退款完成
    		5  => '退款完成',
    		6  => '仲裁中',
    ),
    /**
     * 售后类型
     */
    'RETURN_TYPE'=>array(
        0=>'仅退款',
        1=>'退货退款',
        2=>'换货',
        3=>'维修'
    ),
    /**
     *  订单用户端显示按钮
        去支付     AND pay_status=0 AND order_status=0 AND pay_code ! ="cod"
        取消按钮  AND pay_status=0 AND shipping_status=0 AND order_status=0
        确认收货  AND shipping_status=1 AND order_status=0
        评价      AND order_status=1
        查看物流  if(!empty(物流单号))
        退货按钮（联系客服）  所有退换货操作， 都需要人工介入   不支持在线退换货
     */

    'DEFAULT_MODULE'        =>  'Home',  // 默认模块
    'DEFAULT_CONTROLLER'    =>  'Index', // 默认控制器名称
    'DEFAULT_ACTION'        =>  'index', // 默认操作名称

    'goods_state' => array(
        '0'=>'待审核',
        '1'=>'审核通过',
        '2'=>'审核失败'
    ),
	'TEAM_FOUND_STATUS' => array(
		'0'=>'待开团',
		'1'=>'已开团',
		'2'=>'拼团成功',
		'3'=>'拼团失败',
	),
	'TEAM_FOLLOW_STATUS' => array(
		'0'=>'待拼单',
		'1'=>'拼单成功',
		'2'=>'成团成功',
		'3'=>'成团失败',
	),
    'WITHDRAW_STATUS'=>[
        '-2'=>'删除作废',
        '-1'=>'审核失败',
        '0'=>'申请中',
        '1'=>'审核通过',
        '2'=>'付款成功',
        '3'=>'付款失败',
    ],
    //短信使用场景
    'SEND_SCENE' => array(
        '1'=>array('用户注册','验证码${code}，用户注册新账号, 请勿告诉他人，感谢您的支持!','regis_sms_enable'),
        '2'=>array('用户找回密码','验证码${code}，用于密码找回，如非本人操作，请及时检查账户安全','forget_pwd_sms_enable'),
        '3'=>array('客户下单','您有新订单，收货人：${consignee}，联系方式：${phone}，请您及时查收.','order_add_sms_enable'),
        '4'=>array('客户支付','客户下的单(订单ID:${orderId})已经支付，请及时发货.','order_pay_sms_enable'),
        '5'=>array('商家发货','尊敬的${userName}用户，您的订单已发货，收货人${consignee}，请您及时查收','order_shipping_sms_enable'),
        '6'=>array('身份验证','尊敬的用户，您的验证码为${code}, 用于绑定手机号，请勿告诉他人.','bind_mobile_sms_enable'),
        '7'=>array('购买虚拟商品通知','尊敬的用户，您购买的虚拟商品${goodsName}兑换码已生成,请注意查收.','virtual_goods_sms_enable'),
    ),

    'APP_TOKEN_TIME' => 60 * 60 * 24 * 1000 * 30, //App保持token时间 , 此处为1天

    /**假设这个访问地址是 www.tpshop.cn/home/goods/goodsInfo/id/1.html
     *就保存名字为 home_goods_goodsinfo_1.html
     *配置成这样, 指定 模块 控制器 方法名 参数名
     */
    'HTML_CACHE_ARR'=> [
        ['mca'=>'home_Goods_goodsInfo','p'=>['id'],'t'=>10],
        ['mca'=>'home_Index_index'],  // 缓存首页静态页面
        ['mca'=>'home_Goods_ajaxComment','p'=>['goods_id','commentType','p']],  // 缓存评论静态页面 http://www.tpshop2.0.com/index.php?m=Home&c=Goods&a=ajaxComment&goods_id=142&commentType=1&p=1
        ['mca'=>'home_Goods_ajax_consult','p'=>['goods_id','consult_type','p']],  // 缓存咨询静态页面 http://www.tpshop2.0.com/index.php?m=Home&c=Goods&a=ajax_consult&goods_id=142&consult_type=0&p=2
        ['mca'=>'home_Goods_ajaxHotGoods'],  // 缓存热卖商品
        ['mca'=>'home_Goods_ajaxSalesGoods'],  // 缓存销售排行榜
        ['mca'=>'home_Index_ajax_favorite','p'=>['i','p']],  // 缓存猜你喜欢
        ['mca'=>'home_activity_promoteList','t'=>10],  // 促销商品
        ['mca'=>'home_Index_street','p'=>['sc_id','province','city','order','area','p'],'t'=>20],  // 店铺街
        ['mca'=>'home_Goods_integralMall','p'=>['id','minValue','maxValue','brandType','is_new','exchange','p'],'t'=>20],  //  积分商城
        ['mca'=>'home_Activity_group_list','p'=>['cat_id','order','title','p'],'t'=>10],  // 团购商品
        ['mca'=>'home_Store_index','p'=>['store_id'],'t'=>10],  // 店铺首页
        ['mca'=>'home_Activity_flash_sale_list','t'=>10],  // 秒杀列表页
        ['mca'=>'home_Activity_ajax_flash_sale','p'=>['start_time','end_time','p'],'t'=>10],  // 秒杀ajax 加载
    ],
    'PAYMENT_PLUGIN_PATH' =>  PLUGIN_PATH.'payment',
    'LOGIN_PLUGIN_PATH' =>  PLUGIN_PATH.'login',
    'FUNCTION_PLUGIN_PATH' => PLUGIN_PATH.'function',
    'PAGESIZE' =>20,

    /**
     * coreseek/sphinx全文检索引擎配置
     */
    'SPHINX_HOST'         =>      '127.0.0.1',
    'SPHINX_PORT'         =>      9312,

	/**
	 * 公司性质
	 */
	'company_type' => array('股份有限公司', '个人独立企业', '有限责任公司', '外资', '中外合资', '国企', '合伙制企业', '其它'),
	//纳税类型税码
	'rate_list' => array(0 => 0, 3 => 3, 6 => 6, 7 => 7, 11 => 11, 13 => 13, 17 => 17),
    'buy_year'=>2015,
    'buy_version'=>0,
	'image_upload_limit_size'=>1024 * 1024 * 20,//上传图片大小限制
    'erasable_type' =>['.gif','.jpg','.jpeg','.bmp','.png','.mp4','.3gp','.flv','.avi','.wmv'],
    'finally_pay_time' =>1*24*3600,
];
