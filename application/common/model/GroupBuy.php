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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */
namespace app\common\model;
use think\Model;
class GroupBuy extends Model {
    public function goods(){
        return $this->hasOne('goods','goods_id','goods_id');
    }
    public function groupBuyGoodsItem(){
        return $this->hasMany('groupBuyGoodsItem','group_buy_id','id');
    }
    
    //状态描述
    public function getStatusDescAttr($value, $data)
    {
        $status = array('审核中', '正常', '审核失败', '管理员关闭');
        if ($data['status'] != 1) {
            return $status[$data['status']];
        } else {
            if (time() < $data['start_time']) {
                return '未开始';
            } else if (time() > $data['start_time'] && time() < $data['end_time']) {
                return '进行中';
            } else {
                return '已结束';
            }
        }
    }
}
