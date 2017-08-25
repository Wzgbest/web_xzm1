$(".new-speechcraft").click(function(){
	loadPage("/knowledgebase/speechcraft/new", "speechcraftfr");
});
$(".knowledgebase_speechcraft_new .radio-select input[name='class']").change(function(){
	console.log(1);
	if($(this).val()==1){
		$("form.original").removeClass("hide");
		$("form.quote").addClass("hide");
	}else if($(this).val()==2){
		$("form.original").addClass("hide");
		$("form.quote").removeClass("hide");
	}
})
$(".knowledgebase_speechcraft_new header li").click(function(){
	loadPage("/knowledgebase/speechcraft/index", "speechcraftfr");
});

var ap = new APlayer({ 
	element: document.getElementById('player1'), 
	narrow:false, 
	autoplay: false, 
	showlrc: false, 
	music: { 
	    title: 'Preparation', 
	    author: 'Hans Zimmer/Richard Harvey', 
	    url: '/knowledgebase/music/1.mp3', 
	    pic: '/knowledgebase/img/9.jpg' 
	} 
}); 
ap.init();
//图片点击事件
$(".knowledgebase_company_library_index .library-list .lib-content .pic-list li img").click(function(){
	$(this).parent("li").addClass("current").siblings("li").removeClass("current");
	$(this).parent("li").parent("ul").siblings(".pic-show").children("img").attr("src",$(this).attr("src"));
})
//评论
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
	var content3 = '</span><span>：</span><span class="content">';
	var content4 = '</span></div><div class="reply-ago-operator"><span class="datetime">8月24日 15:13</span><ul class="fr"><li>回复</li><li><i class="fa fa-thumbs-up"></i>赞</li></ul></div></li>';
	var content = content1 +face_src+content2+name+content3+txt+content4;
	$(this).parent(".reply-operator").parent("li").parent(".reply-now").siblings(".reply-ago").append(content);  
	$(this).parent(".reply-operator").siblings("input").val(null);
	        				
});
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li").eq(0).click(function(){
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").focus();
	$(this).parents(".reply-ago").siblings(".reply-now").children("li").children("input").attr("placeholder","回复"+$(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text());
	/*$(".knowledgebase_company_library_index .library-list .lib-reply .reply-now .reply-operator button").click(function(){
	var txt = $(this).parent(".reply-operator").siblings("input").val();
	var face_src = $(this).parent(".reply-operator").siblings(".face").children("img").attr("src");
	var name = $("#nav-user span").text();
	console.log(name);
	var content1 = '<li><div class="face"><img src="';
	var content2 = '" /></div><div class="reply-ago-content"><span class="name color-blue2">';
	var content25 = '</span><span class="reply-reply hide">&nbsp;回复&nbsp;<span class="name2 color-blue2">';
	var name2 = $(this).parents(".reply-ago-operator").siblings(".reply-ago-content").children(".name").text();
	var content3 = '</span></span><span>：</span><span class="content">';
	var content4 = '</span></div><div class="reply-ago-operator"><span class="datetime">8月24日 15:13</span><ul class="fr"><li>回复</li><li><i class="fa fa-thumbs-up"></i>赞</li></ul></div></li>';
	var content = content1 +face_src+content2+name+content25+name2+content3+txt+content4;
	$(this).parent(".reply-operator").parent("li").parent(".reply-now").siblings(".reply-ago").append(content);  
	$(this).parent(".reply-operator").siblings("input").val(null);
	        				
});*/
});
$(".knowledgebase_company_library_index .library-list .lib-reply .reply-ago .reply-ago-operator li").eq(1).click(function(){
	$(this).toggleClass("active");
});
//点赞
$(".knowledgebase_company_library_index .library-list .lib-operator .praise").click(function(){
	$(this).toggleClass("active");
});
