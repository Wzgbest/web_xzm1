// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$("#frames #my-contractfr .my_contract .u-tabList .u-tabOperation .retract").click(function(){
	var id = $(this).parent().siblings("input").val();
	$.ajax({
		url: '/crm/contract/retract',
		type: 'post',
		data: "id="+id,
		success: function(data) {
			//console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
			if(data.status) {
				my_customer_list_manage.reload_list();
			}
		},
		error: function() {
            layer.msg('撤回合同申请时发生错误!',{icon:2});
		}
	});
});

$(".crm_contract .myAllContractPage .m-tableBox .customer_name").click(function(){
    var id = $(this).attr("customer_id");
    //console.log("id",id);
    my_contract_info_manage.general(id);
});