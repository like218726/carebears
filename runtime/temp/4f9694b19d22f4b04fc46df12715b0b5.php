<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:50:"./application/admin/view/article/categoryList.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default;">
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>文章分类</h3>
        <h5>网站文章分类添加与管理</h5>

      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
      <span id="explanationZoom" title="收起提示"></span>
    </div>
    <ul>
      <li>新增文章时，可选择文章分类。文章分类将在前台文章列表页显示</li>
      <li>系统文章分类不可以删除</li>
    </ul>
  </div>
  <form method="post">
    <input type="hidden" name="form_submit" value="ok">
    <div class="flexigrid">
      <div class="mDiv">
        <div class="ftitle">
          <h3>文章分类列表</h3>
          <h5></h5>
        </div>
      </div>
      <div class="hDiv">
        <div class="hDivBox">
          <table cellpadding="0" cellspacing="0">
            <thead>
              <tr>
                <th align="center" class="sign" axis="col0">
                  <div style="text-align: center; width: 24px;"><i class="ico-check"></i></div>
                </th>
                <th align="center" class="handle" axis="col1">
                  <div style="text-align: center; width: 150px;">操作</div>
                </th>
                <th align="center" axis="col2">
                  <div style="text-align: center; width: 60px;">排序</div>
                </th>
                <th align="center" axis="col3" class="">
                  <div class="sundefined" style="text-align: center; width: 250px;">分类名称</div>
                </th>
                <th align="center" axis="col3" class="">
                  <div style="text-align: center; width: 350px;">分类描述</div>
                </th>
                <th axis="col4">
                  <div></div>
                </th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div class="tDiv">
        <div class="tDiv2">
         <a href="<?php echo U('Article/category'); ?>">
          <div class="fbutton">
            <div class="add" title="新增分类">
              <span><i class="fa fa-plus"></i>新增分类</span>
            </div>
          </div>
         </a> 
         <div class="fbutton">
            <div class="add" title="收缩分类">
              <span onclick="tree_open(this);"><i class="fa fa-angle-double-up"></i>收缩分类</span>
            </div>
          </div>
        </div>
        <div style="clear:both"></div>
      </div>      
      <div class="bDiv" style="height: auto;">
        <table class="flex-table autoht" cellpadding="0" cellspacing="0" border="0" id="article_cat_table">
          <tbody id="treet1">
          <?php if(is_array($cat_list) || $cat_list instanceof \think\Collection || $cat_list instanceof \think\Paginator): if( count($cat_list)==0 ) : echo "" ;else: foreach($cat_list as $k=>$vo): ?>
            <tr nctype="0" class="parent_id_<?php echo $vo[parent_id]; ?>" data-level="<?php echo $vo[level]; ?>" id="<?php echo $vo['level']; ?>_<?php echo $vo['cat_id']; ?>">
              <td class="sign">
                <div style="text-align: center; width: 24px;"> 
                	<img src="/public/static/images/tv-collapsable-last.gif" fieldid="2" status="open" id="icon_<?php echo $vo['level']; ?>_<?php echo $vo['id']; ?>" onClick="treeClicked(this,<?php echo $vo[cat_id]; ?>)">                    
                </div>
              </td>
              <td class="handle">
                <div style="text-align:center;min-width:150px !important; max-width:inherit !important;">
                  <span class="btn" style="padding-left:<?php echo ($vo[level] * 4); ?>em"><em><i class="fa fa-cog"></i>设置<i class="arrow"></i></em>
                  <ul>
                     <li><a href="<?php echo U('Article/category',array('act'=>'edit','cat_id'=>$vo['cat_id'])); ?>">编辑分类信息</a></li>                  
                                     
                     <li><a href="<?php echo U('Article/category',array('parent_id'=>$vo['cat_id'])); ?>">新增下级分类</a></li>
                     <?php if($vo['cat_id'] > 8): ?>
	                     <li><a href="javascript:void(0)" data-url="<?php echo U('Article/categoryHandle'); ?>" data-id="<?php echo $vo['cat_id']; ?>" onClick="delfun(this)">删除当前分类</a></li>
                     <?php endif; ?>                                        
                  </ul>
                  </span>
                </div>
              </td>
              <td class="sort">
                <div style="text-align: center; width: 60px;">
                  <input type="text" onKeyUp="this.value=this.value.replace(/[^\d]/g,'')" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onblur="changeTableVal('article_cat','cat_id','<?php echo $vo['cat_id']; ?>','sort_order',this)" size="4" value="<?php echo $vo['sort_order']; ?>" />
                </div>
              </td>
              <td class="name">
                <div style="text-align: center; width: 250px;">
                  <!--<input type="text" value="<?php echo $vo['name']; ?>" onblur="changeTableVal('article_cat','cat_id',<?php echo $vo['cat_id']; ?>,'cat_name',this)" <?php if(in_array(($vo['id']), is_array($article_system_id)?$article_system_id:explode(',',$article_system_id))): ?>readonly="readonly"<?php endif; ?>/>-->
                    <?php echo $vo['name']; ?>
                </div>
              </td>
            <td class="name">
              <div style="text-align: center; width: 350px;">
                <span><?php echo $vo['cat_desc']; ?></span>
              </div>
            </td>
              <td style="width: 100%;">
                <div>&nbsp;</div>
              </td>
            </tr>
            <?php endforeach; endif; else: echo "" ;endif; ?>                    
          </tbody>
        </table>        
      </div>
    </div>
  </form>
  <script>
     // 点击展开 收缩节点
    function  tree_open(obj)
	{
		var tree = $('#article_cat_table tr[id^="1_"],#article_cat_table tr[id^="2_"], #list-table tr[id^="3_"] '); //,'table-row'
		if(tree.css('display')  == 'table-row')
		{
			$(obj).html("<i class='fa fa-angle-double-down'></i>展开分类");
			tree.css('display','none');
			//$("span[id^='icon_']").removeClass('glyphicon-minus');
			$("img[id^='icon_']").attr('src','/public/static/images/tv-expandable.gif');
		}else
		{
			$(obj).html("<i class='fa fa-angle-double-up'></i>收缩分类");
			tree.css('display','table-row');
			$("img[id^='icon_']").attr('src','/public/static/images/tv-collapsable-last.gif');
		}
	}
     
     function treeClicked(obj,cat_id){
		 var src = $(obj).attr('src');
		 if(src == '/public/static/images/tv-expandable.gif')
		 {
			 $(".parent_id_"+cat_id).show();
			 $(obj).attr('src','/public/static/images/tv-collapsable-last.gif');
		 }else{			 
			 $(obj).attr('src','/public/static/images/tv-expandable.gif');			 
			 
			 // 如果是点击减号, 遍历循环他下面的所有都关闭
			 var tbl = document.getElementById("article_cat_table");
			 cur_tr = obj.parentNode.parentNode.parentNode;
			 var fnd = false;
			  for (i = 0; i < tbl.rows.length; i++)
			  {
				  var row = tbl.rows[i];
				  
				  if (row == cur_tr)
				  {
					  fnd = true;         
				  }
				  else
				  {
					  if (fnd == true)
					  {
						 
						  var level = parseInt($(row).data('level'));
						  var cur_level = $(cur_tr).data('level');
						 
						  if (level > cur_level)
						  {
							  $(row).hide();		
							  $(row).find('img').attr('src','/public/static/images/tv-expandable.gif');
						  }
						  else
						  {
							  fnd = false;
							  break;
						  }
					  }
				  }
			  }			 
		 }		 
	 }

     function delfun(obj) {
       layer.confirm('确认删除？', {
                 btn: ['确定', '取消'] //按钮
               }, function () {
                 // 确定
                 $.ajax({
                   type: 'post',
                   url : $(obj).attr('data-url'),
                   data : {act:'del',cat_id:$(obj).attr('data-id')},
                   dataType: 'json',
                   success: function (data) {
                     layer.closeAll();
                     if (data.status === 1) {
                       layer.msg('操作成功', {icon: 1});
                       $(obj).parent().parent().parent().parent().parent().parent().remove();
                       location.reload();
                     } else {
                       layer.msg(data.msg, {icon: 2, time: 2000});
                     }
                   }
                 })
               }, function (index) {
                 layer.close(index);
               }
       );
     }
  </script>
</div>
</body>
</html>