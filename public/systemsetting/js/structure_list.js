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
var struct_edit_panel = struct_list_panel_base+" .structure_edit";
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
//$(".structure_list .top .add").click(function(){
//  var struct_id = findActivityStructId();
//  console.log(struct_id);
//  if(struct_id>0){
//      var url = "/systemsetting/structure/not_struct_employee_list/id/"+struct_id;
//      $.ajax({
//          url:url,
//          type:'get',
//          async:false,
//          success:function (data) {
//              update_add_employee_num(0);
//              $(struct_add_employee_panel+' .add_employee_list').html(data);
//              $(struct_add_employee_panel).reveal("{data-animation:'fade'}");
//          },
//          error:function(){
//              layer.msg('获取员工信息失败!',{icon:2});
//          }
//      });
//  }
//});
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
        var employee_ids_num = 0;
        $(struct_add_employee_checked).each(function(){
            employee_ids_arr.push($(this).val());
            employee_ids_num++;
        });
        if(employee_ids_num<=0){
            layer.msg('请选择员工!',{icon:2});
            return;
        }
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

$(struct_file_panel+" .structure_del_cancel").click(function(){
	
//	$(struct_file_panel).children('.mange').children('input').val()=" ";
//	$(struct_file_panel).children('.herd').children('input').prop("checked",false);
    $(struct_file_panel).trigger('reveal:close');
});
$(struct_edit_panel+" .structure_del_cancel").click(function(){
//	$(this).parent(".p5").siblings('.herd').children("input").removeProp("checked");
	
    $(struct_edit_panel).trigger('reveal:close');
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
//  console.log("hlplusFun",id);
    var tree_height = getStructureListTreeHeight();
//  $(struct_list_panel_base+" .fold").height(tree_height+51);
});
structure_tree.listen("subFun",function(id){
    //console.log("hlsubFun",id);
    var tree_height = getStructureListTreeHeight();
//  $(struct_list_panel_base+" .fold").height(tree_height+51);
});
structure_tree.listen("selFun",function(id){
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

structure_tree.listen("addFun",function(id,name){
	console.log("addFun_id",id);
    var struct_file_panel_temp = struct_file_panel;
    if(id){
        console.log("addFun_struct_name",name);
        if(name){
            $(struct_file_panel+' .pd_panel').removeClass("hide");
            $(struct_file_panel+' .pd_name').text(name);
        }else{
            $(struct_file_panel+' .pd_panel').addClass("hide");
        }
        $(struct_file_panel+' .mange input').val('');
        $(struct_file_panel_temp).reveal("{data-animation:'fade'}");
              
    }
	
	
 console.log("hladdFun",id);
 // var add_item = $(struct_item_list_panel+" .add_item");
 // if(add_item.length>0){
 //     console.log(add_item.find(".add_item_text"));
 //     add_item.find(".add_item_text").focus();
 //     return;
 // }
 // $(struct_item_list_panel+" .node"+id+" .node_item:first .node_plus").addClass("node_sub");
 // $(struct_item_list_panel+" .node"+id+" .child_list"+id+"").removeClass('hide');
 struct_add_item_pid = id;
 // show_node_add(id);
});


$(".structure_list .content .fold .title .fa-plus").click(function(){
    console.log("add_struct");
    var struct_file_panel_temp = struct_file_panel;
   
    struct_add_item_pid = 1;
    structure_tree_del_struct_id = 1;
    $(struct_file_panel+' .pd_panel').addClass("hide");
    $(struct_file_panel_temp).reveal("{data-animation:'fade'}");
    
});

$(struct_list_panel_base).on("click",".u-btnAdd",function(){
	console.log(struct_list_panel_base);
    var struct_id = findActivityStructId();
    console.log(struct_id);
    if(struct_id>0){
        var url = "/systemsetting/structure/not_struct_employee_list/id/"+struct_id;
        $.ajax({
            url:url,
            type:'get',
            async:false,
            success:function (data) {
                update_add_employee_num(0);
                $(struct_add_employee_panel+' .add_employee_list').html(data);
                $(struct_add_employee_panel).reveal("{data-animation:'fade'}");
            },
            error:function(){
                layer.msg('获取员工信息失败!',{icon:2});
            }
        });
    }
    
});


$(struct_file_panel).on('click',".p5 input",function(){
 
    var add_struct_name = $(struct_file_panel).children('.mange').children('input').val();
    var add_group_value = $(struct_file_panel).children('.herd').children('input').prop("checked");

    if (add_group_value == true) {
        add_group_value = 1;
    }else{
        add_group_value = 0;
    }
    if(!add_struct_name){
        return;
    }
    $.ajax({
        url: '/systemsetting/structure/add',
        type: 'post',
        data: "name="+add_struct_name+"&pid="+struct_add_item_pid+"&is_group="+add_group_value,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            layer.msg(data.info,{icon:data.status==1?1:2});
            if(data.status==1) {
                structure_tree.add(data.data,struct_add_item_pid,add_struct_name);
                $(struct_list_panel_base+" .reveal-modal").trigger('reveal:close');
                //loadPage("/systemsetting/structure/index","division-managementfr");
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
	
	var struct_edit_panel_temp = struct_edit_panel;
    if(id){
        $.ajax({
            url: '/systemsetting/structure/getStructureInfo',
            type: 'post',
            data: "struct_id="+id,
            dataType:"json",
            success: function(data) {
                var info = data.data;
                console.log(info.struct_name);
                if(data.status) {
                    structure_tree_del_struct_id = id;
                    $(struct_edit_panel).children('.mange').children('input').val('');
                    $(struct_edit_panel_temp).reveal("{data-animation:'fade'}");
                    $(struct_edit_panel).children('.mange').children('input').val(info.struct_name);
                    $(struct_edit_panel).attr({
                        'node_id': id,
                    });
                    if (info.groupid != null) {
                        $(struct_edit_panel).children('.herd').children('input').prop({
                            'checked': true,
                        });
                    }else{
                    	$(struct_edit_panel).children('.herd').children('input').prop({
                            'checked': false,
                        });
                    }
                }else{
                    layer.msg(data.message,{icon:2});
                }
            },
            error: function() {
                layer.msg('编辑部门时发生错误!',{icon:2});
            }
        });
    }
	

});
$('.structure_edit').on('click',".p5 input",function(){
    var edit_struct_name = $(this).parent(".p5").siblings(".mange").children('input').val();
    //console.log(edit_struct_name);
    if(!edit_struct_name){
        return;
    }
    var struct_id = findStructId(this);
    var add_group_value = $(this).parent(".p5").siblings(".herd").children('input').prop("checked"); 
    if (add_group_value == true) {
        add_group_value = 1;
    }else{
        add_group_value = 0;
    }
    //console.log(struct_id);
    //console.log(add_group_value);
    $.ajax({
        url: '/systemsetting/structure/renameStructure',
        type: 'post',
        data: "struct_id="+struct_id+"&new_name="+edit_struct_name+"&is_group="+add_group_value,
        dataType:"json",
        success: function(data) {
            //console.log(data);
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status) {
                structure_tree.update(struct_id,edit_struct_name);
                $(struct_list_panel_base+" .reveal-modal").trigger('reveal:close');
                //loadPage("/systemsetting/structure/index","division-managementfr");
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
    var havChild = structure_tree.isHavChild(id);
    if(havChild){
        console.log("havChild",havChild);
        layer.msg('该部门下存在子部门无法删除!',{icon:2});
        return;
    }
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
                        // struct_del_panel_temp += "_move";
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
$(struct_del_panel+"_move .structure_del_move_ok").click(function(){
    deleteStructure(structure_tree_del_struct_id,1);
});
$(struct_del_panel+"_move .structure_del_move_cancel").click(function(){
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
                structure_tree.del(struct_id);
                //loadPage("/systemsetting/structure/index","division-managementfr");
            }
            $(struct_list_panel_base+" .reveal-modal").trigger('reveal:close');
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
    //console.log(struct_list_employee_move_struct_id);
    //console.log(struct_list_employee_move_employee_id);
    // var url = "/systemsetting/structure/move_employee_page/";
    var url = "/systemsetting/structure/employee_list_transfer/";
    $.ajax({
        url:url,
        type:'get',
        success:function (data) {
            $(struct_list_panel_base+" .structure_move").html(data);
            // class="big-link" data-reveal-id="structure_move" data-animation="fade"
            $(struct_list_panel_base+" .structure_move").reveal("{data-animation:'fade'}");
        },
        error:function(){
            layer.msg('加载转移员工部门失败!',{icon:2});
        }
    });
}
$(struct_list_panel_base+" .structure_move").on("click",".structure_move_ok",function(){
    var to_struct_id = $(struct_list_panel_base+" .structure_move .to_struct_id").val();
    console.log(to_struct_id);
    struct_list_employee_move_to(
        struct_list_employee_move_struct_id,
        struct_list_employee_move_employee_id,
        to_struct_id
    );
});
$(struct_list_panel_base+" .structure_move").on("click",".structure_move_cancel",function(){
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
//批量转移
$(struct_list_panel_base).on("click",".employee_list_transfer_btn",function(){
    if(!$(this).hasClass("active")){
        return ;
    }
    var url = "/systemsetting/structure/employee_list_transfer/";
    $.ajax({
        url:url,
        type:'get',
        success:function (data) {
            // $(struct_list_panel_base+" .employee_list_transfer").html(data);
            // $(struct_list_panel_base+" .employee_list_transfer").reveal("{data-animation:'fade'}");

            $(struct_list_panel_base+" .structure_move").html(data);
            $(struct_list_panel_base+" .structure_move").reveal("{data-animation:'fade'}");

        },
        error:function(){
            layer.msg('加载批量转移员工部门失败!',{icon:2});
        }
    });
});

structure_tree.listen("resetFun",function(){
    $(struct_employee_list_panel).html('');
    $(struct_employee_list_panel).addClass("hide");
    $(".structure_list .top .add").addClass("hide");
});

var first_reload = 1;
structure_tree.listen("reloadFun",function(){
    //console.log("reloadFun");
    //console.log($(".structure_list .structure_tree .node_name"));
    $(".structure_list .structure_tree .node_name").mouseenter(function(){
        $(this).parent(".node_item").append("<div class='floating_window'></div>");
        $('.floating_window').html($(this).text());
    });
    $(".structure_list .structure_tree .node_name").mouseleave(function(){
        $(".node_item .floating_window").hide();
    });

    if(first_reload==1){
        first_reload = 0;
        $(".structure_list .structure_tree .node_name:eq(0)").click();
    }
});

structure_tree.reload();