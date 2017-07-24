// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
function customer_import(panel,type){
    this.panel = panel;
    this.type = type;
    var self=this;
    this.load_list=function(){
        var url = "/crm/customer_import/index/type/"+this.type;
        var load_panel = this.panel+" .customer_import_record_list";
        $.ajax({
            url:url,
            type:'get',
            async:false,
            success:function (data) {
                $(load_panel).html(data);
                $(load_panel).height(window.innerHeight);
                $(self.panel).removeClass("hide");
                $(self.panel+" .u-tabList .u-tabOperation .fail_download").click(function(){
                    var record_id = $(this).siblings(":hidden").val();
                    if(record_id==""){
                        return;
                    }
                    //console.log(record_id);
                    window.open("/crm/customer_import/exportFailCustomer/type/"+self.type+"/record_id/"+record_id);
                });
            },
            error:function(){
                alert("获取客户导入信息失败!");
            }
        });
    };
}