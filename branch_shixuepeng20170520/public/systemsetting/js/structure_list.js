// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

var struct_list_panel_base = "#frames #division-managementfr .structure_list .content";
var struct_employee_list_panel = struct_list_panel_base+" .dv2";
var struct_add_employee_panel = struct_list_panel_base+" .addEmployeeModal";
var struct_del_panel = struct_list_panel_base+" .structure_del";
var struct_file_panel = struct_list_panel_base+" .structure_file";
var struct_item_list_panel = struct_list_panel_base+' .fold .structure_tree';
function findActivityStructId(){
    return structure_tree.getActivityId();
}
function findStructId(target){
    return $(target).parent().parent().attr("node_id");
}
function loadStructEmployeeTable(id){
    var url = "/systemsetting/structure/employee_list/id/"+id;
    $.ajax({
        url:url,
        type:'get',
        async:false,
        success:function (data) {
            $(struct_employee_list_panel).html(data);
            $(struct_employee_list_panel).removeClass("hide");
            $(".structure_list .top .add").removeClass("hide");
        },
        error:function(){
            layer.msg('获取成员失败!',{icon:2});
        }
    });
}
$(".structure_list .top .add").click(function(){
    var struct_id = findActivityStructId();
    console.log(struct_id);
    if(struct_id>0){
        var url = "/systemsetting/structure/not_struct_employee_list/id/"+struct_id;
        $.ajax({
            url:url,
            type:'get',
            async:false,
            success:function (data) {
                $(struct_add_employee_panel+' .add_employee_list').html(data);
                $(struct_add_employee_panel).reveal("{data-animation:'fade'}");
            },
            error:function(){
                layer.msg('获取员工信息失败!',{icon:2});
            }
        });
    }
});
function update_add_employee_num(num){
    $(struct_add_employee_panel+" .select_num").html(num);
}
var struct_add_employee_checked = struct_add_employee_panel+" .add_employee_list .u-tabList .u-tabCheckbox :checked";
$(struct_add_employee_panel+" .add_employee_list").on('click','.u-tabCheckbox input[type="checkbox"]',function(){
    var struct_add_employee_checked_num = $(struct_add_employee_checked).length;
    update_add_employee_num(struct_add_employee_checked_num);
});
$(struct_add_employee_panel+" .add_employee_ok").click(function(){
    var struct_id = findActivityStructId();
    console.log(struct_id);
    if(struct_id>0) {
        var employee_ids = '';
        var employee_ids_arr = new Array();
        $(struct_add_employee_checked).each(function(){
            employee_ids_arr.push($(this).val());
        });
        employee_ids += employee_ids_arr.join(",");
        $.ajax({
            url: '/systemsetting/structure/addEmployeeStructure',
            type: 'post',
            data: "struct_id="+struct_id+"&user_ids="+employee_ids,
            dataType:"json",
            success: function(data) {
                //console.log(data);
                layer.msg(data.message,{icon:data.status==1?1:2});
                if(data.status) {
                    $(struct_add_employee_panel).trigger('reveal:close');
                    loadStructEmployeeTable(struct_id);
                }
            },
            error: function() {
                layer.msg('添加部门员工时发生错误!',{icon:2});
            }
        });
    }
});
$(struct_add_employee_panel+" .add_employee_cancel").click(function(){
    $(struct_add_employee_panel).trigger('reveal:close');
});

function getStructureListTreeHeight(){
    var tree_height = $(struct_list_panel_base+" .five_tree").height();
    if(tree_height<window.innerHeight){
        tree_height = window.innerHeight;
    }
    return tree_height;
}
structure_tree.listen("plusFun",function(id){
    console.log("hlplusFun",id);
    var tree_height = getStructureListTreeHeight();
    $(struct_list_panel_base+" .fold").height(tree_height+51);
});
structure_tree.listen("subFun",function(id){
    //console.log("hlsubFun",id);
    var tree_height = getStructureListTreeHeight();
    $(struct_list_panel_base+" .fold").height(tree_height+51);
});
structure_tree.listen("selFun",function(id){
    console.log("hlselFun",id);
    loadStructEmployeeTable(id)
});
var struct_add_item_pid = 0;
function show_node_add(id){
    var node_sub_panel = struct_item_list_panel+" .node"+id+" .child_list:first";
    var item_html = '';
    item_html+='<div class="node node_add">' +
        '<div class="node_item add_item temp_item">' +
        '<input type="text" class="item_text add_item_text" value=""/>' +
        '<i class="fa fa-check item_btn item_check add_item_check"></i>' +
        '<i class="fa fa-remove item_btn item_remove add_item_remove"></i>' +
        '</div></div>';
    $(node_sub_panel).append(item_html);
}

