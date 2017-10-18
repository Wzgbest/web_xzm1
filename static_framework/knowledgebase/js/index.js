$(".new-speechcraft").click(function(){
	loadPage("/knowledgebase/speechcraft/new", "speechcraftfr");
});
$(".knowledgebase_speechcraft_new header li").click(function(){
	loadPage("/knowledgebase/speechcraft/index", "speechcraftfr");
});
$(".new-company-library").click(function(){
	loadPage("/knowledgebase/company_library/new", "company-libraryfr");
});
$(".knowledgebase_company_library_new header li").click(function(){
	loadPage("/knowledgebase/company_library/index", "company-libraryfr");
});
$(".knowledgebase .new_panel .radio-select input[name='class']").change(function(){
	var num = $(this).val();
	var form = $(this).parent().siblings("form");
	form.addClass("hide").eq(num).removeClass("hide");
})



//图片点击事件
$(".knowledgebase_company_library_index .library-list .lib-content .pic-grid li img").click(function(){
	$(this).parents(".pic-grid").addClass("hide").siblings().removeClass("hide");
	$(this).parents(".pic-grid").siblings(".pic-show").removeClass("hide").children("img").attr("src",$(this).attr("src"));
	$(this).parents(".pic-grid").siblings(".pic-list").removeClass("hide").children("li").eq($(this).parent().index()).addClass("current");
});
$(".knowledgebase_company_library_index .library-list .lib-content .pic-list li img").click(function(){
	$(this).parent("li").addClass("current").siblings("li").removeClass("current");
	$(this).parent("li").parent("ul").siblings(".pic-show").children("img").attr("src",$(this).attr("src"));
})
//评论
var comment = {
	state:false,
	name:null
}
$(".knowledgebase_company_library_index .library-list .lib-operator .comment").click(function(){
	$(this).parent(".lib-operator").siblings(".lib-reply").toggleClass("hide");
})
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-now .reply-operator button").click(function(){
	var txt = $(this).parent(".reply-operator").siblings("input").val();
	var face_src = $(this).parent(".reply-operator").siblings(".face").children("img").attr("src");
	var name = $("#nav-user span").text();
	console.log(name);
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
	$(this).parent(".reply-operator").parent("li").parent(".reply-now").siblings(".reply-ago").append(content);  
	$(this).parent(".reply-operator").siblings("input").val(null).attr("placeholder","");
	comment.state = false;
	comment.name = null;
	        				
});
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li").click(function(){
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").focus();
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").attr("placeholder","回复"+$(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text());
	comment.state = true;
	comment.name = $(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text();
});
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li.praise").click(function(){
	$(this).toggleClass("active");
});
//点赞
$(".knowledgebase_company_library_index .library-list .lib-operator .praise").click(function(){
	$(this).toggleClass("active");
});