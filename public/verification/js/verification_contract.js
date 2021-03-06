// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
$(".verification_contract_index_pop").on("change",".approved-page-radio",function(){
    $(this).attr("checked","checked").prop("checked",true);
    $(this).siblings().removeAttr("checked").prop("checked",false);
    if($(".approved-page-radio:checked").val()==1){
        $(".contract-number-list").addClass("hide");
        $(".contract-new-number-list").removeClass("hide");
    }
    if($(".approved-page-radio:checked").val()==2){
        $(".contract-new-number-list").addClass("hide");
        $(".contract-number-list").removeClass("hide");
    }
}); 
//通过
$(".verification_contract_index_pop").on("click",".approved-page-pop .pop-submit-btn",function(){
    let id = $(".verification_contract_index_pop").attr("id").trim();
    let remark = $(".verification_contract_index_pop .approved-page-pop .u-mark").val();
    let use_withdrawal = $(".verification_contract_index_pop .approved-page-pop .approved-page-radio:checked").val();
    let contract_id = $(".verification_contract_index_pop .approved-page-pop .contract-number-list").val();
    let data = "id="+id+"&remark="+remark+"&use_withdrawal="+use_withdrawal+"&contract_id="+contract_id;
    $.ajax({
        url: '/verification/contract/approved',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_contract_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('通过合同申请时发生错误!',{icon:2});
        }
    });
}); 


//驳回
$(".verification_contract_index_pop").on("click",".rejected-page-pop .pop-submit-btn",function(){
    let id = $(".verification_contract_index_pop").attr("id");
    let remark = $(".contract-reject-radio:checked").val();
    if(remark=="其他原因"){
        remark=$(".verification_contract_index_pop .rejected-page-pop .u-mark").val();
    }
    if(remark==""){
        layer.msg('请输入原因',{icon:2});
        return 0;
    }
    let data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/contract/rejected',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_contract_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('驳回合同申请时发生错误!',{icon:2});
        }
    });
});

$("#frames #verification-contractfr .verification_contract .u-tabList .u-tabOperation .invalid").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/contract/invalid',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_contract_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('作废时发生错误!',{icon:2});
        }
    });
});

$("#frames #verification-contractfr .verification_contract .u-tabList .u-tabOperation .received").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/contract/received',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_contract_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('已领取时发生错误!',{icon:2});
        }
    });
});

$("#frames #verification-contractfr .verification_contract .u-tabList .u-tabOperation .withdrawal").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    var remark = "";
    if($(this).hasClass("remark")){
        remark = prompt("请输入备注","");
    }
    var data = "id="+id+"&remark="+remark;
    $.ajax({
        url: '/verification/contract/withdrawal',
        type: 'post',
        data: data,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                verification_contract_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('收回时发生错误!',{icon:2});
        }
    });
});
