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
