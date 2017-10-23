// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

var contract_setting_apply_arr = null;
try{
    contract_setting_apply_arr = JSON.parse(contract_setting_applys);
}catch (ex){
    console.log(ex);
}
if(contract_setting_apply_arr==null){
    console.log("applys data not found");
    contract_setting_apply_arr = [];
}
//console.log(contract_setting_apply_arr);

var contract_setting_role_arr = null;
try{
    contract_setting_role_arr = JSON.parse(contract_setting_roles);
}catch (ex){
    console.log(ex);
}
if(contract_setting_role_arr==null){
    console.log("roles data not found");
    contract_setting_role_arr = [];
}
//console.log(contract_setting_role_arr);

function get_last_contract_setting_index(){
    var apply_arr_length = contract_setting_apply_arr.length;
    var last_index = apply_arr_length;
    return last_index;
}

function add_contract_setting_apply(){
    var last_index = get_last_contract_setting_index();
    //console.log(last_index);
    if(contract_setting_apply_max==last_index){
        return;
    }
    contract_setting_apply_arr.push({apply:1,create_contract_num:0});
    //console.log(contract_setting_apply_arr);
}

function update_contract_setting_apply(index,type,value){
    //console.log(contract_setting_apply_arr);
    if(index>0){
        if(type==1){
            contract_setting_apply_arr[index-1].apply = value;
        }else if(type==2){
            contract_setting_apply_arr[index-1].create_contract_num = value;
        }
    }
    //console.log(contract_setting_apply_arr);
}

function del_contract_setting_apply(index){
    //console.log(contract_setting_apply_arr);
    if(index>0){
        contract_setting_apply_arr.splice(index-1,1);
        /*
        for(var i=index;i<contract_setting_apply_arr.length;i++){
            contract_setting_apply_arr[i-1] = contract_setting_apply_arr[i];
        }
        contract_setting_apply_arr.pop();
        */
    }
    //console.log(contract_setting_apply_arr);
}

function get_contract_setting_apply_html(){
    var selected_html = ' selected="selected"';
    var checked_html = ' checked="checked"';
    var html = '';
    //console.log(contract_setting_apply_arr.length);
    for(var i=1;i<contract_setting_apply_arr.length;i++){
        var contract_setting_apply = contract_setting_apply_arr[i];
        if(!contract_setting_apply.apply>0){
            continue;
        }
        var add_html = '<div class="dv1 apply_role apply_role_other" apply_num="'+(i+1)+'">';
        add_html += '<p><span>合同审核角色'+(i+1)+'</span>';
        add_html += '<select class="apply" name="apply_'+(i+1)+'">';
        for(var j=0;j<contract_setting_role_arr.length;j++){
            var contract_setting_role = contract_setting_role_arr[j];
            add_html += '<option';
            if(contract_setting_apply.apply == contract_setting_role.id){
                add_html += selected_html;
            }
            add_html += ' value="'+contract_setting_role.id+'">'+contract_setting_role.role_name+'</option>';
        }
        add_html += '</select>';
        add_html += '<div class="switch_panel';
        if(contract_setting_apply.create_contract_num!=1){
            add_html += ' close';
        }
        add_html += '" ><div class="switch_btn"></div>'+
            '<input type="hidden" name="create_contract_num_'+(i+1)+'" value="' +
            contract_setting_apply.create_contract_num+
            '"/></div>';
        add_html += '<span class="num_tip">生成合同号</span>';
        if(contract_setting_apply_max>(i+1)){
            add_html += '<img src="/systemsetting/images/plus.jpg" class="img2 add" />';
        }
        add_html += '<img src="/systemsetting/images/delelet.png" class="img2 del" />';
        add_html += '</p></div>';
        html+=add_html;
    }
    //console.log(html);
    return html;
}

function get_contract_setting_apply_num(){
    var num = contract_setting_apply_arr.length;
    var num_to_str_arr = ['零','一','二','三','四','五','六','七','八','九','十'];
    return num_to_str_arr[num];
}

function update_contract_setting_apply_html(){
    var html = get_contract_setting_apply_html();
    $(".systemsetting_contract_edit .content .apply_role_other").remove();
    $(".systemsetting_contract_edit .content .apply_role_first").after(html);
    $(".systemsetting_contract_edit .content .apply_role_num").html(get_contract_setting_apply_num());
}

function get_contract_setting_apply_index(target){
    var index = $(target).parent().parent().attr("apply_num");
    return index;
}

function get_contract_setting_apply_tools_index(target){
    var index = $(target).parent().attr("apply_num");
    return index;
}

$(".systemsetting_contract_edit .content").on("click",".apply_role .add",function(){
    add_contract_setting_apply();
    update_contract_setting_apply_html();
});
$(".systemsetting_contract_edit .content").on("change",".apply_role .apply",function(){
    var index = get_contract_setting_apply_index(this);
    //console.log(index);
    var value = $(this).val();
    update_contract_setting_apply(index,1,value);
    update_contract_setting_apply_html();
});
$(".systemsetting_contract_edit .content").on("click",".apply_role .create_contract_num",function(){
    var index = get_contract_setting_apply_index(this);
    var target_check = ".systemsetting_contract_edit .content .apply_role .create_contract_num_"+index;
    //console.log($(target_check).attr("checked")!="checked");
    var value = $(target_check).attr("checked")!="checked";
    if(value){
        $(target_check).attr("checked","checked");
    }else{
        $(target_check).removeAttr("checked");
    }
    $(target_check).prop("checked",value);
    update_contract_setting_apply(index,2,value?1:0);
    update_contract_setting_apply_html();
});
$(".systemsetting_contract_edit .content").on("click",".apply_role .switch_panel",function(){
    var index = get_contract_setting_apply_tools_index(this);
    console.log(index);
    var value = $(this).hasClass("close")?1:0;
    console.log(value);
    if(value){
        $(this).removeClass("close");
    }else{
        $(this).addClass("close");
    }
    $(this).children("input").val(value);
    update_contract_setting_apply(index,2,value?1:0);
    update_contract_setting_apply_html();
});
$(".systemsetting_contract_edit .content").on("click",".apply_role .del",function(){
    var index = get_contract_setting_apply_tools_index(this);
    console.log(index);
    del_contract_setting_apply(index);
    update_contract_setting_apply_html();
});
update_contract_setting_apply_html();


