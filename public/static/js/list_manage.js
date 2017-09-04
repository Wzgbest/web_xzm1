function list_manage(from,target,url,p,num,max,in_column,sub){
    //当前列表变量
    this.from = from;
    this.target = target;
    this.url = url;
    this.p = parseInt(p);
    this.num = parseInt(num);
    this.max = parseInt(max);
    this.in_column = parseInt(in_column);
    this.sub = sub;
    this.activity_buttons = new Array();
    this.searchForm = $("#"+this.target+" ."+this.from+this.sub+" .search_form").serialize();

    //事件绑定
    var self = this;
    $("."+this.from+" .m-firNav .in_column").click(function(){
        var in_column = $(this).attr("in_column");
        self.columnChange(in_column);
    });
    $("."+this.from+this.sub+" .m-filterNav .u-btnSearch").click(function(){
        self.search();
    });
    $("."+this.from+" .u-tabTitle input[type='checkbox']").click(function(){
        self.selectAll($(this).attr("checked")!="checked");
    });
    $("."+this.from+' .u-tabList input[type="checkbox"]').click(function(){
        self.select($(this).val(),$(this).attr("checked")!="checked");
    });
    $("."+this.from+" .u-tabControlRow select").change(function(){
        var num = $(this).val();
        self.listNumChange(num);
    });
    $("."+this.from+" .u-tabControlRow .previous_page").click(function(){
        self.previous_page();
    });
    $("."+this.from+" .u-tabControlRow .next_page").click(function(){
        self.next_page();
    });
    /*$("."+this.from+" .u-tabControlRow input").blur(function(){
        var p = parseInt($(this).val());
        self.jump_page(p);
     });*/
    $("."+this.from+" .u-tabControlRow input").bind('keypress',function(event){
        if(event.keyCode == "13"){
            var p = parseInt($(this).val());
            self.jump_page(p);
        }
    });

    //列表动作
    this.columnChange=function(in_column){
        this.load_list(1,this.num,in_column);
    };
    this.search=function(){
        //console.log($("#"+this.target+" ."+this.from+this.sub+" .search_form"));
        this.searchForm = $("#"+this.target+" ."+this.from+this.sub+" .search_form").serialize();
        //console.log(search_form_data);
        this.load_list(1,this.num,this.in_column);
    };
    this.isSelect=function(){
        var selected_length = $("."+this.from+' .u-tabList .u-tabCheckbox :checked').length;
        //console.log(selected_length);
        return (selected_length>0);
    };
    this.isSelectAll=function(){
        var selected_length = $("."+this.from+' .u-tabList .u-tabCheckbox :checked').length;
        //console.log(selected_length);
        return (selected_length==this.num);
    };
    this.select=function(id,status){
        if(status){
            $("."+this.from+' .u-tabList input[type="checkbox"]').find("[value="+id+"]").attr("checked","checked");
        }else{
            $("."+this.from+' .u-tabList input[type="checkbox"]').find("[value="+id+"]").removeAttr("checked");
        }
        var selected_all = this.isSelectAll();
        //console.log(selected_all);
        if(selected_all){
            $("."+this.from+" .u-tabTitle input[type='checkbox']").attr("checked","checked");
        }else{
            $("."+this.from+" .u-tabTitle input[type='checkbox']").removeAttr("checked");
        }
        $("."+this.from+' .u-tabTitle input[type="checkbox"]').prop("checked",selected_all);

        this.updateActivityButtons();
    };
    this.selectAll=function(status){
        //console.log(status);
        if(status){
            $("."+this.from+" .u-tabTitle input[type='checkbox']").attr("checked","checked");
            $("."+this.from+' .u-tabList input[type="checkbox"]').attr("checked","checked");
        }else{
            $("."+this.from+" .u-tabTitle input[type='checkbox']").removeAttr("checked");
            $("."+this.from+' .u-tabList input[type="checkbox"]').removeAttr("checked");
        }
        $("."+this.from+' .u-tabList input[type="checkbox"]').prop("checked",status);

        this.updateActivityButtons();
    };
    this.getAllSelectVal=function(header,delimiter){
        header = header||"ids[]=";
        delimiter = delimiter||"&";
        var ids_str = "";
        if($("."+this.from+' .u-tabList .u-tabCheckbox :checked').length==0){
            return ids_str;
        }
        var ids_arr = new Array();
        $("."+this.from+' .u-tabList .u-tabCheckbox :checked').each(function(index){
            ids_arr[index] = $(this).val();
        });
        ids_str += header+ids_arr.join(delimiter+header);
        //console.log(ids_arr);
        //console.log(ids_str);
        return ids_str;
    };
    this.listenSelect=function(class_name){
        this.activity_buttons[this.activity_buttons.length] = class_name;
    };
    this.updateActivityButtons=function(){
        var selected = this.isSelect();
        for (var i = 0; i < this.activity_buttons.length; i++) {
            var btn = $("."+this.from+' .m-secNav .'+this.activity_buttons[i]);
            if(selected){
                btn.addClass("active");
            }else{
                btn.removeClass("active");
            }
        }
    };
    this.listNumChange=function(num){
        this.load_list(1,num,this.in_column);
    };
    this.previous_page=function(){
        if(this.p-1<1){
            return;
        }
        this.load_list(this.p-1,this.num,this.in_column);
    };
    this.next_page=function(){
        if(this.p+1>this.max){
            return;
        }
        //console.log(this.p);
        this.load_list(this.p+1,this.num,this.in_column);
    };
    this.jump_page=function(p){
        if(p>this.max || p<1 || p==this.p){
            return;
        }
        this.load_list(p,this.num,this.in_column);
    };
    this.reload_list=function(){
        this.load_list(this.p,this.num,this.in_column);
    };

    //公共方法
    this.load_list=function(p,num,in_column){
        loadPagebypost(this.get_url(p,num,in_column),this.searchForm,this.target+this.sub);
    };
    this.get_url=function(p,num,in_column){
        return this.url+"/p/"+p+"/num/"+num+"/in_column/"+in_column;
    };
}
