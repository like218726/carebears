<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:46:"./application/seller/new/uploadify/upload.html";i:1587634378;}*/ ?>
<html lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
<title>文件管理</title>
<link rel="stylesheet" type="text/css" href="/public/plugins/webuploader/webuploader.css">
<link rel="stylesheet" type="text/css" href="/public/plugins/webuploader/css/style.css">
</head>
<body>
	<style type="text/css">
		#album option{
			height: 30px;
			line-height: 30px;
		}
		.tabs li{
			height: 30px;
			line-height: 30px;
		}
		.tabs {
			margin: 15px 5px 0 5px;
		}
		.file-list{
			overflow: hidden;
		}
		
	</style>
<div class="upload-box">
	<ul class="tabs">
		<li class="checked" id="upload_tab">本地上传</li>
		<?php if($onlyUpload != 1): ?>
				<li id="manage_tab">相册图片</li>
				<li id="search_tab">文件搜索</li>
				<select name="album" id="album" class="album-none" style=" width: 120px;   color: #565656;font-size:14px;background-color: #FFF;height: 30px;cursor: pointer; vertical-align: middle;padding: 0 4px;margin-left: 10px; border: solid 1px #E6E9EE;">
					<?php if(is_array($albumList) || $albumList instanceof \think\Collection || $albumList instanceof \think\Paginator): if( count($albumList)==0 ) : echo "" ;else: foreach($albumList as $k=>$v): ?>                                                                           
	                    <option value="<?php echo $v['id']; ?>" <?php if($v['sort'] == 0): ?>selected="selected"<?php endif; ?> >
	                         <?php echo $v['album_name']; ?>
	                    </option>
	               <?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
			<!-- button class="saveBtn album-none" id="add_album" style="color: #565656; margin-left: 10px;padding: 0 18px;height: 30px;line-height: 30px; cursor: pointer;     display: inline-block; vertical-align: middle;" >新建相框</button -->
		<?php endif; ?>
		
	</ul>
	
	<div class="container">
		<div class="area upload-area area-checked" id="upload_area">
			<div id="uploader">
				<div class="statusBar" style="display:none;">
					<div class="progress">
						<span class="text">0%</span>
						<span class="percentage"></span>
					</div><div class="info"></div>
					<div class="btns">
						<div id="filePicker2"></div><div class="uploadBtn">开始上传</div>
						<div class="saveBtn">确定使用</div>
					</div>
				</div>
				<div class="queueList">
					<div id="dndArea" class="placeholder">
						<div id="filePicker"></div>
						<p>或将文件拖到这里，本次最多可选<?php echo (isset($info['num']) && ($info['num'] !== '')?$info['num']:1); ?>个</p>
					</div>
				</div>
			</div>
		</div>
		<div class="area manage-area" id="manage_area">
			<ul class="choose-btns">
				<li class="btn sure checked">确定</li>
				<li class="btn cancel">取消</li>
			</ul>
			<div class="file-list">
				<ul id="file_all_list">
					<!--<li class="checked">
						<div class="img">
							<img src="" />
							<span class="icon"></span>
						</div>
						<div class="desc"></div>
					</li>-->
				</ul>
			</div>
		</div>
		<div class="area search-area" id="search_area">
			<ul class="choose-btns">
				<li class="search">
					<div class="search-condition">
						<input class="key" type="text" />
						<input class="submit" type="button" hidefocus="true" value="搜索" />
					</div>
				</li>
				<li class="btn sure checked">确定</li>
				<li class="btn cancel">取消</li>
			</ul>
			<div class="file-list">
				<ul id="file_search_list">
					<!--<li>
						<div class="img">
							<img src="" />
							<span class="icon"></span>
						</div>
						<div class="desc"></div>
					</li>-->
				</ul>
			</div>
		</div>
		<div class="fileWarp" style="display:none;">
			<fieldset>
				<legend>列表</legend>
				<ul>
				</ul>
			</fieldset>
		</div>
	</div>
