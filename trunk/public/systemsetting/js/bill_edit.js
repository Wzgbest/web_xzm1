// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
//审核角色
var bill_setting_handle_arr = null;
try{
    var bill_setting_handle_arr = JSON.parse(bill_setting_handles);
}catch (ex){
    console.log(ex);
}
if(bill_setting_handle_arr==null){
    console.log("handles data not found");
    bill_setting_handle_arr = [];
}
//console.log(bill_setting_handle_arr);

var bill_setting_role_arr = null;
try{
    bill_setting_role_arr = JSON.parse(bill_setting_roles);
}catch (ex){
    console.log(ex);
}
if(bill_setting_role_arr==null){
    console.log("items data not found");
    bill_setting_role_arr = [];
}
//console.log(bill_setting_role_arr);

function get_last_bill_setting_index(){
    var handle_arr_length = bill_setting_handle_arr.length;
    var last_index = handle_arr_length;
    return last_index;
}

function get_bill_setting_handle_index(target){
    var index = $(target).parent().parent().attr("handle_num");
    return index;
}

function get_bill_setting_handle_tools_index(target){
    var index = $(target).parent().attr("handle_num");
    return index;
}

function add_bill_setting_handle(){
    var last_index = get_last_bill_setting_index();
    //console.log(last_index);
    if(bill_setting_handle_max==last_index){
        return;
    }
    bill_setting_handle_arr.push({handle:1,create_bill_num:0});
    //console.log(bill_setting_handle_arr);
}

function update_bill_setting_handle(index,type,value){
    //console.log(bill_setting_handle_arr);
    if(index>0){
        if(type==1){
            bill_setting_handle_arr[index-1].handle = value;
        }else if(type==2){
            bill_setting_handle_arr[index-1].create_bill_num = value;
        }
    }
    //console.log(bill_setting_handle_arr);
}

function del_bill_setting_handle(index){
    //console.log(bill_setting_handle_arr);
    if(index>0){
        bill_setting_handle_arr.splice(index-1,1);
        /*
        for(var i=index;i<bill_setting_handle_arr.length;i++){
            bill_setting_handle_arr[i-1] = bill_setting_handle_arr[i];
        }
        bill_setting_handle_arr.pop();
        */
    }
    //console.log(bill_setting_handle_arr);
}

function get_bill_setting_handle_html(){
    var selected_html = ' selected="selected"';
    var checked_html = ' checked="checked"';
    var html = '';
    //console.log(bill_setting_handle_arr.length);
    for(var i=1;i<bill_setting_handle_arr.length;i++){
        var bill_setting_handle = bill_setting_handle_arr[i];
        if(!bill_setting_handle.handle>0){
            continue;
        }
        var add_html = '<div class="dv1 handle_role handle_role_other" handle_num="'+(i+1)+'">';
        add_html += '<p><span>发票审核角色'+(i+1)+'</span>';
        add_html += '<select class="handle" name="handle_'+(i+1)+'">';
        for(var j=0;j<bill_setting_role_arr.length;j++){
            var bill_setting_item = bill_setting_role_arr[j];
            add_html += '<option';
            if(bill_setting_handle.handle == bill_setting_item.id){
                add_html += selected_html;
            }
            add_html += ' value="'+bill_setting_item.id+'">'+bill_setting_item.role_name+'</option>';
        }
        add_html += '</select>';
        add_html += '<div class="switch_panel';
        if(bill_setting_handle.create_bill_num!=1){
            add_html += ' close';
        }
        add_html += '" ><div class="switch_btn"></div>'+
            '<input type="hidden" name="create_bill_num_'+(i+1)+'" value="' +
            bill_setting_handle.create_bill_num+
            '"/></div>';
        add_html += '<span class="num_tip">填写发票号</span>';
        if(bill_setting_handle_max>(i+1)){
            add_html += '<img src="/systemsetting/images/plus.jpg" class="img2 add" />';
        }
        add_html += '<img src="/systemsetting/images/delelet.png" class="img2 del" />';
        add_html += '</p></div>';
        html+=add_html;
    }
    //console.log(html);
    return html;
}

function get_bill_setting_handle_num(){
    var num = bill_setting_handle_arr.length;
    var num_to_str_arr = ['零','一','二','三','四','五','六','七','八','九','十'];
    return num_to_str_arr[num];
}

function update_bill_setting_handle_html(){
    var html = get_bill_setting_handle_html();
    $(".systemsetting_bill_edit .content .handle_role_other").remove();
    $(".systemsetting_bill_edit .content .handle_role_first").after(html);
    $(".systemsetting_bill_edit .content .handle_role_num").html(get_bill_setting_handle_num());
}