structure_tree.listen("addFun",function(id){
	console.log("addFun_id",id);
	//class="big-link" data-reveal-id="structure_file" data-animation="fade";
    var struct_file_panel_temp = struct_file_panel;
    if(id){
        $.ajax({
            url: '/systemsetting/structure/getStructureEmployeenum',
            type: 'post',
            data: "struct_id="+id,
            dataType:"json",
            success: function(data) {
                //console.log(data);
                if(data.status) {
                    structure_tree_del_struct_id = id;
                    $(struct_file_panel_temp).reveal("{data-animation:'fade'}");
                }else{
                    layer.msg(data.message,{icon:2});
                }
            },
            error: function() {
                layer.msg('添加部门时发生错误!',{icon:2});
            }
        });
    }
	
	
//  console.log("hladdFun",id);
//  var add_item = $(struct_item_list_panel+" .add_item");
//  if(add_item.length>0){
//      console.log(add_item.find(".add_item_text"));
//      add_item.find(".add_item_text").focus();
//      return;
//  }
//  $(struct_item_list_panel+" .node"+id+" .node_item:first .node_plus").addClass("node_sub");
//  $(struct_item_list_panel+" .node"+id+" .child_list"+id+"").removeClass('hide');
//  struct_add_item_pid = id;
//  show_node_add(id);
});


$(".structure_list .content .fold .title .fa-plus").click(function(){
    console.log("add_struct");
    var add_item = $(struct_item_list_panel+" .add_item");
    if(add_item.length>0){
        //console.log(add_item.children(".add_item_text"));
        add_item.children(".add_item_text").focus();
        return;
    }
    $(struct_item_list_panel+" .node1 .node_item:first .node_plus").addClass("node_sub");
    $(struct_item_list_panel+" .node1 .child_list1").removeClass('hide');
    struct_add_item_pid = 1;
    show_node_add(1);
});
$(struct_item_list_panel).on('click',".add_item .add_item_check",function(){
    var add_struct_name = $(struct_item_list_panel+" .add_item").children(".add_item_text").val();
    console.log(add_struct_name);
    if(!add_struct_name){
        return;
    }
    $.ajax({
        url: '/systemsetting/structure/add',
        type: 'post',
        data: "name="+add_struct_name+"&pid="+struct_add_item_pid,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status) {
                loadPage("/systemsetting/structure/index","division-managementfr");
            }
        },
        error: function() {
            layer.msg('添加部门时发生错误!',{icon:2});
        }
    });
});
$(struct_item_list_panel).on('click',".add_item .add_item_remove",function(){
    $(struct_item_list_panel+" .node_add").remove();
});

structure_tree.listen("editFun",function(id){
	
	console.log("editFun_id",id);
	
	var struct_file_panel_temp = struct_file_panel;
    if(id){
        $.ajax({
            url: '/systemsetting/structure/getStructureEmployeenum',
            type: 'post',
            data: "struct_id="+id,
            dataType:"json",
            success: function(data) {
                //console.log(data);
                if(data.status) {
                    structure_tree_del_struct_id = id;
                    $(struct_file_panel_temp).reveal("{data-animation:'fade'}");
                }else{
                    layer.msg(data.message,{icon:2});
                }
            },
            error: function() {
                layer.msg('编辑部门时发生错误!',{icon:2});
            }
        });
    }
	
	
//  console.log("hleditFun",id);
//  var node_item_panel = struct_item_list_panel+" .node"+id+" .node_item"+id;
//  var edit_item_text = $(node_item_panel+" .node_name").text();
//  $(node_item_panel+" .node_head").addClass("hide");
//  $(node_item_panel+" .node_name").addClass("hide");
//  $(node_item_panel+" .node_tool").addClass("hide");
//  var edit_html = '<input type="text" class="item_text edit_item_text" value="'+edit_item_text+'"/>' +
//      '<i class="fa fa-check item_btn item_check edit_item_check"></i>' +
//      '<i class="fa fa-remove item_btn item_remove edit_item_remove"></i>';
//  $(node_item_panel).append(edit_html);
});
$(struct_item_list_panel).on('click',".node_item .edit_item_check",function(){
    var edit_struct_name = $(this).siblings(".edit_item_text").val();
    console.log(edit_struct_name);
    if(!edit_struct_name){
        return;
    }
    var struct_id = findStructId(this);
    console.log(struct_id);
    $.ajax({
        url: '/systemsetting/structure/renameStructure',
        type: 'post',
        data: "struct_id="+struct_id+"&new_name="+edit_struct_name,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status) {
                loadPage("/systemsetting/structure/index","division-managementfr");
            }
        },
        error: function() {
            layer.msg('编辑部门名时发生错误!',{icon:2});
        }
    });
});
$(struct_item_list_panel).on('click',".node_item .edit_item_remove",function(){
    $(this).siblings(".node_head").removeClass("hide");
    $(this).siblings(".node_name").removeClass("hide");
    $(this).siblings(".node_tool").removeClass("hide");
    $(this).siblings(".edit_item_text").remove();
    $(this).siblings(".edit_item_check").remove();
    $(this).remove();
});

