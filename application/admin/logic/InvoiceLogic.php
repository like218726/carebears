<?php

/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 商业用途务必到官方购买正版授权, 使用盗版将严厉追究您的法律责任。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Date: 2017-10-23
 */


namespace app\admin\logic;

use app\common\model\Invoice;
use think\Db;
use think\Model;

class InvoiceLogic extends Model
{
    //发票创建
	function createInvoice($order){
        $data = [
            'order_id'       => $order['order_id'],  //订单id
            'user_id'        => $order['user_id'],  //用户id
            'store_id'       => $order['store_id'],  //商家id
            'invoice_rate'   => $order['invoice_id'],//发票税率
            'atime'          => 0,              //创建时间
            'ctime'          => time(),//开票时间
            'invoice_money'  => $order['total_amount']-$order['shipping_price'],
            'suppliers_id'   => $order['suppliers_id'],
        ];
        $invoiceinfo = Db::name('user_extend')->where(['user_id'=>$order['user_id']])->find();
        if($invoiceinfo['invoice_desc']	!= '不开发票'){
            $data['invoice_desc']    = '明细';//发票内容
            $data['taxpayer']        = $order['taxpayer'];//纳税人识别号
            $data['invoice_title']   = $order['invoice_title'];//发票抬头
            Db::name('invoice')->add($data);
        }
    }

}