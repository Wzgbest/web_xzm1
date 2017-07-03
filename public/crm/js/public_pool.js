$("#high-seafr .anonymous_pool .u-tabList .u-tabOperation .take_public_customer").click(function(){
    var id = $(this).parent().siblings().children(":checkbox").val();
    $.ajax({
        url: '/crm/customer/take_public_customers_to_self',
        type: 'post',
        data: "ids="+id,
        success: function(data) {
            //console.log(data);
            alert(data.info);
            if(data.status) {
                my_customer_list_manage.reload_list();
            }
        },
        error: function() {
            alert("变更为我的客户时发生错误!");
        }
    });
});

var public_pool_nav_base = "#high-seafr .public_pool .m-secNav";
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
            alert(data.info);
            if(data.status) {
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            alert("删除客户时发生错误!");
        }
    });
});
public_pool_list_manage.listenSelect("change_customers_visible_range");
$(public_pool_nav_base+" .change_customers_visible_range").click(function(){
    var ids = public_pool_list_manage.getAllSelectVal();
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
                public_pool_list_manage.reload_list();
            }
        },
        error: function() {
            alert("更改可见范围时发生错误!");
        }
    });
});