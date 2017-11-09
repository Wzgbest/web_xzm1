/*新增*/
var rule_list_hide_panel = 'rule-managementfr .sys_rule_list .rule_list_panel';
function list_rule(from, target, url){
    //当前列表变量
    this.from = from;
    this.target = target;
    this.url = url;
    this.reload_list=function(){
        this.load_list();
    };
    //公共方法
    this.load_list=function(){
        loadPagebypost(this.url,this.searchForm,this.target);
        changeFramesSize();
    };
}
function rule_list_newRule(){
    $("#frames .sys_rule_list .blackBg").height(window.innerHeight);
    document.getElementById("rule_list_newRule").classList.remove("hide");
    document.getElementById("rule_list_blackBg").classList.remove("hide");
}
function rule_list_removeNewRule(){
    document.getElementById("rule_list_newRule").classList.add("hide");
    document.getElementById("rule_list_blackBg").classList.add("hide");
}
function rule_list_check_form_html5(eles){
    var ele;
    for(var i = 0;i<eles.length;i++){
        ele = eles[i];
        if(ele.name){
            if(!ele.checkValidity()){
                ele.focus();
                return false;
            }
        }
    }
    return true;
}
function rule_list_add_rule(){
    if(!rule_list_check_form_html5($("#rule_list_newRuleForm").get(0).elements)){
        return;
    }
    var rule_list_add_rule_from_data = $("#rule_list_newRuleForm").serialize();
    var url = '/systemsetting/rule/addRule';
    //console.log(employee_list_add_employee_from_data);
    $.ajax({
        url: url,
        type: 'post',
        data: rule_list_add_rule_from_data,
        dataType: 'json',
        success: function(data) {
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status) {
                rule_list_list_manage.reload_list();
            }
        },
        error: function() {
            layer.msg('保存失败!',{icon:2});
        }
    });
}

function rule_list_edit(id,status){
    var url = "/systemsetting/rule/edit/id/"+id+"/fr/rule_list";
    var panel = 'rule-managementfr .sys_rule_list .rule_info';
    $.ajax({
        url:url,
        type:'get',
        async:false,
        success:function (data) {
            rule_list_edit_hide_flg = status;
            $('#frames #'+rule_list_hide_panel).addClass("hide");
            $('#frames #'+panel).html(data);
            $('#frames #'+panel).removeClass("hide");
        },
        error:function(){
            layer.msg('获取权限信息失败!',{icon:2});
        }
    });
}

function rule_list_show_list(){
    $('#frames #'+rule_list_hide_panel).addClass("hide");
    $('#frames #rule-managementfr .sys_rule_list .rule_list').removeClass("hide");
}

function rule_list_panel_close(){
    $('#frames #'+rule_list_hide_panel).addClass("hide");
    $('#frames #rule-managementfr .sys_rule_list .rule_list').removeClass("hide");
}

function rule_list_edit_update(id){
    var rule_list_edit_form_data = $(".rule_list_edit_form").serialize();
    rule_list_edit_form_data += "&id="+id;
    //console.log(employee_list_edit_form_data);
    $.ajax({
        url: '/systemsetting/rule/editRule.html',
        type: 'post',
        data: rule_list_edit_form_data,
        dataType: 'json',
        success: function(data) {
            console.log('llalal'+data);
            layer.msg(data.message,{icon:data.status==1?1:2});
            if(data.status) {
                rule_list_list_manage.reload_list();
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            layer.msg('保存权限信息时发生错误!',{icon:2});
        },
    });
}

function rule_list_del(id){
    var options = {
        id:id,
        url: "/systemsetting/rule/delRule.html",
        title: '您确认删除？此操作不可恢复，请谨慎操作！'
    };
    delData(options,function(){
        rule_list_list_manage.reload_list();
    });
}