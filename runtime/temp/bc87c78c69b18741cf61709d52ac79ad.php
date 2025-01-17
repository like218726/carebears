<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:40:"./application/admin/view/system/seo.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: #FFF; overflow: auto;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>SEO设置</h3>
        <h5>商城各级页面搜索引擎优化设置选项</h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="JavaScript:void(0);" nctype="index" <?php if($_GET[type] == 'index'): ?>class="current"<?php endif; ?>>首页</a></li>
        <li><a href="JavaScript:void(0);" nctype="flash" <?php if($_GET[type] == 'flash'): ?>class="current"<?php endif; ?>>抢购</a></li>
        <li><a href="JavaScript:void(0);" nctype="brand" <?php if($_GET[type] == 'brand'): ?>class="current"<?php endif; ?>>品牌</a></li>
        <li><a href="JavaScript:void(0);" nctype="group" <?php if($_GET[type] == 'group'): ?>class="current"<?php endif; ?>>拼团</a></li>
        <li><a href="JavaScript:void(0);" nctype="integralMall" <?php if($_GET[type] == 'integralMall'): ?>class="current"<?php endif; ?>>积分中心</a></li>
        <li><a href="JavaScript:void(0);" nctype="article" <?php if($_GET[type] == 'article'): ?>class="current"<?php endif; ?>>文章</a></li>
        <li><a href="JavaScript:void(0);" nctype="shop" <?php if($_GET[type] == 'shop'): ?>class="current"<?php endif; ?>>店铺</a></li>
        <li><a href="JavaScript:void(0);" nctype="goodsInfo" <?php if($_GET[type] == 'goodsInfo'): ?>class="current"<?php endif; ?>>商品</a></li>
        <!--<li ><a href="JavaScript:void(0);" nctype="category" <?php if($_GET[type] == 'category'): ?>class="current"<?php endif; ?>>商品分类</a></li>-->
        <!--<li><a href="JavaScript:void(0);" nctype="coupon_list" <?php if($_GET[type] == 'coupon_list'): ?>class="current"<?php endif; ?>>领券</a></li>-->
      </ul>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span id="explanationZoom" title="收起提示"></span> </div>
    <ul>
      <li>插入的变量必需包括花括号“{}”，当应用范围不支持该变量时，该变量将不会在前台显示(变量后边的分隔符也不会显示)，留空为系统默认设置，SEO自定义支持手写。以下是可用SEO变量:</li>
      <li>站点名称 {sitename}，（应用范围：全站）</li>
      <li nctype="vmore">名称 {name}，（应用范围：抢购名称、商品名称、品牌名称、文章标题、分类名称）</li>
      <li nctype="vmore">文章分类名称 {article_class}，（应用范围：文章分类页）</li>
      <li nctype="vmore">店铺名称 {shopname}，（应用范围：店铺页）</li>
      <li nctype="vmore">关键词 {key}，（应用范围：商品关键词、文章关键词、店铺关键词）</li>
      <li nctype="vmore">简单描述 {description}，（应用范围：商品描述、文章摘要、店铺关键词）</li>
      <!--<li><a>提交保存后，需要到 工具 -> 清理缓存 清理SEO，新的SEO设置才会生效</a></li>-->
    </ul>
  </div>
  <form method="post" name="form_index" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>首页</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[index][title]" name="SEO[index][title]" value="<?php echo $seo_config[index][title]; ?>" type="text" class="input-txt"/>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[index][keywords]" name="SEO[index][keywords]" value="<?php echo $seo_config[index][keywords]; ?>" type="text" class="input-txt" maxlength="200" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[index][description]" name="SEO[index][description]" value="<?php echo $seo_config[index][description]; ?>" type="text" class="input-txt" maxlength="200"/>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_index.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_flash" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>抢购</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[flash][title]" name="SEO[flash][title]" value="<?php echo $seo_config[flash][title]; ?>" type="text" class="input-txt"/>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[flash][keywords]" name="SEO[flash][keywords]" value="<?php echo $seo_config[flash][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[flash][description]" name="SEO[flash][description]" value="<?php echo $seo_config[flash][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="title">
        <h3>抢购内容</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[flash_content][title]" name="SEO[flash_content][title]" value="<?php echo $seo_config[flash_content][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[flash_content][keywords]" name="SEO[flash_content][keywords]" value="<?php echo $seo_config[flash_content][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[flash_content][description]" name="SEO[flash_content][description]" value="<?php echo $seo_config[flash_content][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_flash.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_brand" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>品牌</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[brand][title]" name="SEO[brand][title]" value="<?php echo $seo_config[brand][title]; ?>" type="text" class="input-txt"/>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[brand][keywords]" name="SEO[brand][keywords]" value="<?php echo $seo_config[brand][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[brand][description]" name="SEO[brand][description]" value="<?php echo $seo_config[brand][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="title">
        <h3>某一品牌商品列表</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[brand_list][title]" name="SEO[brand_list][title]" value="<?php echo $seo_config[brand_list][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[brand_list][keywords]" name="SEO[brand_list][keywords]" value="<?php echo $seo_config[brand_list][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[brand_list][description]" name="SEO[brand_list][description]" value="<?php echo $seo_config[brand_list][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_brand.submit()">确认提交</a></div>
    </div>
  </form>
   <form method="post" name="form_group" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>拼团</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[group][title]" name="SEO[group][title]" value="<?php echo $seo_config[group][title]; ?>" type="text" class="input-txt"/>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[group][keywords]" name="SEO[group][keywords]" value="<?php echo $seo_config[group][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[group][description]" name="SEO[group][description]" value="<?php echo $seo_config[group][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="title">
        <h3>拼团内容</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[group_content][title]" name="SEO[group_content][title]" value="<?php echo $seo_config[group_content][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[group_content][keywords]" name="SEO[group_content][keywords]" value="<?php echo $seo_config[group_content][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[group_content][description]" name="SEO[group_content][description]" value="<?php echo $seo_config[group_content][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_group.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_integralMall" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a><a>{key}</a><a>{description}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>积分中心</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[integralMall][title]" name="SEO[integralMall][title]" value="<?php echo $seo_config[integralMall][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[integralMall][keywords]" name="SEO[integralMall][keywords]" value="<?php echo $seo_config[integralMall][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[integralMall][description]" name="SEO[integralMall][description]" value="<?php echo $seo_config[integralMall][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="title">
        <h3>积分中心商品内容</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[point_content][title]" name="SEO[point_content][title]" value="<?php echo $seo_config[point_content][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[point_content][title]" name="SEO[point_content][keywords]" value="<?php echo $seo_config[point_content][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[point_content][title]" name="SEO[point_content][description]" value="<?php echo $seo_config[point_content][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_integralMall.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_article" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{article_class}</a><a>{name}</a><a>{key}</a><a>{description}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>文章分类列表</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[article][title]" name="SEO[article][title]" value="<?php echo $seo_config[article][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[article][keywords]" name="SEO[article][keywords]" value="<?php echo $seo_config[article][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[article][description]" name="SEO[article][description]" value="<?php echo $seo_config[article][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="title">
        <h3>文章内容</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[article_content][title]" name="SEO[article_content][title]" value="<?php echo $seo_config[article_content][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[article_content][keywords]" name="SEO[article_content][keywords]" value="<?php echo $seo_config[article_content][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[article_content][description]" name="SEO[article_content][description]" value="<?php echo $seo_config[article_content][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_article.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_shop" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{shopname}</a><a>{key}</a><a>{description}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>店铺</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[shop][title]" name="SEO[shop][title]" value="<?php echo $seo_config[shop][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[shop][keywords]" name="SEO[shop][keywords]" value="<?php echo $seo_config[shop][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[shop][description]" name="SEO[shop][description]" value="<?php echo $seo_config[shop][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_shop.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_goodsInfo" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>商品详情</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[goodsInfo][title]" name="SEO[goodsInfo][title]" value="<?php echo $seo_config[goodsInfo][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[goodsInfo][keywords]" name="SEO[goodsInfo][keywords]" value="<?php echo $seo_config[goodsInfo][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[goodsInfo][desciption]" name="SEO[goodsInfo][description]" value="<?php echo $seo_config[goodsInfo][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      
      <div class="title">
        <h3>商品列表</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[goodsList][title]" name="SEO[goodsList][title]" value="<?php echo $seo_config[goodsList][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[goodsList][keywords]" name="SEO[goodsList][keywords]" value="<?php echo $seo_config[goodsList][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[goodsList][description]" name="SEO[goodsList][description]" value="<?php echo $seo_config[goodsList][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_goodsInfo.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_category" action="<?php echo U('System/seo_update'); ?>"  style="display:none">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"> <a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>商品分类</h3>
      </div>
      <dl class="row">
        <dt class="tit">商品分类</dt>
        <dd class="opt">
          <select name="category" id="category">
            <option value="">-请选择-</option>
            <?php echo $cat_select; ?>
			</select>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[category][title]" name="SEO[category][title]" value="<?php echo $seo_config[category][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[category][keywords]" name="SEO[category][keywords]" value="<?php echo $seo_config[category][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[category][description]" name="SEO[category][description]" value="<?php echo $seo_config[category][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_category.submit()">确认提交</a></div>
    </div>
  </form>
  <form method="post" name="form_coupon" action="<?php echo U('System/seo_update'); ?>">
    <input type="hidden" name="form_submit" value="ok" />
    <span style="display:none" nctype="hide_tag"><a>{sitename}</a><a>{name}</a></span>
    <div class="ncap-form-default">
      <div class="title">
        <h3>领券中心</h3>
      </div>
      <dl class="row">
        <dt class="tit">title</dt>
        <dd class="opt">
          <input id="SEO[coupon_list][title]" name="SEO[coupon_list][title]" value="<?php echo $seo_config[coupon_list][title]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">keywords</dt>
        <dd class="opt">
          <input id="SEO[coupon_list][keywords]" name="SEO[coupon_list][keywords]" value="<?php echo $seo_config[coupon_list][keywords]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">description</dt>
        <dd class="opt">
          <input id="SEO[coupon_list][desciption]" name="SEO[coupon_list][description]" value="<?php echo $seo_config[coupon_list][description]; ?>" type="text" class="input-txt" />
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form_coupon.submit()">确认提交</a></div>
    </div>
  </form>
  <div id="tag_tips"> <span class="dialog_title">可用的代码，点击插入</span>
    <div style="margin: 0px; padding: 0px;line-height:25px;"></div>
  </div>
</div>
<script type="text/javascript">
$(function(){
	$('.tab-base').find('a').bind('click',function(){
		$("#tag_tips").css('display','none');
		$('.tab-base').find('a').removeClass('current');
		$(this).addClass('current');
		$('form').css('display','none');
		$('form[name="form_'+$(this).attr('nctype')+'"]').css('display','');
		$('span[nctype="hide_tag"]').find('a').css('padding-left','5px');
		$("#tag_tips>div").html($('form[name="form_'+$(this).attr('nctype')+'"]').find('span').html());
		$("#tag_tips").find('a').css('cursor','pointer');
		$("#tag_tips").find('a').bind('click',function(){
			var value = $(CUR_INPUT).val();
			if(value.indexOf($(this).html())<0 ){
				$(CUR_INPUT).val(value+$(this).html());
			}
		});
	});
	$('input[type="text"]').bind('focus',function(){
		CUR_INPUT = this;
		//定位弹出层的坐标
		var pos = $(this).offset();
		var pos_x = pos.left+300;
		var pos_y = pos.top-20;
		$("#tag_tips").css({'left' : pos_x, 'top' : pos_y,'position' : 'absolute','display' : 'block'});
	});

	$('form').css('display','none');
	$('form[name="form_index"]').css('display','');
	$('.tab-base').find('a').eq(0).click();
	
});
</script>
<style>
#tag_tips{
	padding:4px;border-radius: 2px 2px 2px 2px;box-shadow: 0 0 4px rgba(0, 0, 0, 0.75);display:none;padding: 4px;width:300px;z-index:9999;background-color:#FFFFFF;
}
.dialog_title {
    background-color: #F2F2F2;
    border-bottom: 1px solid #EAEAEA;
    color: #666666;
    display: block;
    font-weight: bold;
    line-height: 14px;
    padding: 5px;
}
</style>
<div id="goTop"> 
<a href="JavaScript:void(0);" id="btntop"><i class="fa fa-angle-up"></i></a>
<a href="JavaScript:void(0);" id="btnbottom"><i class="fa fa-angle-down"></i></a></div>
</body>
</html>