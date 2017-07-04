var customer_manage_nav_base = "#cilents-managefr .customer_manage .m-secNav";
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
            alert(data.info);
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            alert("删除客户时发生错误!");
        }
    });
});
customer_manage_list_manage.listenSelect("change_customers_to_employee");
$(customer_manage_nav_base+" .change_customers_to_employee").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/change_customers_to_employee',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            alert("重分客户时发生错误!");
        }
    });
});
customer_manage_list_manage.listenSelect("change_customers_visible_range");
$(customer_manage_nav_base+" .change_customers_visible_range").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/change_customers_visible_range',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            alert("更改可见范围时发生错误!");
        }
    });
});
customer_manage_list_manage.listenSelect("imposed_release_customers");
$(customer_manage_nav_base+" .imposed_release_customers").click(function(){
    var ids = customer_manage_list_manage.getAllSelectVal();
    if(ids==""){
        return;
    }
    //console.log(ids);
    $.ajax({
        url: '/crm/customer/imposed_release_customers',
        type: 'post',
        data: ids,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                customer_manage_list_manage.reload_list();
            }
        },
        error: function() {
            alert("强制释放客户时发生错误!");
        }
    });
});