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
 * $Author: 当燃 2016-01-09
 */ 
namespace app\mobile\controller;
class Channel extends MobileBase {
	
	public function index(){
		$cat_id = I('cat_id/d',1);
		$channel_cate = $this->cateTrre[$cat_id]['tmenu'];
		$sub_id = ''; $sub_goods = array();
		foreach ($channel_cate as $k=>$val){
			foreach ($val['sub_menu'] as $v){
				$sub_id .= $v['id'].',';//三级分类ID集
			}
			$second_str .= $val['id'].',';
		}
		
		$all_cat_id = trim(($second_str.$sub_id),',');
		
		//查询所有此频道三级分类商品
		$sub_id_str = '('.trim($sub_id,',').')';
		$sql = "select goods_id,cat_id,goods_name,shop_price,market_price from __PREFIX__goods where is_on_sale=1 and cat_id in $sub_id_str ";
		$sub_goods_arr = DB::query($sql);
		if($sub_goods_arr){
			foreach ($sub_goods_arr as $val){
				$sub_goods[$val['cat_id']][] = $val;//商品按分类分组
			}
			//商品归属到三级分类下sub_goods项
			foreach ($channel_cate as $kk=>$vv){
				foreach ($vv['sub_menu'] as $mk=>$vo){
					$channel_cate[$kk]['sub_menu'][$mk]['sub_goods'] = empty($sub_goods[$vo['id']]) ? array() : $sub_goods[$vo['id']];
				}
			}
		}	
		//echo '<pre>';
		//print_r($channel_cate);
		//exit;
		$this->assign('parent_name', $this->cateTrre[$cat_id]['name']);
		$this->assign('channel_cate',$channel_cate);
		return $this->fetch();
	}
}