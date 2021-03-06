// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

var business_flow_setting_role_arr = null;
try{
    business_flow_setting_role_arr = JSON.parse(business_flow_setting_roles);
}catch (ex){
    console.log(ex);
}
if(business_flow_setting_role_arr==null){
    //console.log("role data not found");
    business_flow_setting_role_arr = [];
}
console.log(business_flow_setting_role_arr);

var business_flow_item_arr = null;
try{
    business_flow_item_arr = JSON.parse(business_flow_items);
}catch (ex){
    console.log(ex);
}
if(business_flow_item_arr==null){
    //console.log("item data not found");
    business_flow_item_arr = [];
}
console.log(business_flow_item_arr);

var business_flow_item_link_arr = null;
try{
    business_flow_item_link_arr = JSON.parse(business_flow_item_links);
}catch (ex){
    console.log(ex);
}
if(business_flow_item_link_arr==null){
    //console.log("item data not found");
    business_flow_item_link_arr = [];
}
console.log(business_flow_item_link_arr);

//业务流程项目
function get_business_flow_item(id){
    //console.log(business_flow_item_arr);
    var business_flow_item = false;
    for(var i in business_flow_item_arr){
        //console.log(business_flow_item_arr[i]);
        if(business_flow_item_arr[i]['id'] == id){
            //console.log("find");
            business_flow_item = business_flow_item_arr[i];
            break;
        }
    }
    return business_flow_item;
}
function business_flow_item_list_get_arr(){
    //console.log(business_flow_item_link_arr);
    return business_flow_item_link_arr;
}
function business_flow_item_list_set_arr(arr){
    //console.log(arr);
    business_flow_item_link_arr = arr;
}
function business_flow_item_link_sort(a,b){
    return a['order_num']-b['order_num'];
}
function business_flow_item_list_get_html(){
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    var arr_html = "";
    for(var item=0;item<arr.length;item++){
        arr_html+='<span index = '+arr[item]['item_id']+' class="item">';
        if(item!=0){
            arr_html+='<img src=' + "/systemsetting/images/arrows.png" + '  class="arrows tupian'+arr[item]['item_id']+'"/>';
        }
        arr_html+='<span index = '+arr[item]['item_id']+' class="item_name">'+arr[item]['item_name']+'</span>'+
            '<img src='+"/systemsetting/images/delelet.png"+' index = "'+arr[item]['item_id']+'" class="del"/></span>';

    }
    return arr_html;
}
function business_flow_item_list_update_html(html){
    //console.log($(.systemsetting_business_flow_edit .item_add_panel"));
    $(".systemsetting_business_flow_edit .item_add_panel .item").remove();
    $(".systemsetting_business_flow_edit .item_add_panel").append(html);
}
function business_flow_item_list_update_check(){
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    for(var item in arr){
        var id = arr[item]['item_id'];
        $(".systemsetting_business_flow_edit .business_flow_item_list input[index="+id+"]").prop('checked',true);
    }
}
function business_flow_item_list_add(id){
    var arr = business_flow_item_list_get_arr();
    var flg = false;
    var item = get_business_flow_item(id);
    //console.log(item);
    if(!item){
        return flg;
    }
    var item_link = {
        "id":"0",
        'setting_id':0,
        'item_id':item['id'],
        'order_num':arr.length,
        'item_name':item['item_name'],
        'have_verification':item['have_verification'],
        'handle_1':0,
        'handle_2':0,
        'handle_3':0,
        'handle_4':0,
        'handle_5':0,
        'handle_6':0,
    };
    for(var i in arr){
        if(arr[i]['item_id'] == id){
            //console.log("find",arr[i]['item_id'],id);
            flg = true;
            break;
        }
    }
    if(flg){
        return false;
    }
    arr.push(item_link);
    arr.sort(business_flow_item_link_sort);
    for(var i=0;i<arr.length;i++){
        arr[i]['order_num'] = i+1;
    }
    business_flow_item_list_set_arr(arr);
    return true;
}
function business_flow_item_list_del(id){
    var arr = business_flow_item_list_get_arr();
    var flg = false;
    for(var i=0;i<arr.length;i++){
        if(arr[i]['item_id'] == id){
            arr.splice(i,1);
            flg = true;
            break;
        }
    }
    arr.sort(business_flow_item_link_sort);
    for(var i=0;i<arr.length;i++){
        arr[i]['order_num'] = i+1;
    }
    business_flow_item_list_set_arr(arr);
    return flg;
}
function get_business_flow_item_link_json(){
    return JSON.stringify(business_flow_item_link_arr);
}

