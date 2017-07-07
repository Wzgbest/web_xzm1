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
    this.listen_arr.selFun = config.selFun?config.selFun:null;
    this.listen_arr.addFun = config.addFun?config.addFun:null;
    this.listen_arr.editFun = config.editFun?config.editFun:null;
    this.listen_arr.delFun = config.delFun?config.delFun:null;
    this.tree_index = new Array();
    this.tree_html = "";
    var self = this;

    //get html by tree
    this.get_html=function(node){
        var html = '<div class="five_tree">';
        var lenght = node.length;
        for(var i=0;i<lenght;i++){
            var sub_node = node[i];
            var class_str = '';
            var child_str = '';
            if(i==lenght){
                class_str+=" is_last";
            }
            html+='<div class="node node'+sub_node["id"];
            html+=class_str+'" node_id="'+sub_node["id"]+'">';
            html+='<div class="node_item">';
            if(sub_node.hasOwnProperty("child")){
                child_str+='<div class="child_list child_list'+sub_node["id"]+' hide">';
                child_str+=self.get_html(sub_node["child"]);
                child_str+='</div>';
                html+="<i class='node_plus'>+</i>";
            }
            html+='<span class="node_name">'+sub_node["struct_name"]+'</span>';
            html+='<img class="node_tool add" src="/systemsetting/images/add.png" />';
            html+='<img class="node_tool info" src="/systemsetting/images/compile.png" />';
            html+='<img class="node_tool del" src="/systemsetting/images/del.png" /></div>';
            html+=child_str+'</div>';
        }
        html+='</div>';
        return html;
    };

    //load and listen
    this.listen=function(name,fun){
        if(this.listen_arr.hasOwnProperty(name)){
            this.listen_arr[name] = fun;
        }
    };
    this.update=function(){
        this.tree_html = this.get_html(this.data,1);
        $(this.target).html(this.tree_html);
    };
    this.update();
    $(this.target).on('click','.node_plus',function(){
        $(this).parent().parent().children(".child_list").toggleClass('hide');
    });
    $(this.target).on('click','.node_name',function(){
        if(self.listen_arr.selFun!=null){
            var id = $(this).parent().parent().attr("node_id");
            console.log("sel",id);
            self.listen_arr.selFun(id);
        }
    });
    $(this.target).on('click','.node .add',function(){
        if(self.listen_arr.addFun!=null){
            var id = $(this).parent().parent().attr("node_id");
            console.log("add",id);
            self.listen_arr.addFun(id);
        }
    });
    $(this.target).on('click','.node .info',function(){
        if(self.listen_arr.editFun!=null){
            var id = $(this).parent().parent().attr("node_id");
            console.log("info",id);
            self.listen_arr.editFun(id);
        }
    });
    $(this.target).on('click','.node .del',function(){
        if(self.listen_arr.delFun!=null){
            var id = $(this).parent().parent().attr("node_id");
            console.log("del",id);
            self.listen_arr.delFun(id);
        }
    });
}
