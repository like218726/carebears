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
 
use app\common\logic\Block;
use app\common\logic\User;
use app\common\model\UserAddress;
use think\Page;
use think\Db;
use app\common\logic\GoodsLogic;
use app\common\logic\FreightLogic;
use app\common\model\SpecGoodsPrice;

class Goods extends Base {
    
    /**
     * 析构流函数
     */
    public function  __construct() {   
        parent::__construct();    
    } 
    
    public function index(){
       // $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover,{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        $this->display();
    }
    
    /**
     * 获取商品分类列表
     */
    public function goodsCategoryList()
    {
        $parent_id = I("parent_id/d",0);
        $new_ad = I("new_ad/d",0);
        $where = array("parent_id"=>$parent_id);
        $goodsCategoryList = M('GoodsCategory')->where("is_show=1")->where($where)->order("sort_order desc")->cache(7200)->select();
        
        //查找广告
        $start_time = strtotime(date('Y-m-d H:00:00'));
        $end_time = strtotime(date('Y-m-d H:00:00')); 
        $adv = M("ad")->field(array('ad_link','ad_name','ad_code','media_type,pid'))->where("pid=401 AND enabled=1 and start_time< $start_time and end_time > $end_time")->find();
        
        if($new_ad == 1){
            $result = [
                "category"=> $goodsCategoryList,
                "adv" =>empty($adv) ? "" : $adv
            ];
            $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$result]);
        }
        
        
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$goodsCategoryList]);
    }
    
    /**
     * 获取根据一级分类获取对应的二三级分类
     */
    public function goodsSecAndThirdCategoryList()
    {
        $parent_id = I("parent_id/d",0); 
        /** 一次查询所有二级和三级分类  **/
        $list = M('GoodsCategory')->where("parent_id_path LIKE :parent_id AND is_show=1 and level in (2,3)")->bind(['parent_id'=>"0\_".$parent_id."\_%"])->order('sort_order asc')->getField('id,mobile_name,image, level , parent_id , sort_order');
        $list2 = array();
        foreach ($list as $k =>$v ) {
            if($v['level'] == 3) {
                continue;
            }
            $arr = array();
            $arr['mobile_name'] = $v['mobile_name'];
            $arr['image'] = $v['image'];
            $arr['id'] = $v['id'];
            $arr['level'] = $v['level'];
            $arr['parent_id'] = $v['parent_id'];

            $arr3 = array();
            foreach ($list as $k2 => $v2){                
                if($v['id'] == $v2['parent_id']){
                    $arr3['mobile_name'] = $v2['mobile_name'];
                    $arr3['image'] = $v2['image'];
                    $arr3['id'] = $v2['id'];
                    $arr3['level'] = $v2['level'];
                    $arr3['parent_id'] = $v2['parent_id'];
                   $arr['sub_category'][] = $arr3;
                }
            }  
            $list2[] =$arr;
        } 
        
        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功','result'=>$list2]);
    }
    
    
    /**
     * 商品列表页
     */
    public function goodsList()
    {
        $id = I('get.id/d',0); // 当前分类id 
        $brand_id = I('get.brand_id/d',0);
        $attr = I('get.attr',''); // 属性        
        $sort = I('get.sort','sort'); // 排序
        $sort_asc = I('get.sort_asc','asc'); // 排序
        $price = I('get.price',''); // 价钱
        $start_price = trim(I('post.start_price','0')); // 输入框价钱
        $end_price = trim(I('post.end_price','0')); // 输入框价钱       
        
    	if ($start_price && $end_price) {
            $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱   	 
        }
        $filter_param = array(); // 帅选数组  
    	$filter_param['id'] = $id; //加入帅选条件中
    	$brand_id && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中
    	$attr && ($filter_param['attr'] = $attr); //加入帅选条件中
    	$price && ($filter_param['price'] = $price); //加入帅选条件中
         
    	$goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
        // 当前分类
        if ($id && $goodsCate = M('GoodsCategory')->where("id", $id)->find()) {
            $filter_goods_id = M('goods')->where(['is_on_sale' => 1, 'goods_state' => 1, 'cat_id' . $goodsCate['level'] => $id])->cache(3600)->order([$sort=>$sort_asc,'goods_id'=>'desc'])->getField("goods_id", true);
        } else {
            $filter_goods_id = M('goods')->where("is_on_sale=1 and goods_state = 1")->cache(3600)->order([$sort=>$sort_asc,'goods_id'=>'desc'])->getField("goods_id", true);
        }

    	// 过滤帅选的结果集里面找商品
    	if ($brand_id || $price) {// 品牌或者价格
    		$goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id, $price); // 根据 品牌 或者 价格范围 查找所有商品id
    		$filter_goods_id = array_intersect($filter_goods_id, $goods_id_1); // 获取多个帅选条件的结果 的交集
    	}
    	if ($attr) {// 属性
    		$goods_id_3 = $goodsLogic->getGoodsIdByAttr($attr); // 根据 规格 查找当所有商品id
    		$filter_goods_id = array_intersect($filter_goods_id, $goods_id_3); // 获取多个帅选条件的结果 的交集
    	}
    	 
    	$filter_menu  = $goodsLogic->get_filter_menu($filter_param,'goodsList'); // 获取显示的帅选菜单
    	$filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'goodsList'); // 帅选的价格期间
    	$filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'goodsList'); // 获取指定分类下的帅选品牌
    	$filter_attr  = $goodsLogic->get_filter_attr($filter_goods_id,$filter_param,'goodsList',1); // 获取指定分类下的帅选属性

    	$count = count($filter_goods_id);
    	$page = new Page($count, 15);
    	if ($count > 0) {
            $goods = new \app\common\model\Goods();
    		$goods_list = $goods->field('goods_id,cat_id3,goods_sn,goods_name,shop_price,comment_count,sales_sum,is_virtual,virtual_sales_sum')
                    ->where("goods_id in (".  implode(',', $filter_goods_id).")")
                    ->order([$sort=>$sort_asc,'goods_id'=>'desc'])
                    ->limit($page->firstRow.','.$page->listRows)
                    ->select();
            if($goods_list){
                $goods_list = collection($goods_list)->append(['comment_statistics'])->toArray();
                foreach ($goods_list as $k=>$v){
                    $goods_list[$k]['activity'] = (new Block())->check_activity($v);
                    //遍历图片
                    $url = goods_thum_images($v['goods_id'],400,400);
                    if (strpos($url, 'http') !== 0) {
                        $url = SITE_URL . $url;
                    }
                    $goods_list[$k]['original_img'] = $url;
                }
            }
            foreach ($goods_list as &$g) {
                $g['good_comment_rate'] = $g['comment_statistics']['high_rate'];
            }
    	}
    	$list['goods_list'] = $goods_list;
        
        //数据格式转换：
        $i = 1;
    	//菜单
        foreach ($filter_menu as $k => $v) {
            $v['name'] = $v['text'];
            unset($v['text']);
            $list['filter_menu'][] = $v;  // 帅选规格
        }
        // 属性
        foreach ($filter_attr as $k => $v) {
            $items['name'] = $v['attr_name'];
            foreach ($v['attr_value'] as $k2 => $v2) {
                $items['item'][] = array('name'=>$v2['attr_value'],'href'=>$v2['href'],'id'=>$i++);
            }
            $list['filter_attr'][] = $items;
            $items = array();
        }
        // 品牌
        foreach ($filter_brand as $k => $v) {
            $list['filter_brand'][] = array('name'=>$v['name'],'hreg'=>$v['href'],'id'=>$i++);
        }
        // 价格
        foreach ($filter_price as $k => $v) {       
            $list['filter_price'][] = array('name'=>$v['value'],'href'=>$v['href'],'id'=>$i++);
        }
        
        $list['sort'] =  $sort;
        $list['sort_asc'] =  $sort_asc;
    	$sort_asc = $sort_asc == 'asc' ? 'desc' : 'asc';        
        $list['orderby_default'] = urldecode(U("Goods/goodsList",$filter_param,'')); // 默认排序
        $list['orderby_sales_sum'] = urldecode(U("Goods/goodsList",array_merge($filter_param,array('sort'=>'sales_sum','sort_asc'=>'desc')),'')); // 销量排序
        $list['orderby_price'] = urldecode(U("Goods/goodsList",array_merge($filter_param,array('sort'=>'shop_price','sort_asc'=>$sort_asc)),'')); // 价格
        $list['orderby_comment_count'] = urldecode(U("Goods/goodsList",array_merge($filter_param,array('sort'=>'comment_count','sort_asc'=>'desc')),'')); // 评论
        $list['orderby_is_new'] = urldecode(U("Goods/goodsList",array_merge($filter_param,array('sort'=>'is_new','sort_asc'=>'desc')),'')); // 新品

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $list]);
    }    

     /**
     * 商品搜索列表页
     */
    public function search(){
    	
        C('URL_MODEL',0); // 返回给手机app 生成路径格式 为 普通 index.php?=api&c=  最普通的路径格式
    	$filter_param = array(); // 帅选数组
    	$id = I('get.id/d',0); // 当前分类id
    	$brand_id = I('brand_id/d',0);    	    	
    	$sort = I('sort','sort'); // 排序
    	$sort_asc = I('sort_asc','asc'); // 排序
    	$price = I('price',''); // 价钱
    	$start_price = trim(I('start_price','0')); // 输入框价钱
    	$end_price = trim(I('end_price','0')); // 输入框价钱
    	if($start_price && $end_price) $price = $start_price.'-'.$end_price; // 如果输入框有价钱 则使用输入框的价钱   	 
    	$filter_param['id'] = $id; //加入帅选条件中
    	$brand_id  && ($filter_param['brand_id'] = $brand_id); //加入帅选条件中    	    	
    	$price  && ($filter_param['price'] = $price); //加入帅选条件中
        $q = urldecode(trim(I('q',''))); // 关键字搜索
        $q  && ($_GET['q'] = $filter_param['q'] = $q); //加入帅选条件中
        if ($q === '' && !$brand_id) {
            $this->ajaxReturn(['status'=>-1, 'msg'=>'请输入搜索关键词']); 
        }           
        
    	$goodsLogic = new GoodsLogic(); // 前台商品操作逻辑类
     
    	$filter_goods_id = M('goods')->where(['is_on_sale'=>1,'goods_name'=>['like',"%$q%"]])->order([$sort=>$sort_asc,'goods_id'=>'desc'])->getField("goods_id",true);
 
    	// 过滤帅选的结果集里面找商品
    	if($brand_id || $price)// 品牌或者价格
    	{
    		$goods_id_1 = $goodsLogic->getGoodsIdByBrandPrice($brand_id,$price); // 根据 品牌 或者 价格范围 查找所有商品id
    		$filter_goods_id = array_intersect($filter_goods_id,$goods_id_1); // 获取多个帅选条件的结果 的交集
    	}
    	  
    	$filter_menu  = $goodsLogic->get_filter_menu($filter_param,'search'); // 获取显示的帅选菜单
    	$filter_price = $goodsLogic->get_filter_price($filter_goods_id,$filter_param,'search'); // 帅选的价格期间
    	$filter_brand = $goodsLogic->get_filter_brand($filter_goods_id,$filter_param,'search'); // 获取指定分类下的帅选品牌
    	
    	$count = count($filter_goods_id);
    	$page = new Page($count,10);
        if ($count > 0) {
            $goods = new \app\common\model\Goods();
            $goods_list = $goods->field('goods_id,cat_id3,goods_sn,goods_name,shop_price,comment_count,sales_sum,is_virtual,virtual_sales_sum')
                ->where("goods_id in (".  implode(',', $filter_goods_id).")")
                ->order([$sort=>$sort_asc,'goods_id'=>'desc'])
                ->limit($page->firstRow.','.$page->listRows)
                ->select();
            if($goods_list){
                $goods_list = collection($goods_list)->append(['comment_statistics'])->toArray();
                foreach ($goods_list as $k=>$v){
                    $goods_list[$k]['activity'] = (new Block())->check_activity($v);
                }
            }
            foreach ($goods_list as &$g) {
                $g['good_comment_rate'] = $g['comment_statistics']['high_rate'];
            }
        }

    	$list['goods_list'] = $goods_list;
    	 
        $i = 1;
    	//菜单
        foreach($filter_menu as $k => $v) // 依照app端的要求 去掉 键名
        {
            $v['name'] = $v['text'];
            unset($v['text']);
            $list['filter_menu'][] = $v;  // 帅选规格
        }
      
        // 品牌
        foreach($filter_brand as $k => $v) // 依照app端的要求 去掉 键名
        {                                              
            $list['filter_brand'][] = array('name'=>$v['name'],'href'=>$v['href'],'id'=>$i++);
        }        
                    
        // 价格
        foreach($filter_price as $k => $v) // 依照app端的要求 去掉 键名
        {                                              
            $list['filter_price'][] = array('name'=>$v['value'],'href'=>$v['href'],'id'=>$i++);
        }
        $list['sort'] =  $sort;
        $list['sort_asc'] =  $sort_asc;
    	$sort_asc = $sort_asc == 'asc' ? 'desc' : 'asc';        
        $list['orderby_default'] = urldecode(U("Goods/search",$filter_param,'')); // 默认排序
        $list['orderby_sales_sum'] = urldecode(U("Goods/search",array_merge($filter_param,array('sort'=>'sales_sum','sort_asc'=>'desc')),'')); // 销量排序
        $list['orderby_price'] = urldecode(U("Goods/search",array_merge($filter_param,array('sort'=>'shop_price','sort_asc'=>$sort_asc)),'')); // 价格
        $list['orderby_comment_count'] = urldecode(U("Goods/search",array_merge($filter_param,array('sort'=>'comment_count','sort_asc'=>'desc')),'')); // 评论
        $list['orderby_is_new'] = urldecode(U("Goods/search",array_merge($filter_param,array('sort'=>'is_new','sort_asc'=>'desc')),'')); // 新品
    	C('TOKEN_ON',false);
        $this->ajaxReturn(array('status'=>1,'msg'=>'获取成功','result'=>$list ));
       
    }    
    
    /**
     * 获取商品列表
     */
    public function goodsInfo()
    {
        $goods_id = I("get.id/d", 0);
        $store_id = input("get.store_id/d", 0);
        $where['goods_id'] = $goods_id;
        $where['is_on_sale'] = 1;

        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($where);
        if(empty($goods)){
            $this->ajaxReturn(['status'=>-1, 'msg'=>'此商品不存在或者已下架']);
        }
        // 添加浏览记录
        $goodsLogic = new GoodsLogic();
        if ($this->user_id) {
            $user = new User();
            $user->setUserById($this->user_id);
            $user->visitGoodsLog($goods);
            //获取默认收货人地址
             $user_address = M('user_address')->where(array('user_id'=>$this->user_id , 'is_default'=>1))->find();
             //返回默认收货人
             $addr =  array();   
             if($user_address){
                 $regions = M('region')->cache(true)->getField('id,name');
                 $addr[] = $regions[$user_address['province']] ?: '';
                 $addr[]     = $regions[$user_address['city']] ?: '';
                 $addr[] = $regions[$user_address['district']] ?: '';
                
                 $addresss= implode("",$addr);
                 $consignee['address'] = $addresss;
                 $consignee['address_id'] = $user_address['address_id'];
                 $consignee['district'] = $user_address['district'];
             }
        }
        
        if(!$consignee){
            $consignee['address_id'] = 0;
            $consignee['address'] = "请选择收货人";
            $consignee['district'] = 0;
        }
         
        // 获取促销活动的对象
        $specGoodsPrices = SpecGoodsPrice::all(['goods_id' => $goods_id], '', 120);
        //积分兑换商品不参与活动
        if($goods['exchange_integral'] > 0){
            $return['activity'] = [ 'prom_type' => 0];
        }else{
            $goodsPromFactory = new \app\common\logic\GoodsPromFactory;
            if ($specGoodsPrices) {
                $goodsPromLogic = $goodsPromFactory->makeModule($goods, $specGoodsPrices[0]);//默认显示第一个规格
            } else {
                $goodsPromLogic = $goodsPromFactory->makeModule($goods, null);
            }
            // 上面会自动更新商品活动状态，所以商品需要重新查询
            $goodsPromLogic && $goods = $goodsPromLogic->getGoodsInfo();
            // 活动信息
            $activity = $goodsLogic->getActivitySimpleInfo($goods, $goodsPromLogic);
            $activity && $return['activity'] = $activity;
        }
        
        $return['goods'] = $goods->append(['comment_statistics'])->hidden(['goods_content'])->toArray();;
        
        // 商品规格 价钱 库存表 找出 所有 规格项id
        $filter_spec = $goodsLogic->get_spec($goods_id); 
        $goods_spec_list = [];
        foreach ($filter_spec as $key => $val) {
            $goods_spec_list[] = [
                'spec_name' => $key,
                'spec_list' => $val,
            ];
        }
        $return['goods_spec_list'] = $goods_spec_list;
        // 根据规格ID查询规格名称
       if($specGoodsPrices){
           $specGoodsPrices && $specItems = M('spec_item')->cache(120)->getField("id, item");
           foreach ($specGoodsPrices as $spec){
               $specIds = explode('_', $spec['key']);
               $keyName = "";
               foreach ($specIds as $idv){
                   $keyName .=$specItems[$idv]." ";
               }
               $spec['key_name'] = $keyName;
               $spec_goods_price[] = $spec ;
           }   
       } 
        $return['spec_goods_price'] = $spec_goods_price ?: [] ;
        
        //查询店铺信息
        $store = M("store")->where("store_id" , $goods['store_id'])->find();
        $return['store'] = $store;
        //从店铺查看商品带有店铺id，检查改商品属于店铺还是平台
        if($store_id){
            if($goods['store_id'] != $store_id){
                //如果该商品不是店铺的，检查有没有绑定平台
                $store_bind_platform_goods = db('store_bind_platform_goods')->where(['store_id'=>$store_id,'goods_id'=>$goods['goods_id']])->count();
                if($store_bind_platform_goods){
                    $return['store'] = db('store')->where(['store_id'=>$store_id])->find();
                }
            }
        }
        // 添加该店商品总数量app要求
        $return['store']['goods_count'] = Db::name('goods')->where('store_id',$store['store_id'])->count();
        // 推荐商品
        if (isset($goods['cat_id3'])) {
            $return['recommend_goods'] = M('goods')
                    ->field("goods_id, goods_name, shop_price,is_virtual")
                    ->where("goods_state = 1 and is_on_sale=1 and cat_id3 = {$goods['cat_id3']}")
                    ->cache(120)->limit(9)->select();
        }
        
        // 画廊
        $return['gallery'] = M('goods_images')->field('image_url')->where(array('goods_id'=>$goods_id))->select();
        foreach ($return['gallery'] as $key => $val) {
            $image_url = $val['image_url'];
            if(strpos($val['image_url'],'http') === false){
                // 淘宝导入的图片处理
                if(strpos($image_url, '.tbi')){
                    $image_url2 = str_replace('.tbi', '.jpg', $image_url);
                    if(!file_exists('.'.$image_url2)){
                        file_put_contents('.'.$image_url2, file_get_contents('.'.$image_url));
                    }
                    $image_url = $image_url2;
                }
                $image_url = SITE_URL.$image_url;
            }
            $return['gallery'][$key]['image_url'] =  $image_url;
        }
        
        // 获取最近的两条评论
        $return['comment'] = M('comment')->alias('c')
                ->field('c.*,u.head_pic,u.nickname')
                ->join('__USERS__ u', 'c.user_id=u.user_id')
                ->where(['c.goods_id' => $goods_id, 'c.is_show' => 1, 'c.parent_id' => 0])
                ->limit(2)->cache(true, 300)
                ->select();
        foreach ($return['comment'] as &$one_comment) {
            $one_comment['img'] = $one_comment['img'] ? unserialize($one_comment['img']) : [];
            if($one_comment['is_anonymous'] == 1){
                $one_comment['nickname'] =  mb_substr($one_comment['nickname'], 0, 3,'utf-8') . '***';
            }
        }
        
        // 获取某个商品的评论统计
        $return['statistics'] = $goods['comment_statistics'];
        
        //是否收藏店铺和商品
        $store_collect = M('store_collect')->where(['user_id' => $this->user_id, 'store_id' => $goods['store_id']])->find();
        if($store_collect){
            $return['store']['is_collect'] = 1;
        }else{
            $return['store']['is_collect'] = 0;
        }
        $return['goods']['is_collect'] = $goodsLogic->isCollectGoods($this->user_id, $goods_id);
        
        //积分比例
        $return['goods']['point_rate'] = tpCache('shopping.point_rate') ?: 1; 
        $return['store']['auto_service_date'] = tpCache('shopping.auto_service_date') ?: 1;
        $return['consignee'] =  $consignee;
        
        if (!$goods) {
            $json_arr = array('status'=>-1,'msg'=>'没有该商品');
        } else {
            $json_arr = array('status'=>1,'msg'=>'获取成功','result'=>$return);
        }
  
        $this->ajaxReturn($json_arr);
    }

    /**
     * 获取商品活动信息
     */
    public function goods_activity()
    {
        $goods_id = I("get.goods_id/d", 0);
        $item_id = I("get.item_id/d", 0);

        $where['goods_id'] = $goods_id;
        $where['is_on_sale'] = 1;
        $goods = \app\common\model\Goods::get($where, '', 3600);
        if (empty($goods)) {
            $this->ajaxReturn(['status'=>-1, 'msg'=>'此商品不存在或者已下架']);
        }
        //积分兑换商品不参与活动
        if($goods['exchange_integral'] > 0){
            $this->ajaxReturn(['status' => 1, 'msg' => '积分商品不参与活动信息', 'result' =>[ 'prom_type' => 0]]);
        }

        $specWhere['goods_id'] = $goods_id;
        if($item_id){
            $specWhere['item_id'] = $item_id;
        }
        // 获取促销活动的对象
        $specGoodsPrice = SpecGoodsPrice::get($specWhere, '', 120);
        $goodsPromFactory = new \app\common\logic\GoodsPromFactory;
        $goodsPromLogic = $goodsPromFactory->makeModule($goods, $specGoodsPrice);//默认显示第一个规格

        // 上面会自动更新商品活动状态，所以商品需要重新查询\
        $goodsPromLogic && $goods = $goodsPromLogic->getGoodsInfo();
        unset($goods['goods_content']);

        // 活动信息
        $goodsLogic = new GoodsLogic();
        $activity = $goodsLogic->getActivitySimpleInfo($goods, $goodsPromLogic);

        $this->ajaxReturn(['status' => 1, 'msg' => '获取成功', 'result' => $activity]);
         
    }

    public function activity(){
        $goods_id = input('goods_id/d');//商品id
        $item_id = input('item_id/d');//规格id
        $Goods = new \app\common\model\Goods();
        $goods = $Goods::get($goods_id,'',true);
        $goods['qitian'] = $goods['store']['qitian'];
        $goodsPromFactory = new \app\common\logic\GoodsPromFactory();
        if ($goodsPromFactory->checkPromType($goods['prom_type'])) {
            //这里会自动更新商品活动状态，所以商品需要重新查询
            if($item_id){
                $specGoodsPrice = SpecGoodsPrice::get($item_id,'',true);
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,$specGoodsPrice);
            }else{
                $goodsPromLogic = $goodsPromFactory->makeModule($goods,null);
            }
            //检查活动是否有效
            if($goodsPromLogic->checkActivityIsAble()){
                $goods = $goodsPromLogic->getActivityGoodsInfo();
                $goods['activity_is_on'] = 1;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品参与活动','result'=>['goods'=>$goods]]);
            }else{
                $goods['activity_is_on'] = 0;
                $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动.','result'=>['goods'=>$goods]]);
            }
        }
        $this->ajaxReturn(['status'=>1,'msg'=>'该商品没有参与活动','result'=>['goods'=>$goods]]);
    }
    
    /**
     * 商品内容
     */
    public function goodsContent()
    {
        $is_json = I('is_json', 0);
        $goods_id = I("get.id/d" , 0);
        $goods = M('Goods')->field('goods_content')->where("goods_id" , $goods_id)->find();
        if(empty($goods)){
        	$this->error('此商品不存在或者已下架');
        }

        $goods_attribute = M('GoodsAttribute')->getField('attr_id,attr_name'); // 查询属性
        $goods_attr_list = M('GoodsAttr')->where("goods_id" , $goods_id)->select(); // 查询商品属性表                        

        if ($is_json) {
            foreach ($goods_attr_list as &$attr) {
                $attr['attr_name'] = $goods_attribute[$attr['attr_id']];
            }
            $this->ajaxReturn(['status'=>1,'msg'=>'获取成功', 'result' => [
                'goods_content' => $goods['goods_content'],
                'goods_attr_list' => $goods_attr_list,
            ]]);
        }
        
        $this->assign('goods_attribute',$goods_attribute);//属性值     
        $this->assign('goods_attr_list',$goods_attr_list);//属性列表
        $this->assign('goods',$goods);
        return $this->fetch();
    }    

    /**
     *  获取商品的缩略图
     */
    function goodsThumImages()
    {
        $goods_id = I('goods_id/d');
        $width = I('width/d');
        $height = I('height/d');
        $url = goods_thum_images($goods_id,$width,$height);
        if (strpos($url, 'http') !== 0) {
            $url = SITE_URL . $url;
        } 
        return $this->redirect($url);
    }
    
    
    /**
     * 获取某个商品的评价
     */
    function getGoodsComment()
    {        
        $p = I('p', 1);
        $goods_id = I('goods_id/d', 0);       
        $type = input('type/d', 1); // 1 全部 2好评 3 中评 4差评 5晒图
        if(!$type){$type=1;}
        if ($type == 5) {
            $where = "c.is_show = 1 and c.goods_id = :goods_id and c.parent_id = 0 and c.img !='' and c.img NOT LIKE 'N;%' and c.deleted = 0";
        } else {
            $typeArr = array('1' => '0,1,2,3,4,5', '2' => '4,5', '3' => '3', '4' => '0,1,2');
            $where = "c.is_show = 1 and c.goods_id = :goods_id and c.parent_id = 0 and c.goods_rank in($typeArr[$type]) and c.deleted = 0";
        }

        $list = M('comment')->alias('c')
                ->field('c.*,u.nickname,u.head_pic')
                ->join('__USERS__ u', 'u.user_id = c.user_id', 'left')
                ->where($where)->bind(['goods_id'=>$goods_id])
                ->order("c.comment_id desc")
                ->page($p, 10)
                ->select();
        
        foreach ($list as $key => $val) {
            if($val['is_anonymous'] == 1){
                $list[$key]['nickname'] =  mb_substr($val['nickname'], 0, 3,'utf-8') . '***';
            }
            /* 图片处理 */
            if(empty($val['img'])) {
                $list[$key]['img'] = [];
                continue;
            }
            $val['img'] = unserialize($val['img']);
            if (is_array($val['img'])) {
                foreach ($val['img'] as $k => $v) {
                    $val['img'][$k] = SITE_URL.$v;
                }
            } else {
                $list[$key]['img'] = [];
            }
            $list[$key]['img'] = $val['img'] ?: [];


        }

        $this->ajaxReturn(['status'=>1,'msg'=>'获取成功', 'result'=>$list]);
    }
    
    /**
     * 收藏商品
     */
    function collectGoodsOrNo()
    {
        $goods_id = I('goods_id/d');
        $goods = M('Goods')->where("goods_id", $goods_id)->find();
        if (!$goods) {
            $this->ajaxReturn(['status'=>1,'msg'=>'收藏商品不存在']);
        }
        
        $collect = M('goods_collect')->where("user_id", $this->user_id)->where('goods_id',$goods_id)->find();
        if ($collect) {
            //删除收藏商品
            M('goods_collect')->where("user_id",$this->user_id)->where('goods_id',$goods_id)->delete();
            $this->ajaxReturn(['status'=>1,'msg'=>'已取消收藏']);
        }
        
        M('goods_collect')->add(['goods_id'=>$goods_id,'user_id'=>$this->user_id,'add_time'=>time()]);
        $this->ajaxReturn(['status'=>1,'msg'=>'收藏成功']);
    }
    
    
    /**
     * 猜你喜欢/热门推荐
     */
    public function guessYouLike(){
        $p = I('p',1);
       $favourite_goods = M('goods')->where("is_on_sale=1")->order('sort DESC')->page($p,10)->getField('goods_id,goods_sn,goods_name,shop_price,comment_count,is_virtual');//首页/购物车/我的 推荐商品
       $goods = array();
    	foreach ($favourite_goods as $k => $v){
    	    $goods[] = $v;
    	}
    	$json_arr = array('status'=>1,'msg'=>'获取成功','result'=>$goods);
    	$this->ajaxReturn($json_arr);
    }
    
    /**
     * 找相似
     */
    public function similar()
    {
        $goods_id = I('get.id', 0);
        if (!$goods_id) {
            $json = ['status' => 1, 'msg' => 'id不能为空'];
            $this->ajaxReturn($json);
        }
        
        $p = I('get.p', 1);
        $count = I('get.count', 9);
        
        $goodsLogic = new GoodsLogic();
        $return = $goodsLogic->getSimilar($goods_id, $p, $count);

        $json = ['status' => 1, 'msg' => '获取成功', 'result' => $return];
    	$this->ajaxReturn($json);
    }
    
    /**
     * 积分商城
     */
    public function integralMall()
    {
        $rank= I('get.rank', '');
        $p = I('p', 1);
        
        $goodsLogic = new GoodsLogic();
        $result = $goodsLogic->integralMall($rank, $this->user_id, $p);
        
        //查找广告
        $start_time = strtotime(date('Y-m-d H:00:00'));
        $end_time = strtotime(date('Y-m-d H:00:00'));
        $adv = M("ad")->field(array('ad_link','ad_name','ad_code','media_type,pid'))->where("pid=535 AND enabled=1 and start_time< $start_time and end_time > $end_time")->find();
        if($adv && $adv['media_type'] == 4){//如果是分类, 截取最后一个分类
            $cats = explode('_',$adv['ad_link']);
            $count = count($cats);
            if($count > 0) {
                $adv['ad_link'] = $cats[$count-1];
            }
        } 
        $return = ['status' => 1, 'msg' => '获取成功', 
            'result' => [
                'goods_list' => $result['goods_list'],
                'goods_list_count' => $result['goods_list_count'],
                'point_rate' => $result['point_rate'],
                'ad'=> empty($adv) ? "" : $adv,
            ]
        ];

    	$this->ajaxReturn($return);
    }
    
    /**
     * 商品物流配送和运费
     */
    public function dispatching()
    {
        $goods_id = I('goods_id/d');//143
        $region_id = I('region_id/d');//28242
//        $dispatching_data = S("goods_dispatching_{$goods_id}_$region_id");
//        if($dispatching_data){
//            $this->ajaxReturn($dispatching_data);
//        }
        $Goods = new \app\common\model\Goods();
        $goods = $Goods->cache(true)->where('goods_id',$goods_id)->find();
        $freightLogic = new FreightLogic();
        $freightLogic->setGoodsModel($goods);
        $freightLogic->setRegionId($region_id);
        $freightLogic->setGoodsNum(1);
        $isShipping = $freightLogic->checkShipping();
        if($isShipping){
            $freightLogic->doCalculation();
            $freight = $freightLogic->getFreight();
            $dispatching_data = ['status'=>1,'msg'=>'可配送','result'=>$freight];
        }else{
            $dispatching_data = ['status'=>1,'msg'=>'该地区不支持配送','result'=>-1];
            //如果用户还没有填地址，则默认配送
            if($this->user_id){
                $count =  UserAddress::where(['user_id'=>$this->user_id])->cache(60)->count();
                if(0 == $count){
                    $dispatching_data = ['status' => 1, 'msg' => '暂无收货地址', 'result' =>  ['freight' => 0]];
                }
            }
        }
        S("goods_dispatching_{$goods_id}_$region_id", $dispatching_data ,60);
        $this->ajaxReturn($dispatching_data);
    }

    /**
     * 获取商品分享海报
     */
    public function goodsSharePoster(){
        $goods_id = I("get.id/d",0);
        $item_id = I("get.item_id/d",0);
        $team_id = I("get.team_id/d",0);//拼团
        $prom_id = I("get.prom_id/d",0);
        $prom_type = I("get.prom_type/d",0);
        $leader_id = I("get.leader_id/d",0);
        $data = ['goods_id'=>$goods_id,'item_id'=>$item_id,'prom_id'=>$prom_id,'prom_type'=>$prom_type,'first_leader'=>$leader_id,'team_id'=>$team_id];
        $goodsLogic = new GoodsLogic();
        $goodsLogic->getGoodsSharePoster($data);
    }
}