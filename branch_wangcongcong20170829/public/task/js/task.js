var task_type='';
var order_name='';
$(".nav li").click(function() {
    $(".nav li").removeClass("flow");
    $(this).addClass("flow");
    task_type=$(this).attr('data-id');
    var method=$(this).parents('div').attr('class')||'';
    var panel=method.replace('_load','');
    var url="/task/employee_task/"+method;
    loadPagebypost(url,{'task_type':task_type,'order_name':order_name},panel);
});
$(".classify p").click(function() {
    order_name=$(this).attr('data-id');
    var method=$(this).parents("div .sort").parents('div').attr('class');
    var panel=method.replace('_load','');
    var url="/task/employee_task/"+method;
    loadPagebypost(url,{'order_name':order_name,'task_type':task_type},panel);
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
//领红包
$(".dv1 .grade .get").hide();
$(".dv1 .mengceng").addClass("m_c");
//	var picture = '<img src="/task/img/redPacket.png" class="picture"/>';
//	$(".dv1 .mengceng").append(picture);
$('.direct_participation_load').on('click','.picture',function(){
    $(this).parent().removeClass("m_c")
    $(this).remove()

    $(".dv1 .grade .get").show()
    $(".dv1 .grade .get").addClass('p1').removeClass("p2").text("已领取100元")
});
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
$(".task").on('click','.get_reward',function(){
    var that=$(this);
    var type=that.attr('task-type');
    var money=that.attr('task-money');
    var task_id=that.attr('data-id');
    if(type==2)
    {
        //需要弹出支付页面
        var pop = new popLoad("#hot_task_fr .pay-pop","/task/employee_task/pk_pay/money/"+money);
        return;
    }
    else
    {
        //提交领取任务接口
        $.ajax({
            url: '/task/index/take',
            type: 'post',
            data: {'task_id':task_id},
            success: function(data) {
                layer.msg(data.info,{icon:data.status==1?1:2});
                if(data.status)
                {
                    //领取成功
                    that.addClass("get_succeed").removeClass("p2").text("已领取");
                }
            },
            error: function() {
                layer.msg('申请时发生错误!',{icon:2});
            }
        });
    }
});

//点赞
$(".task").on('click','img.add',function(){
    var self = this;
    var qw = $(this).attr('index_img');
    var task_id = $(this).attr('task_id');
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
});

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
        url: '/task/employee_task/task_like',
        type: 'post',
        data: post_data,
        dataType:"json",
        success: function(data) {
            if(data.success == 1) {
                fun(data);
            }else{
                console.log(data.msg,{icon:2});
            }
        },
        error: function() {
            console.log('操作出现错误',{icon:2});
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
    })
    $("article .dv4 ul .largest").text(max_num2);
    $("article .dv4 ul .total").text(s);//总计的钱
})


