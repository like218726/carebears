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
 * Author: lhb
 * Date: 2017-05-15
 */

namespace app\common\logic\team;
use app\common\logic\OrderLogic;
use app\common\model\Order;
use app\common\model\team\TeamActivity;
use app\common\model\team\TeamFollow;
use app\common\model\team\TeamFound;
use app\common\model\team\TeamGoodsItem;
use app\common\model\Users;
use app\common\util\TpshopException;
use think\Cache;
use think\Db;

/**
 * 拼团活动逻辑类
 */
class Team
{
    private $teamGoodsItem;
    private $userId;
    private $user;
    private $teamActivity;
    private $teamId;
    private $foundId;
    private $teamFound;
    private $buyNum;
    private $order;

    private $teamGoods;//虚构的商品模型

    public function setTeamGoodsItemById($goods_id, $item_id)
    {
        $this->teamGoodsItem = TeamGoodsItem::get(['goods_id' => $goods_id, 'item_id' => $item_id, 'deleted' => 0]);
        if ($this->teamGoodsItem) {
            $this->teamId = $this->teamGoodsItem['team_id'];
            $this->teamActivity = $this->teamGoodsItem['team_activity'];
        }
    }

    public function setTeamActivityById($team_id)
    {
        if($team_id > 0){
            $this->teamId = $team_id;
            $this->teamActivity = TeamActivity::get($team_id);
        }
    }

    public function getTeamActivity()
    {
        return $this->teamActivity;
    }

    public function setTeamFoundById($found_id)
    {
        if($found_id){
            $this->foundId = $found_id;
            $this->teamFound = TeamFound::get($this->foundId);
            $this->teamGoodsItem = TeamGoodsItem::get(['team_id' =>$this->teamFound['team_id'],'deleted' => 0]);
            $this->teamActivity = $this->teamGoodsItem['team_activity'];
        }
    }

    public function getFoundId()
    {
        return $this->foundId;
    }

    /**
     * 查找差几个人参团
     * 查已参团成功支付成功的总数
     * @return int
     */
    public function getTeamFollowNum(){
        $team = TeamFound::get($this->foundId);
        $need = $team['need'] - 1;
        // status=2 3 不计算
        $num = Db::name('team_follow')->where('found_id', $this->foundId)->where('status', 1)->count(); // 有效
        if($num > 0){
            $need -= $num;
        }
        if($need <= 0) return 0;

        $num0 = Db::name('team_follow')->where('found_id', $this->foundId)->where('status', 0)->count(); // 是否有效？
        if($num0 > 0){
            $need -= $num0;
            $order_id = Db::name('team_follow')->where('found_id', $this->foundId)->where('status', 0)->column('order_id');
            $num2 = Db::name('order')->where('order_id','in',$order_id)->where('order_status','in','3,5')->count();
            if($num2 > 0){
                $need += $num2;
            }
        }
        if($need > 0 ){
            // 不作处理
            //$team->join = $team['need'] - $need;
            //$team->save();
        }
        return $need;
    }

    public function setUserById($user_id)
    {
        if($user_id > 0){
            $this->userId = $user_id;
            $this->user = Users::get($user_id);
        }
    }

    public function setBuyNum($buy_num)
    {
        $this->buyNum = $buy_num;
    }

    /**
     * 设置order模型
     * @param $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * 拼团支付后操作
     * @throws \think\Exception
     */
    public function doOrderPayAfter(){
        $teamFound = TeamFound::get(['order_id' => $this->order['order_id']]);
        //团长的单
        if ($teamFound) {
            $teamFound->found_time = time();
            $teamFound->found_end_time = time() + intval($this->teamActivity['time_limit']);
            $teamFound->status = 1;
            $teamFound->save();
            $team_found_queue =  Cache::get('team_found_queue');
            $team_found_queue[$teamFound->found_id] = $teamFound->need - $teamFound->join;
            Cache::set('team_found_queue',$team_found_queue);
        }else{
            //团员的单
            $teamFollow = TeamFollow::get(['order_id' => $this->order['order_id']]);
            if($teamFollow){
                $teamFollow->status = 1;
                $teamFollow->save();
                //更新团长的单
                $teamFollow->team_found->join = $teamFollow['team_found']['join'] + 1;//参团人数+1
                //如果参团人数满足成团条件
                $teamFollowCount = Db::name('team_follow')->where(['found_id' => $teamFollow['found_id'], 'status' => 1])->count('follow_id');
                if(($teamFollowCount + 1) >= $teamFollow->team_found->need){ // 并且不是抽奖团  && $teamFollow->teamActivity->team_type != 2
                    //加一是因为还有团长
                    $teamFollow->team_found->status = 2;//团长成团成功
                    //更新团员成团成功
                    Db::name('team_follow')->where(['found_id'=>$teamFollow->team_found->found_id,'status'=>1])->update(['status'=>2]);
					if($teamFollow->teamActivity->team_type != 2){
						//自动确认团成员的订单
						$follow_order_id = Db::name('team_follow')->where(['found_id' =>$teamFollow->team_found->found_id])->getField('order_id', true);
						array_push($follow_order_id,$teamFollow->team_found->order_id);
						Db::name('order')->where(['prom_type' => 6,'order_id'=>['IN', $follow_order_id]])->update(['order_status' => 1]);

                        $wechat = new \app\common\logic\WechatLogic;
                        $wechat->sendTemplateMsgOnTeam($teamFollow->team_found->found_id);

					}
                    
                }
                $teamFollow->team_found->save();
            }
        }
    }

