<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp5助手函数特性实现单字母函数M D U等简写方式
 * ============================================================================
 * Author: yhj
 * Date: 2018-06-25
 *  消息通知管理
 */
namespace app\admin\controller; 

use app\common\model\MemberMsgTpl;
use think\Controller;
use think\AjaxPage;
use think\Db;
use think\Page;
use think\Loader;

class MessageTemplate extends Base {
        
    public function index(){
        $list = Db::name('store_msg_tpl')->select();
		$this->assign('list', $list);
        return $this->fetch();
    }
    public function member(){
        $userMsgTpl = new MemberMsgTpl();
        $list = $userMsgTpl->select();
        $this->assign('list', $list);
        return $this->fetch();
    }
    /**
     * 修改或添加,用户模板消息
     */
    public function editMemberTemplate(){
        
        if(IS_POST)
        {  
            $data = I('post.');
            $userMsgTpl = Loader::validate('UserMsgTpl');
            if (!$userMsgTpl->batch()->scene($data['act'])->check($data)) {
                $return_arr = ['status' => 0, 'msg' => '所有输入项不能为空', 'data' => $userMsgTpl->getError()];
            } else {
                if($data['act'] == 'edit'){
                    $mmt_code = $data['mmt_code'];
                    unset($data['mmt_code']);
                    Db::name("member_msg_tpl")->where('mmt_code', $mmt_code)->update($data);
                }else{
                    $mmt_code = $data['mmt_code'];
                    $arr = Db::name("member_msg_tpl")->where('mmt_code', $mmt_code)->find();
                    if ($arr) {
                        $return_arr = ['status' => 0, 'msg' => '模板编号已存在'];
                        return $this->ajaxReturn($return_arr);
                    }
                    Db::name("member_msg_tpl")->insert($data);
                }
                $return_arr = array('status' => 1,'arr'=>$arr, 'msg' => '操作成功', 'url' => U('Admin/MessageTemplate/member'));
            }

            return $this->ajaxReturn($return_arr);
        }

        $mmt_code = I('mmt_code');
        if (!empty($mmt_code)) {
            $arr = Db::name("member_msg_tpl")->where('mmt_code', $mmt_code)->find();
            $this->assign('arr', $arr);
        }

        return $this->fetch();
    }
    /**
     * 修改或添加,商家模板消息
     */
    public function editStoreTemplate(){

        if(IS_POST)
        {
            $data = I('post.');
            $userMsgTpl = Loader::validate('StoreMsgTpl');
            if (!$userMsgTpl->batch()->scene($data['act'])->check($data)) {
                $return_arr = ['status' => 0, 'msg' => '所有输入项不能为空', 'data' => $userMsgTpl->getError()];
            } else {
                if($data['act'] == 'edit'){
                    $mmt_code = $data['smt_code'];
                    unset($data['smt_code']);
                    Db::name("store_msg_tpl")->where('smt_code', $mmt_code)->update($data);
                }else{
                    $mmt_code = $data['smt_code'];
                    $arr = Db::name("store_msg_tpl")->where('smt_code', $mmt_code)->find();
                    if ($arr) {
                        $return_arr = ['status' => 0, 'msg' => '模板编号已存在'];
                        return $this->ajaxReturn($return_arr);
                    }
                    Db::name("store_msg_tpl")->insert($data);
                }
                $return_arr = array('status' => 1,'arr'=>$data, 'msg' => '操作成功', 'url' => U('Admin/MessageTemplate/index'));
            }

            return $this->ajaxReturn($return_arr);
        }

        $mmt_code = I('smt_code');
        if (!empty($mmt_code)) {
            $arr = Db::name("store_msg_tpl")->where('smt_code', $mmt_code)->find();
            $this->assign('arr', $arr);
        }

        return $this->fetch();
    }

}