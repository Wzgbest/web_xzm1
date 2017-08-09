$(".nav li").click(function(){
	$(".nav li").removeClass("flow");
	$(this).addClass("flow");
});

var content = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$("#myModalone .table").append(content);	
}


var content2 = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$("#myModaltwo .table").append(content2);	
}

var content3 = "<ul class='number2'><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t=10;
for(var i=0;i<t;i++){
	$("#myModalsix .table").append(content3);	
}
//红包
$(".dv1 .grade .get").click(function(){
	$(this).hide()
	$(".dv1 .mengceng").addClass("m_c")
//	$(".mengceng").append('<img src=' + "img/redPacket.png" + ""/>)
//	$(".mengceng").append('<img src='+"../img/redPacket.png"+'>')
var a='<img src="../img/redPacket.png" class="picture"/>'
	$(".mengceng").append(a)
	

//$('.mengceng').on("click",'.picture',function(){
//	alert(1);
//	$(this).parent().removeClass("m_c")
//	$(this).remove();
//})

	$('.picture').click(function(){
			
		$(this).parent().removeClass("m_c")		
		$(this).remove()


	$(".dv1 .grade .get").show()
	$(".dv1 .grade .get").addClass('p1').removeClass("p2").text("已领取")
		
	})	
	//.removeClass("get")
	
})
	$(".dv1 .grade .p1").removeClass("get")	
	

//评论
$('.comment .criticism').click(function(){
//	$(".comment .review").hide();先后消失
	$(".comment .triangle img:nth-last-of-type(1)").css('display','inline-block');
	$(".comment .review").css('display','block')
})
$(function() {
				$(document).bind("click", function(e) {
					var target = $(e.target); //表示当前对象，切记，如果没有e这个参数，即表示整个BODY对象
					if(target.closest(".comment").length == 0){
						$(".comment .review").hide();
					}
				})
			})
$(function() {
				$(document).bind("click", function(e) {
					var target = $(e.target); //表示当前对象，切记，如果没有e这个参数，即表示整个BODY对象
					if(target.closest(".comment ").length == 0){
						$(".comment .triangle img:nth-last-of-type(1)").hide();
					}
				})
			})



//点击span时加.motai 
$('.grade .show_ranking_task').click( function() {
		$(this).parent().parent().siblings('.motai').addClass('motai1');
		$(this).parents().addClass("change");
		var revealObj = $(".myModalone").reveal("{data-animation:'fade'}");
		revealObj.setCloseHandle(function(){
			$('.grade .show_ranking_task').parent().parent().siblings('.motai').removeClass('motai1');
			$('.grade .show_ranking_task').parents().removeClass("change");
		});
});