    /**
     * 过滤拼团订单能使用的优惠券列表
     * @param $userCouponList
     * @return array
     */
    public function getCouponOrderList($userCouponList)
    {
        $userCouponArray = collection($userCouponList)->toArray();
        $couponNewList = [];
        foreach ($userCouponArray as $couponKey => $couponItem) {
            //过滤掉购物车没有的店铺优惠券
            if ($userCouponArray[$couponKey]['store_id'] == $this->order['store_id']) {
                if ($this->order['goods_price'] >= $userCouponArray[$couponKey]['coupon']['condition']) {
                    $userCouponArray[$couponKey]['coupon']['able'] = 1;
                } else {
                    $userCouponArray[$couponKey]['coupon']['able'] = 0;
                }
                $couponNewList[] = $userCouponArray[$couponKey];
            }
        }
        return $couponNewList;
    }
    /**
     * 过滤拼团订单能使用的优惠券列表|api专用
     * @param $userCouponList
     * @return array
     */
    public function getCouponOrderAbleList($userCouponList)
    {
        $userCouponArray = collection($userCouponList)->toArray();
        $couponNewList = [];
        foreach ($userCouponArray as $couponKey => $couponItem) {
            //过滤掉购物车没有的店铺优惠券
            if ($userCouponArray[$couponKey]['store_id'] == $this->order['store_id']) {
                if ($this->order['goods_price'] >= $userCouponArray[$couponKey]['coupon']['condition']) {
                    $coupon = $userCouponArray[$couponKey]['coupon'];
                    $coupon['id'] = $userCouponArray[$couponKey]['id'];
                    $coupon['cid'] = $userCouponArray[$couponKey]['cid'];
                    $store = Db::name('store')->field('store_name')->where('store_id', $coupon['store_id'])->find();
                    $coupon['store_name'] = $store['store_name'];
                    unset($coupon['goods_coupon']);
                    $couponNewList[] = $coupon;
                }
            }
        }
        return $couponNewList;
    }

