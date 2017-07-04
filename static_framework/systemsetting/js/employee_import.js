// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
var employee_import_base = "#frames #staff-managementfr .sys_employee_list .employee_import_record";
$(employee_import_base+" .u-tabList .u-tabOperation .fail_download").click(function(){
    var record_id = $(this).siblings(":hidden").val();
    if(record_id==""){
        return;
    }
    console.log(record_id);
    window.open("/systemsetting/employee_import/exportFailEmployee/record_id/"+record_id);
});