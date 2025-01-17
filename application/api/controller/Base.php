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
 * $Author: IT宇宙人 2015-08-10 $
 */ 
namespace app\api\controller;
use think\Db;
use think\Controller;
use think\Session;

class Base extends Controller {
    public $http_url;
    public $user = array();
    public $user_id = 0;    //6
    public $token = '';
    /**
     * 析构函数
     */
    function __construct() {       
        parent::__construct();
        if ($_REQUEST['test'] == '1') {
            $test_str = 'POST'.print_r($_POST,true);
            $test_str .= 'GET'.print_r($_GET,true);
        }
        
        $this->checkToken(); // 检查token
//        $this->user = M('users')->where('user_id', $this->user_id)->find();
        
        $unique_id = I("unique_id"); // 唯一id  类似于 pc 端的session id
        define('SESSION_ID',$unique_id); //将当前的session_id保存为常量，供其它方法调用                

    }    
    
    /*
     * 初始化操作
     */
    public function _initialize() {
        //读缓存
        cache_result_json(null,1);
        if(isset($_REQUEST["unique_id"])){           // 兼容手机app
            session_id($_REQUEST["unique_id"]);
            setcookie('token',$_REQUEST["token"]);
        }
        Session::start();
        
        $local_sign = $this->getSign();
        $api_secret_key = C('API_SECRET_KEY');
        
     //    if('www.tp-shop.cn' == $api_secret_key)
     //           exit(json_encode(array('status'=>-1,'msg'=>'请到后台修改php文件Application/Api/Conf/config.php 文件内的秘钥','data'=>'' )));
            
        // 不参与签名验证的方法
        //@modify by wangqh. add notify
        if(!in_array(strtolower(ACTION_NAME), array('getservertime','group_list','getconfig','alipaynotify', 'notify', 'goodslist','search','goodsthumimages','login','favourite','homepage')))
        {        
            if($local_sign != $_POST['sign'])
            {    
                $json_arr = array('status'=>-1,'msg'=>'签名失败!!!','result'=>'' );
                //exit(json_encode($json_arr));

            }
            if(time() - $_POST['time'] > 600)
            {    
                $json_arr = array('status'=>-1,'msg'=>'请求超时!!!','result'=>'' );
                //exit(json_encode($json_arr));
            }
        }       
    }
    
    /**
     *  app 端万能接口 传递 sql 语句 sql 错误 或者查询 错误 result 都为 false 否则 返回 查询结果 或者影响行数
     */
    public function sqlApi()
    {            
        exit(json_encode(array('status'=>-1,'msg'=>'使用万能接口必须开启签名验证才安全','result'=>''))); //  开启后注释掉这行代码即可
        
        C('SHOW_ERROR_MSG',1);
            $sql = $_REQUEST['sql'];
            try
            {
                 if(preg_match("/insert|update|delete/i", $sql))            
                     $result = Db::execute($sql);
                 else             
                     $result =  Db::query($sql);
             }
             catch (\Exception $e)
             {
                 $json_arr = array('status'=>-1,'msg'=>'系统错误','result'=>'');
                 $json_str = json_encode($json_arr);            
                 exit($json_str);            
             }            
                         
            if($result === false) // 数据非法或者sql语句错误            
                $json_arr = array('status'=>-1,'msg'=>'系统错误','result'=>'');
            else
                $json_arr = array('status'=>1,'msg'=>'成功!','result'=>$result);
                                   
            $json_str = json_encode($json_arr);            
            exit($json_str);            
    }

    /**
     * app端请求签名
     * @return type
     */
    protected function getSign(){
        header("Content-type:text/html;charset=utf-8");
        $data = $_POST;        
        unset($data['time']);    // 删除这两个参数再来进行排序     
        unset($data['sign']);    // 删除这两个参数再来进行排序
        ksort($data);
        $str = implode('', $data);        
        $str = $str.$_POST['time'].C('API_SECRET_KEY');        
        return md5($str);
    }
        
    /**
     * 获取服务器时间
     */
    public function getServerTime()
    {
        $json_arr = array('status'=>1,'msg'=>'成功!','result'=>time());
        $json_str = json_encode($json_arr);
        exit($json_str);       
    }
    