$(".systemsetting_business_flow_edit .business_flow_item_list").on('click','input',function(){
    var id = $(this).attr("index");
    //console.log(id);
    if(!id>0){
        return;
    }
    var flg = false;
    if($(this).prop('checked')==true){
        flg = business_flow_item_list_add(id);
    }else{
        flg = business_flow_item_list_del(id);
        business_flow_now_role_item_id = 0;
        business_flow_role_list_update_html('');
    }
    if(!flg){
        return;
    }
    var html = business_flow_item_list_get_html();
    business_flow_item_list_update_html(html);
});

$('.systemsetting_business_flow_edit .business_flow_item_selected').on('click','.item .del',function(){
    var id = $(this).attr("index");
    //console.log(id);
    if(!id>0){
        return;
    }
    var flg = business_flow_item_list_del(id);
    if(!flg){
        return;
    }
    $(".systemsetting_business_flow_edit .business_flow_item_list input[index="+id+"]").prop('checked',false);
    var html = business_flow_item_list_get_html();
    business_flow_item_list_update_html(html);
    business_flow_now_role_item_id = 0;
    business_flow_role_list_update_html('');
});

$('.systemsetting_business_flow_edit .business_flow_item_selected').on('click','.item .item_name',function(){
    var id = $(this).attr("index");
    //console.log(id);
    if(!id>0){
        return;
    }
    //console.log("load_role_panel");
    business_flow_now_role_item_id = id;
    var html = business_flow_role_list_get_html();
    business_flow_role_list_update_html(html);
});
var business_flow_item_load_html = business_flow_item_list_get_html();
business_flow_item_list_update_html(business_flow_item_load_html);
business_flow_item_list_update_check();


//审核权限
var business_flow_now_role_item_id = 0;

function get_business_flow_item_handle_index(target){
    var index = $(target).parent().parent().attr("handle_num");
    return index;
}

function add_business_flow_item_handle(index) {
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    if (index > 0 && index < business_flow_setting_handle_max) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i]['item_id'] == business_flow_now_role_item_id) {
                var role_item = arr[i];
                if (!role_item['have_verification'] > 0) {
                    return;//...
                }
                arr[i]["handle_" + (index*1 + 1)] = -1;
            }
        }
    }
    business_flow_item_list_set_arr(arr);
    //console.log(arr);
}

function update_business_flow_item_handle(index,value) {
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    if (index > 0 && index <= business_flow_setting_handle_max) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i]['item_id'] == business_flow_now_role_item_id) {
                var role_item = arr[i];
                //console.log('role_item',role_item);
                if (!role_item['have_verification'] > 0) {
                    return;//...
                }
                arr[i]["handle_" + index] = value;
            }
        }
    }
    business_flow_item_list_set_arr(arr);
    //console.log(arr);
}

function del_business_flow_item_handle(index) {
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    if (index > 0 && index <= business_flow_setting_handle_max) {
        for (var i = 0; i < arr.length; i++) {
            if (arr[i]['item_id'] == business_flow_now_role_item_id) {
                var role_item = arr[i];
                if (!role_item['have_verification'] > 0) {
                    return;//...
                }
                for (var j = index; j < business_flow_setting_handle_max; j++) {
                    arr[i]["handle_" + j] = arr[i]["handle_" + (j*1+1)];
                }
                arr[i]["handle_" + business_flow_setting_handle_max] = 0;
            }
        }
    }
    business_flow_item_list_set_arr(arr);
    //console.log(arr);
}
function business_flow_role_list_item_html(item_handle,name,i){
    var selected_html = ' selected="selected"';
    var add_html = '<div class="dv1 role_list handle_role handle_role_other" handle_num="'+(i+1)+'">';
    add_html += '<p><span>'+name+' 审核角色'+(i+1)+'</span>';
    add_html += '<select class="handle">';
    add_html += '<option value="0">请选择</option>';
    for(var j=0;j<business_flow_setting_role_arr.length;j++){
        var business_flow_setting_item = business_flow_setting_role_arr[j];
        add_html += '<option';
        if(item_handle == business_flow_setting_item.id){
            add_html += selected_html;
        }
        add_html += ' value="'+business_flow_setting_item.id+'">'+business_flow_setting_item.role_name+'</option>';
    }
    add_html += '</select>';
    if(business_flow_setting_handle_max>(i+1)){
        add_html += '<img src="/systemsetting/images/plus.jpg" class="img2 add" />';
    }
    if(i!=0){
        add_html += '<img src="/systemsetting/images/delelet.png" class="img2 del" />';
    }
    add_html += '</p></div>';
    return add_html;
}
function business_flow_role_list_get_html(){
    var arr = business_flow_item_list_get_arr();
    //console.log(arr);
    var all_html = '';
    for(var i=0;i<arr.length;i++){
        if(arr[i]['item_id'] == business_flow_now_role_item_id){
            var role_item = arr[i];
            //console.log("role_item",role_item);
            //console.log("parseInt(role_item['have_verification'])",parseInt(role_item['have_verification']));
            if(!parseInt(role_item['have_verification'])>0){
                //console.log("break");
                break;
            }
            //console.log("not break");
            all_html += '<div class="dv1 role_list"><p>'+
                '<img src="/systemsetting/images/line_purple.jpg" class="img1">'+
                '<span class="sp1">'+role_item['item_name']+'</span></p></div>'+
                '<div class="dv2 role_list full"><p><span></span></p></div>';
            var add_html = business_flow_role_list_item_html(role_item["handle_1"],role_item['item_name'],0);

            var num = 1;
            for(var j=1;j<6;j++){
                if(role_item["handle_"+(j+1)]==0){
                    break;
                }
                num++;
                add_html += business_flow_role_list_item_html(role_item["handle_"+(j+1)],role_item['item_name'],j);
            }

            var num_to_str_arr = ['零','一','二','三','四','五','六','七','八','九','十'];
            all_html+=add_html;
            all_html+='<div class="dv1 role_list full"><p class="p1">每一个审核角色代表一个审批环节，当前审核需要'+num_to_str_arr[num]+'个审批环节</p></div>';
            break;
        }
    }
    return all_html;
}

