<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:51:"./application/seller/new/goods/dealerGoodsList.html";i:1587634376;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/head.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/left.html";i:1587634378;s:77:"/home/wwwroot/testshop.kingdeepos.com/application/seller/new/public/foot.html";i:1587634378;}*/ ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?php if(!empty($_SERVER['HTTPS']) and strtolower($_SERVER['HTTPS']) != 'off'): ?>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" >
<?php endif; ?>
<title>商家中心</title>
<link href="/public/static/css/base.css" rel="stylesheet" type="text/css">
<link href="/public/static/css/seller_center.css" rel="stylesheet" type="text/css">
<link href="/public/static/font/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo (isset($tpshop_config['shop_info_store_ico']) && ($tpshop_config['shop_info_store_ico'] !== '')?$tpshop_config['shop_info_store_ico']:'/public/static/images/logo/storeico_default.png'); ?>" media="screen"/>
<!--[if IE 7]>
  <link rel="stylesheet" href="/public/static/font/font-awesome/css/font-awesome-ie7.min.css">
<![endif]-->
<script type="text/javascript" src="/public/static/js/jquery.js"></script>
<script type="text/javascript" src="/public/static/js/seller.js"></script>
<script type="text/javascript" src="/public/static/js/waypoints.js"></script>
<script type="text/javascript" src="/public/static/js/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="/public/static/js/jquery.validation.min.js"></script>
<script type="text/javascript" src="/public/static/js/layer/layer.js"></script>
<script type="text/javascript" src="/public/js/dialog/dialog.js" id="dialog_js"></script>
<script type="text/javascript" src="/public/js/global.js"></script>
<script type="text/javascript" src="/public/js/myAjax.js"></script>
<script type="text/javascript" src="/public/js/myFormValidate.js"></script>
<script type="text/javascript" src="/public/static/js/layer/laydate/laydate.js"></script>

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="/public/static/js/html5shiv.js"></script>
      <script src="/public/static/js/respond.min.js"></script>
