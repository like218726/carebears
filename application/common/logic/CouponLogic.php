<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: dyr
 * Date: 2016-08-09
 */

namespace app\common\logic;

use app\common\model\Coupon;
use app\common\model\CouponList;
use think\Model;
use think\Db;

/**
 * 回复
 * Class CatsLogic
 * @package common\Logic
 */
class CouponLogic extends Model
{

    /**
     * 根据优惠券代码获取优惠券金额
     * @param $couponCode |优惠券代码
     * @param $order_money |订单金额
     * @param $store_id |商家id
     * @return array
     */
    public function getCouponMoneyByCode($couponCode, $order_money, $store_id)
    {
        if (empty($couponCode)) {
            return array('status' => -20, 'msg' => '优惠券码不存在', 'result' => '');
        }
        $couponList = M('CouponList')->where("store_id", $store_id)->where("code", $couponCode)->find(); // 获取用户的优惠券
        if (empty($couponList))
            return array('status' => -20, 'msg' => '优惠券码不存在', 'result' => '');
        if ($couponList['order_id'] > 0) {
            return array('status' => -20, 'msg' => '该优惠券已被使用', 'result' => '');
        }
        $coupon = M('Coupon')->where("id", $couponList['cid'])->find(); // 获取优惠券类型表
        if (time() < $coupon['use_start_time']) {
            return array('status' => -13, 'msg' => '该优惠券开始使用时间' . date('Y-m-d H:i:s', $coupon['use_start_time']), 'result' => '');
        }
        if (time() > $coupon['use_end_time'] or $coupon['status'] == 2) {
            return array('status' => -10, 'msg' => '优惠券已失效或过期', 'result' => '');
        }
        if ($order_money < $coupon['condition']) {
            return array('status' => -11, 'msg' => '金额没达到优惠券使用条件', 'result' => '');
        }
        return array('status' => 1, 'msg' => '', 'result' => $coupon['money']);
    }

    /**
     * 获取用户可以使用的优惠券金额
     * @param $user_id|用户id
     * @param $coupon_id|优惠券id
     * @param $store_id|商家id
     * @return int|mixed
     */
    public function getCouponMoney($user_id, $coupon_id, $store_id)
    {
        if ($coupon_id == 0) {
            return 0;
        }
        $couponList = M('CouponList')->where("store_id = $store_id and uid = $user_id and id = $coupon_id")->find(); // 获取用户的优惠券
        if (empty($couponList)) {
            return 0;
        }
        $coupon = M('Coupon')->where("id", $couponList['cid'])->find(); // 获取 优惠券类型表
        $coupon['money'] = $coupon['money'] ? $coupon['money'] : 0;
        return $coupon['money'];
    }
    
    /**
     * 获取发放有效的优惠券金额
     * @param type $coupon_id
     * @param type $goods_id
     * @param type $store_id
     * @param type $cat_id
     * @return boolean
     */
    public function getSendValidCouponMoney($coupon_id, $goods_id, $store_id, $cat_id)
    {
        $curtime = time();
        $coupon = M('coupon')->where('id', $coupon_id)->find();
        $goods_coupon = M('goods_coupon')->where('coupon_id', $coupon_id)->where(function ($query) use ($goods_id, $cat_id) {
            $query->where('goods_id', $goods_id)->whereOr('goods_category_id',$cat_id);
        })->select();
        
        if ($coupon && $coupon['send_start_time'] <= $curtime && $coupon['send_end_time'] > $curtime
                && $coupon['createnum'] > $coupon['send_enum']) {
            //use_type：0全店通用 1指定商品可用 2指定分类商品可用
            if (($coupon['use_type'] == 0 && ($store_id == $coupon['store_id'] || $coupon['store_id'] == 0))
             || ($coupon['use_type'] > 0 && !empty($goods_coupon))) {
                return $coupon['money'];
            }
        }
        return false;
    }

