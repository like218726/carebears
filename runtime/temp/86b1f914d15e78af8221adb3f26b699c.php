<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:42:"./application/seller/new/freight/area.html";i:1587634376;}*/ ?>
<link href="/public/static/css/base.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/js/global.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script>
<style type="text/css">
    html{
        min-height: 100%;
    }
    body{
        min-width: 320px;
    }
    .tp-area-list-wrap{
        padding-top:10px;
        padding-left:20px;
        max-height:120px;
        overflow: auto; 
    }
    .tp-area-list li{
        float: left;
        margin-right: 20px;
        margin-bottom: 10px;
    }
    .tp-inline-block-wrap{
        text-align: center;
    }
    .tp-inline-block-wrap>div{
        display: inline-block;
    }
    .tp-inline-block-wrap select{
        height:auto;
    }
    .tp-layer-btns-wrap{
        padding: 10px;
        text-align: center;
    }
    .tp-layer-btns-wrap>a{
        display: inline-block;
        width: 96px;
        line-height: 36px;
        margin: 0 10px;
        text-align: center;
        border-radius: 4px;
        background-color: #48cfae;
        color: #fff;
    }
</style>
<div id="layoutRight">
    <div class="tp-area-list-wrap">
        <ul class="tp-area-list clearfix" id="area_list">
            <?php if(is_array($select_area) || $select_area instanceof \think\Collection || $select_area instanceof \think\Paginator): $i = 0; $__LIST__ = $select_area;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$area): $mod = ($i % 2 );++$i;?>
                <li>
                    <label><input class="checkbox" type="checkbox" checked name="area_list[]" data-name="<?php echo $area['name']; ?>" value="<?php echo $area['region_id']; ?>"><?php echo $area['name']; ?></label>
                </li>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
    <div class="tp-inline-block-wrap">
        <div class="main-content" id="mainContent">
            <select name="province" id="province" size="6" onChange="get_city(this,0)">
                <option value="0">请选择省份</option>
                <?php if(is_array($province_list) || $province_list instanceof \think\Collection || $province_list instanceof \think\Paginator): $i = 0; $__LIST__ = $province_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$province): $mod = ($i % 2 );++$i;?>
                    <option value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
            <select name="city" id="city" size="6"  onChange="get_area(this)">
                <option value="0">请选择城市</option>
            </select>
            <select name="district" size="6" id="district">
                <option value="0">请选择</option>
            </select>
        </div>
    </div>
    <div class="tp-layer-btns-wrap">
        <a onclick="addArea();" class="ncsc-btn" href="javascript:void(0);"><i class="icon-plus"></i>添　加</a>
        <a onclick="confirm();" class="ncsc-btn" href="javascript:void(0);"><i class="icon-plus"></i>确　定</a>
    </div>
</div>
<script type="text/javascript">
    function confirm(){
        var input = $("input[type='checkbox']:checked");
        if (input.length == 0) {
            layer.alert('请添加区域', {icon: 2});
            return false;
        }
        var area_list = new Array();
        input.each(function(i,o){
            var area_id = $(this).attr("value");
            var area_name = $(this).data("name");
            var cartItemCheck = new Area(area_id,area_name);
            area_list.push(cartItemCheck);
        })
        window.parent.call_back(area_list);
    }
    //地区对象
    function Area(id, name) {
        this.id = id;
        this.name = name;
    }
    //  添加配送区域
    function addArea(){
        //
        var province = $("#province").val(); // 省份
        var city = $("#city").val();        // 城市
        var district = $("#district").val(); // 县镇
        var text = '';  // 中文文本
        var tpl = ''; // 输入框 html
        var is_set = 0; // 是否已经设置了

        // 设置 县镇
        if(district > 0){
            text = $("#district").find('option:selected').text();
            tpl = '<li><label><input class="checkbox" type="checkbox" checked name="area_list[]" data-name="'+text+'" value="'+district+'">'+text+'</label></li>';
            is_set = district; // 街道设置了不再设置市
        }
        // 如果县镇没设置 就获取城市
        if(is_set == 0 && city > 0){
            text = $("#city").find('option:selected').text();
            tpl = '<li><label><input class="checkbox" type="checkbox" checked name="area_list[]" data-name="'+text+'"  value="'+city+'">'+text+'</label></li>';
            is_set = city;  // 市区设置了不再设省份

        }
        // 如果城市没设置  就获取省份
        if(is_set == 0 && province > 0){
            text = $("#province").find('option:selected').text();
            tpl = '<li><label><input class="checkbox" type="checkbox" checked name="area_list[]" data-name="'+text+'"  value="'+province+'">'+text+'</label></li>';
            is_set = province;

        }

        var obj = $("input[class='checkbox']"); // 已经设置好的复选框拿出来
        var exist = 0;  // 表示下拉框选择的 是否已经存在于复选框中
        $(obj).each(function(){
            if($(this).val() == is_set){  //当前下拉框的如果已经存在于 复选框 中
                layer.alert('已经存在该区域', {icon: 2});  // alert("已经存在该区域");
                exist = 1; // 标识已经存在
            }
        })
        if(!exist)
            $('#area_list').append(tpl); // 不存在就追加进 去
    }
</script>