<![endif]-->
</head>
<body>
<div id="append_parent"></div>
<div id="ajaxwaitid"></div>
<header class="ncsc-head-layout w">
  <div class="wrapper">
    <div class="ncsc-admin w252" id="user-info">
      <!-- <dl class="ncsc-admin-info">
        <dt class="admin-avatar"></dt>
      </dl> -->
      <!-- 店铺 -->
      <div class="seller-logo">
         <a class="iconshop" href="<?php echo U('Home/Store/index',array('store_id'=>STORE_ID)); ?>" title="前往店铺" ><i class="icon-home"></i>&nbsp;店铺</a>

      </div>
      
       <!-- 店铺头像 -->
      <div class="seller-img"><img src="/public/static/images/seller/default_user_portrait.gif" width="32" class="pngFix" alt=""/></div>
      <!-- 店铺名 -->
      <div class="storename">
          <div class="bgd-opa"><p class="admin-name" style="height: 72px;line-height: 72px"><a class="seller_name " href=""><?php echo $seller['seller_name']; ?></a>
            <i class="opa-arow"></i>
          </p>
          <ul class="bgdopa-list">
              <li><a class="iconshop" href="<?php echo U('Admin/modify_pwd',array('seller_id'=>$seller['seller_id'])); ?>" title="修改密码" target="_blank">设置</a></li>
              <li><a class="iconshop" href="<?php echo U('Admin/logout'); ?>" title="安全退出">退出</a> </li>
          </ul>
          </div>
      </div>
      


      
        <!--   <div class="bottom"> 
             
              <a class="iconshop" href="<?php echo U('Admin/modify_pwd',array('seller_id'=>$seller['seller_id'])); ?>" title="修改密码" target="_blank"><i class="icon-wrench"></i>&nbsp;设置</a>
              <a class="iconshop" href="<?php echo U('Admin/logout'); ?>" title="安全退出"><i class="icon-signout"></i>&nbsp;退出</a></div> -->
           </div>
     
      <div class="center-logo"> <a href="/" target="_blank">

        <img src="<?php echo (isset($tpshop_config['shop_info_store_logo']) && ($tpshop_config['shop_info_store_logo'] !== '')?$tpshop_config['shop_info_store_logo']:'/public/static/images/logo/pc_home_logo_default.png'); ?>" class="pngFix" alt=""/></a>
        <h1>商家中心</h1>
      </div>
      <nav class="ncsc-nav">
        <dl <?php if(ACTION_NAME == 'index' AND CONTROLLER_NAME == 'Index'): ?>class="current"<?php endif; ?>>
          <dt><a href="<?php echo U('Index/index'); ?>">首页</a></dt>
          <dd class="arrow"></dd>
        </dl>
        <?php if(is_array($menuArr) || $menuArr instanceof \think\Collection || $menuArr instanceof \think\Paginator): if( count($menuArr)==0 ) : echo "" ;else: foreach($menuArr as $kk=>$vo): ?>
        <dl <?php if(ACTION_NAME == $vo[child][0][act] AND CONTROLLER_NAME == $vo[child][0][op]): ?>class="current"<?php endif; ?>>
          <dt><a href="/index.php?m=Seller&c=<?php echo $vo[child][0][op]; ?>&a=<?php echo $vo[child][0][act]; ?>"><?php echo $vo['name']; ?></a></dt>
          <dd>
            <ul>
                <?php if(is_array($vo['child']) || $vo['child'] instanceof \think\Collection || $vo['child'] instanceof \think\Paginator): if( count($vo['child'])==0 ) : echo "" ;else: foreach($vo['child'] as $key=>$vv): ?>
                  <li> <a href='<?php echo U("$vv[op]/$vv[act]"); ?>'> <?php echo $vv['name']; ?> </a> </li>
          <?php endforeach; endif; else: echo "" ;endif; ?>
             </ul>
          </dd>
          <dd class="arrow"></dd>
        </dl>
        <?php endforeach; endif; else: echo "" ;endif; ?>
          <dl>
              <dt><a href="http://help.tp-shop.cn/Index/Help/info/cat_id/24"target="_blank">帮助手册</a></dt>
              <dd class="arrow"></dd>
          </dl>
    </nav>
              <div class="index-sitemap" id="shortcut">
                  <a class="iconangledown" href="javascript:void(0);">快捷方式 <i class="icon-angle-down"></i></a>
                    <div class="sitemap-menu-arrow"></div>
                    <div class="sitemap-menu">
                        <div class="title-bar">
                          <h2>管理导航</h2>
                          <p class="h_tips"><em>小提示：添加您经常使用的功能到首页侧边栏，方便操作。</em></p>
                          <img src="/public/static/images/obo.png" alt="">
                          <span id="closeSitemap" class="close">X</span>
                        </div>
                        <div id="quicklink_list" class="content">
                        <?php if(is_array($menuArr) || $menuArr instanceof \think\Collection || $menuArr instanceof \think\Paginator): if( count($menuArr)==0 ) : echo "" ;else: foreach($menuArr as $k2=>$v2): ?>
                        <dl>
                          <dt><?php echo $v2['name']; ?></dt>
                            <?php if(is_array($v2['child']) || $v2['child'] instanceof \think\Collection || $v2['child'] instanceof \think\Paginator): if( count($v2['child'])==0 ) : echo "" ;else: foreach($v2['child'] as $key=>$v3): ?>
                            <dd class="<?php if(!empty($quicklink)){if(in_array($v3['op'].'_'.$v3['act'],$quicklink)){echo 'selected';}} ?>">
                              <i nctype="btn_add_quicklink" data-quicklink-act="<?php echo $v3[op]; ?>_<?php echo $v3[act]; ?>" class="icon-check" title="添加为常用功能菜单"></i>
                              <a href=<?php echo U("$v3[op]/$v3[act]"); ?>> <?php echo $v3['name']; ?> </a>
                            </dd>
                          <?php endforeach; endif; else: echo "" ;endif; ?>
                        </dl>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        </div>
                    </div>
                  </div> 
    </div>

 </div>
 
