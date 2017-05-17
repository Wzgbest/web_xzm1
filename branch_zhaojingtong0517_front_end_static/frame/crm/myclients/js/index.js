$("ul.firNav li").click(function(){
	$("ul.firNav li").removeClass("current");
	$(this).addClass("current");
});
var content = "<ul class='tab-list'><li class='tab-1-ver'><input type='checkbox'/></li><li class='tab-2-ver'></li><li class='tab-3-ver'></li><li class='tab-4-ver'></li><li class='tab-5-ver'></li><li class='tab-6-ver'></li><li class='tab-7-ver'></li><li class='tab-8-ver'></li><li class='tab-9-ver'></li><li class='tab-10-ver'></li><li class='tab-11-ver'></li><li class='tab-12-ver'></li><li class='tab-13-ver'></li><li class='tab-14-ver'></li><li class='tab-15-ver'></li><li class='tab-16-ver'></li><div class='clearfix'></div></ul>";
//$(".table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$(".table").append(content);
}
