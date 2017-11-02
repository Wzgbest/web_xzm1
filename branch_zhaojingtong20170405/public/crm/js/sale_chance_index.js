// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$(".crm_sale_chance .myAllSaleChancePage .m-tableBox .customer_name").click(function(){
    var id = $(this).attr("customer_id");
    //console.log("id",id);
    my_sale_chance_info_manage.general(id);
});
$(".crm_sale_chance .myAllSaleChancePage .m-tableBox .sale_chance_show").click(function(){
    var customer_id = $(this).attr("customer_id");
    var sale_chance_id = $(this).attr("sale_chance_id");
    //console.log("id",id);
    my_sale_chance_info_manage.sale_chance_show(customer_id,sale_chance_id);
});