</div>

</header>

</body>
</html>
<div class="ncsc-layout wrapper">
     <div id="layoutLeft" class="ncsc-layout-left">
   <div id="sidebar" class="sidebar">
     <div class="column-title" id="main-nav"><span class="ico-<?php echo $leftMenu['icon']; ?>"></span>
       <h2><?php echo $leftMenu['name']; ?></h2>
     </div>
     <div class="column-menu">
       <ul id="seller_center_left_menu">
      	 <?php if(is_array($leftMenu['child']) || $leftMenu['child'] instanceof \think\Collection || $leftMenu['child'] instanceof \think\Paginator): if( count($leftMenu['child'])==0 ) : echo "" ;else: foreach($leftMenu['child'] as $key=>$vu): ?>
           <li class="<?php if(ACTION_NAME == $vu[act] AND CONTROLLER_NAME == $vu[op]): ?>current<?php endif; ?>">
           		<a href="<?php echo U("$vu[op]/$vu[act]"); ?>"> <?php echo $vu['name']; ?></a>
           </li>
	 	<?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
     </div>
   </div>
 </div>
  <div id="layoutRight" class="ncsc-layout-right">
    <div class="ncsc-path"><i class="icon-desktop"></i>商家管理中心<i class="icon-angle-right"></i>商品<i class="icon-angle-right"></i>供应商品</div>
