$(".new-speechcraft").click(function(){
	loadPage("/knowledgebase/speech_craft/add_article", "speech-databasefr");
});
$(".knowledgebase_speechcraft_new .radio-select input[name='class']").change(function(){
	console.log(1);
	if($(this).val()==1){
		$("form.original").removeClass("hide");
		$("form.original").addClass("new_article_form");
		$("form.quote").addClass("hide");
		$("form.quote").removeClass("new_article_form");
	}else if($(this).val()==2){
		$("form.original").addClass("hide");
		$("form.original").removeClass("new_article_form");
		$("form.quote").removeClass("hide");
		$("form.quote").addClass("new_article_form");
	}
})
$(".knowledgebase_speechcraft_new header li").click(function(){
	loadPage("/knowledgebase/speech_craft/index", "speech-databasefr");
});
function add_talk_article(){
	editor.sync();
	var new_article_data = $(".new_article_form").serialize();
	var url = '/knowledgebase/speech_craft/addArticle';
	// console.log(new_article_data);
	$.ajax({
		url: url,
		type: 'post',
		data: new_article_data,
		dataType: 'json',
		success: function(data) {
			// alert(data.data);
			if(data.status) {
				alert(data.info);
				loadPage("/knowledgebase/speech_craft/index", "speech-databasefr");
			}else {
				alert(data.message);
			}
		},
		error: function() {
			alert("添加失败!");
		}
	});
}
function article_info_show(id){
	var url = "/knowledgebase/speech_craft/show/id/"+id;
	var panel = 'speech-databasefr';
	loadPage(url,panel);
}
function search_article(){
	var key_word = $("input[name='key_word']").val();
	// alert(key_word);
	var url = "/knowledgebase/speech_craft/index/key_word/"+key_word;
	var panel = 'speech-databasefr';
	loadPage(url,panel);

}
function show_class_article(class_id){
	var url = "/knowledgebase/speech_craft/index/class_id/"+class_id;
	var panel = 'speech-databasefr';
	loadPage(url,panel);
}