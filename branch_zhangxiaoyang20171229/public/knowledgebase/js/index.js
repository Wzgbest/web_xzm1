$(".new-speechcraft2").click(function(){
	loadPage("/knowledgebase/speech_craft/add_article", "speech-databasefr");
});
$(".knowledgebase_speechcraft_new header li").click(function(){
	loadPage("/knowledgebase/speech_craft/index", "speech-databasefr");
});
$(".new-company-library").click(function(){
	loadPage("/knowledgebase/corporation_share/add_share_page", "company-libraryfr");
});
$(".knowledgebase_company_library_new header li").click(function(){
	loadPage("/knowledgebase/corporation_share/index", "company-libraryfr");
});
$(".knowledgebase .new_panel .radio-select input[name='class']").change(function(){
	var num = $(this).val();
	var form = $(this).parent().siblings("form");
	form.addClass("hide").removeClass('new_article_form').eq(num).removeClass("hide").addClass('new_article_form');
})

//图片点击事件

//初始图片点击
$(".knowledgebase_company_library_index .library-list .lib-content .pic-grid li").click(function(){
	$(this).parents(".pic-grid").addClass("hide").siblings().removeClass("hide");	
	let num = $(this).index();
	picShow(this,num);
	picShowCursor(this,num);
});
//小图片点击
$(".knowledgebase_company_library_index .library-list .lib-content .pic-list li").click(function(){
	let num = $(this).index();
	picShow(this,num);
	picShowCursor(this,num);
})
//←切换
$(".knowledgebase_company_library_index .library-list .lib-content .pic-show .cursor-controler .left").click(function () {
	let num = $(this).parents(".picture").children(".pic-list").children("li.current").index();
	picShow(this,num-1);
	picShowCursor(this,num-1);
});
//→切换
$(".knowledgebase_company_library_index .library-list .lib-content .pic-show .cursor-controler .right").click(function () {
	let num = $(this).parents(".picture").children(".pic-list").children("li.current").index();
	picShow(this,num+1);
	picShowCursor(this,num+1);
});
//收起
$(".knowledgebase_company_library_index .library-list .lib-content .pic-show img").click(function(){
	$(this).parents(".pic-show").addClass("hide").siblings(".pic-list").addClass("hide").siblings(".pic-grid").removeClass("hide");
});
//图片显示控制方法
function picShow(e,n) {
	let pic = $(e).parents(".picture");
	let list = pic.children(".pic-list").children("li");
	let show = pic.children(".pic-show").find("img");
	let src = list.eq(n).children("img").attr("src");
	list.removeClass("current").eq(n).addClass("current");
	show.attr("src",src);
}
//左右切换图标控制方法
function picShowCursor(e,n) {
	let len = $(e).parents(".picture").children(".pic-list").children("li").length;
	if(n==0&&n!=len-1){
		$(e).parents(".picture").children(".pic-show").find(".left").addClass("hide").siblings(".right").removeClass("hide");
	}
	if(n==len-1&&n!=0){
		$(e).parents(".picture").children(".pic-show").find(".right").addClass("hide").siblings(".left").removeClass("hide");
	}
	if(n==0&&n==len-1){
		$(e).parents(".picture").children(".pic-show").children(".cursor-controler").children("div").addClass("hide");
	}
	if(n!=0&&n!=len-1){
		$(e).parents(".picture").children(".pic-show").children(".cursor-controler").children("div").removeClass("hide");
	}
}
//评论
var comment = {
	state:false,
	id:null,
	name:null
}
$(".knowledgebase_company_library_index .library-list .lib-operator .comment").click(function(){
	$(this).parent(".lib-operator").siblings(".lib-reply").removeClass("hide");
})
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-now .reply-operator button.cancel").click(function() {
	// body...
	$(this).parents(".lib-reply").addClass("hide");
});
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-now .reply-operator button.submit").click(function(){
	var sel = $(this);
	var txt = $(this).parent(".reply-operator").siblings("input").val();
	var face_src = $(this).parent(".reply-operator").siblings(".face").children("img").attr("src");
	var name = $("#nav-user span").text();
	// console.log(name);
	var content1 = '<li><div class="face"><img src="';
	var content2 = '" /></div><div class="reply-ago-content"><span class="name color-blue2">';
	if(comment.state){
		var content3 = '</span><span class="reply-reply">&nbsp;回复&nbsp;<span class="name2 color-blue2">';
	}else{
		var content3 = '</span><span class="reply-reply hide">&nbsp;回复&nbsp;<span class="name2 color-blue2">';
	}
	var content35 = '</span></span><span>：</span><span class="content">';
	var content4 = '</span></div><div class="reply-ago-operator"><span class="datetime">';
	var time = new Date().toLocaleString();
	var content5 = '</span><ul class="fr"><li>回复</li></ul></div></li>';
	var content = content1 +face_src+content2+name+content3+comment.name+content35+txt+content4+time+content5;
	var share_id = $(this).parents(".lib").attr("share_id");
	var comment_id = comment.id;
	// console.log(share_id,comment_id,txt);
	$.ajax({
		url: '/knowledgebase/corporation_share/addComment',
		type: 'POST',
		dataType: 'json',
		data: {'share_id': share_id,'comment_id':comment_id,'reply_content':txt},
		success:function(data){
			if (data.status) {
				layer.msg(data.info,{icon:data.status==1?1:2});
				sel.parent(".reply-operator").parent("li").parent(".reply-now").siblings(".reply-ago").append(content); 
				sel.parent(".reply-operator").parent("li").parent(".reply-now").siblings(".reply-ago").children('li').last().attr("comment_id",data.data);
			}else{
				layer.msg(data.info,{icon:data.status==1?1:2});
			}
		},
		error:function() {
            layer.msg('评论失败!',{icon:2});
		},
	});
	
	 
	$(this).parent(".reply-operator").siblings("input").val(null).attr("placeholder","");
	comment.state = false;
	comment.id = null;
	comment.name = null;
	        				
});
$(document).on('click','.knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li',function(){
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").focus();
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").attr("placeholder","回复"+$(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text());
	comment.state = true;
	comment.id = $(this).parents('.reply-ago-operator').parent("li").attr("comment_id");
	comment.name = $(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text();
});
/*$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li").click(function(){
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").focus();
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").attr("placeholder","回复"+$(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text());
	comment.state = true;
	comment.id = $(this).parents('.reply-ago-operator').parent("li").attr("comment_id");
	comment.name = $(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text();
});*/
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li.praise").click(function(){
	$(this).toggleClass("active");
});
//点赞
$(".knowledgebase_company_library_index .library-list .lib-operator .praise").click(function(){
	var own = $(this);
	var share_id = $(this).parents(".lib").attr("share_id");
	var not_like;
	if ($(this).hasClass("active")) {
		not_like = 1;
	}else{
		not_like = 0;
	}
	// console.log(share_id,not_like);return;
	$.ajax({
		url: '/knowledgebase/corporation_share/like',
		type: 'POST',
		dataType: 'json',
		data: {'share_id': share_id, 'not_like':not_like},
		success:function(data){
			if (data.status) {
				layer.msg(data.info,{icon:data.status==1?1:2});
				own.toggleClass("active");
			}else{
				layer.msg(data.info,{icon:data.status==1?1:2});
			}
		},
		error:function(){
            layer.msg('点赞失败!',{icon:2});
		},
	});
	
});
