<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:46:"./application/admin/view/system/shop_info.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
<!doctype html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-capable" content="yes">
<!-- Apple devices fullscreen -->
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link href="/public/static/css/main.css" rel="stylesheet" type="text/css">
<link href="/public/static/css/page.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/css/font-awesome.min.css" rel="stylesheet" />
<!--[if IE 7]>
  <link rel="stylesheet" href="/public/static/font/css/font-awesome-ie7.min.css">
<![endif]-->
<link href="/public/static/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
<link href="/public/static/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css"/>
<style type="text/css">html, body { overflow: visible;}</style>
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
<script type="text/javascript" src="/public/static/js/admin.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.mousewheel.js"></script>
<script src="/public/js/myFormValidate.js"></script>
<script src="/public/js/myAjax2.js"></script>
<script src="/public/js/global.js"></script>
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<script type="text/javascript">
function delfunc(obj){
	layer.confirm('确认删除？', {
		  btn: ['确定','取消'] //按钮
		}, function(){
			$.ajax({
				type : 'post',
				url : $(obj).attr('data-url'),
				data : {act:'del',del_id:$(obj).attr('data-id')},
				dataType : 'json',
				success : function(data){
					layer.closeAll();
					if(data.status==1){
                        $(obj).parent().parent().parent().html('');
						layer.msg('操作成功', {icon: 1});
					}else{
						layer.msg('删除失败', {icon: 2,time: 2000});
					}
				}
			})
		}, function(index){
			layer.close(index);
		}
	);
}

function delAll(obj,name){
	var a = [];
	$('input[name*='+name+']').each(function(i,o){
		if($(o).is(':checked')){
			a.push($(o).val());
		}
	})
	if(a.length == 0){
		layer.alert('请选择删除项', {icon: 2});
		return;
	}
	layer.confirm('确认删除？', {btn: ['确定','取消'] }, function(){
			$.ajax({
				type : 'get',
				url : $(obj).attr('data-url'),
				data : {act:'del',del_id:a},
				dataType : 'json',
				success : function(data){
					layer.closeAll();
					if(data == 1){
						layer.msg('操作成功', {icon: 1});
						$('input[name*='+name+']').each(function(i,o){
							if($(o).is(':checked')){
								$(o).parent().parent().remove();
							}
						})
					}else{
						layer.msg(data, {icon: 2,time: 2000});
					}
				}
			})
		}, function(index){
			layer.close(index);
			return false;// 取消
		}
	);	
}

//表格列表全选反选
$(document).ready(function(){
	$('.hDivBox .sign').click(function(){
	    var sign = $('#flexigrid > table>tbody>tr');
	   if($(this).parent().hasClass('trSelected')){
	       sign.each(function(){
	           $(this).removeClass('trSelected');
	       });
	       $(this).parent().removeClass('trSelected');
	   }else{
	       sign.each(function(){
	           $(this).addClass('trSelected');
	       });
	       $(this).parent().addClass('trSelected');
	   }
	})
});

//获取选中项
function getSelected(){
	var selectobj = $('.trSelected');
	var selectval = [];
    if(selectobj.length > 0){
        selectobj.each(function(){
        	selectval.push($(this).attr('data-id'));
        });
    }
    return selectval;
}

function selectAll(name,obj){
    $('input[name*='+name+']').prop('checked', $(obj).checked);
}   

function get_help(obj){

	window.open("http://www.tp-shop.cn/");
	return false;

    layer.open({
        type: 2,
        title: '帮助手册',
        shadeClose: true,
        shade: 0.3,
        area: ['70%', '80%'],
        content: $(obj).attr('data-url'), 
    });
}

//
///**
// * 全选
// * @param obj
// */
//function checkAllSign(obj){
//    $(obj).toggleClass('trSelected');
//    if($(obj).hasClass('trSelected')){
//        $('#flexigrid > table>tbody >tr').addClass('trSelected');
//    }else{
//        $('#flexigrid > table>tbody >tr').removeClass('trSelected');
//    }
//}
/**
 * 批量公共操作（删，改）
 * @returns {boolean}
 */
