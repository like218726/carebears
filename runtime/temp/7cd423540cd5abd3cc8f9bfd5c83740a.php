<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:55:"./application/admin/view/promotion/prom_goods_list.html";i:1587634374;s:79:"/home/wwwroot/testshop.kingdeepos.com/application/admin/view/public/layout.html";i:1587634374;}*/ ?>
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
<script src="/public/static/js/layer/laydate/laydate.js"></script>
<body style="background-color: rgb(255, 255, 255); overflow: auto; cursor: default; -moz-user-select: inherit;">
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<div class="page">
	<div class="fixed-bar">
		<div class="item-title">
			<div class="subject">
				<h3>优惠促销管理</h3>
				<h5>网站系统优惠促销审核与管理</h5>
			</div>
		</div>
	</div>
	<!-- 操作说明 -->
	<div id="explanation" class="explanation" style="color: rgb(44, 188, 163); background-color: rgb(237, 251, 248); width: 99%; height: 100%;">
		<div id="checkZoom" class="title"><i class="fa fa-lightbulb-o"></i>
			<h4 title="提示相关设置操作时应注意的要点">操作提示</h4>
			<span title="收起提示" id="explanationZoom" style="display: block;"></span>
		</div>
		<ul>
			<li>优惠促销管理, 由总平台设置管理.</li>
		</ul>
	</div>
	<div class="flexigrid">
		<div class="mDiv">
			<div class="ftitle">
				<h3>优惠促销列表</h3>
				<h5>(共<?php echo $pager->totalRows; ?>条记录)</h5>
			</div>
			<div title="刷新数据" class="pReload"><i class="fa fa-refresh"></i></div>
			<form class="navbar-form form-inline" id="search-form" action="<?php echo U('Promotion/prom_goods_list'); ?>" method="get" onsubmit="return check_form();">
				<div class="sDiv">
					<div class="sDiv2" style="margin-right: 10px;">
						<input type="text" size="30" id="start_time" name="start_time" value="<?php echo $start_time; ?>" placeholder="起始时间" class="qsbox">
						<input type="button" class="btn" value="起始时间">
					</div>
					<div class="sDiv2" style="margin-right: 10px;">
						<input type="text" size="30" id="end_time" name="end_time" value="<?php echo $end_time; ?>" placeholder="截止时间" class="qsbox">
						<input type="button" class="btn" value="截止时间">
					</div>
					<div class="sDiv2">
						<select name="status" class="select">
							<option value="-1" <?php if($status == -1): ?>selected<?php endif; ?>>活动状态</option>
							<option value="1" <?php if($status == 1): ?>selected<?php endif; ?>>正常</option>
							<option value="0" <?php if($status == 0): ?>selected<?php endif; ?>>已结束</option>
						</select>
						<input size="30" name="title" value="<?php echo $_POST['title']; ?>" class="qsbox" placeholder="输入活动名称" type="text">
						<input type="submit" class="btn" value="搜索">
					</div>
				</div>
			</form>
		</div>
		<div class="hDiv">
			<div class="hDivBox">
				<table cellspacing="0" cellpadding="0">
					<thead>
					<tr>
						<th class="sign" axis="col0">
							<div style="width: 24px;"><i class="ico-check"></i></div>
						</th>
						<th align="left" abbr="article_title" axis="col3" class="">
							<div style="text-align: left; width: 240px;" class="">活动名称</div>
						</th>
						<th align="left" abbr="ac_id" axis="col4" class="">
							<div style="text-align: center; width: 80px;" class="">活动类型</div>
						</th>
					<!--	<th align="center" abbr="article_show" axis="col5" class="">
							<div style="text-align: center; width: 120px;" class="">适用范围</div>
						</th>-->
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 120px;" class="">开始时间</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 120px;" class="">结束时间</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 80px;" class="">状态</div>
						</th>
						<th align="center" abbr="article_time" axis="col6" class="">
							<div style="text-align: center; width: 80px;" class="">推荐</div>
						</th>

						<th align="left" axis="col1" class="handle">
							<div style="text-align: center; width: 150px;">操作</div>
						</th>
						<th style="width:100%" axis="col7">
							<div></div>
						</th>
					</tr>
					</thead>
				</table>
			</div>
		</div>
		<div class="bDiv" style="height: auto;">
			<div id="flexigrid" cellpadding="0" cellspacing="0" border="0">
				<table>
					<tbody>
					<?php if(is_array($prom_list) || $prom_list instanceof \think\Collection || $prom_list instanceof \think\Paginator): if( count($prom_list)==0 ) : echo "" ;else: foreach($prom_list as $k=>$vo): ?>
						<tr>
							<td class="sign">
								<div style="width: 24px;"><i class="ico-check"></i></div>
							</td>
							<td align="left" class="">
								<div style="text-align: left; width: 240px;"><?php echo getSubstr($vo['title'],0,30); ?></div>
							</td>
							<td align="left" class="">
								<div style="text-align: center; width: 80px;"><?php echo $parse_type[$vo[type]]; ?></div>
							</td>
							<!--<td align="left" class="">
								<div style="text-align: center; width: 120px;"><?php echo $vo['group_name']; ?></div>
							</td>-->
							<td align="left" class="">
								<div style="text-align: center; width: 120px;"><?php echo date('Y-m-d',$vo['start_time']); ?></div>
							</td>
							<td align="left" class="">
								<div style="text-align: center; width: 120px;"><?php echo date('Y-m-d',$vo['end_time']); ?></div>
							</td>
							<td align="left" class="">
								<div style="text-align: center; width: 80px;"><?php echo $vo['state']; ?></div>
							</td>
							<td align="center" class="">
								<div style="text-align: center; width: 80px;">
									<?php if($vo[recommend] == 1): ?>
										<span class="yes" onClick="changeTableVal('prom_goods','id','<?php echo $vo['id']; ?>','recommend',this)" ><i class="fa fa-check-circle"></i>是</span>
										<?php else: ?>
										<span class="no" onClick="changeTableVal('prom_goods','id','<?php echo $vo['id']; ?>','recommend',this)" ><i class="fa fa-ban"></i>否</span>
									<?php endif; ?>
								</div>
							</td>
							<td align="left" class="handle">
								<div style="text-align: left; width: 170px; max-width:170px;">
									<a class="btn red goods_list" data-url="<?php echo U('Promotion/get_goods',array('id'=>$vo['id'])); ?>"><i class="fa fa-search"></i>查看商品</a>
									<?php if($vo['status'] == 1 && $vo[end_time] > time()): ?>
										<a class="btn red closeProm" data-prom-id="<?php echo $vo['id']; ?>"><i class="fa fa-close"></i>取消</a>
									<?php endif; ?>
									<a class="btn red" href="javascript:void(0)" data-url="<?php echo U('Promotion/ajax_prom_goods_del'); ?>" data-id="<?php echo $vo['id']; ?>" onClick="delfun(this)"><i class="fa fa-trash-o"></i>删除</a>
								</div>
							</td>
							<td align="" class="" style="width: 100%;">
								<div>&nbsp;</div>
							</td>
						</tr>
					<?php endforeach; endif; else: echo "" ;endif; ?>
					</tbody>
				</table>
			</div>
			<div class="iDiv" style="display: none;"></div>
		</div>
		<!--分页位置-->
		<?php echo $page; ?> </div>
