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
 * Author: yhj
 * Date: 2018-08-01
 */

namespace app\common\logic;

use think\Model;
use think\db;

/**
 * 私信消息逻辑定义
 * Class CatsLogic
 * @package admin\Logic
 */
class MessagePrivateLogic extends MessageBase
{
    /**
     * 添加一条私信消息
     */
    public function addMessage(){
        $this->message['category'] = 3;
        db('message_private')->insert($this->message);
        $message_id = db('message_private')->getLastInsID();
        if($message_id) {
            $this->message['message_id'] = $message_id;
        }
    }


    /**
     * 删除消息
     * @param $message_id
     * @param $send_user_id
     * @throws \think\Exception
     */
    public function deletedMessage($message_id, $send_user_id)
    {
        db('message_notice')->where(['message_id' => $message_id, 'send_user_id' => $send_user_id])->delete();
        db('user_message')->where(['message_id' => $message_id, 'category' => 3])->delete();
    }

    /**
     * 检测必填参数
     * @return bool
     */
    public function checkParam()
    {
        if (empty($this->message['send_user_id'])
            || empty($this->message['message_content'])
        ) {
            return false;
        }
        return true;
    }

    /**
     * 必填
     * @param $value
     */
    public function setSendUserId($value){
        $this->message['send_user_id'] = $value;
    }
    /**
     * 必填
     * @param $value
     */
    public function setMessageContent($value){
        $this->message['message_content'] = $value;
    }

    /**
     * 必填
     * @param $value
     */
    public function setSendTime($value){
        $this->message['send_time'] = $value;
    }


}