<div class="main-content" id="mainContent">
      
	<div class="tabmenu">
	  <ul class="tab pngFix">
	  <li class="active"><a href="">供应商品</a></li>
	  <li class="normal"><a href="<?php echo U('Seller/Goods/supplierGoodsHandleList'); ?>">供应商品审理</a></li>
	  </ul>
	</div>
	<div class="alert mt15 mb5">
		操作提示：
		<ul>
			<li>1、当源供应商品进行部分商品数据的修改时（如供货价、运费等），商品状态将显示为“数据修改”，此时需要你再次审理修改的商品数据，同意后可再次上架</li>
			<li>2、当商品状态为“数据修改”，操作栏显示“待源供应商品审核”时，表示源供应商品还在审核中，请等待源供应商品审核成功后再进行操作</li>
		</ul>
	</div>
	<form method="get" action="">
	  <table class="search-form">
	    <input type="hidden" name="act" value="goods_offline" />
	    <tr>
	      <td>&nbsp;</td>
		  <th style="width: 75px;">供应商品状态</th>
		  <td class="w90">
	        <select name="supplier_goods_status">
	          	<option value="-1">请选择...</option>
	            <option value="0" <?php if(\think\Request::instance()->param('supplier_goods_status') == 0): ?>selected<?php endif; ?>>正常供应</option>
	            <option value="1" <?php if(\think\Request::instance()->param('supplier_goods_status') == 1): ?>selected<?php endif; ?>>数据修改</option>
	            <option value="2" <?php if(\think\Request::instance()->param('supplier_goods_status') == 2): ?>selected<?php endif; ?>>暂停供应</option>
	          </select>
	      </td>
	      <th>审核状态</th>
	      <td class="w90">
	        <select name="goods_state">
	          	<option value="">请选择...</option>
	            <option value="0" <?php if(\think\Request::instance()->param('goods_state') === 0): ?>selected<?php endif; ?>>待审核</option>
	            <option value="1" <?php if(\think\Request::instance()->param('goods_state') == 1): ?>selected<?php endif; ?>>审核通过</option>
	            <option value="2" <?php if(\think\Request::instance()->param('goods_state') == 2): ?>selected<?php endif; ?>>未通过</option>
	          </select>
	      </td>
	      <td class="w160"><input type="text" class="text" name="key_word" value="<?php echo \think\Request::instance()->param('key_word'); ?>" placeholder="搜索词"/></td>
	      <td class="tc w70"><label class="submit-border"><input type="submit" class="submit" value="搜索" /></label></td>
	    </tr>
	  </table>
	</form>
	  <table class="ncsc-default-table">
	  <thead>
	    <tr nc_type="table_header">
	      <th class="w80">ID</th>
	      <th class="w50"></th>
	      <th>商品名称</th>
	      <th class="w80">供应商</th>
	      <th class="w50">上架</th>
	      <th style="min-width: 80px;">商品状态</th>
	      <th class="w80">审核状态</th>
	      <th class="w80">供货价</th>
	      <th class="w50">销量</th>
	      <th class="w50">库存</th>
	      <th class="w200">操作</th>
	    </tr>
	      </thead>
	  	  <tbody>
	  	  	  <?php if(is_array($goodsList) || $goodsList instanceof \think\Collection || $goodsList instanceof \think\Paginator): if( count($goodsList)==0 ) : echo "" ;else: foreach($goodsList as $key=>$vo): ?>
		      <tr id="list_<?php echo $vo[goods_id]; ?>">
		      <td class="trigger">
                  <!--<i class="icon-plus-sign" nctype="ajaxGoodsList"></i>-->
                  <?php echo $vo['goods_id']; ?></td>
		      <td>
				<div class="pic-thumb">
					<img src="<?php echo goods_thum_images($vo['goods_id'],50,50); ?>"/>
				</div>
			  </td>
		      <td class="tl">
		      	<dl class="goods-name">
		          <dt style="max-width: 450px !important;">
		          <?php echo getSubstr($vo['goods_name'],0,33); ?></dt>
		          <dd>商家货号：<?php echo $vo['goods_sn']; ?></dd>
		        </dl>
		      </td>
			  <td>
				<?php echo $vo['store_name']; ?>
			  </td>
		      <td><img width="20" height="20" src="/public/images/<?php if($vo[is_on_sale] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" 
					<?php if($vo[goods_state] != 1): ?>
						onclick="layer.msg('该商品需通过审核才能上架',{icon:2});"
					<?php elseif($vo[supplier_goods_status] == 1): ?>
						onclick="modifyGoods(<?php echo $vo['goods_id']; ?>)"
					<?php elseif($vo[supplier_goods_status] == 2): ?>
						onclick="layer.msg('该供应商品已暂停供应，请在供应商恢复供应后再尝试上架',{icon:2});"
					<?php else: ?>
						onclick="changeTableVal2('goods','goods_id','<?php echo $vo['goods_id']; ?>','is_on_sale',this)"
					<?php endif; ?> /></td>
				<?php 
					$sgs_txt = [
						'0' => '正常供应',
						'1' => '变价修改',
						'2' => '暂停供应',
						'3' => '暂停供应',
					];
				 ?>
		      <td><?php echo $sgs_txt[$vo[supplier_goods_status]]; if($vo[supplier_goods_status] == 1 and $modify_status_list[$vo[root_goods_id]][dealer_status] == 2): ?>(拒绝变价)<?php endif; ?></td>
		      <td><?php echo $state[$vo[goods_state]]; ?></td>
		       <td><?php echo $vo['cost_price']; ?></td>
		      <td><?php echo $vo['sales_sum']; ?></td>
		      <td><?php echo $vo['store_count']; ?></td>
			  <td class="nscs-table-handle tr">
				<?php if($vo['supplier_goods_status'] == 1): if($modify_status_list[$vo[root_goods_id]][modify_status] == 1): ?>
						<span><a href="<?php echo U('Goods/supplierGoodsModify',array('goods_id'=>$vo['goods_id'])); ?>" class="btn-bluejeans"><i class="icon-check"></i><p>查看修改</p> </a></span>
					<?php else: ?>
						<span><a href="javascript:void(0);" class="btn-bluejeans"><p>待源供应</p><p>商品审核</p> </a></span>
					<?php endif; endif; ?>
				<span><a href="<?php echo U('Goods/addEditGoods',array('goods_id'=>$vo['goods_id'])); ?>" class="btn-bluejeans"><i class="icon-edit"></i><p>编辑</p> </a></span>
		        <span><a href="javascript:void(0);" onclick="del('<?php echo $vo[goods_id]; ?>')" class="btn-grapefruit"><i class="icon-trash"></i><p>删除</p></a></span>
		      </td>
		      </tr>
		      <?php endforeach; endif; else: echo "" ;endif; ?>
	      </tbody>
	      <tfoot>
		    <tr>
		       <td colspan="20"><?php echo $page; ?></td>
		    </tr>
		  </tfoot>
	  </table>
   </div>
  </div>
