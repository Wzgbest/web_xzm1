// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

var contract_setting_apply_arr = null;
try{
    var contract_setting_apply_arr = JSON.parse(contract_setting_applys);
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
        add_html += ' <input type="checkbox" class="create_contract_num create_contract_num_'+(i+1)+'" name="create_contract_num_'+(i+1)+'"';
        if(contract_setting_apply.create_contract_num==1){
            add_html += checked_html;
        }
        add_html += ' value="1"/>';
        add_html += '<span class="contract_num_tip">生成合同号</span>';
        add_html += '<img src="/systemsetting/images/delelet.png" class="img2 del" />';
        if(contract_setting_apply_max>(i+1)){
            add_html += '<img src="/systemsetting/images/plus.jpg" class="img2 add" />';
        }
        add_html += '</p></div>';
        html+=add_html;
    }
    //console.log(html);
    return html;
}

function update_contract_setting_apply_html(){
    var html = get_contract_setting_apply_html();
    $(".systemsetting_contract_edit .content .apply_role_other").remove();
    $(".systemsetting_contract_edit .content .apply_role_first").after(html);
}

function get_contract_setting_apply_index(target){
    var index = $(target).parent().parent().attr("apply_num");
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
$(".systemsetting_contract_edit .content").on("click",".apply_role .del",function(){
    var index = get_contract_setting_apply_index(this);
    del_contract_setting_apply(index);
    update_contract_setting_apply_html();
});
update_contract_setting_apply_html();