function publicHandleAll(type){
    var ids = '';
    $('#flexigrid .trSelected').each(function(i,o){
//            ids.push($(o).data('id'));
        ids += $(o).data('id')+',';
    });
    if(ids == ''){
        layer.msg('至少选择一项', {icon: 2, time: 2000});
        return false;
    }
    publicHandle(ids,type); //调用删除函数
}
/**
 * 公共操作（删，改）
 * @param type
 * @returns {boolean}
 */
function publicHandle(ids,handle_type){
    layer.confirm('确认当前操作？', {
                btn: ['确定', '取消'] //按钮
            }, function () {
                // 确定
                $.ajax({
                    url: $('#flexigrid').data('url'),
                    type:'post',
                    data:{ids:ids,type:handle_type},
                    dataType:'JSON',
                    success: function (data) {
                        layer.closeAll();
                        if (data.status == 1){
                            layer.msg(data.msg, {icon: 1, time: 2000},function(){
                                location.href = data.url;
                            });
                        }else{
                            layer.msg(data.msg, {icon: 2, time: 3000});
                        }
                    }
                });
            }, function (index) {
                layer.close(index);
            }
    );
}
</script>
</head>
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<style>
    .system_img_location{text-align: center; width: 120px;position:absolute;top:2px; left:255px;}
