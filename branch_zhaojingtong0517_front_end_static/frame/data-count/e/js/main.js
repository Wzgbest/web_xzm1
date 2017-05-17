
function Hover(obj, calssName) {
	obj.hover(function(){
		$(this).addClass(calssName);
	},function(){
		$(this).removeClass(calssName);
	})
}

function list(i){
	$(".lists .w1004 a").eq(i).addClass("list-cur")
}
function per(i){
	$(".menus a").eq(i).addClass("ing")
}

function ban(){
	$(".ab-3").animate({
	top:510,
	opacity:1
},600,function(){
	$(".ab-1").animate({
		top:330,
		opacity:1
	},500)
	$(".ab-2").animate({
		top:366,
		opacity:1
	},500)	
})
}
