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
			alert(data.info);
			if(data.status) {
				my_customer_list_manage.reload_list();
			}
		},
		error: function() {
			alert("撤回合同申请时发生错误!");
		}
	});
});