﻿!function ($) {
	$.extend({
		_jsonp: {
			scripts: {},
			counter: 1,
			charset: "gb2312",
			head: document.getElementsByTagName("head")[0],
			name: function (callback) {
				var name = "_jsonp_" + (new Date).getTime() + "_" + this.counter;
				this.counter++;
				var cb = function (json) {
					eval("delete " + name),
							callback(json),
							$._jsonp.head.removeChild($._jsonp.scripts[name]),
							delete $._jsonp.scripts[name]
				};
				return eval(name + " = cb"),
						name
			},
			load: function (a, b) {
				var c = document.createElement("script");
				c.type = "text/javascript",
						c.charset = this.charset,
						c.src = a,
						this.head.appendChild(c),
						this.scripts[b] = c
			}
		},
		getJSONP: function (a, b) {
			var c = $._jsonp.name(b),
					a = a.replace(/{callback};/, c);
			return $._jsonp.load(a, c),
					this
		}
	})
}
(jQuery);
// 百度定位时，不调用doInitRegion方法，定位成功后,才调用,如果成功获得地址id，设置cookie,没有就以默认
//商品物流配送与运费
function ajaxDispatching(region_id,province_name,city_name,district_name) {
	if(typeof (auto_bd) != 'undefined'  && auto_bd==1 ) return;
	province_name = province_name || '';
	city_name = city_name || '';
	district_name = district_name || '';
	var goods_id = $("input[name='goods_id']").val();
	var goods_prom_type = $("input[name='goods_prom_type']").val();//预售包邮
	if(region_id == '' && district_name == ''){
		region_id = getCookie('district_id');
	}
	if(typeof(goods_id) != 'undefined' && (region_id != '' || district_name != '')){
		$.ajax({
			type: "POST",
			dataType: 'json',
			data: {goods_id: goods_id, region_id: region_id,province:province_name,city:city_name,district:district_name},
			url: "/index.php?m=Home&c=Goods&a=dispatching",
			success: function (data) {
				if (data.status == 1) {
					$('#dispatching_msg').show().html(data.msg);
					if(data.result.freight == 0){
						$('#dispatching_desc').show().html('免运费');
					}else{
						$('#dispatching_desc').show().html("运费 ￥" + data.result.freight);
					}
					$('.buy_button').removeClass('buy_bt_disable');
					var store_count = $('input[name="store_count"]').val();
					if(store_count == 0){
						$('.buy_button').addClass('buy_bt_disable');
						$("#number").val(0)
					}else{
						$('.buy_button').removeClass('buy_bt_disable');
					}
				} else if (data.status == -1) {
					$('#dispatching_msg').show().html(data.msg);
					$('#dispatching_desc').hide();
					$('.buy_button').addClass('buy_bt_disable');
				} else {
					$('#dispatching_msg').show().html(data.msg);
					$('#dispatching_desc').hide();
					$('.buy_button').addClass('buy_bt_disable');
					if(data.is_not==1){//不支持配送
						$('#join_cart').removeClass('buy_bt_disable')
					}
				}
				if(goods_prom_type == 4){
					$('#dispatching_desc').hide();
				}
				if(auto_bd == 2){
					auto_bd = 0;
					if(data.addr_id.province_id && data.addr_id.city_id && data.addr_id.district_id){
						setCookies('province_id',data.addr_id.province_id);
						setCookies('city_id',data.addr_id.city_id);
						setCookies('district_id',data.addr_id.district_id);
					}
					doInitRegion();
				}
			}
		});
	}
}
//循环输出省html
function getProvinceHtml()
{
	var str = '';
	for(var o in iplocation){
		str += '<li><a href="#none" data-value="'+iplocation[o].id+'">'+o+'</a></li>';
	}
	return str;
}

//-----------------------------------------------------------


//根据省份ID获取名称
function getNameById(provinceId) {
	for (var o in iplocation) {
		if (iplocation[o] && iplocation[o].id == provinceId) {
			return o;
		}
	}
	return "北京";
}

