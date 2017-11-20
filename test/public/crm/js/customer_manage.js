var customer_manage_list_base = "#cilents-managefr .customer_manage";
var customer_manage_nav_base = customer_manage_list_base+" .m-secNav";
customer_manage_list_manage.listenSelect("exportCustomer");
$(customer_manage_nav_base+" .exportCustomer").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal(" ",",");
    if(ids==""){
        return;
    }
    console.log(ids);
    window.open("/crm/customer_import/exportCustomer/ids/"+ids);
});
customer_manage_list_manage.listenSelect("delete");
$(customer_manage_nav_base+" .delete").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal();
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
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('删除客户时发生错误!',{icon:2});
        }
    });
});
customer_manage_list_manage.listenSelect("change_customers_to_employee");
$(customer_manage_nav_base+" .change_customers_to_employee").click(function(){
    if($(this).hasClass("active")){
        var pop = new popLoad(customer_manage_list_base+" .crm-customer-manage-pop","/crm/customer/change_customers_to_employee_page/");
    }  
});
change_customers_to_employee=function(uid){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/change_customers_to_employee',
        type: 'post',
        data: {ids:ids,uid:uid},
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('重分客户时发生错误!',{icon:2});
        }
    });
};
customer_manage_list_manage.listenSelect("change_customers_visible_range");
$(customer_manage_nav_base+" .change_customers_visible_range").click(function(){
    if($(this).hasClass("active")){
        var pop = new popLoad(customer_manage_list_base+" .crm-customer-manage-pop","/crm/customer/change_customers_visible_range_page/");
    }    
});
change_customers_visible_range=function(is_public,employees,departments){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/change_customers_visible_range',
        type: 'post',
        data: {ids:ids,is_public:is_public,employees:employees,departments:departments},
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('更改可见范围时发生错误!',{icon:2});
        }
    });
};
customer_manage_list_manage.listenSelect("imposed_release_customers");
$(customer_manage_nav_base+" .imposed_release_customers").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    if(confirm("你确定要释放选中的客户吗?")!=true){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/imposed_release_customers',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('强制释放客户时发生错误!',{icon:2});
        }
    });
});