</div>
<div id="cti">
  <div class="wrapper">
    <ul>
          </ul>
  </div>
</div>
<div id="faq">
  <div class="wrapper">
      </div>
</div>

<div id="footer">
  <p>
      <?php $i = 1;
                                   
                                $md5_key = md5("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 3 ORDER BY `sort` DESC");
                                $result_name = $sql_result_vv = S("sql_".$md5_key);
                                if(empty($sql_result_vv))
                                {                            
                                    $result_name = $sql_result_vv = \think\Db::query("SELECT * FROM `__PREFIX__navigation` where is_show = 1 AND position = 3 ORDER BY `sort` DESC"); 
                                    S("sql_".$md5_key,$sql_result_vv,31104000);
                                }    
                              foreach($sql_result_vv as $kk=>$vv): if($i > 1): ?>|<?php endif; ?>
         <a href="<?php echo $vv[url]; ?>" <?php if($vv[is_new] == 1): ?> target="_blank" <?php endif; ?> ><?php echo $vv[name]; ?></a>
          <?php $i++; endforeach; ?>
      <!--<a href="/">首页</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">招聘英才</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">合作及洽谈</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">联系我们</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">关于我们</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">物流自取</a>-->
      <!--| <a  href="http://<?php echo $_SERVER['HTTP_HOST']; ?>">友情链接</a>-->
  </p>
  Copyright 2017 <a href="http://<?php echo $_SERVER['HTTP_HOST']; ?>" target="_blank"><?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?></a> All rights reserved.<br />
  
</div>
<script type="text/javascript" src="/public/static/js/jquery.cookie.js"></script>
<link href="/public/static/js/perfect-scrollbar.min.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/public/static/js/perfect-scrollbar.min.js"></script>
<script type="text/javascript" src="/public/static/js/qtip/jquery.qtip.min.js"></script>
<link href="/public/static/js/qtip/jquery.qtip.min.css" rel="stylesheet" type="text/css">
<div id="tbox">
  <div class="btn" id="msg"><a href="<?php echo U('Seller/index/store_msg'); ?>"><i class="msg"><?php if(!(empty($storeMsgNoReadCount) || (($storeMsgNoReadCount instanceof \think\Collection || $storeMsgNoReadCount instanceof \think\Paginator ) && $storeMsgNoReadCount->isEmpty()))): ?><em><?php echo $storeMsgNoReadCount; ?></em><?php endif; ?></i>站内消息</a></div>
  <div class="btn" id="im">
      <a href="tencent://message/?uin=<?php echo $tpshop_config['shop_info_qq3']; ?>&Site=<?php echo (isset($tpshop_config['shop_info_store_name']) && ($tpshop_config['shop_info_store_name'] !== '')?$tpshop_config['shop_info_store_name']:'TPshop商城'); ?>&Menu=yes" target="_blank">
          <i class="im"><em id="new_msg" style="display:none;"></em></i>
          在线联系</a>
  </div>
  <div class="btn" id="gotop" style="display: block;"><i class="top"></i><a href="javascript:void(0);">返回顶部</a></div>
