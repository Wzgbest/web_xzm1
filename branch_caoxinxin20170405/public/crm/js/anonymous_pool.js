$("#high-seafr .anonymous_pool .u-tabList .u-tabOperation .take_customer").click(function(){
    var id = $(this).parent().siblings(".u-tabCheckbox").children(":checkbox").val();
    //console.log("id",id);
    $.ajax({
        url: '/crm/customer/take_customers_to_self',
        type: 'post',
        data: "ids="+id,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                anonymous_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('申领客户时发生错误!',{icon:2});
        }
    });
});

var anonymous_pool_base = "#frames #high-seafr .crm_anonymous_pool";
var anonymous_pool_nav_base = anonymous_pool_base+" .anonymous_pool .m-secNav";
$(anonymous_pool_base+" .customer_import_record .m-firNav .current").click(function(){
    $(anonymous_pool_base+" .customer_import_record").addClass("hide");
});
$(anonymous_pool_nav_base+" .customer_import").click(function(){
    anonymous_pool_customer_import.load_list();
});
anonymous_pool_list_manage.listenSelect("delete");
$(anonymous_pool_nav_base+" .delete").click(function(){
    var ids = anonymous_pool_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/del',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                anonymous_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('删除客户时发生错误!',{icon:2});
        }
    });
});
anonymous_pool_list_manage.listenSelect("change_customers_visible_range");
$(anonymous_pool_nav_base+" .change_customers_visible_range").click(function(){
    var ids = anonymous_pool_list_manage.getAllSelectVal();
    console.log(ids);
    if(ids==""){
        return;
    }
    $.ajax({
        url: '/crm/customer/change_customers_visible_range',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                anonymous_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('更改可见范围时发生错误!',{icon:2});
        }
    });
});

anonymous_pool_list_manage.listenSelect("take_customers");
$(anonymous_pool_nav_base+" .take_customers").click(function(){
    var ids = anonymous_pool_list_manage.getAllSelectVal();
    console.log(ids);
    if(ids==""){
        return;
    }
    $.ajax({
        url: '/crm/customer/take_customers_to_self',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                anonymous_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('批量申领时发生错误!',{icon:2});
        }
    });
});
