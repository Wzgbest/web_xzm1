// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
var role_list_panel_base = "#frames #role-managementfr .systemsetting_role .content";
var panel = role_list_panel_base+" .dv2";
$(window).resize(function() {
    $("#frames .once").width(window.innerWidth-220);
    $("#frames .once").height(window.innerHeight-80);
});
function findRoleId(target){
    return $(target).parent().attr("role_id");
}
function listenRuleManage(role_id){
    $(panel+" .one .role_manage").click(function(){
        loadRuleManage(role_id);
    });
}
function listenEmployeeTable(role_id){
    $(panel+" .one .employee_manage").click(function(){
        loadEmployeeTable(role_id);
    });
}
function loadRuleManage(role_id){
    var url = "/systemsetting/role/rule_manage/id/"+role_id;
    $.ajax({
        url:url,
        type:'get',
        async:false,
        success:function (data) {
            $(panel).html(data);
            listenEmployeeTable(role_id);
        },
        error:function(){
            alert("获取权限失败!");
        }
    });
}
function loadEmployeeTable(role_id){
    var url = "/systemsetting/role/employee_list/id/"+role_id;
    $.ajax({
        url:url,
        type:'get',
        async:false,
        success:function (data) {
            $(panel).html(data);
            listenRuleManage(role_id);
        },
        error:function(){
            alert("获取成员失败!");
        }
    });
}
$(role_list_panel_base+" .dv1 .compile").click(function(){
    var role_id = findRoleId(this);
    //console.log(role_id);
    loadRuleManage(role_id);
});
$(".systemsetting_role .content .dv1 .del").click(function(){
    var role_id = findRoleId(this);
    //console.log(role_id);
    $.ajax({
        url: '/systemsetting/role/deleteRole',
        type: 'post',
        data: "role_id="+role_id,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            alert(data.message);
            if(data.status) {
                loadPage("/systemsetting/role/index","role-managementfr");
            }
        },
        error: function() {
            alert("删除职位时发生错误!");
        }
    });
});
function role_list_employee_show(id){
    var url = "/systemsetting/role/employee_show/id/"+id;
    var panel = 'role-managementfr .systemsetting_role .employee_info';
    $.ajax({
        url:url,
        type:'get',
        async:false,
        success:function (data) {
            $('#frames #'+employee_list_hide_panel).addClass("hide");
            $('#frames #'+panel).html(data);
            $('#frames #'+panel).removeClass("hide");
            $('#frames #'+panel+" .firNav .current").click(function(){
                $('#frames #'+panel).addClass("hide");
            });
        },
        error:function(){
            alert("获取员工信息失败!");
        }
    });
}
function role_list_employee_del(role_id,user_id){
    var url = "/systemsetting/role/deleteRoleMember/";
    $.ajax({
        url:url,
        type:'post',
        async:false,
        dataType:"json",
        data:"role_id="+role_id+"&user_id="+user_id,
        success:function (data) {
            alert(data.message);
            if(data.status){
                loadEmployeeTable(role_id);
            }
        },
        error:function(){
            alert("移除员工职位失败!");
        }
    });
}