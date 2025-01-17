<?php
namespace app\seller\validate;
use think\Validate;
class FreightTemplate extends Validate
{
    // 验证规则
    protected $rule = [
        ['template_name', 'require|checkStringMax|unique:freight_template,template_name^store_id'],
        ['type', 'require'],
        ['is_enable_default', 'require'],
        ['config_list','require|checkConfigList']
    ];
    //错误信息
    protected $message  = [
        'template_name.require'         => '请填写模板名称',
        'template_name.checkStringMax'       => '模板名称不能超过10个字符',
        'template_name.unique'          => '已有重名的模板名称',
        'type.require'                  => '请选择计价方式',
        'is_enable_default.require'     => '请选择是否启用默认配送配置',
        'config_list.require'           => '请添加配送区域',
    ];

    /**
     * 检查用户输入配置
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function checkStringMax($value,$rule,$data){
        if (mb_strlen($value,'utf8') > 10){
            return false;
        }
        return true;
    }

    /**
     * 检查用户输入配置
     * @param $value
     * @param $rule
     * @param $data
     * @return bool
     */
    protected function checkConfigList($value,$rule,$data){
        $config_list_length = count($value);
        if($data['type'] == 0){
            for ($i = 0; $i < $config_list_length; $i++) {
                if(((int)$value[$i]['first_unit']) == 0 || ((int)$value[$i]['continue_unit']) == 0){
                    return "件数必须大于0";
                }
            }
        }
        $arr_recursive = [];
        for ($i = 0; $i < $config_list_length; $i++) {
            if(!empty($value[$i]['area_ids'])){
                $temp = explode(',',$value[$i]['area_ids']);
                $arr_recursive = array_merge($temp, $arr_recursive);
            }
        }
        $arr_recursive_length = count($arr_recursive);
        $arr_unique = array_unique($arr_recursive);
        $arr_unique_length = count($arr_unique);
        if($arr_recursive_length != $arr_unique_length){
            $str = '';
            $arr = $this->getRepeat($arr_recursive);
            $arr = array_values($arr);
            $arr1 = db('region')->where('id',$arr[0])->find();
            if($arr1){
                $str =$arr1['name'];
                if($arr1['level'] == 2){
                    $str2 = db('region')->where('id',$arr1['parent_id'])->value('name');
                    $str = $str2 . $str;
                }elseif($arr1['level'] == 3){
                    $arr2 = db('region')->where('id',$arr1['parent_id'])->find();
                    $str = $arr2['name'].$str;
                    $str2 = db('region')->where('id',$arr2['parent_id'])->value('name');
                    $str = $str2 . $str;
                }
                $str = ': '.$str;
            }
            return '配送区域存在重复区域'.$str;
        }
        return true;
    }
    function getRepeat($arr)
    {
        // 获取去掉重复数据的数组
        $unique_arr = array_unique($arr);
        // 获取重复数据的数组
        $repeat_arr = array_diff_assoc($arr, $unique_arr);
        return $repeat_arr;
    }
}