    /**
     * 校验token
     */
    public function checkToken()
    {
        $this->token = I("token",''); // token
        if (empty($this->token)) {
            $this->token = $_COOKIE['token'];
        }
       
        // 判断哪些控制器的 哪些方法需要登录验证的
        $check_arr = [
            'cart'      => ['cart2','cart3', 'cart4','integral','integral2'],
            'distribut' => ['add_goods', 'goods_list', 'index', 'lower_list', 'my_store', 'order_list', 'rebate_log', 'store','distribution_list','delete','lower_store_list'],
            'goods'     => ['collectGoodsOrNo'], 
            'message'   => ['message_read'],
            'order'     => ['add_comment', 'ajaxZan', 'cancel_order', 'checkType', 'comment', 'complain_handle', 'conmment_add', 'delComment', 
                'del_order', 'dispute', 'dispute_apply', 'dispute_info', 'dispute_list', 'expose', 'expose_info', 'expose_list', 'expose_info',
                'get_complain_talk', 'order_confirm', 'order_detail', 'order_list', 'publish_complain_talk', 'reply_add', 'return_goods',
                'return_goods_cancel', 'return_goods_index', 'return_goods_info', 'return_goods_list', 'return_goods_refund'],
	        'payment'   => ['alipay_sign'],
            'store'     => ['collectStoreOrNo'],
            'user'      => ['account', 'account_list', 'addAddress', 'add_comment', 'add_service_comment', 'cancelOrder', 'clear_message',
                'clear_visit_log', 'comment', 'comment_num', 'del_address', 'del_visit_log', 'getAddressList',
                'getCollectStoreData', 'getCouponList', 'getOrderList', 'getUserCollectStore', 'logout', 'message',
                'message_switch', 'orderConfirm', 'password', 'points', 'points_list', 'recharge_list', 'return_goods','return_goods_info',
                'return_goods_list','return_goods_status','service_comment','setDefaultAddress','updateUserInfo','upload_headpic','userInfo', 'getUserPoster' ,
                'visit_log','withdrawals','withdrawals_list','paypwd'],
            'newjoin'      => ['agreement','basicInfo', 'storeInfo','remark','getApply'],
        ];
        
        // 保留状态的检查组
        $check_session_arr = [
            'cart' => ['cartlist','addcart'],
            'user' => ['getGoodsCollect']
        ];
        
        $not_session_arr = [
            'user' => ['reg']
        ];
       
        $controller_name = strtolower(CONTROLLER_NAME);
        $action_name = strtolower(ACTION_NAME);
        if(in_array($controller_name, array_keys($check_arr)) && in_array($action_name, array_map('strtolower',$check_arr[$controller_name])))
        {
            $return = $this->getUserByToken($this->token);
            if ($return['status'] != 1) {
                $this->ajaxReturn($return);
            }
            $this->user = $return['result'];
            $this->user_id = $this->user['user_id'];                    
             // 更新最后一次操作时间 如果用户一直操作 则一直不超时
            M('users')->where("user_id",$this->user_id)->save(array('last_login'=>time()));
            
        } elseif (in_array($controller_name, array_keys($check_session_arr)) && in_array($action_name, array_map('strtolower',$check_session_arr[$controller_name]))) {
            if ($this->token) {
                $this->user = M('users')->where("token",$this->token)->find();
            }
            !$this->user && $this->user = session('user');
            $this->user && $this->user_id = $this->user['user_id'];
            
        } elseif (in_array($controller_name, array_keys($not_session_arr)) && in_array($action_name, array_map('strtolower',$not_session_arr[$controller_name]))) {
            session('user', null);
        } else {
            $this->user = M('users')->where("token", $this->token)->find();
            $this->user && ($this->user_id = $this->user['user_id']);
        }
        session('user', $this->user);
    }
    
    protected function getUserByToken($token)
    {
        if (empty($token)) {
            return ['status'=>-100, 'msg'=>'请登录'];
        }

        $user = M('users')->where("token", $token)->find();
        if (empty($user)) {
            return ['status'=>-101, 'msg'=>'请登录'];
        }
        
        // 登录超过72分钟 则为登录超时 需要重新登录.  //这个时间可以自己设置 可以设置为 20分钟
        if(time() - $user['last_login'] > C('APP_TOKEN_TIME')) {  //3600
            return ['status'=>-102, 'msg'=>'登录超时,请重新登录!', 'result'=>(time() - $user['last_login'])];
        }
        
        return ['status' => 1, 'msg' => '获取成功', 'result' => $user];
    }


    /**
     * 替换属性中的NULL为""
     * @param $data
     */
    public function ajaxReturn($data){
        if($data && $data['result']){
            $result = $data['result'];
            if(is_array($result)){
                $dealResult = $this->batchFilterNullAttr($result);
                $data['result'] = $dealResult;
            }
        }
//        ob_clean();
        //设置缓存
        cache_result_json(json_encode($data, JSON_UNESCAPED_UNICODE));
        exit(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    
    public function batchFilterNullAttr($arr){
         if(!$arr){
            return $arr;
        }
        if(is_array($arr)){
            foreach ($arr as $k => $v){
                if(!isset($v)){
                    $arr[$k] = "";
                }else if(is_array($v)){
                    $filterResult = $this->batchFilterNullAttr($arr[$k]);
                    $arr[$k] = $filterResult;
                }
            }
            return $arr;
        }else{
            return empty($arr) ? "" : $arr;
        }
    }
}