$(".systemsetting_bill_edit .content").on("click",".handle_role .add",function(){
    add_bill_setting_handle();
    update_bill_setting_handle_html();
});
$(".systemsetting_bill_edit .content").on("change",".handle_role .handle",function(){
    var index = get_bill_setting_handle_index(this);
    //console.log(index);
    var value = $(this).val();
    update_bill_setting_handle(index,1,value);
    update_bill_setting_handle_html();
});
$(".systemsetting_bill_edit .content").on("click",".handle_role .create_bill_num",function(){
    var index = get_bill_setting_handle_tools_index(this);
    var target_check = ".systemsetting_bill_edit .content .handle_role .create_bill_num_"+index;
    //console.log($(target_check).attr("checked")!="checked");
    var value = $(target_check).attr("checked")!="checked";
    if(value){
        $(target_check).attr("checked","checked");
    }else{
        $(target_check).removeAttr("checked");
    }
    $(target_check).prop("checked",value);
    update_bill_setting_handle(index,2,value?1:0);
    update_bill_setting_handle_html();
});
$(".systemsetting_bill_edit .content").on("click",".handle_role .switch_panel",function(){
    var index = get_bill_setting_handle_tools_index(this);
    console.log(index);
    var value = $(this).hasClass("close")?1:0;
    console.log(value);
    if(value){
        $(this).removeClass("close");
    }else{
        $(this).addClass("close");
    }
    $(this).children("input").val(value);
    update_bill_setting_handle(index,2,value?1:0);
    update_bill_setting_handle_html();
});
$(".systemsetting_bill_edit .content").on("click",".handle_role .del",function(){
    var index = get_bill_setting_handle_tools_index(this);
    del_bill_setting_handle(index);
    update_bill_setting_handle_html();
});
update_bill_setting_handle_html();


//产品类型
function bill_item_list_get_arr(target){
    //console.log($(target).parent());
    //console.log($(target).parent().siblings("[type=hidden]"));
    var arr = [];
    var arr_str = $(target).parent().siblings("[type=hidden]").val();
    if(arr_str!=""){
        arr = arr_str.split(",");
    }
    return arr;
}
function bill_item_list_set_arr(target,arr){
    //console.log($(target).parent());
    //console.log($(target).parent().siblings("[type=hidden]"));
    var arr_str = "";
    try{
        arr_str += arr.join(",");
    }catch (ex){
        console.log(ex);
    }
    $(target).parent().siblings("[type=hidden]").val(arr_str);
}
function bill_item_list_get_html(target){
    var arr = bill_item_list_get_arr(target);
    console.log(arr);
    var arr_html = "";
    for(var item in arr){
        arr_html+='<span class="redact">'+
            '<span class="item_name">'+arr[item]+'</span>'+
            '<img class="compile" src="/systemsetting/images/compile.png" />'+
            '<img class="del" src="/systemsetting/images/del.png" />'+
            '</span>';
    }
    return arr_html;
}
function bill_item_list_update_html(target,html){
    //console.log($(target).parent().parent().children(".redact"));
    $(target).parent().parent().children(".redact").addClass("hide");
    $(target).parent().siblings(".add").before(html);
    $(target).parent().parent().children(".hide").remove();
}
function bill_item_list_add(target,item_name){
    var arr = bill_item_list_get_arr(target);
    var flg = false;
    for(var item in arr){
        if(arr[item] == item_name){
            console.log("find",arr[item],item_name);
            flg = true;
            break;
        }
    }
    if(flg){
        return false;
    }
    arr.push(item_name);
    bill_item_list_set_arr(target,arr);
    return true;
}
function bill_item_list_update(target,old_item_name,new_item_name){
    var arr = bill_item_list_get_arr(target);
    var flg = false;
    for(var i=0;i<arr.length;i++){
        if(arr[i] == old_item_name){
            arr[i] = new_item_name;
            flg = true;
            break;
        }
    }
    bill_item_list_set_arr(target,arr);
    return flg;
}
function bill_item_list_del(target,item_name){
    var arr = bill_item_list_get_arr(target);
    var flg = false;
    for(var i=0;i<arr.length;i++){
        if(arr[i] == item_name){
            arr.splice(i,1);
            flg = true;
            break;
        }
    }
    bill_item_list_set_arr(target,arr);
    return flg;
}
var bill_item_list_panel = '.systemsetting_bill_edit .content .later';
$(bill_item_list_panel+" .add").click(function(){
    console.log("add_item");
    var add_item = $(this).siblings(".add_item");
    if(add_item.length>0){
        //console.log(add_item.children(".add_item_text"));
        add_item.children(".add_item_text").focus();
        return;
    }
    var item_html = '<span class="redact add_item temp_item">' +
        '<input type="text" class="item_text add_item_text" value=""/>' +
        '<i class="fa fa-check item_btn item_check add_item_check"></i>' +
        '<i class="fa fa-remove item_btn item_remove add_item_remove"></i>' +
        '</span>';
    $(this).before(item_html);
});
$(bill_item_list_panel).on('click',".add_item .add_item_check",function(){
    var add_item_name = $(this).siblings(".add_item_text").val();
    console.log(add_item_name);
    if(!add_item_name){
        return;
    }
    //add
    var add_flg = bill_item_list_add(this,add_item_name);
    console.log(add_flg);
    if(add_flg){
        var new_html = bill_item_list_get_html(this);
        console.log(new_html);
        bill_item_list_update_html(this,new_html);
    }
});
$(bill_item_list_panel).on('click',".add_item .add_item_remove",function(){
    $(this).parent().remove();
});
$(bill_item_list_panel).on('click',".redact .compile",function(){
    var edit_item = $(this).siblings(".edit_item_text");
    if(edit_item.length>0){
        console.log(edit_item);
        edit_item.focus();
        return;
    }
    var edit_item_text = $(this).siblings(".item_name").text();
    $(this).siblings(".item_name").addClass("hide");
    $(this).addClass("hide");
    $(this).siblings(".del").addClass("hide");
    var edit_html = '<input type="text" class="item_text edit_item_text" value="'+edit_item_text+'"/>' +
        '<i class="fa fa-check item_btn item_check edit_item_check"></i>' +
        '<i class="fa fa-remove item_btn item_remove edit_item_remove"></i>';
    $(this).parent().append(edit_html);
});