/**
 * 获取县区列表
 * @param result
 * @returns {string}
 */
function getAreaList(result) {
	var html = ["<ul class='area-list'>"];
	var longhtml = [];
	var longerhtml = [];
	if (result && result.length > 0) {
		for (var i = 0, j = result.length; i < j; i++) {
			result[i].name = result[i].name.replace(" ", "");
			if (result[i].name.length > 12) {
				longerhtml.push("<li class='longer-area'><a href='#none' data-value='" + result[i].id + "'>" + result[i].name + "</a></li>");
			}
			else if (result[i].name.length > 5) {
				longhtml.push("<li class='long-area'><a href='#none' data-value='" + result[i].id + "'>" + result[i].name + "</a></li>");
			}
			else {
				html.push("<li><a href='#none' data-value='" + result[i].id + "'>" + result[i].name + "</a></li>");
			}
		}
	}
	else {

		return false;
	}
	html.push(longhtml.join(""));
	html.push(longerhtml.join(""));
	html.push("</ul>");

	return html.join("");
}

//初始化布局
(function ($) {
	$.fn.Address = function (cfg) {
		return this.each(function () {
			var JD_stock = $('<div class="content"><div data-widget="tabs" class="m JD-stock">'
					+ '<div class="mt">'
					+ '    <ul class="tab">'
					+ '        <li data-index="0" data-widget="tab-item" class="curr"><a href="#none" class="hover"><em>请选择</em><i></i></a></li>'
					+ '        <li data-index="1" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
					+ '        <li data-index="2" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
					+ '        <li data-index="3" data-widget="tab-item" style="display:none;"><a href="#none" class=""><em>请选择</em><i></i></a></li>'
//                        + '        <li data-index="4" style="float:right;"><a href="#none" class=""><em>确定</em><i></i></a></li>'
//                        + '        <li data-index="5" style="float:right;" ><a href="#none" class=""><em>清除</em><i></i></a></li>'
					+ '    </ul>'
					+ '    <div class="stock-line"></div>'
					+ '</div>'
					+ '<div class="mc stock_province_item" data-area="0" data-widget="tab-content">'
					+ '    <ul class="area-list">'
//                        + '       <li><a href="#none" data-value="1">北京</a></li><li><a href="#none" data-value="2">上海</a></li><li><a href="#none" data-value="3">天津</a></li><li><a href="#none" data-value="4">重庆</a></li><li><a href="#none" data-value="5">河北</a></li><li><a href="#none" data-value="6">山西</a></li><li><a href="#none" data-value="7">河南</a></li><li><a href="#none" data-value="8">辽宁</a></li><li><a href="#none" data-value="9">吉林</a></li><li><a href="#none" data-value="10">黑龙江</a></li><li><a href="#none" data-value="11">内蒙古</a></li><li><a href="#none" data-value="12">江苏</a></li><li><a href="#none" data-value="13">山东</a></li><li><a href="#none" data-value="14">安徽</a></li><li><a href="#none" data-value="15">浙江</a></li><li><a href="#none" data-value="16">福建</a></li><li><a href="#none" data-value="17">湖北</a></li><li><a href="#none" data-value="18">湖南</a></li><li><a href="#none" data-value="19">广东</a></li><li><a href="#none" data-value="20">广西</a></li><li><a href="#none" data-value="21">江西</a></li><li><a href="#none" data-value="22">四川</a></li><li><a href="#none" data-value="23">海南</a></li><li><a href="#none" data-value="24">贵州</a></li><li><a href="#none" data-value="25">云南</a></li><li><a href="#none" data-value="26">西藏</a></li><li><a href="#none" data-value="27">陕西</a></li><li><a href="#none" data-value="28">甘肃</a></li><li><a href="#none" data-value="29">青海</a></li><li><a href="#none" data-value="30">宁夏</a></li><li><a href="#none" data-value="31">新疆</a></li><li><a href="#none" data-value="32">台湾</a></li><li><a href="#none" data-value="42">香港</a></li><li><a href="#none" data-value="43">澳门</a></li><li><a href="#none" data-value="84">钓鱼岛</a></li>'
					+ getProvinceHtml()
					+ '    </ul>'
					+ '</div>'
					+ '<div class="mc stock_city_item" data-area="1" data-widget="tab-content"></div>'
					+ '<div class="mc stock_area_item" data-area="2" data-widget="tab-content""></div>'
					+ '<div class="mc stock_town_item" data-area="3" data-widget="tab-content"></div>'
					+ '</div></div>');
			var ul = $(this), store_selector = ul.find('div.store-selector'), addrIDContainer = ul.find('div.addrID'), getAreaListcallback = 'areaCallBack' + new Date().getTime(); //回调函数名称
			var currentAreaInfo; //保存当前信息变量

			function setAddressName(){
				var address = currentAreaInfo.currentProvinceName;
				if(typeof (currentAreaInfo.currentCityName) != 'undefined'){
					address += ','+currentAreaInfo.currentCityName
				}
				if(typeof (currentAreaInfo.currentAreaName) != 'undefined'){
					address += ','+currentAreaInfo.currentAreaName
				}
				if(typeof (currentAreaInfo.currentTownName) != 'undefined'){
					address += ','+currentAreaInfo.currentTownName
				}
				var tCity = cleanKuohao(currentAreaInfo.currentCityName);
				var tArea = cleanKuohao(currentAreaInfo.currentAreaName);
				var tTown = cleanKuohao(currentAreaInfo.currentTownName);
				var tID=currentAreaInfo.currentAreaId;
				if (tTown != "") {
					//tID = currentAreaInfo.currentTownId;
				}
				else if (tArea != "") {

					tID = currentAreaInfo.currentAreaId;
				}
				else if (tCity != "") {

					tID = currentAreaInfo.currentCityId;
				}
				else {
					tID = currentAreaInfo.currentProvinceId;
				}

				store_selector.find(".text div").html(address).attr("title", tID);
				delCookie('province_id');
				delCookie('city_id');
				delCookie('district_id');
				delCookie('town_id');
				setCookies('province_id',currentAreaInfo.currentProvinceId,30*24*60*60*1000);
				setCookies('city_id',currentAreaInfo.currentCityId,30*24*60*60*1000);
				setCookies('district_id',currentAreaInfo.currentAreaId,30*24*60*60*1000);
				setCookies('town_id',currentAreaInfo.currentTownId,30*24*60*60*1000);
				store_selector.removeClass('hover');
			}

			window.getAreaListcallback = function (r) { ////////全局JSONP回调
				parentRegion = JSON.stringify(r);
				if(!getAreaList(r)){
					var i = parseInt(currentAreaInfo.act)-1;
					areaTabContainer.eq(i).show().addClass('curr')
					areaTabContainer.eq(i+1).hide();
					$("[data-area="+i+"]").show();
					setAddressName();
					return;
				}
				currentDom.html(getAreaList(r));
				if (currentAreaInfo.act == 2){
					currentAreaInfo.act = 3;

				}else if (currentAreaInfo.act == 3){
					currentAreaInfo.act = 4;
				}

				if (currentAreaInfo.act >= 2) {
					currentDom.find("a").unbind('click');
					currentDom.find("a").click(function () {
						var id = $(this).attr("data-value")
						var name = $(this).html();
						if (page_load) {
							page_load = false;
						}

						if (currentAreaInfo.act == 2){

						}else if (currentAreaInfo.act == 3){


							areaTabContainer.eq(2).removeClass("curr").find("em").html(name);
							areaTabContainer.eq(3).addClass("curr").show().find("em").html("请选择");
							//areaContainer.show().html("<div class='iloading'>正在加载中，请稍候...</div>");
							areaContainer.hide();
							townaContainer.show();
							currentDom = townaContainer;


							currentAreaInfo.currentAreaId = id;
							currentAreaInfo.currentAreaName = name;
							currentAreaInfo.currentTownName = '';
							ajaxDispatching(id);//选中地址事件
							$.getJSONP("/index.php?m=Home&c=Goods&callback=getAreaListcallback&a=region&fid="+id);
						}else if (currentAreaInfo.act == 4){
							areaTabContainer.eq(3).find("em").html(name);
							currentAreaInfo.currentTownId = id;
							currentAreaInfo.currentTownName = name;
							setAddressName();
						}

					});
					if (page_load) { //初始化加载
						if (currentAreaInfo.act == 3){

							currentAreaInfo.currentAreaName = areaContainer.find("a[data-value='" + currentAreaInfo.currentAreaId + "']").html()
							if(currentAreaInfo.currentAreaName){
								areaTabContainer.eq(2).addClass("curr").show().find("em").html(currentAreaInfo.currentAreaName);
							}
							currentDom = townaContainer;
							ajaxDispatching(currentAreaInfo.currentAreaId);//选中地址事件
							$.getJSONP("/index.php?m=Home&c=Goods&callback=getAreaListcallback&a=region&fid="+currentAreaInfo.currentAreaId);
						}else if (currentAreaInfo.act == 4){
							currentAreaInfo.currentTownName = townaContainer.find("a[data-value='" + currentAreaInfo.currentTownId + "']").html()
							if(currentAreaInfo.currentTownName){
								areaTabContainer.eq(2).removeClass("curr")
								areaTabContainer.eq(3).addClass("curr").show().find("em").html(currentAreaInfo.currentTownName);
							}
							setAddressName();
						}
					}
				}
			}
			// 弃用
			window.getAreaListcallbackold = function (r) { ////////全局JSONP回调
				parentRegion = JSON.stringify(r);

				currentDom.html(getAreaList(r));
				if (currentAreaInfo.currentLevel >= 2) {
					currentDom.find("a").click(function () {
						if (page_load) {
							page_load = false;
						}
						if (currentDom.hasClass("stock_area_item")) {
							currentAreaInfo.currentLevel = 3;
						}
						else if (currentDom.hasClass("stock_town_item")) {
							currentAreaInfo.currentLevel = 4;
						}
						getStockOpt($(this).attr("data-value"), $(this).html());
					});
					if (page_load) { //初始化加载
						currentAreaInfo.currentLevel = currentAreaInfo.currentLevel == 2 ? 3 : 4;
						if (currentAreaInfo.currentAreaId && new Number(currentAreaInfo.currentAreaId) > 0) {
							getStockOpt(currentAreaInfo.currentAreaId, currentDom.find("a[data-value='" + currentAreaInfo.currentAreaId + "']").html());
						}
						else {
							getStockOpt(currentDom.find("a").eq(0).attr("data-value"), currentDom.find("a").eq(0).html());
						}
					}
				}
			}


			function chooseProvince(provinceId) {
				provinceContainer.hide();
				currentAreaInfo.act = 1;
				currentAreaInfo.currentLevel = 1;
				currentAreaInfo.currentProvinceId = provinceId;
				currentAreaInfo.currentProvinceName = getNameById(provinceId);
				//currentDom = provinceContainer;
				if (!page_load) {
					currentAreaInfo.currentCityId = 0;
					currentAreaInfo.currentCityName = "";
					currentAreaInfo.currentAreaId = 0;
					currentAreaInfo.currentAreaName = "";
					currentAreaInfo.currentTownId = 0;
					currentAreaInfo.currentTownName = "";
				}
				areaTabContainer.eq(0).removeClass("curr").find("em").html(currentAreaInfo.currentProvinceName);
				areaTabContainer.eq(1).addClass("curr").show().find("em").html("请选择");
				areaTabContainer.eq(2).hide();
				areaTabContainer.eq(3).hide();
				cityContainer.show();
				areaContainer.hide();
				townaContainer.hide();
				if (provinceCityJson["" + provinceId]) {
					cityContainer.html(getAreaList(provinceCityJson["" + provinceId]));
					cityContainer.find("a").click(function () {
						currentAreaInfo.act = 2;
						if (page_load) {
							page_load = false;
						}
						store_selector.unbind("mouseout");
						chooseCity($(this).attr("data-value"), $(this).html());
					});
					if (page_load) { //省初始化加载
						if (currentAreaInfo.currentCityId && new Number(currentAreaInfo.currentCityId) > 0) {
							chooseCity(currentAreaInfo.currentCityId, cityContainer.find("a[data-value='" + currentAreaInfo.currentCityId + "']").html());
						}
						else {
							chooseCity(cityContainer.find("a").eq(0).attr("data-value"), cityContainer.find("a").eq(0).html());
						}
					}
				}
			}

			function chooseCity(cityId, cityName) {
				provinceContainer.hide();
				cityContainer.hide();
				currentAreaInfo.act=2;
				currentAreaInfo.currentLevel = 2;
				currentAreaInfo.currentCityId = cityId;
				currentAreaInfo.currentCityName = cityName;
				if (!page_load) {
					currentAreaInfo.currentAreaId = 0;
					currentAreaInfo.currentAreaName = "";
					currentAreaInfo.currentTownId = 0;
					currentAreaInfo.currentTownName = "";
				}
				areaTabContainer.eq(1).removeClass("curr").find("em").html(cityName);
				areaTabContainer.eq(2).addClass("curr").show().find("em").html("请选择");
				areaTabContainer.eq(3).hide();
				areaContainer.show().html("<div class='iloading'>正在加载中，请稍候...</div>");
				townaContainer.hide();
				currentDom = areaContainer;
				$.getJSONP("/index.php?m=Home&c=Goods&callback=getAreaListcallback&a=region&fid="+cityId);
			}

			function chooseArea(areaId, areaName) {
				provinceContainer.hide();
				cityContainer.hide();
				areaContainer.hide();
				currentAreaInfo.act=4;
				currentAreaInfo.currentLevel = 3;
				currentAreaInfo.currentAreaId = areaId;
				currentAreaInfo.currentAreaName = areaName;
				if (!page_load) {
					currentAreaInfo.currentTownId = 0;
					currentAreaInfo.currentTownName = "";
				}
				areaTabContainer.eq(2).removeClass("curr").find("em").html(areaName);
				areaTabContainer.eq(3).addClass("curr").show().find("em").html("请选择");
				townaContainer.show().html("<div class='iloading'>正在加载中，请稍候...</div>");
				currentDom = townaContainer;
				$.getJSONP("/index.php?m=Home&c=Goods&callback=getAreaListcallback&a=region&fid="+areaId);
				//store_selector.removeClass('hover');
			}


			store_selector.find('.text').after(JD_stock);
			var areaTabContainer = JD_stock.find(".tab li");
			var provinceContainer = ul.find("div.stock_province_item");
			var cityContainer = ul.find("div.stock_city_item");
			var areaContainer = ul.find("div.stock_area_item");
			var townaContainer = ul.find("div.stock_town_item");
			var currentDom = provinceContainer;
			var parentRegion = null;
			//当前地域信息
			var currentAreaInfo;
			//初始化当前地域信息
			function CurrentAreaInfoInit(proid, cityid, areaid, townid) {
				currentAreaInfo = { "currentLevel": 1, "currentProvinceId": proid, "currentProvinceName": "北京", "currentCityId": cityid, "currentCityName": "", "currentAreaId": areaid, "currentAreaName": "", "currentTownId": townid, "currentTownName": "" };
				var ipLoc = getCookieByName("ipLoc-djd");
				ipLoc = ipLoc ? ipLoc.split("-") : [proid || 1, cityid || 72, areaid || 0, townid || 0];
				if (ipLoc.length > 0 && ipLoc[0]) {
					currentAreaInfo.currentProvinceId = ipLoc[0];
					currentAreaInfo.currentProvinceName = getNameById(ipLoc[0]);
				}
				if (ipLoc.length > 1 && ipLoc[1]) {
					currentAreaInfo.currentCityId = ipLoc[1];
				}
				if (ipLoc.length > 2 && ipLoc[2]) {
					currentAreaInfo.currentAreaId = ipLoc[2];
				}
				if (ipLoc.length > 3 && ipLoc[3]) {
					currentAreaInfo.currentTownId = ipLoc[3];
				}
				chooseProvince(currentAreaInfo.currentProvinceId); //加载省，需要一级一级加载，不能越级
			}
			var page_load = true;

			store_selector.on("click",'.text',function (e) {
				store_selector.addClass('hover');
				store_selector.find('.content').show();
				JD_stock.show();
			}).find("dl").remove();
			CurrentAreaInfoInit(cfg.proid, cfg.cityid, cfg.areaid, cfg.townid);

			//当第0个选项卡被点击时，显示“省”选项面板
			areaTabContainer.eq(0).find("a").click(function () {
				currentAreaInfo.act=1;
				areaTabContainer.removeClass("curr");
				areaTabContainer.eq(0).addClass("curr").show();
				provinceContainer.show();
				cityContainer.hide();
				areaContainer.hide();
				townaContainer.hide();
				areaTabContainer.eq(1).hide();
				areaTabContainer.eq(2).hide();
				areaTabContainer.eq(3).hide();
			});

			//当第1个选项卡被点击时，显示“市”选项面板
			areaTabContainer.eq(1).find("a").click(function () {
				currentAreaInfo.act=2;
				areaTabContainer.removeClass("curr");
				areaTabContainer.eq(1).addClass("curr").show();
				provinceContainer.hide();
				cityContainer.show();
				areaContainer.hide();
				townaContainer.hide();
				areaTabContainer.eq(2).hide();
				areaTabContainer.eq(3).hide();
			});

			//当第2个选项卡被点击时，显示“县”选项面板
			areaTabContainer.eq(2).find("a").click(function () {
				currentAreaInfo.act=3;
				areaTabContainer.removeClass("curr");
				areaTabContainer.eq(2).addClass("curr").show();
				provinceContainer.hide();
				cityContainer.hide();
				areaContainer.show();
				townaContainer.hide();
				areaTabContainer.eq(3).hide();
			});


			//当第四个选项卡被点击时，确定地名------------------------------------------------------
			areaTabContainer.eq(3).find("a").click(function () {
				currentAreaInfo.act=4;

			}); //当第四个选项卡被点击时，确定地名------------------------------------------------------------

			provinceContainer.find("a").click(function () {
				if (page_load) {
					page_load = false;
				}
				store_selector.unbind("mouseout");
				chooseProvince($(this).attr("data-value"));
			}).end();

		});
	};
})(jQuery);

