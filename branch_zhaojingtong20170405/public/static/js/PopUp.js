function pop(id,url,clicker){
    $(clicker).click(function(){
    	console.log($(this));
    	$.ajax({
        	url: url,
        	type: 'get',
        	async: false,
        	success: function(data) {
            	$(id).html(data);
            	$(id).removeClass("hide");
        	},
        	error: function() {
                layer.msg('加载失败!',{icon:2});
        	}
    	});   	
    });
    $(document).on('click', id+" .pop-close-btn", function() {
	   $(id).children().remove();
	}); 
}
function popLoad(id,url){   
	$.ajax({
    	url: url,
    	type: 'get',
    	async: false,
    	success: function(data) {
        	$(id).html(data);
        	$(id).removeClass("hide");
    	},
    	error: function() {
            layer.msg('加载失败!',{icon:2});
    	}
	});   	
    $(document).on('click', id+" .pop-close-btn", function() {
	   $(id).children().remove();
	}); 
}
function popUp(e){
	console.log(e.innerHTML);
	blackBgshow();
	/*获取点击的文字*/
	var txt1 = document.createTextNode(e.innerHTML);
	console.log(e.innerHTML);
	/*根据不同点击对象创建不同的列表*/
	if(e.innerHTML=="群发短信"){
		console.log(1);
		var detail = document.createElement("textarea");
		detail.setAttribute("placeholder","请输入短信内容");
		detail.setAttribute("style","margin-top:20px;margin-left:0;width: 520px;height: 150px;position: relative;left: 50%;top: 50%;transform: translateX(-50%);");
	}
	/*页面最后创建section*/
	var pop = document.createElement("section");
	pop.setAttribute("id","popUpContent");
	pop.setAttribute("class","m-form");
	/*插入文本*/
	var header = document.createElement("header");
	var h1 = document.createElement("h1");
	h1.appendChild(txt1);
	var i = document.createElement("i");
	i.setAttribute("class","fa fa-close fa-2x close");
	var div = document.createElement("div");
	div.setAttribute("class","u-submitButton");
	div.setAttribute("style","margin-top:10px;");
	var btn1 = document.createElement("button");
	var txt2 = document.createTextNode("确定");
	btn1.appendChild(txt2);
	var btn2 = document.createElement("button");
	var txt3 = document.createTextNode("取消");
	btn2.appendChild(txt3);
	div.appendChild(btn1);
	div.appendChild(btn2);
	h1.appendChild(i);
	header.appendChild(h1);
	pop.appendChild(header);
	pop.appendChild(detail);
	pop.appendChild(div);
	document.getElementsByTagName("body")[0].appendChild(pop);
	btn2.onclick = function(){
		removePop();
	}
	i.onclick = function(){
		removePop();
	}
}
function removePop(){
	document.getElementById("popUpContent").remove();
	blackBghide();
}
