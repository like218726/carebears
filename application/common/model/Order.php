<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * Author: dyr
 * Date: 2016-08-23
 */

namespace app\common\model;

use think\Model;
use think\Db;

class Order extends Model
{
    protected $table='';


    //自定义初始化
    protected function initialize()
    {
        parent::initialize();
        $select_year = select_year(); // 查询 三个月,今年内,2016年等....订单
        $prefix = C('database.prefix');  //获取表前缀
        $this->table = $prefix.'order'.$select_year;
    }

    public function shop()
    {
        return $this->hasOne('shop', 'shop_id', 'shop_id');
    }

    public function shopOrder()
    {
        return $this->hasOne('ShopOrder', 'order_id', 'order_id');
    }

    /**
     * 获取订单商品
     * @return \think\model\relation\HasMany
     */
    public function OrderGoods()
    {
        return $this->hasMany('OrderGoods','order_id','order_id');
    }
    /**
     * 订单商品总数
     * @return float|int
     */
    public function countGoodsNum()
    {
        return $this->hasMany('OrderGoods', 'order_id', 'order_id')->sum('goods_num');
    }

    /**
     * 获取快递公司信息
     * @return \think\model\relation\HasMany
     */
    public function Shipping()
    {
        return $this->hasOne('Shipping','shipping_code','shipping_code');
    }
    /**
     * 获取订单操作记录
     * @return \think\model\relation\HasMany
     */
    public function OrderAction()
    {
        return $this->hasMany('OrderAction','order_id','order_id')->order('action_id desc');
    }
    /**
     * 获取订单发货单
     * @return \think\model\relation\HasMany
     */
    public function DeliveryDoc()
    {
        return $this->hasMany('DeliveryDoc','order_id','order_id');
    }
    /**
     * 获取订单店铺
     * @return $this
     */
    public function store()
    {
        return $this->hasone('store','store_id','store_id')->field('store_id,store_name,store_qq,store_phone,store_logo,store_avatar,qitian,store_free_price');
    }

    public function users(){
        return $this->hasone('users','user_id','user_id')->field('user_id,nickname');
    }

    /**
     * 配送方式
     */
    public function getDeliveryMethodAttr($value, $data)
    {
        if ($data['shop_id'] > 0) {
            return "上门自提";
        } else {
            return "快递配送";
        }
    }

    /**
     * 获取虚拟订单的兑换码
     * @return \think\model\relation\HasMany
     */
    public function VrOrderCode(){
        return $this->hasMany('vr_order_code','order_id','order_id');
    }

    public function preSell()
    {
        return $this->hasOne('PreSell', 'pre_sell_id', 'prom_id');
    }

    /**
     *  只有在订单为拼团才有用:prom_type = 6
     */
    public function teamActivity()
    {
        return $this->hasOne('app\common\model\team\TeamActivity', 'team_id', 'prom_id');
    }

    public function teamFollow(){
        return $this->hasOne('app\common\model\team\TeamFollow','order_id','order_id');
    }

    public function teamFound(){
        return $this->hasOne('app\common\model\team\TeamFound','order_id','order_id');
    }
    /**
     * 是否虚拟订单，获取虚拟订单的code码
     */
    public function getVrordersCodeAttr($value, $data)
    {
        if($data['prom_type'] == 5){
            $VirtualLogic = new VirtualLogic();
            $virtual = $VirtualLogic->check_virtual_code($data);
            return $virtual['vrorders'];
        }
        return [];
    }

    public function getPayStatusDetailAttr($value, $data)
    {
        $pay_status = config('PAY_STATUS');
        return $pay_status[$data['pay_status']];
    }
    //支付尾款按钮
    public function getPayTailBtnAttr($value, $data){
        if($data['prom_type'] == 4 && $data['pay_status'] == 2){
            $pre_sell = db('pre_sell')->where('pre_sell_id', $data['prom_id'])->find();
            if($pre_sell['is_finished'] == 2 && (time() >= $pre_sell['pay_start_time'] && $pre_sell['pay_end_time'] >= time())){
                return 1;
            }
        }
        return 0;
    }
    public function getWxQrAttr($value, $data)
    {
        //获取自提订单页面公众号图片
        if ($data['prom_type'] == 9) {
            $wx = DB::name('wx_user')->find();
            return !empty($wx['qr'])?$wx['qr']:1;
        }
        return 1;
    }
    public function getShippingStatusDetailAttr($value, $data)
    {
        $shipping_status = config('SHIPPING_STATUS');
        return $shipping_status[$data['shipping_status']];
    }

