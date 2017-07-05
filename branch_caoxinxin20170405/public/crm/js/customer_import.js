// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
var customer_import_base = "#frames #myclietsfr .crm_my_customer .customer_import_record";
$(customer_import_base+" .u-tabList .u-tabOperation .fail_download").click(function(){
    var record_id = $(this).siblings(":hidden").val();
    if(record_id==""){
        return;
    }
    console.log(record_id);
    window.open("/crm/customer_import/exportFailCustomer/record_id/"+record_id);
});