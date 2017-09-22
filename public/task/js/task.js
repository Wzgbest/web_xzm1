$(".nav li").click(function() {
    $(".nav li").removeClass("flow");
    $(this).addClass("flow");
    var task_type=$(this).attr('data-id');
    var url="/task/employee_task/hot_task_load";
    loadPagebypost(url,{'task_type':task_type},'hot_task');
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
//猜输赢弹出框添加ul
var content3 = "<ul class='number2'><li></li><li></li><li></li></ul>";
var t;
t = 2;
for(var i = 0; i < t; i++) {
    $("#myModalsix .list").append(content3);
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
$('.grade .show_ranking_incentive').click(function(){
    $(this).parent().parent().siblings('.motai').addClass('motai1');
    $(this).parents().addClass("change");
    var revealObj = $(".myModaltwo").reveal("{data-animation:'fade'}");
    revealObj.setCloseHandle(function() {
        $('.grade .show_ranking_incentive').parent().parent().siblings('.motai').removeClass('motai1');
        $('.grade .show_ranking_incentive').parents().removeClass("change");
    });
});
$('.grade .show_ranking_reward').click(function(){
    $(this).parent().parent().siblings('.motai').addClass('motai1');
    $(this).parents().addClass("change");
    var revealObj = $(".myModalthree").reveal("{data-animation:'fade'}");
    revealObj.setCloseHandle(function(){
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

//点赞
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
            $(self).siblings().text(q);
        });
    }
    i++;
    $(this).attr('index_img', i)

})

$(".dv3 .up .like .right .add").click(function() {
    var self = this;
    var jt = $(this).attr('index_img');
    var task_id = $(this).attr('task_id');
    console.log($(this).siblings(".yi"));
    var x = parseInt($(this).siblings(".yi").text());
    var j = parseInt(jt);
    if(j % 2){
        task_like(task_id,true,function(data){
            $(self).attr('src', '/task/img/praise.png');
            var y = x + 1;
            $(self).siblings(".yi").text(y);
        });
    }else {
        task_like(task_id,false,function(data){
            $(self).attr('src', '/task/img/zan.png');
            var y = x - 1;
            $(self).siblings(".yi").text(y);
        });
    }
    j++;
    $(this).attr('index_img', j)

})
//$(".dv3 .up .like .right .add").click(function() {
//
//	var qw = $('.dv3 .up .like .right .add').attr('index_img');
//				
//				var i = parseInt(qw);
//				if(i % 2){
//					$('.dv3 .up .like .right .add').attr('src', '/task/img/praise.png');
//					
//				}else {
//					$('.dv3 .up .like .right .add').attr('src', '/task/img/zan.png');
//				
//				}
//				i++;
//				$('.dv3 .up .like .right .add').attr('index_img', i)
//
//
//})

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
    console.log("add");

    var num1=parseInt($('.num1').val());
    var num2=parseInt($('.num2').val());
    var num3=parseInt($('.num3').val());

    var neirong="<li>第<span>"+num1+"</span>~<span>"+num2+"</span>名，各奖励<span>"+num3+"</span>元<i class='fa fa-edit'></i><i class='fa fa-trash-o trash'></i></li>"

    $("article .dv4 ul").prepend(neirong);
    var s=0;
    var max_num2=0;

    $("article .dv4 ul li:not(:last)").each(function(){

        var num1=parseInt($(this).children("span:eq(0)").text());
        var num2=parseInt($(this).children("span:eq(1)").text());
        var num3=parseInt($(this).children("span:eq(2)").text());

        var i=num2-num1+1;
        s=s+i*num3;

        if(max_num2<num2){
            max_num2=num2
        }
    });
    $("article .dv4 ul .largest").text(max_num2);
    $("article .dv4 ul .total").text(s);//总计的钱
})

// 评论跳转
// $(".task .comment .commont_img").click(function(){
//     var id = $(this).attr("task_id");
// 	loadPage('/task/going_task/PK_details/id/'+id,'task-hallfr');
// });


//function fenlei(id,url){
//	this.id=id;
//	this.url=url;
//}


//评论
$(".dv3 .up .right p").click(function(){
    //alert(1)
    var pinglun="";
    var sk=$(".dv3 .up textarea").val();
    //$(".speek").text(sk);
    //alert(sk)

    pinglun+="<div class='one'><img src='/task/img/man.png'/><div>";
    pinglun+="<p><span class='name'>刘美娜</span><span>:</span><span class='speek'>";
    pinglun+=sk;
    pinglun+="</span></p>";
    pinglun+="<p class='reply'><span>2分钟前</span></p></div></div>";
    $(".dv3 .down .review").prepend(pinglun);

})


