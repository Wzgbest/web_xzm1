// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------
function tree(config) {
    this.target = config.target;
    if($(this.target).length==0){
        console.log("tree target not found");
        return null;
    }
    this.data = null;
    try{
        this.data = JSON.parse(config.data);
    }catch (ex){
        console.log(ex);
    }
    if(this.data==null){
        console.log("tree data not found");
        return null;
    }
    this.listen_arr = [];
    this.listen_arr.plusFun = config.plusFun?config.plusFun:null;
    this.listen_arr.subFun = config.subFun?config.subFun:null;
    this.listen_arr.selFun = config.selFun?config.selFun:null;
    this.listen_arr.addFun = config.addFun?config.addFun:null;
    this.listen_arr.editFun = config.editFun?config.editFun:null;
    this.listen_arr.delFun = config.delFun?config.delFun:null;
    this.activity_id = 0;
    this.tree_index = new Array();
    this.tree_html = "";
    var self = this;

    //get html by tree
    this.get_html=function(node,head){
        //console.log(head);
        var level = head.length;
        var html = '';
        var node_length = node.length;
        for(var i=0;i<node_length;i++){
            var node_item = node[i];
            var class_str = ' level'+level;
            var plus_str = '';
            var child_str = '';
            var is_last_node = false;
            if(i+1==node_length){
                class_str+=" is_last";
                is_last_node = true;
            }
            for(var j=1;j<=level;j++){
                var is_last_head = j==level;
                if(is_last_head){
                    if(is_last_node){
                        plus_str+='<img class="node_head node_branch_last" src="/static/images/none.png"/>';
                    }else{
                        plus_str+='<img class="node_head node_branch" src="/static/images/none.png"/>';
                    }
                }else{
                    if(head[j]){
                        plus_str+='<img class="node_head node_none" src="/static/images/none.png"/>';
                    }else{
                        plus_str+='<img class="node_head node_line" src="/static/images/none.png"/>';
                    }
                }
            }
            if(node_item.hasOwnProperty("child")){
                child_str+='<div class="child_list child_list'+node_item["id"]+' hide">';
                var head_sub = head.concat();
                head_sub.push(is_last_node);
                child_str+=self.get_html(node_item["child"],head_sub);
                child_str+='</div>';
                plus_str+='<img class="node_head node_plus" src="/static/images/none.png"/>';
            }else{
                class_str+=" is_leaf";
                child_str+='<div class="child_list child_list'+node_item["id"]+' hide">';
                child_str+='</div>';
                plus_str+='<img class="node_head node_leaf" src="/static/images/none.png"/>';
            }
            html+='<div node_id="'+node_item["id"]+'" class="node node'+node_item["id"];
            html+=class_str+'" node_id="'+node_item["id"]+'">';
            html+='<div class="node_item">';
            html+=plus_str;
            html+='<span class="node_name">'+node_item["struct_name"]+'</span>';
            if(node_item["id"]!=1){
                if(level<5){
                    html+='<img class="node_tool add" src="/systemsetting/images/add.png" />';
                }
                html+='<img class="node_tool info" src="/systemsetting/images/compile.png" />';
                html+='<img class="node_tool del" src="/systemsetting/images/del.png" />';
            }
            html+='</div>'+child_str+'</div>';
        }
        return html;
    };

    //load and listen
    this.listen=function(name,fun){
        if(this.listen_arr.hasOwnProperty(name)){
            this.listen_arr[name] = fun;
        }
    };
    this.update=function(){
        this.tree_html = '<div class="five_tree">'+this.get_html(this.data,[true])+'</div>';
        $(this.target).html(this.tree_html);
    };
    this.update();
    this.getItem=function(sel_lab){
        return $(sel_lab).parent().parent();
    };
    this.getId=function(sel_lab){
        return this.getItem(sel_lab).attr("node_id");
    };
    this.getActivityId=function(){
        return this.activity_id;
    };
    $(this.target).on('click','.node_plus',function(){
        var is_plus = !$(this).hasClass("node_sub");
        if(is_plus){
            self.getItem(this).children(".child_list").removeClass('hide');
            $(this).addClass('node_sub');
            if(self.listen_arr.subFun!=null){
                var id = self.getId(this);
                //console.log("sub",id);
                self.listen_arr.subFun(id);
            }
        }else{
            self.getItem(this).children(".child_list").addClass('hide');
            $(this).removeClass('node_sub');
            if(self.listen_arr.plusFun!=null){
                var id = self.getId(this);
                //console.log("plus",id);
                self.listen_arr.plusFun(id);
            }
        }
    });
    $(this.target).on('click','.node_name',function(){
        var id = self.getId(this);
        //console.log("sel",id);
        self.activity_id = id;
        $(self.target).find(".node_item").removeClass("activity");
        $(this).parent().addClass("activity");
        if(self.listen_arr.selFun!=null){
            self.listen_arr.selFun(id);
        }
    });
    $(this.target).on('click','.node .add',function(){
        if(self.listen_arr.addFun!=null){
            var id = self.getId(this);
            //console.log("add",id);
            self.listen_arr.addFun(id);
        }
    });
    $(this.target).on('click','.node .info',function(){
        if(self.listen_arr.editFun!=null){
            var id = self.getId(this);
            //console.log("info",id);
            self.listen_arr.editFun(id);
        }
    });
    $(this.target).on('click','.node .del',function(){
        if(self.listen_arr.delFun!=null){
            var id = self.getId(this);
            //console.log("del",id);
            self.listen_arr.delFun(id);
        }
    });
}
