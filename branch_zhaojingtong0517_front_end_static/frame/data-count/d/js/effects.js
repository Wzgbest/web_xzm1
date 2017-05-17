// JavaScript Document
var _selectObj = 0;
$(document).ready(function(){
	window.winwidth = $(window).width();
	window.winheight = $(window).height();
	//滚动条
	scrollbind = function(obj){
		if($(window).width()>1025){
			obj.find(".viewport .scrollbar").remove();
			obj.find(".viewport .textArea").removeAttr("style");
			obj.find(".viewport").append('<div class="scrollbar"><div class="trackbar"><div class="thumbbar"></div></div></div>');
			obj.tinyscrollbar();
		}
	}
	$(".textArea img").css({"height":"auto"})
	//菜单
	var menusub;
	if($(".menu li.active").length>0){
		var menuid = $(".menu li.active").index();
	}else{
		var menuid = 100;
	}
	$(".menu li").mouseover(function(){
		var obj = $(this);
		$(".menu li").removeClass("active");
		obj.addClass("active");
	});
	$(".menu li").click(function(){
		if(window.winwidth<1024){
			$(".menu").slideUp();
		}
	});
	$(".menu li").mouseleave(function() {
		$(".menu li").removeClass("active").eq(menuid).addClass("active");
	});
	$(".header .menuBtn").click(function(){
		if($(".menu").css("display")=="none"){
			$(".menu").slideDown();
		}else{
			$(".menu").slideUp();
		}
	});
	//内页菜单
	$(".headerCon .menuBtn").toggle(function(){
		$(".headerCon .menuBtn i.m").addClass("hide");
		$(".headerCon .menuBtn i.c").addClass("show");
		$(".headerCon ul").fadeIn();
	},function(){
		$(".headerCon .menuBtn i.c").removeClass("show");
		$(".headerCon .menuBtn i.m").removeClass("hide");
		$(".headerCon ul").fadeOut();
	});
	//banner
	if(window.winwidth>1024){
		$(".banner,.pageBox").css("height",$(window).height());
	}
	var isIE=!!window.ActiveXObject,
		isIE6=isIE&&!window.XMLHttpRequest,
		isIE8=isIE&&!!document.documentMode,
		isIE7=isIE&&!isIE6&&!isIE8;
	var _bLen = $(".banner").length;
	if(_bLen>1){
		var _bauto = false;
	}else{
		var _bauto = true;
	}
	if($(".banner").find("li").length<2){
		var _bauto = false;
		$(".banner .thumb").hide();
	}
	for(var ba=0;ba<_bLen;ba++){
		if($(".banner").eq(ba).find("li").length>0){
			bannerScroll($(".banner").eq(ba));
		}
	}
	function bannerScroll(obj){
		var _this = $(obj),
			_obj = _this.find(".pic"),
			_len = _obj.find("li").length-1,
			move = _this.find(".pic ul")[0],
			_liw = _obj.width(),
			_lih = _obj.height(),
			_ulw = _len*_liw,
			_windowW = window.winwidth,
			defaultX=0,
			startX,
			startY,
			speed = 500,
			long = -50,
			touchx,
			touchgo,
			touchPlay = false,
			autotime = 5000;
		page = 0;
		_obj.find("li").css("width",_liw);
		for(var bt=0;bt<=_len;bt++){
			_this.find(".thumb").append('<span></span>');
		}
		_this.find(".thumb span").eq(0).addClass("active");
		_this.find(".pic li").eq(0).addClass("active");
		if(_len>0){
			_this.find(".thumb").show();
		}
		function touchStart(event) {
			clearInterval(touchgo);
			var touch = event.touches[0];
			startX = touch.pageX;
			startY = touch.pageY;
			if(move.style.left){
				defaultX = parseFloat(move.style.left);
			}
		}
		function touchMove(event) {
			var touch = event.touches[0];
			if(touch.pageY-startY<50&&touch.pageY-startY>-50){
				event.preventDefault();
			}else{
				event.stopPropagation();
			}
			if(parseFloat(move.style.left)>=-long){
				$(move).css("left","50px");
			}else if(parseFloat(move.style.left)<=-_ulw+long){
				$(move).css("left",-_ulw+long);
			}else{
				var x = defaultX+touch.pageX - startX;
				$(move).css("left",x);
				touchx = x-defaultX;
			}
			touchPlay = true;
		}
		function touchEnd(event) {
			if(touchPlay){
				var _left = parseFloat(move.style.left);
				if(_left>0){
					page=0;
					$(move).animate({
						left:0
					},speed,function(){showText(page)});
				}else if(touchx<long&&page<_len){
					page++;
					$(move).animate({
						left:-(page*_liw)
					},speed,function(){showText(page)});
				}else if(touchx<-long){
					$(move).animate({
						left:-(page*_liw)
					},speed,function(){showText(page)});
				}else if(touchx>-long){
					page--;
					$(move).animate({
						left:-(page*_liw)
					},speed,function(){showText(page)});
				}
				_this.find(".thumb span").removeClass("active").eq(page).addClass("active");
				if(_bauto){
					touchgo = setTimeout('autoTouch()', autotime);
				}
				touchPlay = false;
			}
		}
		showText = function(num){
			_obj.find(".textAnimate").removeAttr("style");
			_obj.find("li").eq(num).find(".textAnimate").fadeIn();
		}
		showText(0);
		if (!isIE6 && !isIE7 && !isIE8){ 
			move.addEventListener("touchstart", touchStart, false);
			move.addEventListener("touchmove", touchMove, false);
			move.addEventListener("touchend", touchEnd, false);
		}
		autoTouch = function(){
			clearInterval(touchgo);
			if(page<_len){
				page++;
				_obj.find("li").eq(page).hide();
				if(_windowW<1025){
					_obj.find("li").show();
					$(move).animate({
						left:-(page*_liw)
					},speed,function(){showText(page)});
				}else{
					$(move).css("left",0);
					_obj.find("li.active").removeClass("active").fadeOut(speed);
					_obj.find("li").eq(page).addClass("active").fadeIn(speed);
				}
			}else if(page>=_len){
				if(_windowW<1025){
					_obj.find("li").show();
					$(move).animate({
						left:0
					},speed,function(){showText(page)});
				}else{
					$(move).css("left",0);
					_obj.find("li.active").removeClass("active").fadeOut(speed);
					_obj.find("li").eq(0).addClass("active").fadeIn(speed);
				}
				page=0;
			}
			_this.find(".thumb span").removeClass("active").eq(page).addClass("active");
			if(_bauto){
				touchgo = setTimeout('autoTouch()', autotime);
			}
		}
		if(_bauto){
			touchgo = setTimeout('autoTouch()', autotime);
		}
		_this.find(".thumb span").click(function(){
			clearInterval(touchgo);
			page = $(this).index()-1;
			if(_windowW<1025){
				_obj.find("li").show();
				$(move).animate({
					left:-(page*_liw)
				},speed,function(){showText(page)});
			}else{
				$(move).css("left",0);
				_obj.find("li.active").removeClass("active").fadeOut(speed);
				_obj.find("li").eq(page).addClass("active").fadeIn(speed);
			}
			_this.find(".thumb span").removeClass("active").eq(page).addClass("active");
			if(_bauto){
				touchgo = setTimeout('autoTouch()', autotime);
			}
		});
		_this.find(".thumb .btn").toggle(function(){
			clearInterval(touchgo);
			_bauto = false;
			$(this).removeClass("i01").addClass("i02");
		},function(){
			clearInterval(touchgo);
			_bauto = true;
			$(this).removeClass("i02").addClass("i01");
			touchgo = setTimeout('autoTouch()', autotime);
		});
		if($(".banner .pic li .bannerImg").height()<$(".banner .pic li").height()){
			$(".banner .pic").addClass("hw");
		}
		if($(".banner .pic li .bannerImg").width()<$(".banner .pic li").width()){
			$(".banner .pic").removeClass("hw");
		}
		$(window).resize(function(){
			if($(".banner .pic li").length>0){
				_liw = $(".banner .pic").width();
				_ulw = _len*_liw;
				$(".banner .pic li").css("width",_liw);
				if($(".banner .pic li .bannerImg").height()<$(".banner .pic li").height()){
					$(".banner .pic").addClass("hw");
				}
				if($(".banner .pic li .bannerImg").width()<$(".banner .pic li").width()){
					$(".banner .pic").removeClass("hw");
				}
			}
		});
	}

	//第一屏新闻
	if($(".page1 .news li").length>0){
		if($(window).width()>420){
			$(".page1 .news").newsScroll({
				rowWidth:280,
				auto:true
			});
		}
	}
	//第二屏
	if($(".page2").length>0){
		var _p2Num = 0;
		$(".page2 .left .tab span").eq(0).addClass("active");
		$(".page2 .left .info").hide().eq(0).fadeIn();
		$(".page2 .right img").hide().eq(_p2Num).fadeIn();
		$(".page2 .left .tab span").mouseover(function(){
			_p2Num = $(this).index();
			if($(".page2 .left .info").eq(_p2Num).css("display")=="none"){
				$(".page2 .left .tab span").removeClass("active").eq(_p2Num).addClass("active");
				$(".page2 .left .info").hide().eq(_p2Num).fadeIn();
				p2share();
			}
			$(".page2 .right img").hide().eq(_p2Num).fadeIn();
		});
		
	}
	//第三屏
	if($(".page4").length>0){
		$(".page4 .left .list").hide().eq(0).fadeIn();
		$(".page4 .tab span").mouseover(function(){
			var _num = $(this).index();
			$(".page4 .tab span").removeClass("active").eq(_num).addClass("active");
			$(".page4 .left .list").hide().eq(_num).fadeIn();
		});
		var _p3share = $(".page4 .right .shareIcon"),
			_p3Len = _p3share.length;
		for(var p3=0;p3<_p3Len;p3++){
			var _p3A = _p3share.eq(p3).find("a");
			_p3A.eq(0).attr("data-rel",weixinShare(page3Share[p3].weixin));
			_p3A.eq(1).attr("href",weiboShare(page3Share[p3].weibo));
			_p3A.eq(2).attr("href",bbsShare(page3Share[p3].bbs));
		}
	}
	//第四屏
	if($(".page6").length>0){
		if(window.winwidth>1024){
			var _mapW = $(".page6 .map").height()/1310*1588;
			$(".page6 .map").css({
				"width":_mapW,
				"left":(window.winwidth-130-_mapW)/2
			});
		}else{
			var _mapH = $(".page6 .map").width()/1588*1310;
			$(".page6 .map").css({
				"height":_mapH
			});
		}
		$(".page6 .change .tab a").click(function(){
			var _font = $(this).prevAll("font").length,
				_index = $(this).index()-_font;
			$(".page6 .change .tab a").removeClass("active").eq(_index).addClass("active");
			$(".page6 .change .box").hide().eq(_index).fadeIn();
		});
		/*$(".page6 .change .ipt input").focus(function(){
			if($(this).val() == '请输入您的位置'){
				$(this).val('');
			}
		});
		$(".page6 .change .ipt input").eq(0).blur(function(){
			if($(this).val() == ''){
				$(this).val('请输入您的位置');
			}
		});
		$(".page6 .change select").change(function(){
			$(this).prevAll("span").html($(this).val());
		});*/
		scrollbind($(".page6 .change .box.first"));
		scrollbind($(".page6 .change .box.last"));
		$(".page6 .change .box.last").hide();
	}
	//第六屏
	var _yearMove = true;
	if($('.page5 .age').length>0){
		/*$('.page5 .age').mouseover(function(){
			if(_yearMove){
				_yearMove=false;
				$(this).jparallax({xparallax:false});
			}
		});*/
		var _p6Len = $(".page5 .tabBox .list").length;
		for(var _p6=0;_p6<_p6Len;_p6++){
			var _p6Li = $(".page5 .tabBox .list").eq(_p6);
			if(_p6Li.find("li").length>3){
				_p6Li.find(".prev,.next").addClass("show");
			}
		}
		$(".page5 .tabBox .list").newsScroll({
			rowWidth:$(".page5 .tabBox .list li").width()+20
		});
		$(".page5 .tabBox").hide().eq(0).show().find(".list").hide().eq(0).show();
		$(".page5 .age .move .year").eq(0).addClass("active");
		$(".page5 .tabBox .tab span").click(function(){
			var _index = $(this).index();
			$(this).parents(".tab").find("span").removeClass("active").eq(_index).addClass("active");
			$(this).parents(".tabBox").find(".list").hide().eq(_index).fadeIn();
		});
		$(".page5 .age .move .year").click(function(){
			var _index = $(this).index(),
				_tabNum = $(".page5 .tabBox").eq($(".page5 .age .move .year.active").index()).find(".tab span.active").index();
			$(".page5 .tabBox").hide().eq(_index).fadeIn();
			$(".page5 .tabBox").eq(_index).find(".tab span").removeClass("active").eq(0).addClass("active");
			$(".page5 .tabBox").eq(_index).find(".list").hide().eq(0).show();
			$(".page5 .tabBox").eq(_index).find(".tab span").removeClass("active").eq(_tabNum).trigger("click");
		});
	}
	//第七屏
	if($(".page7").length>0){
		$(".page7 .tool .item .ipt").eq(0).focus(function(){
			if($(this).val() == 'name:'){
				$(this).val('');
			}
		});
		$(".page7 .tool .item .ipt").eq(0).blur(function(){
			if($(this).val() == ''){
				$(this).val('name:');
			}
		});
		$(".page7 .tool .item .ipt").eq(1).focus(function(){
			if($(this).val() == 'Phone:'){
				$(this).val('');
			}
		});
		$(".page7 .tool .item .ipt").eq(1).blur(function(){
			if($(this).val() == ''){
				$(this).val('Phone:');
			}
		});
		$(".page7 .tool .item .ipt").eq(2).focus(function(){
			if($(this).val() == 'message:'){
				$(this).val('');
			}
		});
		$(".page7 .tool .item .ipt").eq(2).blur(function(){
			if($(this).val() == ''){
				$(this).val('message:');
			}
		});
		$(".page7 .tool .item .ipt").eq(3).focus(function(){
			if($(this).val() == 'Code:'){
				$(this).val('');
			}
		});
		$(".page7 .tool .item .ipt").eq(3).blur(function(){
			if($(this).val() == ''){
				$(this).val('Code:');
			}
		});
	}
	//二维码
	$(".shareIcon a:first-child,.page7 .tool .item .club h3 a.a02").click(function(){
		$(".bodyWindow.qrcode img").attr("src",$(this).attr("data-rel"));
		$(".bodyMask,.bodyWindow.qrcode").fadeIn();
	});
	//弹出框
	$(".bodyMask,.bodyWindow .close").click(function(){
		$("html").removeClass("noScroll");
		$(".bodyMask,.bodyWindow").fadeOut(350,function(){
			$(".videoView .playBox").remove();
		});
		if($(".job").length>0){
			$("html,body").animate({
				scrollTop:window.jobScroll
			});
			$(".pagesize").show();
		}
	});
	//页面滚动
	if($(".pageBox").length>0){
		$(window).bind('hashchange',function(){
			/*_pUrl = window.location.hash.split("/");
			urlSwitch();
			pageTo(_pageNum);*/
		});
		window._mousewheelScroll = 0;
		var _pageNum = 0,
			_pageLen = $(".pageBox").length-1,
			_pageThumb = _pageLen+1,
			_out = _default = 0,
			pageOut,
			pageDot;
		_pUrl = window.location.hash.split("/");
		urlSwitch = function(){
			switch(_pUrl[1]){
				case 'Home':
				  _pageNum = 0;
				  break;
				case 'About':
				  _pageNum = 1;
				  break;
				case 'News':
				  _pageNum = 2;
				  break;
				case 'School':
				  _pageNum = 3;
				  break;
				case 'Video':
				  _pageNum = 4;
				  break;
				case 'Product':
				  _pageNum = 5;
				  break;
				case 'Contact':
				  _pageNum = 6;
				  break;
				default:
				  _pageNum = 0;
				  break;
			}
		}
		urlSwitch();
		numSwitch = function(){
			switch(_pageNum){
				case 0:
				  return '#/Home/'
				  break;
				case 1:
				  return '#/About/'
				  break;
				case 2:
				  return '#/News/'
				  break;
				case 3:
				  return '#/School/'
				  break;
				case 4:
				  return '#/Video/'
				  break;
				case 5:
				  return '#/Product/'
				  break;
				case 6:
				  return '#/Contact/'
				  break;
				default:
				  return ''
				  break;
			}
		}
		window._animateName = "fadeIn fadeInRight fadeInLeft fadeInUp fadeInDown zoomIn hidden";
		//翻页动画
		window._switch = numSwitch(_pageNum);
		pageTurning = function(num){
		  $(".pagenow").removeClass(window._animateName).removeClass("scaleIn");
		  clearInterval(pageOut);
		  clearInterval(pageDot);
		  menuid = _pageNum;
		  var _pBox = $(".pageBox").eq(_pageNum).find(".pagenow"),
			  _pBoxLen = _pBox.length;
		  if(_pageNum>0){
			  $(".header,.rightMenu").addClass("fixed");
		  }else{
			  $(".header,.rightMenu").removeClass("fixed");
		  }
		  $(".header .menu li").removeClass("active").eq(_pageNum).addClass("active");
		  if(num==0){
			  for(var n=0;n<_pBoxLen;n++){
				 var _pBoxItem = _pBox.eq(n);
					 _date = _pBoxItem.attr("data-rel");
				 _pBoxItem.removeClass(_animateName).addClass(_date);
			  }
			  pageOut = setTimeout('$(".pagenow,.header").removeClass(window._animateName);',1500);
			  pageDot = setTimeout('$(".pagenow").removeClass("scaleIn");',2400);
		  }
		  window._switch = numSwitch(_pageNum);
		  if(_default>0){
		  	window.top.location.href = "http:///"+window.top.location.host+""+window.location.pathname+window._switch;
		  }
		  _default++;
		}
		$(window).bind("scroll",function(){
			var _windowTop = $(window).scrollTop();
			if(_mousewheelScroll==0){
				pageScroll(_windowTop);
			}
			if(window.winwidth<640){
				$(".bodyWindow.fwcx").css("top",$(window).scrollTop()+50);
			}else{
				$(".bodyWindow.fwcx").css("top",$(window).scrollTop()+window.winheight/2-200);
			}
		});
		$(".viewport").mouseover(function(){
			_out = 1;
		});
		$(".viewport").mouseleave(function(){
			_out = 0;
		});
		$(".pagesize").mousewheel(function(event,delta){
			if(_out==0&&window.winwidth>1024){
				event.preventDefault();
				if(delta==-1&&_mousewheelScroll==0){
					pageUp();
				}else if(delta==1&&_mousewheelScroll==0){
					pageDown();
				}
			}
		});
		pageTime = function(){
			setTimeout('_mousewheelScroll=0',500);
		}
		pageTo = function(num){
			if(_mousewheelScroll==0){
				_mousewheelScroll++;
				menuid = _pageNum = num;
				var _offset = $(".page"+(_pageNum+1)).offset().top;
				if($(window).width()<768){
					_offset-=70
				}
				$("html,body").stop().animate({
					scrollTop:_offset
				},500,function(){
					pageTime();
				});
				pageTurning(0);
			}
		}
		pageTo(_pageNum);
		if(_pUrl[1]!=undefined){
			//正式var _iframeUrl = window.location.hash.split("#")[1];
			if(window.location.hash.split(window._switch)[1]==undefined){
				var _iframeUrl = window.location.hash.split("#")[1];
			}else{
				var _iframeUrl = "/"+window.location.hash.split(window._switch)[1];
			}
			var _iframeWidth = _iframeUrl.split("?");
			if(_iframeWidth[1]!=undefined){
				loadIframe(_iframeWidth[0],_iframeWidth[1]);
			}
		}
		var _numItem1 = parseFloat($(".page6 .numBox .item").eq(0).find("h4 font").html()),
			_numItem2 = parseFloat($(".page6 .numBox .item").eq(1).find("h4 font").html()),
			_numItem3 = parseFloat($(".page6 .numBox .item").eq(2).find("h4 font").html()),
			_numItemTo1 = _numItemTo2 = _numItemTo3 = _numItemNow = 0;
		numItem = function(){
			if(_numItemTo1<=_numItem1){
				$(".page6 .numBox .item").eq(0).find("h4 font").html(_numItemTo1);
				_numItemTo1++;
				_numItemNow = 1;
			}
			if(_numItemTo3<=_numItem3){
				$(".page6 .numBox .item").eq(2).find("h4 font").html(_numItemTo3.toFixed(1));
				_numItemTo3+=0.1;
				_numItemNow = 1;
			}else if($(".page6 .numBox .item").eq(2).find("h4 font").html().split(".")[1] == '0'){
				$(".page6 .numBox .item").eq(2).find("h4 font").html(_numItemTo3.toFixed(0));
			}
			if(_numItemTo2<=_numItem2){
				$(".page6 .numBox .item").eq(1).find("h4 font").html(_numItemTo2);
				_numItemTo2++;
				_numItemNow = 1;
			}
			if(_numItemNow==1){
				setTimeout('numItem()',10);
				_numItemNow = 0;
			}
		}
		pageUp = function(){
			_mousewheelScroll++;
			if(_pageNum == 3 && $(".page6 .numBox.show").length<=0){
				$(".page6 .numBox").addClass("show");
				$(".page6 .map,.page6 .change").addClass("totop");
				_numItemTo1 = _numItem1 - 100;
				_numItemTo2 = _numItem2 - 100;
				_numItemTo3 = _numItem3 - 10;
				numItem();
				pageTime();
			}else if(_pageNum<_pageLen){
				_pageNum++;
				$("html,body").stop().animate({
					scrollTop:window.winheight*_pageNum
				},500,function(){
					pageTime();
					$(".page6 .numBox").removeClass("show");
					$(".page6 .map,.page6 .change").removeClass("totop");
				});
				pageTurning(0);
			}else if(_pageNum==_pageLen){
				_pageNum++;
				$("html,body").stop().animate({
					scrollTop:$(".main").height()
				},1000,function(){
					pageTime();
					$(".page6 .numBox").removeClass("show");
					$(".page6 .map,.page6 .change").removeClass("totop");
				});
				pageTurning(0);
			}else{
				pageTime();
			}
		}
		pageDown = function(){
			_mousewheelScroll++;
			if(_pageNum>0){
				_pageNum--;
				$("html,body").stop().animate({
					scrollTop:window.winheight*_pageNum
				},500,function(){
					$(".page6 .numBox").removeClass("show");
					$(".page6 .map,.page6 .change").removeClass("totop");
					pageTime();
				});
				pageTurning(0);
			}else{
				pageTime();
			}
		}
		pageScroll = function(_top){
			_pageNum = Math.floor(_top/window.winheight);
		}
	}
	//页面大小
	$(window).resize(function(){
		window.winwidth = $(window).width();
		window.winheight = $(window).height();
		if(window.winwidth>1024){
			$(".banner,.pageBox").css("height",$(window).height());
		}else{
			$(".banner,.pageBox").removeAttr("style");
		}
		if(window.winwidth>1024){
			var _mapW = $(".page6 .map").height()/1310*1588;
			$(".page6 .map").css({
				"width":_mapW,
				"left":(window.winwidth-130-_mapW)/2
			});
		}else{
			var _mapH = $(".page6 .map").width()/1588*1310;
			$(".page6 .map").css({
				"height":_mapH
			});
		}
	});
	//表单选择
	if($(".birthday").length>0){
		$(".birthday").datepicker({
			maxDate:0,
			yearRange: "1950:2015",
			onSelect: function(dateText,inst){
			}
		});
	}
	//招聘弹出框
	$(".job span font a").bind("click",function(){
		$(".bodyMask,.bodyWindow.jobView").fadeIn();
		if($(window).width()<768){
			window.jobScroll = $(window).scrollTop();
			$(".pagesize").hide();
		}
	});
	$(".fromBox select").change(function(){
		$(this).parents(".ipt").find("font").html($(this).val());
	});
	//在线咨询弹出框
	$(".rightMenu a.i02").click(function(){
		$(".bodyMask,.bodyWindow.online").fadeIn();
		$(".bodyWindow iframe").removeAttr("style").contents().find("body .launchBtn").css({
			"width":250,
			"height":40,
			"background":"none"
		});
	});
	//内页JS加载
	var jsCode = new Array(app_tmp+'javascripts/appointment.js',app_tmp+'javascripts/about.js',app_tmp+'javascripts/news.js',app_tmp+'javascripts/video.js',app_tmp+'javascripts/product.js',app_tmp+'javascripts/school.js');
	if($(".onePage").length>0){
		jsLoad(jsCode[0]);
	}else if($(".aboutPage").length>0){
		jsLoad(jsCode[1]);
	}else if($(".newsPage").length>0){
		jsLoad(jsCode[2]);
	}else if($(".videoPage").length>0){
		jsLoad(jsCode[3]);
	}else if($(".productPage").length>0){
		jsLoad(jsCode[4]);
	}else if($(".schoolPage").length>0){
		jsLoad(jsCode[5]);
	}
	if($(".videoView").length>0){
		jsLoad(jsCode[3]);
	}
	//产品
	$(".productPage .title i").click(function(){
		var _sub = $(this).find(".sub");
		if(_sub.css("display")=="none"){
			_sub.slideDown();
			if($(window).width()<=1024){
				setTimeout('clearInterval(window.slideTime);$(".productPage .slide").removeClass("hide")',50);
			}
		}else{
			_sub.slideUp();
		}
	});
	/*$(".productPage .title .sub a").click(function(){
		$(".productPage .title span").html($(this).html());
	});*/
	//框架加载
	//.page1 .box a:first-child,.page1 .box a:nth-child(2n),
	$(".page2 .left .more,.page3 .news-ul .new-li a,.page1 .news li a,.page4 .left a,.page4 .right a,.page5 .title a,.page5 .list li a,.page6 .tabBox .list li a,.page6 .tabBox .tab a,.page7 .tool .item .club .link a,.rightMenu a:first-child,.page7 .sub dd a.showThis").click(function(){
		var _url = $(this).attr("href"),
				_width = $(this).attr("data-width"),
				_item = $(this).attr("data-item");
		if($(this).attr("target")==undefined && $(this).attr("data-video")==undefined && $(window).width()>1024 && $(".loadIframe iframe",top.document).length<=0){
			event.preventDefault();
			if(_width==undefined){
				_width = 100;
			}
			loadIframe(_url,_width,_item);
		}else if($(window).width()>1024 && $(".loadIframe iframe",top.document).length>0){
			tabIframe(_url,_width);
		}
	});
	tabIframe = function(_url,_width){
		$(".loadIframe iframe",top.document).attr('src','');
		$(".loadIframe iframe",top.document).attr('src',_url);
		$(".loadIframe iframe",top.document).animate({"width":_width+"%"});
	}
	//框架返回
	$(".headerCon .back,.videoPage .close").click(function(){
		if($(".openPage").length<=0&&$(".loadIframe iframe",top.document).length>0){
			event.preventDefault();
			$(".loadIframe iframe",top.document).removeClass("show");
			$("body",top.document).removeClass("noScroll");
			setTimeout('$(".loadIframe",top.document).remove()',800);
			if($("body",top.document).find(".pageBox").length>0){
				window.top.pageTurning(1);
			}
		}
	});
	//框架顶部菜单回首页
	$(".headerCon li").click(function(){
		if(window.winwidth>1024&&$(".newsPage").length<=0){
			event.preventDefault();
			$(".loadIframe iframe",top.document).removeClass("show");
			$("body",top.document).removeClass("noScroll");
			setTimeout('$(".loadIframe",top.document).remove()',800);
			window.top.pageTo($(this).index());
		}
	});
	//框架菜单
	$(".left .nav li a,.productPage .tab a").click(function(){
		if($(".openPage").length<=0&&$(window).width()>1024){
			event.preventDefault();
			var _url = $(this).attr("href"),
				_width = $(this).attr("data-width"),
				_item = $(this).attr("data-item");
			if(_url!=''&&_url!='javascript:void(0)'){
				if(_width!=undefined&&_item!=undefined){
					_width = _width+"-"+_item;
				}else if(_width==undefined&&_item!=undefined){
					_width = 100+"-"+_item;
				}else{
					_width = 100;
				}
				if($(".loadIframe iframe",top.document).attr("src")!=_url){
					$("body").append('<div class="loadPage load"></div>');
					$(".loadPage.load").fadeIn();
					$(".loadIframe iframe",top.document).attr("src",_url);
					window.top.location.href = "http:///"+window.top.location.host+""+window.top.location.pathname+"#/"+window.top.location.hash.split("/")[1]+_url+"?"+_width;
				}
				$(".left .nav li a").removeClass("active");
				$(this).addClass("active");
			}
		}
	});
	//页面加载
	loadTotal = function(){
		_loaded++;
		if(_loaded<43){
			_time = 100;
		}if(_loaded<57){
			_time = 150;
		}else if(_loaded<73){
			_time = 250;
		}else if(_loaded<82){
			_time = 350;
		}else if(_loaded<92){
			_time = 450;
		}else if(_loaded<99){
			_time = 800;
		}
		if(_loaded>99){
			clearInterval(loadTime);
		}else{
			$(".loadPage .bar").css("width",_loaded+"%");
			$(".loadPage .bar span font").html(_loaded);
			loadTime = setTimeout("loadTotal()",_time);
		}
	}
	if($(".loadPage").length>0){
		var loadTime = _loaded = 0;
		loadTotal();
		window.onload = function(){
			clearInterval(loadTime);
			$(".loadPage .bar").css("width","100%");
			$(".loadPage .bar span font").html(100);
			$(".loadPage").delay(200).fadeOut();
		}
	}
	//城市选择
	//scrollbind($(".page6 .change .box.first"));
	//scrollbind($(".page6 .change .box.last"));
	var _p4first = $(".page6 .change .box.first"),
			_p4last = $(".page6 .change .box.last");
	$(".schoolChange .select").mouseover(function(){
		_selectObj = 1;
	});
	$(".schoolChange .select").mouseleave(function(){
		_selectObj = 0;
	});
	$(".page6 .schoolChange .select .up").click(function(){
		$(this).parents(".select").slideUp();
	});
	$(".page6").delegate(".schoolChange","click",function(){
		var _obj = $(this).find(".select");
		if(_obj.css("display")=="none"){
			schoolChange($(this));
			$(this).find(".select").slideDown();
		}else if(_selectObj==0){
			_obj.slideUp();
		}
	});
	p4ChangeChick = function(){
		$(".page6 .change .viewport .textArea li a.absolute").unbind();
		$(".page6 .change .viewport .textArea li a.absolute").bind("click",function(){
			if($(this).attr("target")==undefined && $(window).width()>1024){
				event.preventDefault();
				var _url = $(this).attr("href"),
					_width = $(this).attr("data-width"),
					_item = $(this).attr("data-item");
				if(_width==undefined){
					_width = 100;
				}
				loadIframe(_url,_width,_item);
			}
		});
	}
	p4ChangeChick();
	_p4first.delegate("dd","click",function(){
		var _parent = $(this).parents(".ipt"),
			_html = $(this).html(),
			_type = $(this).attr("data-type"),
			_index = $(this).index();
		if(_type==0){
			_index -= 1;
			var _pIndex = $(this).parent().index(),
				_json = schoolJson[_pIndex].city[_index].school;
				_len = _json.length;
			$(".page6 .change .box.first .textArea ul").html('');
			for(var s=0;s<_len;s++){
				$(".page6 .change .box.first .textArea ul").append('<li data-id="'+_json[s].id+'"><h3>'+_json[s].school+'</h3><h4>'+_json[s].add+'</h4><a href="'+_json[s].url+'"  data-width="70" class="absolute"><span class="absobg"></span></a></li>');
			}
			scrollbind($(".page6 .change .box.first"));
			_parent.find(".select").slideUp();
			_parent.find("span").eq(0).html(_html);
		}else if(_type==2){
			var _active = $(this).parents(".city").find("dd.active"),
				_pIndex = _active.index()-1,
				_ptIndex = _active.parent().index(),
				_json = schoolJson[_ptIndex].prov[_pIndex].city[_index].school,
				_len = _json.length;
			$(".page6 .change .box.first .textArea ul").html('');
			for(var s=0;s<_len;s++){
				$(".page6 .change .box.first .textArea ul").append('<li data-id="'+_json[s].id+'"><h3>'+_json[s].school+'</h3><h4>'+_json[s].add+'</h4><a href="'+_json[s].url+'" data-width="70" class="absolute"><span class="absobg"></span></a></li>');
			}
			scrollbind($(".page6 .change .box.first"));
			_parent.find(".select").slideUp();
			_parent.find("span").eq(0).html(_html);
		}		
		p4ChangeChick();
	});
	_p4last.delegate("dd","click",function(){
		var _parent = $(this).parents(".ipt"),
			_html = $(this).html(),
			_type = $(this).attr("data-type"),
			_index = $(this).index(),
			_scroll = _p4last.find(".ipt").eq(1).find("select");
		_p4last.find(".ipt").eq(1).find("span").html("校区");
		$(".page6 .change .box.last .viewport .textArea").html('');
		if(_type==0){
			_index -= 1;
			var _pIndex = $(this).parent().index(),
				_json = schoolJson[_pIndex].city[_index].school;
				_len = _json.length;
			_scroll.html('');
			_scroll.append('<option>请选择</option>');
			for(var s=0;s<_len;s++){
				_scroll.append('<option data-pindex="'+_pIndex+'" data-prov="" data-city="'+_index+'" data-id="'+_json[s].id+'">'+_json[s].school+'</option>');
			}
			scrollbind($(".page6 .change .box.last"));
			_parent.find(".select").slideUp();
			_parent.find("span").eq(0).html(_html);
		}else if(_type==2){
			var _active = $(this).parents(".city").find("dd.active"),
				_pIndex = _active.index()-1,
				_ptIndex = _active.parent().index(),
				_json = schoolJson[_ptIndex].prov[_pIndex].city[_index].school,
				_len = _json.length;
			_scroll.html('');
			_scroll.append('<option>请选择</option>');
			for(var s=0;s<_len;s++){
				_scroll.append('<option data-pindex="'+_pIndex+'" data-prov="'+_ptIndex+'" data-city="'+_index+'" data-id="'+_json[s].id+'">'+_json[s].school+'</option>');
			}
			scrollbind($(".page6 .change .box.last"));
			_parent.find(".select").slideUp();
			_parent.find("span").eq(0).html(_html);
		}	
	});

	$(".schoolChange").delegate("dd","click",function(){
		var _this = $(this).parents(".schoolChange"),
			_select = _this.find(".select"),
			_sLen = schoolJson.length,
			_sBox = _this.find(".city"),
			_cBox = _this.find("ul"),
			_type = $(this).attr("data-type");
		_this.find("ul").remove();
		_select.append('<ul></ul>');
		_this.find(".up").appendTo(_select);
		var _cBox = _this.find("ul");
		if(_type!=2){
			var _index = $(this).index()-1,
				_pIndex = $(this).parent().index();
			_this.find("dd").removeClass("active");
			$(this).addClass("active");
			_this.find("http://www.zhenaijiaoyujituan.com/Public/Home/Js/dl.line").remove();
			if(_type==0&&$(".page6").length<=0){
				var _center = schoolJson[_pIndex].city[_index].school,
					_centerLen = _center.length;
				for(var c=0;c<_centerLen;c++){
					var _cen = _center[c];
					_cBox.append('<li data-id="'+_cen.id+'">'+_cen.school+'</li>');
				}
			}else if(_type==1){
				var _city = schoolJson[_pIndex].prov[_index].city,
					_cLen = _city.length;
				_sBox.append('<dl class="line"></dl>');
				var _line = _sBox.find("http://www.zhenaijiaoyujituan.com/Public/Home/Js/dl.line");
				_line.insertAfter(_sBox.find("dl").eq(_pIndex));
				for(var c=0;c<_cLen;c++){
					var _c = _city[c];
					_line.append('<dd data-type="2">'+_c.city+'</dd>');
				}
			}
		}else if($(".page6").length<=0){
			var _index = _this.find("dd.active").eq(0).index()-1,
				_pIndex = _this.find("dd.active").eq(0).parent().index(),
				_center = schoolJson[_pIndex].prov[_index].city[$(this).index()].school,
				_centerLen = _center.length;
			_this.find("dl.line dd").removeClass("active");
			$(this).addClass("active");
			for(var c=0;c<_centerLen;c++){
				var _cen = _center[c];
				_cBox.append('<li data-id="'+_cen.id+'">'+_cen.school+'</li>');
			}
		}
	});
	$(".schoolChange").delegate(".up","click",function(){
		$(this).parents(".select").slideUp();
	});
	//校区城市
	$(".job .fromBox .schoolChange").click(function(){
		var _obj = $(this).find(".select");
		if(_obj.css("display")=="none"){
			schoolChange($(this));
			$(this).find(".select").slideDown();
		}else if(_selectObj==0){
			_obj.slideUp();
		}
	});
	$(".job .fromBox .schoolChange").delegate("li","click",function(){
		var _id = $(this).attr("data-id"),
			_html = $(this).html();
		$(this).parents(".schoolChange").attr("data-id",_id);
		$(this).parents(".schoolChange").find("font").eq(0).html(_html);
		$("#stationid").val(_id);
		$("input[name=station]").val(_html);
		$(this).parents(".select").slideUp();
    loadJobList(_id,_html);
	});
});
function loadJobList(_id,_html){
	/* * Search Job * */
	var stationChange = $('#stationChange').html();
	var changeJobsVal = "stationSid="+_id+"&stationName="+_html;
	$.ajax({
			type: "POST",
			url: changeJobsPath,
			data: changeJobsVal,
			success: function(msg) {
					msg = eval("("+msg+")");
					if(msg['res'] != 1) {
							alert(msg['info']);
					} else {
							var newJobsStr = "";
							var newJobsTotal = msg['info'].length;
							for(var i = 0; i < newJobsTotal; i++) {
									newJobsStr += "<li><span class='w30'><font>"+msg['info'][i]['cname']+"</font></span>";
									newJobsStr += "<span class='w20'><font>"+msg['info'][i]['rname']+"</font></span>";
									newJobsStr += "<span class='w15'><font>"+msg['info'][i]['province']+msg['info'][i]['city']+"</font></span>";
									newJobsStr += "<span class='w15'><font>"+msg['info'][i]['pnum']+"</font></span>";
									newJobsStr += "<span class='w15'><font><a onclick='getjob_detail("+msg['info'][i]['id']+")' class='showInfo' data-width='70'>查看详情</a></font></span>";
									newJobsStr += "<span style='display:none;'>"+msg['info'][i]['id']+"</span>";
									newJobsStr += "</li>";
							}
							var jobList = $('#jobList');
							jobList.html('');
							jobList.append(newJobsStr);
							
							$(".job span font a").unbind();
							$(".job span font a").bind("click",function(){
									$(".bodyMask,.bodyWindow.jobView").fadeIn();
									if($(window).width()<768){
											window.jobScroll = $(window).scrollTop();
											$(".pagesize").hide();
									}
							});
					}
			},
			error: function() {
					alert('服务器繁忙,请稍候重试~');       
			}
	});
	/* * Search Job End * */
}
//框架加载
function loadIframe(u,w,i){
	var _url = u,
		_width = w,
		_item = i;
	$(".loadIframe").remove();
	$("body").append('<div class="loadIframe"><iframe src="'+_url+'" frameborder="0"></iframe></div>');
	$(".loadIframe iframe").css("width",_width+"%").fadeIn(50,function(){$(".loadIframe iframe").addClass("show");});
	//正式window.top.location.href = "#"+_url+"?"+_width;
	if(window.top.location.hash!=''&&window.location.hash.split(window._switch)[1]!=undefined){
		if(_item==undefined){
			window.top.location.href = "#/"+window.top.location.hash.split("/")[1]+_url+"?"+_width;
		}else{
			window.top.location.href = "#/"+window.top.location.hash.split("/")[1]+_url+"?"+_width+'-'+_item;
		}
	}else{
		window.top.location.href = "#"+_url+"?"+_width;
	}
	$("body").addClass("noScroll");
}
//JS加载
function jsLoad(name){
	var _head = document.getElementsByTagName('HEAD').item(0),
		_script= document.createElement("script");
	_script.type = "text/javascript";
	_script.src=name;
	_head.appendChild( _script);
}
//校区选择
function schoolChange(obj){
	var _this = $(obj),
		_select = _this.find(".select"),
		_sLen = schoolJson.length,
		_sBox = _this.find(".city"),
		_cBox = _this.find("ul");
	_sBox.html('');
	_cBox.remove();
	for(var s=0;s<_sLen;s++){
		var _json = schoolJson[s],
			_area = _json.area,
			_prov = _json.prov,
			_city = _json.city;
		if(_prov==''){
			var _cLen = _city.length;
			_sBox.append('<dl><dt>'+_area+'</dt></dl>');
			for(var c=0;c<_cLen;c++){
				var _c = _city[c];
				_sBox.find("dl").eq(-1).append('<dd data-type="0">'+_c.city+'</dd>');
			}
		}else{
			var _pLen = _prov.length;
			_sBox.append('<dl><dt>'+_area+'</dt></dl>');
			for(var p=0;p<_pLen;p++){
				var _p = _prov[p];
				_sBox.find("dl").eq(-1).append('<dd data-type="1">'+_p.prov+'</dd>');
			}
		}
	}
}
//分享
weixinShare = function(json){
	var _url = 'ewm-1.jpg'/*tpa=http://www.zhenaijiaoyujituan.com/Public/Home/Js/__PUBLIC__/Home/Images/ewm.jpg*/;
	return _url;
}
bbsShare = function(json){
	var _url = json;
	return _url;
}
weiboShare = function(json){
	var _url = 'http://api.bshare.cn/share/sinaminiblog?url='+json.url+'&title='+json.title+'&summary='+json.summary+'&publisherUuid=&pic='+json.pic;
	return _url;
}
qrcode = function(obj){
	var _this = $(obj);
	$(".bodyWindow.qrcode img").attr("src",_this.attr("data-rel"));
	$(".bodyMask,.bodyWindow.qrcode").fadeIn();
}
//防伪查询
function fwcxFun(){
	// $("html").addClass("noScroll");
	if(window.winwidth<640){
		$(".bodyWindow.fwcx").css("top",$(window).scrollTop()+50);
	}else{
		$(".bodyWindow.fwcx").css("top",$(window).scrollTop()+window.winheight/2-200);
	}
	$(".bodyMask,.bodyWindow.fwcx").fadeIn();
}
//视频
function videoBubble(){
	$(".videoView").append('<div class="playBox" id="jp_container_1"><div class="movie"></div><div class="gui jp-gui"><div class="playBar"><div class="jp-seek-bar" style="width:100%;"><div class="jp-play-bar" style="width:0%;"></div></div></div><button class="jp-play" role="button">play</button><button class="jp-full-screen" role="button" tabindex="0">full screen</button><button class="jp-mute" role="button">mute</button></div><span class="playBtn"></span> </div>');
}
function photoViewSize(obj){
	var wOffset = $(obj).parent().width(),
		hOffset = $(obj).parent().height(),
		_img = $(obj),
		shootmeWidth = _img.width(),
		shootmeHeight = _img.height();
	if (shootmeWidth >= wOffset || shootmeHeight >= hOffset) {
		if (shootmeWidth > shootmeHeight) {
			var thisHeight = wOffset / shootmeWidth * shootmeHeight;
			_img.css({
				width: wOffset,
				height: thisHeight,
				"padding-top": (hOffset - thisHeight) / 2,
				"padding-bottom": (hOffset - thisHeight) / 2,
				"padding-left": 0,
				"padding-right": 0
			});
		} else if (shootmeWidth == shootmeHeight) {
			_img.css({
				height: shootmeHeight,
				width: shootmeWidth,
				"padding-top": 0,
				"padding-bottom": 0,
				"padding-left": 0,
				"padding-right": 0
			});
		} else {
			var thisWeight = hOffset / shootmeHeight * shootmeWidth;
			_img.css({
				height: hOffset,
				width: thisWeight,
				"padding-top": 0,
				"padding-bottom": 0,
				"padding-left": (wOffset - thisWeight) / 2,
				"padding-right": (wOffset - thisWeight) / 2
			});
		}
	} else {
		_img.css({
			width: shootmeWidth,
			height: shootmeHeight,
			"padding-top": (hOffset - shootmeHeight) / 2,
			"padding-bottom": (hOffset - shootmeHeight) / 2,
			"padding-left": (wOffset - shootmeWidth) / 2,
			"padding-right": (wOffset - shootmeWidth) / 2
		});
	}	
}
var _noSelect = 0;
(function(a) {
	a.fn.extend({
		newsScroll:function (opt, callback){
			var defaults = {
				time: 4000,
				rowWidth: 253,
				speed: 500,
				auto: false,
				stops: true
			};
			var opts = $.extend({},defaults, opt),
				intId = [];
            scrollLeft = function (obj,_width) {
				var _data = obj.find("ul li").eq(0).attr("data-width"),
					_obj = obj.find("ul");
				if(_data != '' && _data != undefined){
					_width = _data;
				}
                _obj.animate({
                    marginLeft:-_width
                }, 500, function () {
                    _obj.find("li:first").appendTo(_obj);
                    _obj.css({marginLeft:0});
                });
            };
            scrollRight = function (obj,_width) {
				var _data = obj.find("ul li").eq(0).attr("data-width"),
					_obj = obj.find("ul");
				if(_data != '' && _data != undefined){
					_width = _data;
				}
                _obj.find("li:last").prependTo(_obj);
                _obj.css({marginLeft:-_width});
                _obj.animate({
                    marginLeft:0
                }, 500);
            };
			this.each(function(i) {
				var sh = opts.rowWidth,
					time = opts.time,
					auto = opts.auto,
					stops = opts.stops,
					_this = $(this);
				_this.find(".prev").bind("click",function(){
					clearInterval(intId[i]);
					scrollRight(_this, sh);
				});
				_this.find(".next").bind("click",function(){
					clearInterval(intId[i]);
					scrollLeft(_this, sh);
				});
				if(auto){
					intId[i] = setInterval(function() {
						scrollLeft(_this, sh);
					},time);
					if(stops){
						_this.hover(function() {
							clearInterval(intId[i]);
						},function() {
							intId[i] = setInterval(function() {
								scrollLeft(_this, sh);
							},time);
						});
					}
				}
			});
		},
		noticeScroll:function (opt, callback){
			var defaults = {
				time: 4000,
				rowWidth: 253,
				speed: 500,
				auto: false,
				loop:5,
				stops: true
			};
			var opts = $.extend({},defaults, opt),
				intId = [],
				_loop = opts.loop;
            function scrollLeft(obj,_width) {
				var _obj = obj.find(".box");
                _obj.animate({
                    marginTop:-_width
                }, 500, function () {
					for(var l=0;l<_loop;l++){
                    	_obj.find("a").eq(0).appendTo(_obj);
					}
                    _obj.css({marginTop:0});
                });
            };
			this.each(function(i) {
				var sh = opts.rowWidth,
					time = opts.time,
					auto = opts.auto,
					stops = opts.stops,
					_this = $(this);
				if(auto){
					intId[i] = setInterval(function() {
						scrollLeft(_this, sh);
					},time);
					if(stops){
						_this.hover(function() {
							clearInterval(intId[i]);
						},function() {
							intId[i] = setInterval(function() {
								scrollLeft(_this, sh);
							},time);
						});
					}
				}
			});
		}
	});
	a.tiny = a.tiny || {};
	a.tiny.scrollbar = {
		options: {
			axis: "y",
			wheel: 40,
			scroll: true,
			lockscroll: true,
			size: "auto",
			sizethumb: "auto",
			invertscroll: false
		}
	};
	a.fn.tinyscrollbar = function(d) {
		var c = a.extend({}, a.tiny.scrollbar.options, d);
		this.each(function() {
			a(this).data("tsb", new b(a(this), c))
		});
		return this
	};
	a.fn.tinyscrollbar_update = function(c) {
		return a(this).data("tsb").update(c)
	};

	function b(q, g) {
		var k = this,
			t = q,
			j = {
				obj: a(".viewport", q)
			}, h = {
				obj: a(".textArea", q)
			}, d = {
				obj: a(".scrollbar", q)
			}, m = {
				obj: a(".trackbar", d.obj)
			}, p = {
				obj: a(".thumbbar", d.obj)
			}, l = g.axis === "x",
			n = l ? "left" : "top",
			v = l ? "Width" : "Height",
			r = 0,
			y = {
				start: 0,
				now: 0
			}, o = {}, e = "ontouchstart" in document.documentElement;
		function c() {
			k.update();
			s();
			return k
		}
		this.update = function(z) {
			j[g.axis] = j.obj[0]["offset" + v];
			h[g.axis] = h.obj[0]["scroll" + v];
			h.ratio = j[g.axis] / h[g.axis];
			d.obj.toggleClass("disable", h.ratio >= 1);
			m[g.axis] = g.size === "auto" ? j[g.axis] : g.size;
			p[g.axis] = Math.min(m[g.axis], Math.max(0, (g.sizethumb === "auto" ? (m[g.axis] * h.ratio) : g.sizethumb)));
			d.ratio = g.sizethumb === "auto" ? (h[g.axis] / m[g.axis]) : (h[g.axis] - j[g.axis]) / (m[g.axis] - p[g.axis]);
			r = (z === "relative" && h.ratio <= 1) ? Math.min((h[g.axis] - j[g.axis]), Math.max(0, r)) : 0;
			r = (z === "bottom" && h.ratio <= 1) ? (h[g.axis] - j[g.axis]) : isNaN(parseInt(z, 10)) ? r : parseInt(z, 10);
			w()
		};

		function w() {
			var z = v.toLowerCase();
			p.obj.css(n, r / d.ratio);
			h.obj.css(n, -r);
			o.start = p.obj.offset()[n];
			d.obj.css(z, m[g.axis]);
			m.obj.css(z, m[g.axis]);
			p.obj.css(z, p[g.axis])
		}

		function s() {
			if (!e) {
				p.obj.bind("mousedown", i);
				m.obj.bind("mouseup", u)
			} else {
				j.obj[0].ontouchstart = function(z) {
					if (1 === z.touches.length) {
						i(z.touches[0]);
						z.stopPropagation()
					}
				}
			} if (g.scroll && window.addEventListener) {
				t[0].addEventListener("DOMMouseScroll", x, false);
				t[0].addEventListener("mousewheel", x, false);
				t[0].addEventListener("MozMousePixelScroll", function(z) {
					z.preventDefault()
				}, false);
			} else {
				if (g.scroll) {
					t[0].onmousewheel = x;
				}
			}
		}

		function i(A) {
			a("body").addClass("noSelect");
			if (_noSelect == 0) {
				$("body")[0].onselectstart = new Function("return false");   
				_noSelect = 1;     
			}
			var z = parseInt(p.obj.css(n), 10);
			o.start = l ? A.pageX : A.pageY;
			y.start = z == "auto" ? 0 : z;
			if (!e) {
				a(document).bind("mousemove", u);
				a(document).bind("mouseup", f);
				p.obj.bind("mouseup", f)
			} else {
				document.ontouchmove = function(B) {
					B.preventDefault();
					u(B.touches[0])
				};
				document.ontouchend = f
			}
		}

		function x(B) {
			if (h.ratio < 1) {
				var A = B || window.event,
					z = A.wheelDelta ? A.wheelDelta / 120 : -A.detail / 3;
				r -= z * g.wheel;
				r = Math.min((h[g.axis] - j[g.axis]), Math.max(0, r));
				p.obj.css(n, r / d.ratio);
				if(q.find(".textArea").height()>q.find(".viewport").height()){
					h.obj.css(n, -r);
				}
				if (g.lockscroll || (r !== (h[g.axis] - j[g.axis]) && r !== 0)) {
					A = a.event.fix(A);
					A.preventDefault()
				}
			}
		}

		function u(z) {
			if (h.ratio < 1) {
				if (g.invertscroll && e) {
					y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + (o.start - (l ? z.pageX : z.pageY)))))
				} else {
					y.now = Math.min((m[g.axis] - p[g.axis]), Math.max(0, (y.start + ((l ? z.pageX : z.pageY) - o.start))))
				}
				r = y.now * d.ratio;
				h.obj.css(n, -r);
				p.obj.css(n, y.now)
			}
		}

		function f() {
			a("body").removeClass("noSelect");
			if (_noSelect == 1) {
				$("body")[0].onselectstart = new Function("return true");
				_noSelect = 0;
			}
			a(document).unbind("mousemove", u);
			a(document).unbind("mouseup", f);
			p.obj.unbind("mouseup", f);
			document.ontouchmove = document.ontouchend = null;
		}
		return c()
	}
}(jQuery));
(function ($) {
    $.fn.waterfall = function(options) {
        var df = {
            item: '.item',
            margin: 0,
            addfooter: false
        };
        options = $.extend(df, options);
        return this.each(function() {
            var $box = $(this), pos = [],
            _box_width = $box.width(),
            $items = $box.find(options.item),
            _owidth = $items.eq(0).outerWidth() + options.margin,
            _oheight = $items.eq(0).outerHeight() + options.margin,
            _num = Math.floor(_box_width/_owidth);

            (function() {
                var i = 0;
                for (; i < _num; i++) {
                    pos.push([i*_owidth,0]);
                } 
            })();

            $items.each(function() {
                var _this = $(this),
                _temp = 0,
                _height = _this.outerHeight() + options.margin;
                for (var j = 0; j < _num; j++) {
                    if(pos[j][1] < pos[_temp][1]){
                        //暂存top值最小那列的index
                        _temp = j;
                    }
                }
                this.style.cssText = 'left:'+pos[_temp][0]+'px; top:'+pos[_temp][1]+'px;';
                //插入后，更新下该列的top值
                pos[_temp][1] = pos[_temp][1] + _height;
            });

            // 计算top值最大的赋给外围div
            (function() {
                var i = 0, tops = [];
                for (; i < _num; i++) {
                    tops.push(pos[i][1]);
                }
                tops.sort(function(a,b) {
                    return a-b;
                });
				$(".waterfall .load").hide();
				$items.css("visibility","visible");
                $items.parent().height(tops[_num-1]);
            })();

        });
    }
})(jQuery);