var structure_tree_del_struct_id = 0;
structure_tree.listen("delFun",function(id){
    console.log("hldelFun",id);
    console.log(id);
    // class="big-link" data-reveal-id="structure_del" data-animation="fade"
    var struct_del_panel_temp = struct_del_panel;
    if(id){
        $.ajax({
            url: '/systemsetting/structure/getStructureEmployeenum',
            type: 'post',
            data: "struct_id="+id,
            dataType:"json",
            success: function(data) {
                //console.log(data);
                if(data.status) {
                    if(data.data>0){
                        struct_del_panel_temp += "_move";
                    }
                    structure_tree_del_struct_id = id;
                    $(struct_del_panel_temp).reveal("{data-animation:'fade'}");
                }else{
                    layer.msg(data.message,{icon:data.status==1?1:2});
                }
            },
            error: function() {
                layer.msg('删除部门时发生错误!',{icon:2});
            }
        });
    }
});
$(struct_del_panel+" .structure_del_ok").click(function(){
    deleteStructure(structure_tree_del_struct_id,1);
});
$(struct_del_panel+" .structure_del_cancel").click(function(){
    $(struct_del_panel).trigger('reveal:close');
});
$(struct_del_panel+"_move .structure_del_ok").click(function(){
    deleteStructure(structure_tree_del_struct_id,1);
});
$(struct_del_panel+"_move .structure_del_cancel").click(function(){
    deleteStructure(structure_tree_del_struct_id,0);
});
function deleteStructure(struct_id,trans){
    //console.log(struct_id);
    $.ajax({
        url: '/systemsetting/structure/deleteStructure',
        type: 'post',
        data: "struct_id="+struct_id+"&trans="+trans,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status) {
                loadPage("/systemsetting/structure/index","division-managementfr");
            }
        },
        error: function() {
            layer.msg('删除部门时发生错误!',{icon:2});
        }
    });
}

function struct_list_employee_del(struct_id,employee_id){
    var url = "/systemsetting/structure/delEmployeeStructure/";
    $.ajax({
        url:url,
        type:'post',
        async:false,
        dataType:"json",
        data:"group="+struct_id+"&user_id="+employee_id,
        success:function (data) {
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status){
                loadStructEmployeeTable(struct_id);
            }
        },
        error:function(){
            layer.msg('移除员工部门失败!',{icon:2});
        }
    });
}
var struct_list_employee_move_struct_id = 0;
var struct_list_employee_move_employee_id = 0;
function struct_list_employee_move(struct_id,employee_id){
    struct_list_employee_move_struct_id = struct_id;
    struct_list_employee_move_employee_id = employee_id;
    console.log(struct_list_employee_move_struct_id);
    console.log(struct_list_employee_move_employee_id);
    // class="big-link" data-reveal-id="structure_move" data-animation="fade"
    $(struct_list_panel_base+" .structure_move").reveal("{data-animation:'fade'}");
}
$(struct_list_panel_base+" .structure_move .structure_move_ok").click(function(){
    var to_struct_id = $(struct_list_panel_base+" .structure_move .to_struct_id").val();
    console.log(to_struct_id);
    struct_list_employee_move_to(
        struct_list_employee_move_struct_id,
        struct_list_employee_move_employee_id,
        to_struct_id
    );
});
$(struct_list_panel_base+" .structure_move .structure_move_structure_move_cancel").click(function(){
    $(struct_list_panel_base+" .structure_move").trigger('reveal:close');
});
function struct_list_employee_move_to(struct_id,employee_id,to_struct_id){
    var url = "/systemsetting/structure/changeEmployeeStructure/";
    $.ajax({
        url:url,
        type:'post',
        async:false,
        dataType:"json",
        data:"group="+struct_id+"&user_id="+employee_id+"&to_group="+to_struct_id,
        success:function (data) {
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status){
                loadStructEmployeeTable(struct_id);
                $(struct_list_panel_base+" .structure_move").trigger('reveal:close');
            }
        },
        error:function(){
            layer.msg('转移员工部门失败!',{icon:2});
        }
    });
}