function business_flow_role_list_update_html(html){
    //console.log($(.systemsetting_business_flow_edit .role_list"));
    $(".systemsetting_business_flow_edit .role_list").remove();
    $(".systemsetting_business_flow_edit .role_list_splice").after(html);
}
$(".systemsetting_business_flow_edit .content").on("click",".handle_role .add",function(){
    var index = get_business_flow_item_handle_index(this);
    //console.log(index);
    add_business_flow_item_handle(index);
    var html = business_flow_role_list_get_html();
    business_flow_role_list_update_html(html);
});
$(".systemsetting_business_flow_edit .content").on("change",".handle_role .handle",function(){
    var index = get_business_flow_item_handle_index(this);
    //console.log(index);
    var value = $(this).val();
    //console.log(value);
    update_business_flow_item_handle(index,value);
    var html = business_flow_role_list_get_html();
    business_flow_role_list_update_html(html);
});
$(".systemsetting_business_flow_edit .content").on("click",".handle_role .del",function(){
    var index = get_business_flow_item_handle_index(this);
    //console.log(index);
    del_business_flow_item_handle(index);
    var html = business_flow_role_list_get_html();
    business_flow_role_list_update_html(html);
});


$('.systemsetting_business_flow_edit .xuanze').on('click', "input", function() {
    var index = $(this).val(); //index为input的自定义index
    if ($(this).prop('checked') == true) {
        var main = $(this).next().html();
        $('.systemsetting_business_flow_edit .xuanzhong ul').append('<li index = ' + index + '>' + main + '<img src=' + "/systemsetting/images/delelet.png" + ' index = "' + index + '"/></li>')
    } else {
        $('.systemsetting_business_flow_edit .xuanzhong ul li').each(function() {
            if ($(this).attr('index') == index) {
                $(this).remove();
            }
        })
    }
    systemsetting_business_flow_update_sel_num();
});

function systemsetting_business_flow_update_sel_num(){
    var allLength=$('.systemsetting_business_flow_edit .xuanzhong ul li').length;
    //console.log(allLength);
    $(".systemsetting_business_flow_edit .xuanzhong p span").html(allLength);
}
	
$('.systemsetting_business_flow_edit .xuanzhong').on('click', "ul li img", function() {
    var index1 = $(this).attr("index");
    //console.log(index1);
    var self = $(this);
    $('.systemsetting_business_flow_edit .xuanze input').each(function() {
        if ($(this).val() == index1) {
            $(this).prop('checked', false);
            $(self).parent().remove();
        }
    });
    systemsetting_business_flow_update_sel_num();
});

$('.systemsetting_business_flow_edit .fuxuan').click(function() {
	$('.systemsetting_business_flow_edit .fuxuan i').toggleClass("rotate");
    $('.systemsetting_business_flow_edit .xuanze').toggle();
    $('.systemsetting_business_flow_edit .xuanzhong').toggle();
});
$(".systemsetting_business_flow_edit .wancheng").click(function() {
    //  	console.log($(this).siblings())
    //  	var content=$(this).siblings().text();
    //
    //  	$('.systemsetting_business_flow_edit .yixuan').add(content)
    $(".systemsetting_business_flow_edit .yixuan").empty();
    $(".systemsetting_business_flow_edit .xuanzhong ul li").each(function() {
        var content = $(this).text();
        var index = $(this).attr("index");
        //			$('.systemsetting_business_flow_edit .yixuan').append(content)
        $('.systemsetting_business_flow_edit .yixuan').append('<span >' + content + '<input type="hidden" name="set_to_role[]" value="'+index+'"/></span>')
    });

    $('.systemsetting_business_flow_edit .xuanze').css('display', 'none');
    $('.systemsetting_business_flow_edit .xuanzhong').css('display', 'none')
});