</div>
<script type="text/javascript" src="/public/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/public/plugins/webuploader/webuploader.min.js"></script>
<script type="text/javascript" src="/public/plugins/webuploader/upload.js"></script>
<script>
$(function(){
	moudle = 'Seller';
	var config = {
			"swf":"/public/plugins/webuploader/Uploader.swf",
			"server":"<?php echo $info['upload']; ?>",
			"filelistPah":"<?php echo $info['fileList']; ?>",
			"delPath":"<?php echo U('Uploadify/delupload'); ?>",
			"chunked":false,
			"chunkSize":524288,
			"fileNumLimit":<?php echo (isset($info['num']) && ($info['num'] !== '')?$info['num']:1); ?>,
			"fileSizeLimit":209715200,
			"fileSingleSizeLimit":0,
			"fileVal":"file",
			"auto":true,
			"formData":{},
			"pick":{"id":"#filePicker","label":"点击选择文件","name":"file"},
			"thumb":{"width":110,"height":110,"quality":70,"allowMagnify":true,"crop":true,"preserveHeaders":false,"type":"image\/jpeg"},
			"compress":false
	};
	var orignalServer = config.server;
	  
    var album_id = 0;
    if($("select[name=album]").length > 0){
    	album_id = $('#album').val();
    	$('#album').on('change',function(){
        	//判断是否选取prompt属性，无返回值；
        	    album_id = $(this).val();
        		var tab_li_id = $('.tabs>li.checked').attr("id");
        		if(tab_li_id == 'manage_tab'){
        			//在线图片:更换相册, 重新刷新图片
        			Manager.changeAlbum(album_id);
        		}else{
        			//本地上传图片
            		config.server = orignalServer +'/album_id/'+album_id;
            		Manager.upload($.extend(config, {type : fileType}));
        		} 
        	});
    }else{
    	 //获取父页面album_id
    	 album_id = $("#album_id" , window.parent.document).val();
    }
    
    config.server = orignalServer +'/album_id/'+album_id;
	var fileType = "<?php echo $info['fileType']=='undefined'?'Images':$info['fileType']; ?>";
    Manager.upload($.extend(config, {type : fileType}));
    
    
	/*点击保存按钮时
	 *判断允许上传数，检测是单一文件上传还是组文件上传
	 *如果是单一文件，上传结束后将地址存入$input元素
	 *如果是组文件上传，则创建input样式，添加到$input后面
	 *隐藏父框架，清空列队，移除已上传文件样式*/
	$(".statusBar .saveBtn").click(function(){
		var callback = "<?php echo $info['func']; ?>";
		var num = <?php echo (isset($info['num']) && ($info['num'] !== '')?$info['num']:1); ?>;
		var fileurl_tmp = [];
		if(callback != "undefined"){	
			if(num > 1){	
				 $("input[name^='fileurl_tmp']").each(function(index,dom){
					fileurl_tmp[index] = dom.value;
				 });	
			}else{
				fileurl_tmp = $("input[name^='fileurl_tmp']").val();	
			}
			eval('window.parent.'+callback+'(fileurl_tmp)');
			window.parent.layer.closeAll();
			return;
		}					 
		if(num > 1){
				var fileurl_tmp = "";
				$("input[name^='fileurl_tmp']").each(function(){
					fileurl_tmp += '<li rel="'+ this.value +'"><input class="input-text" type="text" name="<?php echo $info['input']; ?>[]" value="'+ this.value +'" /><a href="javascript:void(0);" onclick="ClearPicArr(\''+ this.value +'\',\'\')">删除</a></li>';	
				});			
				$(window.parent.document).find("#<?php echo $info['input']; ?>").append(fileurl_tmp);
		}else{
				$(window.parent.document).find("#<?php echo $info['input']; ?>").val($("input[name^='fileurl_tmp']").val());
		}
		window.parent.layer.closeAll();
	});
	
});
 
	$("#search_tab").click(function  () {
			$(".album-none").css({
				'display':'none'
			})
		})
	$("#search_tab").siblings("li").click(function  () {
			$(".album-none").show()
	})
</script>
</body>
</html>