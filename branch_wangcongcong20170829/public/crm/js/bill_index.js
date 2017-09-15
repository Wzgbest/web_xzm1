// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$("#frames #my-billfr .my_bill .u-tabList .u-tabOperation .retract").click(function(){
    var id = $(this).parent().siblings().children("input").val();
    $.ajax({
        url: '/crm/bill/retract',
        type: 'post',
        data: "id="+id,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:1});
            if(data.status) {
                my_customer_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('撤回发票申请时发生错误!',{icon:2});
        }
    });
});