//打款银行类型
function contract_item_list_get_arr(target){
    //console.log($(target).parent());
    //console.log($(target).parent().siblings("[type=hidden]"));
    var arr = [];
    var arr_str = $(target).parent().siblings("[type=hidden]").val();
    if(arr_str!=""){
        arr = arr_str.split(",");
    }
    return arr;
}
function contract_item_list_set_arr(target,arr){
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
function contract_item_list_get_html(target){
    var arr = contract_item_list_get_arr(target);
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
function contract_item_list_update_html(target,html){
    //console.log($(target).parent().parent().children(".redact"));
    $(target).parent().parent().children(".redact").addClass("hide");
    $(target).parent().siblings(".add").before(html);
    $(target).parent().parent().children(".hide").remove();
}
function contract_item_list_add(target,item_name){
    var arr = contract_item_list_get_arr(target);
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
    contract_item_list_set_arr(target,arr);
    return true;
}
function contract_item_list_update(target,old_item_name,new_item_name){
    var arr = contract_item_list_get_arr(target);
    var flg = false;
    for(var i=0;i<arr.length;i++){
        if(arr[i] == old_item_name){
            arr[i] = new_item_name;
            flg = true;
            break;
        }
    }
    contract_item_list_set_arr(target,arr);
    return flg;
}
function contract_item_list_del(target,item_name){
    var arr = contract_item_list_get_arr(target);
    var flg = false;
    for(var i=0;i<arr.length;i++){
        if(arr[i] == item_name){
            arr.splice(i,1);
            flg = true;
            break;
        }
    }
    contract_item_list_set_arr(target,arr);
    return flg;
}
var contract_item_list_panel = '.systemsetting_contract_edit .content .later';
$(contract_item_list_panel+" .add").click(function(){
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
$(contract_item_list_panel).on('click',".add_item .add_item_check",function(){
    var add_item_name = $(this).siblings(".add_item_text").val();
    console.log(add_item_name);
    if(!add_item_name){
        return;
    }
    //add
    var add_flg = contract_item_list_add(this,add_item_name);
    console.log(add_flg);
    if(add_flg){
        var new_html = contract_item_list_get_html(this);
        console.log(new_html);
        contract_item_list_update_html(this,new_html);
    }
});
$(contract_item_list_panel).on('click',".add_item .add_item_remove",function(){
    $(this).parent().remove();
});
$(contract_item_list_panel).on('click',".redact .compile",function(){
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

$(contract_item_list_panel).on('keydown',".item_text",function(event){
	if(event.keyCode==13){
		var edit_flag=$(this).hasClass('edit_item_text');
		//编辑
		if(edit_flag){
			var edit_item_name = $(this).val();
		    console.log('edit_item_name:'+edit_item_name);
		    if(!edit_item_name){
		        return;
		    }
		    var edit_item_text = $(this).siblings(".item_name").text();
		    console.log('edit_item_text'+edit_item_text);
		    if(!edit_item_text){
		        return;
		    }
		    //edit
		    var edit_flg = contract_item_list_update(this,edit_item_text,edit_item_name);
		    console.log('edit_flg:'+edit_flg);
		    if(edit_flg){
		        var new_html = contract_item_list_get_html(this);
		        console.log(new_html);
		        contract_item_list_update_html(this,new_html);
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
		    var add_flg = contract_item_list_add(this,add_item_name);
		    if(add_flg){
		        var new_html = contract_item_list_get_html(this);
		        contract_item_list_update_html(this,new_html);
		    }
		}
	}
})


$(contract_item_list_panel).on('click',".redact .edit_item_check",function(){
    var edit_item_name = $(this).siblings(".edit_item_text").val();
    if(!edit_item_name){
        return;
    }
    var edit_item_text = $(this).siblings(".item_name").text();
    if(!edit_item_text){
        return;
    }
    //edit
    var edit_flg = contract_item_list_update(this,edit_item_text,edit_item_name);
    if(edit_flg){
        var new_html = contract_item_list_get_html(this);
        contract_item_list_update_html(this,new_html);
    }
});



$(contract_item_list_panel).on('click',".redact .edit_item_remove",function(){
    $(this).siblings(".item_name").removeClass("hide");
    $(this).siblings(".compile").removeClass("hide");
    $(this).siblings(".del").removeClass("hide");
    $(this).siblings(".edit_item_text").remove();
    $(this).siblings(".edit_item_check").remove();
    $(this).remove();
});
$(contract_item_list_panel).on('click',".redact .del",function(){
    var del_item_text = $(this).siblings(".item_name").text();
    console.log(del_item_text);
    if(!del_item_text){
        return;
    }
    //del
    var del_flg = contract_item_list_del(this,del_item_text);
    console.log(del_flg);
    if(del_flg){
        var new_html = contract_item_list_get_html(this);
        console.log(new_html);
        contract_item_list_update_html(this,new_html);
    }
});
