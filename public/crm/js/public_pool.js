$("#high-seafr .public_pool .u-tabList .u-tabOperation .take_public_customer").click(function(){
    var id = $(this).parent().siblings().children(":checkbox").val();
    //console.log("id",id);
    $.ajax({
        url: '/crm/customer/take_public_customers_to_self',
        type: 'post',
        data: "ids="+id,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('变更为我的客户时发生错误!',{icon:2});
        }
    });
});

var public_pool_base = "#frames #high-seafr .crm_public_pool";
var public_pool_nav_base = public_pool_base+" .public_pool .m-secNav";
$(public_pool_base+" .customer_import_record .m-firNav .current").click(function(){
    $(public_pool_base+" .customer_import_record").addClass("hide");
});
$(public_pool_nav_base+" .customer_import").click(function(){
    public_pool_customer_import.load_list();
});
public_pool_list_manage.listenSelect("delete");
$(public_pool_nav_base+" .delete").click(function(){
    var ids = public_pool_list_manage.getAllSelectVal();
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
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('删除客户时发生错误!',{icon:2});
        }
    });
});
public_pool_list_manage.listenSelect("change_customers_visible_range");
$(public_pool_nav_base+" .change_customers_visible_range").click(function(){
    var ids = public_pool_list_manage.getAllSelectVal();
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
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('更改可见范围时发生错误!',{icon:2});
        }
    });
});

public_pool_list_manage.listenSelect("take_public_customers");
$(public_pool_nav_base+" .take_public_customers").click(function(){
    var ids = public_pool_list_manage.getAllSelectVal();
    console.log(ids);
    if(ids==""){
        return;
    }
    $.ajax({
        url: '/crm/customer/take_public_customers_to_self',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('批量变更时发生错误!',{icon:2});
        }
    });
});