</style>
<body style="background-color: #FFF; overflow: auto;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>商城设置</h3>
                <h5>网站全局内容基本选项设置</h5>
            </div>
            <ul class="tab-base nc-row">
                <?php if(is_array($group_list) || $group_list instanceof \think\Collection || $group_list instanceof \think\Paginator): if( count($group_list)==0 ) : echo "" ;else: foreach($group_list as $k=>$v): ?>
                    <li><a href="<?php echo U('System/index',['inc_type'=> $k]); ?>" <?php if($k==$inc_type): ?>class="current"<?php endif; ?>><span><?php echo $v; ?></span></a></li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
            <span id="explanationZoom" title="收起提示"></span> </div>
        <ul>
            <li>网站全局基本设置，商城及其他模块相关内容在其各自栏目设置项内进行操作。</li>
            <li>该页面的所有默认图标logo在"网站根目录/public/static/images/logo"目录下, 如果需要重新设计图标可参考默认图</li>
            <li>鼠标移到图标LOGO右侧 <a title="图片所在位置" class="pic-thumb-tip" ><i class="fa fa-picture-o"></i></a>会显示该图片大概所在位置</li>
        </ul>
    </div>
    <form method="post" id="handlepost" action="<?php echo U('System/handle'); ?>" enctype="multipart/form-data" name="form1">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="record_no">网站备案号</label>
                </dt>
                <dd class="opt">
                    <input id="record_no" name="record_no" value="<?php echo $config['record_no']; ?>" class="input-txt" type="text" />
                    <p class="notic">网站备案号，将显示在前台底部欢迎信息等位置</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_name">网站名称</label>
                </dt>
                <dd class="opt">
                    <input id="store_name" name="store_name" value="<?php echo $config['store_name']; ?>" class="input-txt" type="text" />
                    <p class="notic">网站名称，将显示在前台顶部欢迎信息等位置</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="company_name">公司名称</label>
                </dt>
                <dd class="opt">
                    <input id="company_name" name="company_name" value="<?php echo $config['company_name']; ?>" class="input-txt" type="text" />
                    <p class="notic">公司名称:将显示在管理员后台欢迎页面</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_logo">网站Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="store_logo_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo (isset($config['store_logo']) && ($config['store_logo'] !== '')?$config['store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>">
                                <i id="store_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['store_logo']) && ($config['store_logo'] !== '')?$config['store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="store_logo" name="store_logo" value="<?php echo (isset($config['store_logo']) && ($config['store_logo'] !== '')?$config['store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" id="button1" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'store_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
                        <a title="图片所在位置" href="/public/static/images/logo/pc_home_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/pc_home_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
                    	</div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">默认网站首页LOGO,通用头部显示，最佳显示尺寸为159*58像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_user_logo">网站用户中心Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="store_user_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['store_user_logo']) && ($config['store_user_logo'] !== '')?$config['store_user_logo']:'/public/static/images/logo/pc_home_user_logo_default.png'); ?>">
                                <i id="store_user_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['store_user_logo']) && ($config['store_user_logo'] !== '')?$config['store_user_logo']:'/public/static/images/logo/pc_home_user_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="store_user_logo" name="store_user_logo" value="<?php echo (isset($config['store_user_logo']) && ($config['store_user_logo'] !== '')?$config['store_user_logo']:'/public/static/images/logo/pc_home_user_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'store_user_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/pc_home_user_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/pc_home_user_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();">
	                            <i class="fa fa-picture-o"></i>
	                        </a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">默认用户中心网站LOGO,用户中心通用头部显示，最佳显示尺寸为159*58像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_logo">网站标题图标</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="storeico_a" target="_blank" class="nyroModal" rel="gal" href="<?php echo (isset($config['store_ico']) && ($config['store_ico'] !== '')?$config['store_ico']:'/public/static/images/logo/storeico_default.png'); ?>">
                                <i id="storeico_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['store_ico']) && ($config['store_ico'] !== '')?$config['store_ico']:'/public/static/images/logo/storeico_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="store_ico" name="store_ico" value="<?php echo (isset($config['store_ico']) && ($config['store_ico'] !== '')?$config['store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" id="button_ico" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'store_ico','logo','store_ico_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
                            <a title="图片所在位置" href="/public/static/images/logo/storeico_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/storeico_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
                    	</div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">网站标题图标LOGO,最佳显示尺寸为48*48像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_title">网站标题</label>
                </dt>
                <dd class="opt">
                    <input id="store_title" name="store_title" value="<?php echo $config['store_title']; ?>" class="input-txt" type="text" />
                    <p class="notic">网站标题，将显示在前台顶部欢迎信息等位置</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_desc">网站描述</label>
                </dt>
                <dd class="opt">
                    <input id="store_desc" name="store_desc" value="<?php echo $config['store_desc']; ?>" class="input-txt" type="text" />
                    <p class="notic">网站描述，将显示在前台顶部欢迎信息等位置</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="store_keyword">网站关键字</label>
                </dt>
                <dd class="opt">
                    <input id="store_keyword" name="store_keyword" value="<?php echo $config['store_keyword']; ?>" class="input-txt" type="text" />
                    <p class="notic">网站关键字，便于SEO</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="seller_login_logo">商家登录页Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="seller_login_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['seller_login_logo']) && ($config['seller_login_logo'] !== '')?$config['seller_login_logo']:'/public/static/images/logo/seller_login_logo_default.png'); ?>">
                                <i id="seller_login_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['seller_login_logo']) && ($config['seller_login_logo'] !== '')?$config['seller_login_logo']:'/public/static/images/logo/seller_login_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="seller_login_logo" name="seller_login_logo" value="<?php echo (isset($config['seller_login_logo']) && ($config['seller_login_logo'] !== '')?$config['seller_login_logo']:'/public/static/images/logo/seller_login_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'seller_login_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/seller_login_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/seller_login_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">商家登录页LOGO,最佳显示尺寸为159*58像素</p>
                </dd>

            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="admin_login_logo">平台管理员登录页Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="admin_login_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['admin_login_logo']) && ($config['admin_login_logo'] !== '')?$config['admin_login_logo']:'/public/static/images/logo/admin_login_logo_default.png'); ?>">
                                <i id="admin_login_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['admin_login_logo']) && ($config['admin_login_logo'] !== '')?$config['admin_login_logo']:'/public/static/images/logo/admin_login_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="admin_login_logo" name="admin_login_logo" value="<?php echo (isset($config['admin_login_logo']) && ($config['admin_login_logo'] !== '')?$config['admin_login_logo']:'/public/static/images/logo/admin_login_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'admin_login_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/admin_login_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/admin_login_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">平台管理员登录页LOGO,最佳显示尺寸为220*82像素</p>
                </dd>

            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="admin_home_logo">平台后台顶部Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="admin_home_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['admin_home_logo']) && ($config['admin_home_logo'] !== '')?$config['admin_home_logo']:'/public/static/images/logo/admin_home_logo_default.png'); ?>">
                                <i id="admin_home_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['admin_home_logo']) && ($config['admin_home_logo'] !== '')?$config['admin_home_logo']:'/public/static/images/logo/admin_home_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="admin_home_logo" name="admin_home_logo" value="<?php echo (isset($config['admin_home_logo']) && ($config['admin_home_logo'] !== '')?$config['admin_home_logo']:'/public/static/images/logo/admin_home_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'admin_home_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/admin_home_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/admin_home_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">平台后台顶部LOGO,最佳显示尺寸为148*28像素</p>
                </dd>

            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="wap_home_logo">手机端首页Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="wap_home_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['wap_home_logo']) && ($config['wap_home_logo'] !== '')?$config['wap_home_logo']:'/public/static/images/logo/wap_home_logo_default.png'); ?>">
                                <i id="wap_home_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['wap_home_logo']) && ($config['wap_home_logo'] !== '')?$config['wap_home_logo']:'/public/static/images/logo/wap_home_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="wap_home_logo" name="wap_home_logo" value="<?php echo (isset($config['wap_home_logo']) && ($config['wap_home_logo'] !== '')?$config['wap_home_logo']:'/public/static/images/logo/wap_home_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'wap_home_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/wap_home_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/wap_home_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">手机端首页搜索框LOGO,最佳显示尺寸为48*48像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="wap_login_logo">手机端登录页Logo</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="wap_login_logo_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['wap_login_logo']) && ($config['wap_login_logo'] !== '')?$config['wap_login_logo']:'/public/static/images/logo/wap_logo_default.png'); ?>">
                                <i id="wap_login_logo_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['wap_login_logo']) && ($config['wap_login_logo'] !== '')?$config['wap_login_logo']:'/public/static/images/logo/wap_logo_default.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="wap_login_logo" name="wap_login_logo" value="<?php echo (isset($config['wap_login_logo']) && ($config['wap_login_logo'] !== '')?$config['wap_login_logo']:'/public/static/images/logo/wap_logo_default.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'wap_login_logo','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/logo/wap_login_logo_pos.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/logo/wap_login_logo_pos.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    <span class="err"></span>
                    <p class="notic">手机端登录页LOGO,最佳显示尺寸为312*92像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="wap_home_logo">公众号二维码</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="weixin_qrcode_a" class="nyroModal" rel="gal" href="<?php echo (isset($config['weixin_qrcode']) && ($config['weixin_qrcode'] !== '')?$config['weixin_qrcode']:'/public/static/images/qrcode.png'); ?>">
                                <i id="weixin_qrcode_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=<?php echo (isset($config['weixin_qrcode']) && ($config['weixin_qrcode'] !== '')?$config['weixin_qrcode']:'/public/static/images/qrcode.png'); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="weixin_qrcode" name="weixin_qrcode" value="<?php echo (isset($config['weixin_qrcode']) && ($config['weixin_qrcode'] !== '')?$config['weixin_qrcode']:'/public/static/images/qrcode.png'); ?>" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="GetUploadify(1,'weixin_qrcode','logo','img_call_back')" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
                        </span>
                        <div class="system_img_location">
	                        <a title="图片所在位置" href="/public/static/images/qrcode.png" class="pic-thumb-tip" onmouseover="layer.tips('<img src=\'/public/static/images/qrcode.png\' style=\'height:500px;width: 500px\'>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"><i class="fa fa-picture-o"></i></a>
	                    </div>
                    </div>
                    
                    <span class="err"></span>
                    <p class="notic">pc端公共底部官方微信二维码,最佳显示尺寸为90*90像素</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="wap_home_logo">默认空图片</label>
                </dt>
                <dd class="opt">
                    <div class="input-file-show">
                        <span class="show">
                            <a id="thumb_empty_a" class="nyroModal" rel="gal" href="/public/images/icon_goods_thumb_empty_300.png?v=<?php echo time(); ?>">
                                <i id="thumb_empty_i" class="fa fa-picture-o" onmouseover="layer.tips('<img src=/public/images/icon_goods_thumb_empty_300.png?v=<?php echo time(); ?>>',this,{tips: [1, '#fff']});" onmouseout="layer.closeAll();"></i>
                            </a>
                        </span>
           	            <span class="type-file-box">
                            <input type="text" id="thumb_empty" name="thumb_empty" value="/public/images/icon_goods_thumb_empty_300.png" class="type-file-text">
                            <input type="button" name="button" value="选择上传..." class="type-file-button">
                            <input class="type-file-file" onClick="toupfile()" size="30" hidefocus="true" nc_type="change_site_logo" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">

                        </span>
                    </div>
                    <input type="file" name="file" accept="image/*" onchange="changefile(this)" id="id_thumb_empty"
                           data-id="thumb_empty" desc="默认空图片" hidden/>
                    <span class="err"></span>
                    <p class="notic">默认空图片,最佳显示尺寸为360*360的png图片，正方形图片不宜过大,/public/images/icon_goods_thumb_empty_300.png和/template/mobile/default/static/images/zy.png 有写的权限 </p>
                    <p class="notic">/public/images/icon_goods_thumb_empty_300.png</p>
                    <p class="notic">/template/mobile/default/static/images/zy.png</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="contact">联系人</label>
                </dt>
                <dd class="opt">
                    <input id="contact" name="contact" value="<?php echo $config['contact']; ?>" class="input-txt" type="text" />
                    <p class="notic">联系人</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="phone">联系电话</label>
                </dt>
                <dd class="opt">
                    <input id="phone" autocomplete="off"  name="phone" value="<?php echo $config['phone']; ?>" class="input-txt" type="text" />
                    <p class="notic">商家中心右下侧显示，方便商家遇到问题时咨询,固话格式如：0755-88888888</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="address">具体地址</label>
                </dt>
                <dd class="opt">
                    <input id="address" name="address" value="<?php echo $config['address']; ?>" class="input-txt" type="text" />
                    <p class="notic">具体地址</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="qq">平台客服QQ1</label>
                </dt>
                <dd class="opt">
                    <input id="qq" name="qq" value="<?php echo $config['qq']; ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic">商家中心右下侧显示，方便商家遇到问题时咨询</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="qq2">平台客服QQ2</label>
                </dt>
                <dd class="opt">
                    <input id="qq2" name="qq2" value="<?php echo $config['qq2']; ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic">商家中心右下侧显示，方便商家遇到问题时咨询</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="qq3">平台客服QQ3</label>
                </dt>
                <dd class="opt">
                    <input id="qq3"  name="qq3" value="<?php echo $config['qq3']; ?>" class="input-txt" type="text">
                    <span class="err"></span>
                    <p class="notic">商家中心右下侧显示，方便商家遇到问题时咨询</p>
                </dd>
            </dl>
            <div class="bot" style="padding-left: 11%">
                <input type="hidden" name="inc_type" value="<?php echo $inc_type; ?>">
                <a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="check_form();">确认提交</a>
            </div>
        </div>
    </form>
</div>
<div id="goTop"> <a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a><a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
<script type="text/javascript">
    function toupfile(){
        $("#id_thumb_empty").trigger('click')
    }
    function check_form()
    {
        var phone = $('#phone').val();
        if(!checkMobile(phone) && !checkTelphone(phone)){
            layer.alert('请输入正确的号码！',{icon:2});
            return false;
        }
        document.form1.submit()
    }
 
    //网站图标
    function store_ico_call_back(fileurl_tmp)
    {
        $("#store_ico").val(fileurl_tmp);
        $("#storeico_a").attr('href', fileurl_tmp);
        $("#storeico_i").attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
    }
    
    
  	//网站图标
    function img_call_back(fileurl_tmp , elementid)
    { 
        $("#"+elementid).val(fileurl_tmp);
        $("#"+elementid+'_a').attr('href', fileurl_tmp);
        $("#"+elementid+'_i').attr('onmouseover', "layer.tips('<img src="+fileurl_tmp+">',this,{tips: [1, '#fff']});");
    }

    var changefile = function (o) {
        var file = event.target.files[0];
        var fileReader = new FileReader();
        fileReader.onload = function () {
            var result = this.result;
            var blob = new Blob([result], {type: file.type});
            var formData = new FormData();
            formData.append('file', blob, file.name);
            $.ajax({
                type: "post",
                url: "<?php echo U('Admin/Ueditor/imgUp',['savepath'=>'empty']); ?>",
                async: false,
                dataType: "json",
                processData: false, //因为data值是FormData对象，不需要对数据做处理
                contentType: false,
                data: formData,
                timeout: 6000,
                success: function (data) {
                    if (data.state == 'SUCCESS') {
                        layer.open({content: '上传成功', time: 2000});
                        setTimeout(function(){
                            location.reload();
                        },2000)
                    } else {
                        layer.open({content: data.state, time: 2000});
                    }
                },
                error: function (xhr, text) {
                    if (text === 'timeout') {
                        xhr.abort();
                    }
                }
            });
        }
        fileReader.readAsArrayBuffer(file);
    }
</script>
</html>