</div>
<script type="text/javascript">
var current_control = '<?php echo CONTROLLER_NAME; ?>/<?php echo ACTION_NAME; ?>';
$(document).ready(function(){
    //添加删除快捷操作
    $('[nctype="btn_add_quicklink"]').on('click', function() {
        var $quicklink_item = $(this).parent();
        var item = $(this).attr('data-quicklink-act');
        if($quicklink_item.hasClass('selected')) {
            $.post("<?php echo U('Seller/Index/quicklink_del'); ?>", { item: item }, function(data) {
                $quicklink_item.removeClass('selected');
                var idstr = 'quicklink_'+ item;
                $('#'+idstr).remove();
            }, "json");
        } else {
            var scount = $('#quicklink_list').find('dd.selected').length;
            if(scount >= 8) {
                layer.msg('快捷操作最多添加8个', {icon: 2,time: 2000});
            } else {
                $.post("<?php echo U('Seller/Index/quicklink_add'); ?>", { item: item }, function(data) {
                    $quicklink_item.addClass('selected');
                    if(current_control=='Index/index'){
                        var $link = $quicklink_item.find('a');
                        var menu_name = $link.text();
                        var menu_link = $link.attr('href');
                        var menu_item = '<li id="quicklink_' + item + '"><a href="' + menu_link + '">' + menu_name + '</a></li>';
                        $(menu_item).appendTo('#seller_center_left_menu').hide().fadeIn();
                    }
                }, "json");
            }
        }
    });
    //浮动导航  waypoints.js
    $("#sidebar,#mainContent").waypoint(function(event, direction) {
        $(this).parent().toggleClass('sticky', direction === "down");
        event.stopPropagation();
        });
    });
    // 搜索商品不能为空
    $('input[nctype="search_submit"]').click(function(){
        if ($('input[nctype="search_text"]').val() == '') {
            return false;
        }
    });

	function fade() {
		$("img[rel='lazy']").each(function () {
			var $scroTop = $(this).offset();
			if ($scroTop.top <= $(window).scrollTop() + $(window).height()) {
				$(this).hide();
				$(this).attr("src", $(this).attr("data-url"));
				$(this).removeAttr("rel");
				$(this).removeAttr("name");
				$(this).fadeIn(500);
			}
		});
	}
	if($("img[rel='lazy']").length > 0) {
		$(window).scroll(function () {
			fade();
		});
	};
	fade();
	
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
    		    // 确定
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
                        layer.closeAll();
   						if(data==1){
   							layer.msg('操作成功', {icon: 1,time: 1000},function () {
                                location.reload();
                                // window.location.href='';
                            });
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
</script>
<script>
// 删除操作
function del(id) {
	layer.confirm('确定要删除吗？', {
				btn: ['确定','取消'] //按钮
			}, function(){
				// 确定
				$.ajax({
					url: "/index.php?m=Seller&c=goods&a=delGoods&ids=" + id,
                    dataType:'json',
					success: function (data) {
						layer.closeAll();
						if (data.status == 1){
                            layer.msg(data.msg, {icon: 1, time: 1000},function () {
                               $('#list_'+id).remove();
                            });

                        }else{
                            layer.msg(data.msg, {icon: 2, time: 1000}); //alert(v.msg);
                        }

					}
				});
			}, function(index){
				layer.close(index);
			}
	);
}

function modifyGoods(goods_id) {
	layer.confirm('该供应商品存在数据（如供货价）修改，是否前往查看并审理新的供应商品数据？', {
	  btn: ['前往','取消'] //按钮
	}, function(){
	  window.location.href = '<?php echo U("seller/Goods/supplierGoodsModify"); ?>' + '?goods_id=' + goods_id;
	}, function(){
	});
}
</script>
</body>
</html>