    /**
     * 获取订单状态对应的中文
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getOrderStatusDetailAttr($value, $data)
    {
        $data_status_arr = C('ORDER_STATUS_DESC');
        // 货到付款
        if ($data['pay_code'] == 'cod') {
            if (in_array($data['order_status'], array(0, 1)) && $data['shipping_status'] == 0) {
                return $data_status_arr['WAITSEND']; //'待发货',
            }
        } else {
            // 非货到付款
            if ($data['pay_status'] == 0 && $data['order_status'] == 0) {
                return $data_status_arr['WAITPAY']; //'待支付',
            }
            if ($data['pay_status'] == 1 && in_array($data['order_status'], array(0, 1)) && $data['shipping_status'] != 1) {
                if ($data['prom_type'] == 5) { //虚拟商品
                    return $data_status_arr['WAITRECEIVE']; //'待收货',
                } elseif($data['prom_type'] == 6){
                    if($data['order_status'] == 0){
                        return '待处理';
                    }else{
                        return $data_status_arr['WAITSEND']; //'待发货',
                    }
                }
                else {
                    if ($data['shop_id'] > 0) {
                        return '待自提';
                    }
                    return $data_status_arr['WAITSEND']; //'待发货',
                }
            }
            if($data['pay_status'] == 2 && in_array($data['order_status'], array(0, 1)) && $data['shipping_status'] != 1){
                return '已付订金';
            }
        }
        if (($data['shipping_status'] == 1) && ($data['order_status'] == 1)) {
            return $data_status_arr['WAITRECEIVE']; //'待收货',
        } elseif ($data['order_status'] == 2){
            return $data_status_arr['WAITCCOMMENT']; //'待评价',
        } elseif ($data['order_status'] == 3 && $data['pay_status']==3) {
            return $data_status_arr['CANCEL_REFUND']; //'已取消&已退款',
        }elseif ($data['order_status'] == 3) {
            return $data_status_arr['CANCEL']; //'已取消',
        } elseif ($data['order_status'] == 4) {
            return $data_status_arr['FINISH']; //'已完成',
        } elseif ($data['order_status'] == 5) {
            return $data_status_arr['CANCELLED'];
        }
        return $data_status_arr['OTHER'];
    }

    /**
     * 获取订单状态的 显示按钮
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getOrderButtonAttr($value, $data)
    {
        /**
         *  订单用户端显示按钮
         * 去支付     AND pay_status=0 AND order_status=0 AND pay_code ! ="cod"
         * 取消按钮  AND pay_status=0 AND shipping_status=0 AND order_status=0
         * 确认收货  AND shipping_status=1 AND order_status=0
         * 评价      AND order_status=1
         * 查看物流  if(!empty(物流单号))
         */
        $btn_arr = array(
            'pay_btn' => 0, // 去支付按钮
            'cancel_btn' => 0, // 取消按钮
            'receive_btn' => 0, // 确认收货
            'comment_btn' => 0, // 评价按钮
            'shipping_btn' => 0, // 查看物流
            'return_btn' => 0, // 退货按钮 (联系客服)
            'return_overdue' => 0, // 是否过了售后时间
        );
        $auto_service_date = tpCache('shopping.auto_service_date'); //申请售后时间段
        $confirm_time = strtotime ( "-$auto_service_date day" );
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return $btn_arr;
        }
        if($data['confirm_time']<$confirm_time){
            $btn_arr['return_overdue']=1;
        }
        // 货到付款
        if ($data['pay_code'] == 'cod') {
            // 待发货
            if (($data['order_status'] == 0 || $data['order_status'] == 1) && $data['shipping_status'] == 0) {
                $btn_arr['cancel_btn'] = 1; // 取消按钮 (联系客服)
            }
            //待收货
            if ($data['shipping_status'] == 1 && $data['order_status'] == 1) {
                $btn_arr['receive_btn'] = 1;  // 确认收货
                $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
            }
        } else {
            // 非货到付款
            // 待支付
            if ($data['pay_status'] == 0 && $data['order_status'] == 0) {
                $btn_arr['pay_btn'] = 1; // 去支付按钮
                $btn_arr['cancel_btn'] = 1; // 取消按钮

                if ($data['prom_type'] == 4) {//预售活动 活动结束后不能支付定金(就算已经下单了)
                    $sell_end_time = db('pre_sell')->where('pre_sell_id', $data['prom_id'])->value('sell_end_time');
                    if ($sell_end_time < time()) {
                        $btn_arr['pay_btn'] = 0; // 去支付按钮
                    }
                }
            }
			//部分支付
			if ($data['pay_status'] == 2 && $data['order_status'] == 0) {
                $btn_arr['cancel_btn'] = 1; // 取消按钮
            }
            // 待发货
            if ($data['pay_status'] == 1 && $data['order_status'] < 2 && $data['shipping_status'] == 0) {
                if ($data['prom_type'] == 6) {
                    $btn_arr['cancel_btn'] = 0;
                } else {
                    $btn_arr['cancel_btn'] = 1; // 取消按钮
                }
            }
            //待收货
            if ($data['pay_status'] == 1 && $data['order_status'] == 1 && $data['shipping_status'] == 1) {
                $btn_arr['receive_btn'] = 1;  // 确认收货
            }
        }
        if ($data['order_status'] == 4) {
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
        if ($data['order_status'] == 2) {
            if ($data['is_comment'] == 0) $btn_arr['comment_btn'] = 1;  // 评价按钮
            $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
        }
        if ($data['shipping_status'] > 0 && $data['order_status'] == 1) {
            $btn_arr['shipping_btn'] = 1; // 查看物流
        }
        if ($data['order_status'] == 1 && $data['shipping_status'] == 1) {
            $btn_arr['return_btn'] = 1; //确认订单也可以申请售后&物流状态必须为已发货(部分发货暂时不考虑)
        }

        /*    if ($data['shipping_status'] == 2 && $data['order_status'] == 1) // 部分发货
            {
                $btn_arr['return_btn'] = 1; // 退货按钮 (联系客服)
            }*/
        if ($data['order_status'] == 3 && ($data['pay_status'] == 1 || $data['pay_status'] == 4 || $data['pay_status'] == 3)) {
            $btn_arr['cancel_info'] = 1; // 取消订单详情
        }
        return $btn_arr;
    }

    /**
     * 商家可操作订单按钮
     * 操作按钮汇总 ：付款、设为未付款、确认、取消确认、无效、去发货、确认收货、申请退货
     * @param $value
     * @param $data
     * @return array
     */
    public function getSellerButtonAttr($value, $data)
    {
        // 三个月以前订单无任何操作按钮
        if(time() - $data['add_time'] > (86400 * 90)){
            return array();
        }
        $os = $data['order_status'];//订单状态
        $ss = $data['shipping_status'];//发货状态
        $ps = $data['pay_status'];//支付状态
        $pt = $data['prom_type'];//订单类型：0默认1抢购2团购3优惠4预售5虚拟6拼团
        $btn = array();
		if ($data['store_id'] == STORE_ID) {
			//销售商订单按钮
			if ($data['suppliers_id'] > 0) {
				//销售商供应商品的订单按钮
				if($data['pay_code'] == 'cod') {
					if($ss == 1 && $os == 1 && $ps == 0){
						$btn['pay'] = '付款';
					}elseif($ps == 1 && $ss == 1 && $os == 1){
						if($pt != 6){
							$btn['pay_cancel'] = '设为未付款';
						}
					}
				}else{
					if($os == 0 && $ps == 1){
						if(array_key_exists($pt,[4,6])){
							$btn['pay_cancel'] = '订单退款';
						}
					}
				}

				if($ss == 1 && $os == 1 && $ps == 1){
					$btn['delivery_confirm'] = '确认收货';
					$btn['refund'] = '申请退货';
				}elseif($os == 2 || $os == 4){
					$btn['refund'] = '申请退货';
				}
				if($os != 5 && $ps != 1){
					$btn['invalid'] = '无效';
				}
			} else {
				//销售商自己商品的订单按钮
				if($data['pay_code'] == 'cod') {
					if($os == 0 && $ss == 0){
						if($pt != 6){
							$btn['confirm'] = '确认';
						}
					}elseif($os == 1 && $ss == 0 ){
						$btn['delivery'] = '去发货';
						if($pt != 6){
							$btn['cancel'] = '取消确认';
						}
					}elseif($ss == 1 && $os == 1 && $ps == 0){
						$btn['pay'] = '付款';
					}elseif($ps == 1 && $ss == 1 && $os == 1){
						if($pt != 6){
							$btn['pay_cancel'] = '设为未付款';
						}
					}elseif($os == 1 && $ss == 2){
						$btn['delivery'] = '去发货';
					}
				}else{
					if($ps == 0 && $os == 0 || $ps == 2){
						//$btn['pay'] = '付款';
					}elseif($os == 0 && $ps == 1){
						if(array_key_exists($pt,[4,6])){
							$btn['pay_cancel'] = '订单退款';
							$btn['confirm'] = '确认';
						}
						if($pt == 4){
							$pre_sell = Db::name('pre_sell')->where('pre_sell_id',$data['prom_id'])->find();
							if($pre_sell['is_finished'] == 2){
								$btn['confirm'] = '确认';
							}
						}
					}elseif($os == 1 && $ps == 1 && $ss==0){
						if($pt != 6){
							$btn['cancel'] = '取消确认';
						}
						$btn['delivery'] = '去发货';
					}elseif(($os == 1 && $ps == 1 && $ss==2)){
						$btn['delivery'] = '去发货';
					}
				}

				if($ss == 1 && $os == 1 && $ps == 1){
					$btn['delivery_confirm'] = '确认收货';
					$btn['refund'] = '申请退货';
				}elseif($os == 2 || $os == 4){
					$btn['refund'] = '申请退货';
				}elseif($os == 3 || $os == 5){
		//        	$btn['remove'] = '移除';
				}
				if($os != 5 && $ps != 1){
					$btn['invalid'] = '无效';
				}
				if ($os < 2) {
					$btn['edit'] = '修改订单'; // 修改订单
					$select_year = getTabByOrderId($data['order_id']);
					$c = Db::name('order_goods' . $select_year)->where('order_id', $data['order_id'])->sum('goods_num');
					if ($c >= 2 && $ps == 1) {
						$btn['split'] = '拆分订单'; // 拆分订单
					}
				}
			}
		} else {
			//供应商的订单按钮
			if($data['pay_code'] == 'cod') {
				if($os == 0 && $ss == 0){
					if($pt != 6){
						$btn['confirm'] = '确认';
					}
				}elseif($os == 1 && $ss == 0 ){
					$btn['delivery'] = '去发货';
					if($pt != 6){
						$btn['cancel'] = '取消确认';
					}
				}elseif($os == 1 && $ss == 2){
					$btn['delivery'] = '去发货';
				}
			}else{
				if($os == 0 && $ps == 1){
					if(array_key_exists($pt,[4,6])){
						$btn['confirm'] = '确认';
					}
					if($pt == 4){
						$pre_sell = Db::name('pre_sell')->where('pre_sell_id',$data['prom_id'])->find();
						if($pre_sell['is_finished'] == 2){
							$btn['confirm'] = '确认';
						}
					}
				}elseif($os == 1 && $ps == 1 && $ss==0){
					if($pt != 6){
						$btn['cancel'] = '取消确认';
					}
					$btn['delivery'] = '去发货';
				}elseif(($os == 1 && $ps == 1 && $ss==2)){
					$btn['delivery'] = '去发货';
				}
			}
		}
        return $btn;
    }
    /**
     * 支付按钮
     * @param $value
     * @param $data
     * @return int
     */
    public function getPayBtnAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        // 货到付款,虚拟订单不会为cod
        if ($data['pay_code'] == 'cod') {
            return 0;
        }
        // 待支付
        if ($data['pay_status'] == 0 && $data['order_status'] == 0) {
            return 1; // 去支付按钮
        }
        return 0;
    }

    /**
     * 取消按钮
     * @param $value
     * @param $data
     * @return int
     */
    public function getCancelBtnAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        // 货到付款
        if ($data['pay_code'] == 'cod') {
            if (in_array($data['order_status'], [0, 1]) && $data['shipping_status'] == 0) {
                return 1; // 取消按钮 (订单待确认和已确认，未发货情况)
            }
        }
        //(订单已支付待确认和已确认，未发货情况)
        if ($data['pay_status'] == 1 && in_array($data['order_status'], [0, 1])) {
            //拼团和预售已支付订单不能取消，普通订单已支付未发货能取消,虚拟订单不走这个逻辑
            if(!in_array($data['prom_type'], [4, 5, 6]) && $data['shipping_status'] == 0){
                return 1;
            }
            if($data['prom_type'] == 5){
                $vr_order_code = Db::name('vr_order_code')->where(['order_id' => $data['order_id']])->find();
                if (!empty($vr_order_code)) {
                    if ($vr_order_code['vr_state'] != 1 && $vr_order_code['refund_lock'] < 1) {
                        if ($vr_order_code['vr_indate'] > time()) {
                            return 2; // 已支付取消按钮
                        }
                        if ($vr_order_code['vr_indate'] < time() && $vr_order_code['vr_invalid_refund'] == 1) {
                            return 2; // 已支付取消按钮
                        }
                    }
                }
            }
        }
        // 待支付
        if ($data['pay_status'] == 0 && $data['order_status'] == 0) {
            return 1; // 取消按钮
        }
        return 0;
    }

    /**
     * 确认收货
     * @param $value
     * @param $data
     * @return int
     */
    public function getReceiveBtnAttr($value, $data)
    {
        //发起了申请售后之后客户端不可展示确认收货按钮
        if(DB::name('return_goods')->where(['order_id'=>$data['order_id']])->find()){
            return 0;
        }
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        // 货到付款
        if ($data['pay_code'] == 'cod') {
            if ($data['shipping_status'] == 1 && $data['order_status'] == 1 && $data['pay_status'] == 1) //待收货
            {
                return 1;  // 确认收货 （已确认，已发货）
            }
        } else {
            if ($data['pay_status'] == 1 && $data['order_status'] == 1 && $data['shipping_status'] == 1) {
                return 1;  // 确认收货（已支付，已确认，已发货）
            }
        }
        if($data['prom_type'] == 5){
            $vr_order_code = Db::name('vr_order_code')->where(['order_id' => $data['order_id']])->find();
            if (!empty($vr_order_code)) {
                if ($data['pay_status'] == 1 && $data['order_status'] < 2 && $vr_order_code['vr_state'] != 1 && $vr_order_code['refund_lock'] < 1) {
                    return 1;
                }
            }
        }
        return 0;
    }

    /**
     * 取消详情
     * @param $value
     * @param $data
     * @return int
     */
    public function getCancelInfoAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        if ($data['order_status'] == 3 && in_array($data['pay_status'],[1,3,4])) {
            return 1; // 取消订单详情
        }
        return 0;
    }

    /**
     * 评价按钮
     * @param $value
     * @param $data
     * @return int
     */
    public function getCommentBtnAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        if ($data['order_status'] == 2) {
            return 1; // （已收货状态出现评价按钮）
        }
        return 0;
    }

    /**
     * 查看物流
     * @param $value
     * @param $data
     * @return int
     */
    public function getShippingBtnAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }
        if ($data['shipping_status'] >0 && $data['pay_status'] >= 1 && $data['shipping_name'] == ''){
            return 0;// 无需物流
        }
        if ($data['shipping_status'] > 0 && $data['order_status'] > 0) {
            return 1; // 已发货并且已支付
        }
        return 0;
    }

    /**
     * 退货按钮 (联系客服)
     * @param $value
     * @param $data
     * @return int
     */
    public function getReturnBtnAttr($value, $data)
    {
        // 三个月(90天)内的订单才可以进行有操作按钮. 三个月(90天)以外的过了退货 换货期, 即便是保修也让他联系厂家, 不走线上
        if (time() - $data['add_time'] > (86400 * 90)) {
            return 0;
        }

        if (in_array($data['order_status'], [2, 4]) || (in_array($data['shipping_status'], [1]) && $data['order_status'] == 1 && $data['pay_status']==1)) {
            return 1; // 退货按钮 (联系客服)
        }
//        if (in_array($data['shipping_status'], [1, 2]) && $data['order_status'] == 1) {
//            return 1; // 退货按钮 (联系客服)
//        }

        return 0;
    }

    public function getVirtualOrderButtonAttr($value, $data){
        $vr_order_code = Db::name('vr_order_code')->where(['order_id'=>$data['order_id']])->find();
        $Virtual_btn_arr = array(
            'pay_btn' => 0, // 去支付按钮
            'cancel_btn' => 0, // 取消按钮
            'receive_btn' => 0, // 确认收货
            'comment_btn' => 0, // 评价按钮
        );
        if ($data['pay_status'] == 0 && $data['order_status'] == 0) {   // 待支付
            $Virtual_btn_arr['pay_btn'] = 1; // 去支付按钮
            $Virtual_btn_arr['cancel_btn'] = 1; //未支付取消按钮
        }
        if(!empty($vr_order_code)){
            if ($data['pay_status']==1 && $data['order_status']<2 && $vr_order_code['vr_state']!=1 && $vr_order_code['refund_lock']<1)
            {
                if ($vr_order_code['vr_indate'] > time() ) {
                    $Virtual_btn_arr['cancel_btn'] = 2; // 已支付取消按钮
                }
                if ($vr_order_code['vr_indate'] < time() && $vr_order_code['vr_invalid_refund'] == 1)
                {
                    $Virtual_btn_arr['cancel_btn'] = 2; // 已支付取消按钮
                    M('vr_order_code')->where(array('order_id'=>$data['order_id']))->update(['vr_state'=>2]);
                }
                $Virtual_btn_arr['receive_btn'] = 1;
            }
            if ($data['order_status'] == 2) {
                if ($data['is_comment'] == 0) $Virtual_btn_arr['comment_btn'] = 1;  // 评价按钮
            }
        }
        return $Virtual_btn_arr;
    }

    public function getAddressRegionAttr($value, $data){
        $regions = Db::name('region')->where('id', 'IN', [$data['province'], $data['city'], $data['district'],$data['twon']])->order('level desc')->select();
        $address = '';
        if($regions){
            foreach($regions as $regionKey=>$regionVal){
                $address = $regionVal['name'] . $address;
            }
        }
        return $address;
    }

    public function getFinallyPayTimeAttr($value, $data){
        return $data['add_time'] + config('finally_pay_time');
    }

    public function getTotalFeeAttr($value, $data){
        return $data['goods_price'] + $data['shipping_price'] - $data['integral_money'] - $data['coupon_price'] - $data['discount'];
    }
    public function getFullAddressAttr($value, $data){
        $region = Db::name('region')->where('id', 'IN', [$data['province'], $data['city'], $data['district'],$data['twon']])->column('name');
        return implode('', $region) . $data['address'];
    }

    public function getPromTypeDescAttr($value, $data){
        if($data['prom_type'] == 4){
            return '预售订单';
        }elseif($data['prom_type'] == 5){
            return '虚拟订单';
        }elseif($data['prom_type'] == 6){
            return '拼团订单';
        }else{
            return '普通订单';
        }
    }
    /**
     * 获取订单状态对(商家)
     *
    定义一个变量, 用于前端UI显示订单5个状态进度. 1: 提交订单;2:订单支付; 3: 商家发货; 4: 确认收货; 5: 订单完成
    此判断依据根据来源于 Common的config.phpz中的"订单用户端显示状态" @{
    '1'=>' AND pay_status = 0 AND order_status = 0 AND pay_code !="cod" ', //订单查询状态 待支付
    '2'=>' AND (pay_status=1 OR pay_code="cod") AND shipping_status !=1 AND order_status in(0,1) ', //订单查询状态 待发货
    '3'=>' AND shipping_status=1 AND order_status = 1 ', //订单查询状态 待收货
    '4'=> ' AND order_status=2 ', // 待评价 已收货     //'FINISHED'=>'  AND order_status=1 ', //订单查询状态 已完成
    '5'=> ' AND order_status = 4 ', // 已完成
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getShowStatusAttr($value, $data)
    {
        $show_status = 1;
        if ($data['pay_status'] == 0 && $data['order_status'] == 0 && $data['pay_code'] != 'cod') {
            $show_status = 1;
        } else if (($data['pay_status'] == 1 || $data['pay_code'] == "cod") && $data['shipping_status'] != 1 && ($data['order_status'] == 0 || $data['order_status'] == 1)) {
            $show_status = 2;
        } else if (($data['shipping_status'] == 1 AND $data['order_status'] == 1)) {
            $show_status = 3;
        }  else if ($data['is_comment'] == 1) {//评论
            $show_status = 5;
        } else if ($data['order_status'] == 2) {
            $show_status = 4;
        }
        return $show_status;
    }

    /**
     * 是否显示支付到期时间FinallyPayTime
     * @param $value
     * @param $data
     * @return bool
     */
    public function getPayBeforeTimeShowAttr($value, $data)
    {
        if($data['prom_type'] == 4){
            return false;
        }
        return true;
    }

    /**
     * 付款URL
     * @param $value
     * @param $data
     * @return string
     */
    public function getPayUrlAttr($value, $data)
    {
        if($data['prom_type'] == 4){
            return U('Cart/cart4',['master_order_sn'=>$data['master_order_sn']]);
        }else{
            return U('Cart/cart4',['order_id'=>$data['order_id']]);
        }
    }
    /**
     * 订单发票
     * @return string
     */
    public function invoice()
    {
        return $this->hasOne('invoice','order_id','order_id');
    }

    public function getShippingStatusDescAttr($value, $data)
    {
        $config = config('SHIPPING_STATUS');
        return $config[$data['shipping_status']];
    }

}