    /**
     * 获取用户可用的优惠券
     * @param $user_id|用户id
     * @param array $goods_ids|限定商品ID数组
     * @param array $goods_cat_id||限定商品分类ID数组
     * @return array
     */
    public function getUserAbleCouponList($user_id, $goods_ids = array(), $goods_cat_id = array())
    {
        $CouponList = new CouponList();
        $Coupon = new Coupon();
        $userCouponArr = [];
        $userCouponList = $CouponList->where('uid', $user_id)->where('deleted', 0)->where('status', 0)->select();//用户优惠券
        if(!$userCouponList){
            return $userCouponArr;
        }
        $userCouponId = get_arr_column($userCouponList, 'cid');
        $now = time();
        $coupons_id = implode(',',$userCouponId);
        $couponList1 = $Coupon
            ->alias('c')
            ->join('tp_coupon_list l','l.cid = c.id')
            ->field('c.*,l.send_time')
            ->distinct('c.id')
            ->where('c.type','=',4)
            ->where('c.status','=',1)
            ->where('c.id','IN',$userCouponId)
            ->where('l.uid',$user_id)
            ->where(" (l.send_time+(86400*c.validity_day)) >= {$now}")
            ->select();//检查优惠券是否可以用
        $couponList2 = (new Coupon())->with('GoodsCoupon')
            ->where('id','IN',$userCouponId)
            ->where('status',1)
            ->where('use_start_time','<',$now)
            ->where('use_end_time','>',$now)
            ->where('type','<>',4)
            ->select();
        $couponList=array_merge($couponList1,$couponList2);
        foreach($couponList as $k => $coupon){
            $coupon['goods_coupon']=Db::name('goods_coupon')->where(['coupon_id'=>$coupon['id']])->select();
            $couponList[$k]=$coupon;
        }
        foreach ($userCouponList as $userCoupon => $userCouponItem) {
            foreach ($couponList as $coupon => $couponItem) {
                if ($userCouponItem['cid'] == $couponItem['id']) {
                    //全店通用
                    if ($couponItem['use_type'] == 0) {
                        $tmp = $userCouponItem;
                        $tmp['coupon'] = $couponItem->append(['use_type_title'])->toArray();
                        $userCouponArr[] = $tmp;
                    }
                    //限定商品
                    if ($couponItem['use_type'] == 1 && !empty($couponItem['goods_coupon'])) {
                        foreach ($couponItem['goods_coupon'] as $goodsCoupon => $goodsCouponItem) {
                            if (in_array($goodsCouponItem['goods_id'], $goods_ids)) {
                                $tmp = $userCouponItem;
                                $tmp['coupon'] = array_merge($couponItem->append(['use_type_title'])->toArray(), $goodsCouponItem);
                                $userCouponArr[] = $tmp;
                                break;
                            }
                        }
                    }
                    //限定商品类型
                    if ($couponItem['use_type'] == 2 && !empty($couponItem['goods_coupon'])) {
                        foreach ($couponItem['goods_coupon'] as $goodsCoupon => $goodsCouponItem) {
                            if (in_array($goodsCouponItem['goods_category_id'], $goods_cat_id)) {
                                $tmp = $userCouponItem;
                                $tmp['coupon'] = array_merge($couponItem->append(['use_type_title'])->toArray(), $goodsCouponItem);
                                $userCouponArr[] = $tmp;
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $userCouponArr;
    }

    /**
     * 获取店铺商品可领取优惠券
     * @param array $store_ids|店铺id数组
     * @param array $goods_ids|商品id数组
     * @param array $goods_category_ids|商品分类数组
     * @return array
     */
    public function getStoreGoodsCoupon($store_ids = [], $goods_ids = [], $goods_category_ids = [])
    {
        //查询店铺下所有的优惠券
        $storeCoupon = Db::name('coupon')->where('store_id', 'IN', $store_ids)->select();
        $newStoreCoupon = $goodsCouponIds = [];//存放提取的优惠券|存放提取的优惠券id
        foreach ($storeCoupon as $couponKey => $couponVal) {
            //提取（免费领取，还有剩余发放数量，处于发放时间）优惠券，
            if ((($couponVal['createnum'] - $couponVal['send_num']) > 0 || $couponVal['createnum'] == 0)
                && $couponVal['type'] == 2 && $couponVal['send_start_time'] < time() && $couponVal['send_end_time'] > time()
                && $couponVal['status'] == 1
            ) {
                $newStoreCoupon[] = $couponVal;//存放提取的优惠券
                //提取（指定商品或者商品分类类型）优惠券id
                if ($couponVal['use_type'] == 1 || $couponVal['use_type'] == 2) {
                    $goodsCouponIds[] = $couponVal['id'];//存放提取的优惠券id
                }
            }
        }
        if ($goodsCouponIds) {
            //查询（指定商品或者商品分类）优惠券记录
            $goodsCouponList = Db::name('goods_coupon')->where('coupon_id', 'IN', $goodsCouponIds)->select();
            if ($goodsCouponList) {
                $newGoodsCouponIds = [];//存放指定商品Id和商品分类Id的优惠券ID
                foreach ($goodsCouponList as $gcKey => $gcVal) {
                    //验证并提取（指定商品或者商品分类）优惠券id
                    if (in_array($gcVal['goods_id'], $goods_ids) || in_array($gcVal['goods_category_id'], $goods_category_ids)) {
                        if (!in_array($gcVal['coupon_id'], $newGoodsCouponIds)) {
                            array_push($newGoodsCouponIds, $gcVal['coupon_id']);
                        }
                    }
                }
                if ($newGoodsCouponIds) {
                    $tmp = [];
                    //过滤不存在的指定商品或者商品分类类型的优惠券
                    foreach ($newStoreCoupon as $newCouponKey => $newCouponVal) {
                        if (($newCouponVal['use_type'] == 1 || $newCouponVal['use_type'] == 2) && !in_array($newCouponVal['id'], $newGoodsCouponIds)) {
                            continue;
                        }
                        $tmp[] = $newCouponVal;
                    }
                    unset($newStoreCoupon);
                    $newStoreCoupon = $tmp;
                }
            }
        }
        return $newStoreCoupon;
    }
    
    /**
     * 优惠券兑换
     * @param type $user_id
     * @param type $coupon_code
     * @return json
     */
    public function exchangeCoupon($user_id, $coupon_code)
    {
        if ($user_id == 0){
            return ['status' => -100, 'msg' => "登录超时请重新登录!", 'result' => null];
        }
        if (!$coupon_code) {
            return ['status' => '0', 'msg' => '请输入优惠券券码', 'result' => ''];
        }
        $coupon_list = Db::name('coupon_list')->where('code', $coupon_code)->find();
        if (empty($coupon_list)){
            return ['status' => 0, 'msg' => '优惠券码不存在', 'result' => ''];
        }
        if ($coupon_list['order_id'] > 0) {
            return ['status' => 0, 'msg' => '该优惠券已被使用', 'result' => ''];
        }
        if ($coupon_list['uid'] > 0) {
            return ['status' => 0, 'msg' => '该优惠券已兑换', 'result' => ''];
        }
        $coupon = Coupon::get($coupon_list['cid']); // 获取优惠券类型表
        if (time() < $coupon['use_start_time']) {
            return ['status' => 0, 'msg' => '该优惠券开始使用时间' . date('Y-m-d H:i:s', $coupon['use_start_time']), 'result' => ''];
        }
        if (time() > $coupon['use_end_time'] || $coupon['status'] == 2) {
            return ['status' => 0, 'msg' => '优惠券已失效或过期', 'result' => ''];
        }
        $do_exchange = Db::name('coupon_list')->where('id',$coupon_list['id'])->update(['uid'=>$user_id]);
        if ($do_exchange !== false) {
            return ['status' => 1, 'msg' => '兑换成功', 
                'result' => ['coupon' => $coupon->append(['is_expiring','use_start_time_format_dot','use_end_time_format_dot'])->toArray(), 'coupon_list'=>$coupon_list]];
        } else {
            return ['status' => 0, 'msg' => '兑换失败', 'result' => ''];
        }
    }
    /**
     * 用户领券
     * @param $id |优惠券id
     * @param $user_id
     * @return array
     */

    public function get_coupon($id, $user_id)
    {
        if (empty($id)){
            $return = ['status' => 0, 'msg' => '参数错误'];
        }
        if ($user_id) {
            $_SERVER['HTTP_REFERER'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : U('Home/Activity/coupon_list');
            $coupon_info = M('coupon')->where(array('id' => $id, 'status' => 1))->find();
            if (empty($coupon_info)) {
                $return = ['status' => 0, 'msg' => '活动已结束或不存在，看下其他活动吧~','return_url'=>$_SERVER['HTTP_REFERER']];
            } elseif ($coupon_info['send_end_time'] < time()) {
                //来晚了，过了领取时间
                $return = ['status' => 0, 'msg' => '抱歉，已经过了领取时间','return_url'=>$_SERVER['HTTP_REFERER']];
            } elseif ($coupon_info['send_num'] >= $coupon_info['createnum'] && $coupon_info['createnum'] != 0) {
                //来晚了，优惠券被抢完了
                $return = ['status' => 0, 'msg' => '来晚了，优惠券被抢完了','return_url'=>$_SERVER['HTTP_REFERER']];
            } else {
                if (M('coupon_list')->where(array('cid' => $id, 'uid' => $user_id))->find()) {
                    //已经领取过
                    $return = ['status' => 2, 'msg' => '您已领取过该优惠券','return_url'=>$_SERVER['HTTP_REFERER']];
                } else {
                    $data = array('uid' => $user_id, 'cid' => $id, 'type' => 2, 'send_time' => time(),'status'=>0);
                    M('coupon_list')->add($data);
                    M('coupon')->where(array('id' => $id, 'status' => 1))->setInc('send_num');
                    $return = ['status' => 1, 'msg' => '恭喜您，抢到' . $coupon_info['money'] . '元优惠券!','return_url'=>$_SERVER['HTTP_REFERER']];
                }
            }
        } else {
            $return = ['status' => 0, 'msg' => '请先登录','return_url'=>U('User/login')];
        }

        return $return;
    }
}