//新建tab切换
$("article .dv4 .xuanze input").click(function(){
    $(this).parent().siblings().children("input[type='radio']").attr("checked",false);
    $(this).parent().siblings().children("input[type='radio']").prop("checked",false);
    $(this).attr("checked",true);
    $(this).prop("checked",true);
    if($(this).parent(".choice").index()==1){
        $("article .dv4 .tab1").css("display","none");
        $("article .dv4 .tab2").css("display","block");
    }else{
        $("article .dv4 .tab1").css("display","block");
        $("article .dv4 .tab2").css("display","none");
    }

});


function new_task_form(load_table){
    this.load_table = load_table;
    console.log("load_table");
    console.log(load_table);
    var self = this;

    //绑定change事件
    //获取select值
    //panduan
    //tiaozhuan
    $("#"+self.load_table+" .dv4 .parcel .hezi select").change(function(){
        var val=$(".dv4 .parcel .hezi select").val();
        console.log(val);

        if(val==1){
            loadPage('/task/index/new_task/fr/'+self.load_table,self.load_table);
        }else if(val==2){
            loadPage('/task/index/PKnew_task/fr/'+self.load_table,self.load_table);
        }else if(val==3){
            loadPage('/task/index/rewardnew_task/fr/'+self.load_table,self.load_table);
        }
    });

    $("#"+self.load_table+" .task .issue .new_task_submit").click(function(){
        console.log("new_task_submit");
        var form_sel = "#"+self.load_table+" .task .new_task_form";
        // var new_task_form_data = $(form_sel+"").serializeArray();
        // console.log(new_task_form_data);
        var task_type = $(form_sel+" [name='task_type']").val();
        console.log('task_type',task_type);
        var task_name = $(form_sel+" [name='task_name']").val();
        console.log('task_name',task_name);
        if(task_name==''){
            layer.msg('请输入任务名称!',{icon:2});
            return;
        }

        var money = 0;
        if(task_type==1){
            var task_method = $(form_sel+" [name='task_method'][checked]").val();
            console.log('task_method',task_method);

            var target_num = $(form_sel+" [name='target_num']").val();
            console.log('target_num',target_num);
            if(target_num==''){
                layer.msg('请输入达标要求!',{icon:2});
                return;
            }

            if(task_method==1 || task_method==3){
                var s=0;
                var max_num2=0;
                $("#"+self.load_table+" .task article .dv4 ul li:not(:last)").each(function(){
                    var num1=parseInt($(this).children("span:eq(0)").text());
                    var num2=parseInt($(this).children("span:eq(1)").text());
                    var num3=parseInt($(this).children("span:eq(2)").text());
                    var i=num2-num1+1;
                    s=s+i*num3;
                    if(max_num2<num2){
                        max_num2=num2
                    }
                });
                money = s;
                console.log('s',s);
            }else if(task_method==2){
                money = $(form_sel+" [name='reward_amount']").val();
            }
        }else if(task_type==2){
            money = $(form_sel+" [name='reward_amount']").val();
        }else if(task_type==3){
            var num = $(form_sel+" [name='reward_num']").val();
            var amount = $(form_sel+" [name='reward_amount']").val();
            money = num*amount;
        }
        console.log('money',money);
        if(money<=0){
            layer.msg('奖金必须大于0!',{icon:2});
            return;
        }
        var public_to_take_str = $(form_sel+" .public_to_take").attr("data-id");
        if(public_to_take_str==''){
            layer.msg('请选择面向群体!',{icon:2});
            return;
        }
        var pop = new popLoad("#"+self.load_table+" .task .pay-pop","/task/index/pay/money/"+money+"/fr/"+self.load_table);
    });
}


function skip(from,target,comment){
    this.from =from;
    this.target=target;
    this.comment=comment;
    var self = this;

    $("."+this.from+" header .xinjian ").click(function(){

        loadPage('/task/index/new_task/fr/'+self.target,self.target);
    });
    $("."+this.from+" article .dv1 .comment .comment_pk").click(function(){

        loadPage('/task/going_task/PK_details.html',self.comment);
    });
    $("."+this.from+" article .dv1 .comment .comment_incentive").click(function(){

        loadPage('/task/going_task/incentive_details.html',self.comment);
    });
    $("."+this.from+" article .dv1 .comment .comment_reward").click(function(){

        loadPage('/task/going_task/reward_details.html',self.comment);
    });

}