//评论..评论
$(".dv3 .up .right p").click(function(){
    var that=$(this);
    var content=that.parents('div.like').prev('.content').val();
    var task_id=that.attr('data-id');
    var comment_id=0;
    var truename=that.attr('now-truename');
    $.ajax({
        url:'/task/task_comment/addTaskComment',
        type: 'post',
        data:{'task_id':task_id,'replay_content':content,'comment_id':comment_id },
        success:function(data){
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status){
                //评论成功
                //alert(1)
                var pinglun="";
                // var sk=$(".dv3 .up textarea").val();
                //$(".speek").text(sk);
                //alert(sk)

                pinglun+="<div class='one'><img src='/task/img/man.png'/><div>";
                pinglun+="<p><span class='name'>"+truename+"</span><span>:</span><span class='speek'>";
                pinglun+=content;
                pinglun+="</span></p>";
                pinglun+="<p class='reply'><span>刚刚</span></p></div></div>";
                $(".dv3 .down .review").prepend(pinglun);
            }

        }
    });


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


function skip(target){
    this.target=target;
    var self = this;

    $("#"+self.target+" header .xinjian ").click(function(){
        loadPage('/task/index/new_task/fr/'+self.target,self.target);
    });
    $("#"+self.target+" article").on("click",".dv1 .comment .task_details",function(){
        var id = $(this).attr("task_id");
        console.log(id);
        loadPage('/task/index/show/id/'+id+'/fr/'+self.target,self.target);
    });
}



function task_details(load_table,id,type){
    this.load_table = load_table;
    this.id = id;
    this.type = type;
    var self = this;
    var task_details_sel = "#"+self.load_table+" .task_details";

    this.update_ranking=function(){
        $.ajax({
            url: '/task/index/get_ranking_page/id/'+self.id,
            type: 'get',
            success: function(data) {
                //console.log(data);
                //console.log($(task_details_sel+" .left .box"));
                $(task_details_sel+" .left .box").html(data);
            },
            error: function() {
                layer.msg('加载排行榜出现错误',{icon:2});
            }
        });
    };
    this.update_tip=function(){
        $.ajax({
            url: '/task/task_tip/show/id/'+self.id,
            type: 'get',
            success: function(data) {
                //console.log(data);
                //console.log($(task_details_sel+" .right .particulars"));
                $(task_details_sel+" .right .particulars").html(data);
            },
            error: function() {
                layer.msg('加载打赏出现错误',{icon:2});
            }
        });
    };
    this.update_commont=function(){
        $.ajax({
            url: '/task/task_comment/show/id/'+self.id,
            type: 'get',
            success: function(data) {
                //console.log(data);
                //console.log($(task_details_sel+" .down .review"));
                $(task_details_sel+" .down .review").html(data);
            },
            error: function() {
                layer.msg('加载评论出现错误',{icon:2});
            }
        });
    };
    if(self.type!=3){
        self.update_ranking();
    }
    self.update_tip();
    self.update_commont();

    $(task_details_sel+" .right .give .guess").click(function() {
        console.log("guess");
        $(task_details_sel+" .guess_ui").reveal("{data-animation:'fade'}");
    });
    $(task_details_sel+" .right .rate").click(function() {
        console.log("rate");
        $.ajax({
            url: '/task/task_tip/show/id/'+self.id,
            type: 'get',
            success: function(data) {
                //console.log(data);
                //console.log($(task_details_sel+" .tip_ui .mid"));
                $(task_details_sel+" .tip_ui .mid").html(data);
                $(task_details_sel+" .tip_ui").reveal("{data-animation:'fade'}");
            },
            error: function() {
                layer.msg('加载打赏出现错误',{icon:2});
            }
        });
    });
    $(task_details_sel+" .tip_ui .decide .tip_go").click(function() {
        console.log("tip_go");
        var tip_money = $(task_details_sel+" .tip_ui .tip_money").val();
        console.log("tip_money",tip_money);
        $.ajax({
            url: '/task/index/pay_details/money/'+tip_money,
            type: 'get',
            success: function(data) {
                //console.log(data);
                //console.log($(task_details_sel+" .tip_ui .mid"));
                $(task_details_sel+" .pay_ui").html(data);
                $(task_details_sel+" .pay_ui").reveal("{data-animation:'fade'}");
                $(task_details_sel+" .tip_ui").trigger('reveal:close');
                $(task_details_sel+" .pay_ui .pop-submit-btn").click(function() {
                    console.log("tip_submit");
                    var paypassword = $(task_details_sel+" .pay_ui .pay_password").val();
                    $.ajax({
                        url: '/task/task_tip/tip/task_id/'+self.id+'/money/'+tip_money+'/paypassword/'+paypassword,
                        type: 'get',
                        success: function(data) {
                            //console.log(data);
                            layer.msg(data.info,{icon:data.status==1?1:2});
                            if(data.status==1){
                                $(task_details_sel+" .pay_ui").trigger('reveal:close');
                                self.update_tip();
                            }
                        },
                        error: function() {
                            layer.msg('打赏支付出现错误',{icon:2});
                        }
                    });
                });
                $(task_details_sel+" .pay_ui .pop-close-btn").click(function() {
                    console.log("tip_cancel");
                    $(task_details_sel+" .pay_ui").trigger('reveal:close');
                });
            },
            error: function() {
                layer.msg('加载打赏支付出现错误',{icon:2});
            }
        });
    });
    $(task_details_sel+" .tip_ui .decide .tip_cancel").click(function() {
        console.log("tip_cancel");
        $(task_details_sel+" .tip_ui").trigger('reveal:close');
    });
}