</div>
<script>
	$(document).ready(function(){
		// 表格行点击选中切换
		$('#flexigrid > table>tbody >tr').click(function(){
			$(this).toggleClass('trSelected');
		});

		// 点击刷新数据
		$('.fa-refresh').click(function(){
			location.href = location.href;
		});

		$('#start_time').layDate();
		$('#end_time').layDate();
	});

	$('.goods_list').click(function(){
		var url = $(this).attr('data-url');
		layer.open({
			type: 2,
			title: '活动关联商品列表',
			shadeClose: true,
			shade: 0.5,
			area: ['70%', '72%'],
			content: url,
		});
	});

	function changeStatus(status,id,tab,prom_type){
		if(status>1){
			layer.confirm('确认删除？', {btn: ['确定','取消']}, function(){
				$.ajax({
					type : 'GET',
					url : "<?php echo U('Promotion/activity_handle'); ?>",
					data : {'id':id,'tab':tab,'status':status,'prom_type':prom_type},
					dataType :'JSON',
                    success : function(data){
                        layer.closeAll();
                        if(data.status == 1){
                            layer.msg(data.msg, {icon: 1,time: 2000},function () {
                                window.location.reload();
                            });
                        }else{
                            layer.msg(data.msg, {icon: 2,time: 2000});
                        }
                    }
				});
			}, function(index){
				layer.close(index);
				return false;// 取消
			});
		}else{
			$.ajax({
				type : 'GET',
				url : "<?php echo U('Promotion/activity_handle'); ?>",
				data : {'id':id,'tab':tab,'status':status,'prom_type':prom_type},
				dataType :'JSON',
                success : function(data){
                    layer.closeAll();
                    if(data.status == 1){
                        layer.msg(data.msg, {icon: 1,time: 2000},function () {
                            window.location.reload();
                        });
                    }else{
                        layer.msg(data.msg, {icon: 2,time: 2000});
                    }
                }
			});
		}
	}

	function delfun(obj) {
		// 删除按钮
		layer.confirm('确认删除？', {
			btn: ['确定', '取消'] //按钮
		}, function () {
			$.ajax({
				type: 'post',
				url: $(obj).attr('data-url'),
				data : {act:'del',id:$(obj).attr('data-id')},
				dataType: 'json',
				success: function (data) {
					layer.closeAll();
					if (data.status == 1) {
						$(obj).parent().parent().parent().remove();
					} else {
						layer.alert(data.msg, {icon: 2});  //alert('删除失败');
					}
				}
			})
		}, function () {
			layer.closeAll();
		});
	}
	function check_form(){
		var start_time = $.trim($('#start_time').val());
		var end_time =  $.trim($('#end_time').val());
		if(start_time == '' ^ end_time == ''){
			layer.alert('请选择完整的时间间隔', {icon: 2});
			return false;
		}
		return true;
	}
	//关闭活动
	$(function () {
		$(document).on("click", '.closeProm', function (e) {
			var prom_id = $(this).data('prom-id');
			layer.confirm("确认取消", {btn: ['确定','取消']}, function(){
				$.ajax({
					type : "POST",
					url:"<?php echo U('Promotion/closePromGoods'); ?>",
					dataType:'json',
					data: {prom_id:prom_id},
					success: function(data){
						layer.closeAll();
						if(data.status == 1){
							layer.msg(data.msg, {icon: 1,time: 2000},function () {
								window.location.reload();
							});
						}else{
							layer.msg(data.msg, {icon: 2,time: 2000});
						}
					}
				});
			}, function(index){
				layer.close(index);
			});
		})
	})
</script>
</body>
</html>