    public function buy()
    {
        if (empty($this->teamActivity) || $this->teamActivity['status'] != 1) {
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '该商品拼团活动不存在或者已下架', 'result' => '']);
        }
        if ($this->teamActivity['is_lottery'] == 1) {
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '该商品拼团活动已开奖', 'result' => '']);
        }
        $this->teamGoods = $goods = $this->teamActivity->goods;
        if (empty($goods) || $goods['is_on_sale'] != 1 || $goods['prom_type'] != 6) {
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '该商品拼团活动不存在或者已下架', 'result' => '']);
        }
        if ($this->teamGoodsItem['item_id'] > 0) {
            $spec_goods_price = $this->teamGoodsItem->specGoodsPrice;
            $this->teamGoods['spec_key'] = $spec_goods_price['key'];
            $this->teamGoods['spec_key_name'] = $spec_goods_price['key_name'];
            $this->teamGoods['sku'] = $spec_goods_price['sku'];
            $this->teamGoods['prom_id'] = $spec_goods_price['prom_id'];
            $this->teamGoods['prom_type'] = $spec_goods_price['prom_type'];
            $this->teamGoods['shop_price'] = $spec_goods_price['price'];
            if(empty($spec_goods_price) || $spec_goods_price['prom_type'] != 6){
                throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '该商品拼团活动不存在或者已下架', 'result' => '']);
            }
            if($this->buyNum > $spec_goods_price['store_count']){
                throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '商品库存仅剩余'.$spec_goods_price['store_count'], 'result' => '']);
            }
        }
        if($this->buyNum > $goods['store_count']){
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '商品库存仅剩余'.$goods['store_count'], 'result' => '']);
        }
        if ($this->buyNum <= 0) {
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '至少购买一份', 'result' => '']);
        }
        if ($this->teamActivity['buy_limit'] != 0 && $this->buyNum > $this->teamActivity['buy_limit']) {
            throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '购买数已超过该活动单次购买限制数(' . $this->teamActivity['buy_limit'] . ')', 'result' => '']);
        }
        if($this->foundId){
            if(empty($this->teamFound) || $this->teamFound['status'] != 1){
                throw new TpshopException('拼团购买商品',601,['status' => 0, 'msg' => '该拼单数据不存在或已失效', 'result' => '']);
            }
            if($this->teamFound['user_id'] == $this->userId){
                throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '不能自己开团自己拼', 'result' => '']);
            }
            if($this->teamActivity['team_type'] == 2){
                //抽奖团，只能拼一次团
                $teamYouSelfFollow = Db::name('team_follow')->where(['follow_user_id' => $this->userId, 'team_id' => $this->teamId, 'status' => ['in', '1,2']])->find();
                if($teamYouSelfFollow){
                    throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '你已经参与过该拼团活动。', 'result' => '']);
                }
            }
            if($this->teamFound['team_id'] != $this->teamActivity['team_id']){
                throw new TpshopException('拼团购买商品',602,['status' => 0, 'msg' => '该拼单数据不存在或已失效', 'result' => '']);
            }
            //拼团订单里有可能存在未支付订单。
            $this->checkFollowNoPayByFound($this->teamFound);
            if ($this->teamFound['join'] >= $this->teamFound['need']) {
                throw new TpshopException('拼团购买商品', 0, ['status' => 0, 'msg' => '该单已成功结束', 'result' => '']);
            }
            if(time() - $this->teamFound['found_time'] > $this->teamActivity['time_limit']){
                throw new TpshopException('拼团购买商品',0,['status' => 0, 'msg' => '该拼单已过期', 'result' => '']);
            }
        }
        $this->teamGoods['goods_price'] = $this->teamGoodsItem['team_price'];
        $this->teamGoods['goods_num'] = $this->buyNum;
        $this->teamGoods['member_goods_price'] = $this->teamGoodsItem['team_price'];
    }

    public function getTeamBuyGoods()
    {
        return $this->teamGoods;
    }

    public function log(Order $order)
    {
        if($this->teamFound){
            /**团员拼团s**/
            $team_follow_data = [
                'follow_user_id' => $this->userId,
                'follow_user_nickname' => $this->user['nickname'],
                'follow_user_head_pic' => $this->user['head_pic'],
                'follow_time' => time(),
                'order_id' => $order['order_id'],
                'found_id' => $this->teamFound['found_id'],
                'found_user_id' => $this->teamFound['user_id'],
                'team_id' => $this->teamActivity['team_id'],
                'store_id' => $order['store_id'],
            ];
            Db::name('team_follow')->insert($team_follow_data);
//            Db::name('team_found')->where('found_id',$this->teamFound['found_id'])->setInc('join');
            /***团员拼团e***/
        }else{
            /***团长开团s***/
            $team_found_data = [
                'found_time'=>time(),
                'found_end_time' => time() + intval($this->teamActivity['time_limit']),
                'user_id' => $this->userId,
                'team_id' => $this->teamActivity['team_id'],
                'nickname' => $this->user['nickname'],
                'head_pic' =>  $this->user['head_pic'],
                'order_id' => $order['order_id'],
                'need' => $this->teamActivity['needer'],
                'price'=> $this->teamGoodsItem['team_price'],
                'goods_price' => $this->teamGoods['shop_price'],
                'store_id' => $order['store_id'],
            ];
            Db::name('team_found')->insert($team_found_data);
            /***团长开团e***/
        }
    }

    public function getTeamFound()
    {
        $team = $this->teamFound->teamActivity;
        if(time() - $this->teamFound['found_time'] > $team['time_limit']){
            //时间到了
            if($this->teamFound['join'] < $this->teamFound['need']){
                //人数没齐
                $this->teamFound->status = 3;//成团失败
                $this->teamFound->save();
                //更新团员成团失败
                Db::name('team_follow')->where(['found_id'=>$this->teamFound['found_id'],'status'=>1])->update(['status'=>3]);
            }
        }
        if ($this->teamFound['status'] == 1) {
            //拼团订单里有可能存在未支付订单。
            $this->checkFollowNoPayByFound($this->teamFound);
        }
        return $this->teamFound;
    }

    /**
     * 拼团订单里有可能存在未支付订单。
     * @param $found|团长对象
     */
    private function checkFollowNoPayByFound($found){
        $noPayFollowOrderIds = Db::name('team_follow')->where(['status' => 0, 'found_id' => $found['found_id']])->column('order_id');
        if ($noPayFollowOrderIds) {
            $noPayOrderList = Db::name('order')->where(['order_id' => ['IN',$noPayFollowOrderIds], 'pay_status' => 0, 'order_status' => 0])->order('order_id asc')->select();
            //找到最先未支付订单。然后查看是否超时未支付。如果超时，就将该订单做取消订单处理。
            if($noPayOrderList){
                $team_order_limit_time = tpCache('shopping.team_order_limit_time');
                $limitTime = empty($team_order_limit_time) ? 1800 : $team_order_limit_time;
                $orderLogic = new OrderLogic();
                foreach($noPayOrderList as $order){
                    if ((time() - $order['add_time']) > $limitTime) {
                        $orderLogic->cancel_order($order['user_id'], $order['order_id']);
                    }
                }
            }
        }
    }

}