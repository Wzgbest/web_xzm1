function list_manage(from,target,url,p,num,max,in_column){
    //当前列表变量
    this.from = from;
    this.target = target;
    this.url = url;
    this.p = parseInt(p);
    this.num = parseInt(num);
    this.max = parseInt(max);
    this.in_column = parseInt(in_column);

    //事件绑定
    var self = this;
    $("."+this.from+" .m-firNav li").click(function(){
        var in_column = $(this).attr("in_column");
        self.columnChange(in_column);
    });
    $("."+this.from+" .m-filterNav .u-btnSearch").click(function(){
        self.search();
    });
    $("."+this.from+" .u-tabControlRow select").click(function(){
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
        var search_form_data = $("#"+this.target+" ."+this.from+" .search_form").serialize();
        var url = this.get_url(1,this.num,this.in_column);
        $.ajax({
            url: url,
            type: 'post',
            data: search_form_data,
            success: function(data) {
                //console.log(data);
                $('#frames #'+self.target).html(data);
            },
            error: function() {
                alert("搜索时发生错误!");
            }
        });
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
        loadPage(this.get_url(p,num,in_column),this.target);
    };
    this.get_url=function(p,num,in_column){
        return this.url+"/p/"+p+"/num/"+num+"/in_column/"+in_column;
    };
}