$(bill_item_list_panel).on('keydown',".item_text",function(event){
	
	if(event.keyCode==13){
		var edit_flag=$(this).hasClass('edit_item_text');
		//编辑
		if(edit_flag){
			var edit_item_name = $(this).val();
		    if(!edit_item_name){
		        return;
		    }
		    var edit_item_text = $(this).siblings(".item_name").text();
		    if(!edit_item_text){
		        return;
		    }
		    //edit
		    var edit_flg = bill_item_list_update(this,edit_item_text,edit_item_name);
		    console.log(edit_flg);
		    if(edit_flg){
		        var new_html = bill_item_list_get_html(this);
		        console.log(new_html);
		        bill_item_list_update_html(this,new_html);
		    }
		}
		var add_flag=$(this).hasClass('add_item_text');
		//添加
		if(add_flag){
			var add_item_name = $(this).val();
		    if(!add_item_name){
		        return;
		    }
		    //add
		    var add_flg = bill_item_list_add(this,add_item_name);
		    console.log(add_flg);
		    if(add_flg){
		        var new_html = bill_item_list_get_html(this);
		        console.log(new_html);
		        bill_item_list_update_html(this,new_html);
		    }
		}
	}
	
})


$(bill_item_list_panel).on('click',".redact .edit_item_check",function(){
    var edit_item_name = $(this).siblings(".edit_item_text").val();
    console.log(edit_item_name);
    if(!edit_item_name){
        return;
    }
    var edit_item_text = $(this).siblings(".item_name").text();
    console.log(edit_item_text);
    if(!edit_item_text){
        return;
    }
    //edit
    var edit_flg = bill_item_list_update(this,edit_item_text,edit_item_name);
    console.log(edit_flg);
    if(edit_flg){
        var new_html = bill_item_list_get_html(this);
        console.log(new_html);
        bill_item_list_update_html(this,new_html);
    }
});
$(bill_item_list_panel).on('click',".redact .edit_item_remove",function(){
    $(this).siblings(".item_name").removeClass("hide");
    $(this).siblings(".compile").removeClass("hide");
    $(this).siblings(".del").removeClass("hide");
    $(this).siblings(".edit_item_text").remove();
    $(this).siblings(".edit_item_check").remove();
    $(this).remove();
});
$(bill_item_list_panel).on('click',".redact .del",function(){
    var del_item_text = $(this).siblings(".item_name").text();
    console.log(del_item_text);
    if(!del_item_text){
        return;
    }
    //del
    var del_flg = bill_item_list_del(this,del_item_text);
    console.log(del_flg);
    if(del_flg){
        var new_html = bill_item_list_get_html(this);
        console.log(new_html);
        bill_item_list_update_html(this,new_html);
    }
});
