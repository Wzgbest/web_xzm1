$(".nav li").click(function() {
	$(".nav li").removeClass("flow");
	$(this).addClass("flow");
});

var content = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t = 10;
for(var i = 0; i < t; i++) {
	$("#myModalone .table").append(content);
}

var content2 = "<ul class='number2'><li></li><li></li><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t = 10;
for(var i = 0; i < t; i++) {
	$("#myModaltwo .table").append(content2);
}

var content3 = "<ul class='number2'><li></li><li></li><li></li></ul>";
//$("#myModalone .table").append(content);
var t;
t = 10;
for(var i = 0; i < t; i++) {
	$("#myModalsix .table").append(content3);
}
//领红包
//$(".dv1 .grade .get").click(function() {
//	if($(this).hasClass("p1")) {
//		return;
//	}
//
//	$(this).hide();
//	$(".dv1 .mengceng").addClass("m_c");
//	var a = '<img src="/task/img/redPacket.png" class="picture"/>';
//	$(".mengceng").append(a);
//	$('.picture').click(function() {
//
//		$(this).parent().removeClass("m_c")
//		$(this).remove()
//
//		$(".dv1 .grade .get").show()
//		$(".dv1 .grade .get").addClass('p1').removeClass("p2").text("已领取100元")
//
//	})
//
//})
//$(".dv1 .grade .p1").removeClass("get")
	$(".dv1 .grade .get").hide();
	$(".dv1 .mengceng").addClass("m_c");
	var a = '<img src="/task/img/redPacket.png" class="picture"/>';
	$(".mengceng").append(a);
	$('.picture').click(function() {

		$(this).parent().removeClass("m_c")
		$(this).remove()

		$(".dv1 .grade .get").show()
		$(".dv1 .grade .get").addClass('p1').removeClass("p2").text("已领取100元")

	})
//领小红包
$("article .dv2 .left .box img").click(function() {
	
	var a="<span class='two'>（100￥）</span>"
	$(this).parent().append(a);
	$(".turn").text("已领取")
	$(this).remove();
	
})


//评论
$('.comment .criticism').click(function() {
	//	$(".comment .review").hide();先后消失
	$(".comment .triangle img:nth-last-of-type(1)").css('display', 'inline-block');
	$(".comment .review").css('display', 'block')
})
$(function() {
	$(document).bind("click", function(e) {
		var target = $(e.target); //表示当前对象，切记，如果没有e这个参数，即表示整个BODY对象
		if(target.closest(".comment").length == 0) {
			$(".comment .review").hide();
		}
	})
})
$(function() {
	$(document).bind("click", function(e) {
		var target = $(e.target); //表示当前对象，切记，如果没有e这个参数，即表示整个BODY对象
		if(target.closest(".comment ").length == 0) {
			$(".comment .triangle img:nth-last-of-type(1)").hide();
		}
	})
})

//点击span时加.motai 
$('.grade .show_ranking_task').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModalone").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_task').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_task').parents().removeClass("change");
	});
});
$('.grade .show_ranking_incentive').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModaltwo").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_incentive').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_incentive').parents().removeClass("change");
	});
});
$('.grade .show_ranking_reward').click(function() {
	$(this).parent().parent().siblings('.motai').addClass('motai1');
	$(this).parents().addClass("change");
	var revealObj = $(".myModalthree").reveal("{data-animation:'fade'}");
	revealObj.setCloseHandle(function() {
		$('.grade .show_ranking_reward').parent().parent().siblings('.motai').removeClass('motai1');
		$('.grade .show_ranking_reward').parents().removeClass("change");
	});
});

//间接参与，重置P标签
$(".dv1 .right .give .task").click(function() {
	$(this).siblings().remove();
	$(this).remove()
	$(".give").append("<p class='cute'>任务进行中</p>");
})
//$(".dv1 .right .stimulate .task").click(function() {
//
//	$(this).remove()
//	$(".stimulate").append("<p class='cute'>任务进行中</p>");
//
//})
$(".dv1 .right .give .guess").click(function() {
	var n="<p class='cute'>猜输赢进行中</p>"
	$(this).siblings().remove();
	$(this).remove()
	$(".give").append(n);
})
$(".dv1 .right .comment .add").click(function() {
	var self = this;
	var qw = $(this).attr('index_img');
	var task_id = $(this).attr('task_id');
	console.log($(this).siblings(".yi"));
				var p = parseInt($(this).siblings().text());
			
				var i = parseInt(qw);
				if(i % 2){
					task_like(task_id,true,function(data){
						$(self).attr('src', '/task/img/praise.png');
						var q = p + 1;
						$(self).siblings().text(q);
					});
				}else {
					task_like(task_id,false,function(data){
						$(self).attr('src', '/task/img/zan.png');
						var q = p - 1;
	//					$(".dv1 .right .comment .yi").text(q)
						$(self).siblings().text(q);
					});
				}
				i++;
				$(this).attr('index_img', i)
//				console.log($('.dv1 .right .comment .add').attr('index_img'))

})
$(".dv3 .right .like .right .add").click(function() {

	var qw = $('.dv3 .right .like .right .add').attr('index_img');
				
				var i = parseInt(qw);
				if(i % 2){
					$('.dv3 .right .like .right .add').attr('src', '/task/img/praise.png');
					
				}else {
					$('.dv3 .right .like .right .add').attr('src', '/task/img/zan.png');
				
				}
				i++;
				$('.dv3 .right .like .right .add').attr('index_img', i)
//				console.log($('.dv3 .right .like .right .add').attr('index_img'))

})

function task_like(id,like,fun){
	var post_data = "id="+id;
	if(!like){
		post_data+="&unlike=1";
	}
	$.ajax({
		url: '/task/task_like/like',
			type: 'post',
			data: post_data,
			dataType:"json",
			success: function(data) {
					console.log(data);
				if(data.status == 1) {
					fun(data);
				}else{
					alert(data.info);
				}
			},
			error: function() {
				alert("操作出现错误!");
			},
		});
}
//新建里边点击加号ul显示
$("article .dv4 .parcel .add").click(function(){

$("article .dv4 ul").toggleClass('point')
	
})

//$(".dv4 .parcel .hezi select").change(function(){
//	var a=$(".dv4 .parcel .hezi select").val()
//
//	$(".dv4 .parcel .right .b").text(a+'项目')
//})

//绑定change事件
//获取select值
//panduan
//tiaozhuan
$(".dv4 .parcel .hezi select").change(function(){
	var val=$(".dv4 .parcel .hezi select").val();
//	alert(val)

	if(val==1){
		javascript:loadPage('/task/going_task/new_task.html','public-taskfr');
	};
	if(val==2){
		javascript:loadPage('/task/going_task/PKnew_task.html','public-taskfr');
	};
	if(val==3){
		javascript:loadPage('/task/going_task/rewardnew_task.html','public-taskfr');
	}
})


$("article .dv4 .xuanze input").click(function(){
					var index=$(this).attr("index")
					$("article .dv4 .tab").css("display","none");
					$("article .dv4 .tab").eq($(this).attr("index")).css("display",'block')
				})