var iplocation = locationJsonInfoDyr.ip_location;
var provinceCityJson = locationJsonInfoDyr.city_location;

function doInitRegion()
{
	if(typeof (auto_bd) != 'undefined'  && auto_bd>0 ) return;
	var province_id = getCookieByName('province_id'),city_id = getCookieByName('city_id'),district_id = getCookieByName('district_id'),town_id=getCookieByName('town_id');
	if(province_id==null || city_id==null || district_id==null ){
		province_id = 1;
		city_id = 72;
		district_id = 2819;
		town_id = 0;
	}
	$('ul.list1').Address({ proid: province_id, cityid: city_id, areaid: district_id ,townid:town_id});
}

function cleanKuohao(str) {
	if (str && str.indexOf("(") > 0) {
		str = str.substring(0, str.indexOf("("));
	}
	if (str && str.indexOf("（") > 0) {
		str = str.substring(0, str.indexOf("（"));
	}
	return str;
}

function getCookieByName(name) {
	var start = document.cookie.indexOf(name + "=");
	var len = start + name.length + 1;
	if ((!start) && (name != document.cookie.substring(0, name.length))) {
		return null;
	}
	if (start == -1)
		return null;
	var end = document.cookie.indexOf(';', len);
	if (end == -1)
		end = document.cookie.length;
	return unescape(document.cookie.substring(len, end));
}
