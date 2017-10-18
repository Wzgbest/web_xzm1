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
    this.listen_arr.reloadFun = config.reloadFun?config.reloadFun:null;
    this.listen_arr.resetFun = config.resetFun?config.resetFun:null;
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
            var is_open = node_item["is_open"]==1;
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
            if(node_item.hasOwnProperty("child")&&node_item["child"].length>0){
                child_str+='<div class="child_list child_list'+node_item["id"]+' '+(is_open?"":"hide")+'">';
                var head_sub = head.concat();
                head_sub.push(is_last_node);
                child_str+=self.get_html(node_item["child"],head_sub);
                child_str+='</div>';
                plus_str+='<img class="node_head node_plus'+(is_open?" node_sub":"")+'" src="/static/images/none.png"/>';
            }else{
                class_str+=" is_leaf";
                child_str+='<div class="child_list child_list'+node_item["id"]+' '+(is_open?"":"hide")+'">';
                child_str+='</div>';
                plus_str+='<img class="node_head node_leaf" src="/static/images/none.png"/>';
            }
            html+='<div node_id="'+node_item["id"]+'" class="node node'+node_item["id"];
            html+=class_str+'" node_id="'+node_item["id"]+'">';
            html+='<div class="node_item node_item'+node_item["id"]+' '+(this.activity_id==node_item["id"]?'activity':'')+'">';
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

    this.open_item_to_data=function(id,data){
        if(data["id"]==id){
            data["is_open"] = 1;
            //$(this.target+" .node_item"+id+" .node_name").html(name);
        }else{
            for(var idx in data["child"]){
                this.open_item_to_data(id,data["child"][idx]);
            }
        }
    };

    this.close_item_to_data=function(id,data){
        if(data["id"]==id){
            data["is_open"] = 0;
            //$(this.target+" .node_item"+id+" .node_name").html(name);
        }else{
            for(var idx in data["child"]){
                this.close_item_to_data(id,data["child"][idx]);
            }
        }
    };

    this.add_item_to_data=function(id,pid,name,data){
        if(data["id"]==pid){
            //console.log('data["child"]',data["child"]);
            if(!data["child"]){
                data["child"]=[];
            }
            //console.log('data["child"]',data["child"]);
            data["child"].push({
                groupid:null,
                id:id,
                struct_en: null,
                struct_intro: null,
                struct_leader: null,
                struct_name: name,
                struct_pid: pid
            });
        }else{
            for(var idx in data["child"]){
                this.add_item_to_data(id,pid,name,data["child"][idx]);
            }
        }
    };
    this.update_item_to_data=function(id,name,data){
        if(data["id"]==id){
            data["struct_name"] = name;
        }else{
            for(var idx in data["child"]){
                this.update_item_to_data(id,name,data["child"][idx]);
            }
        }
    };
    this.del_item_to_data=function(id,data){
        for(var idx in data["child"]){
            if(data["child"][idx]["id"]==id){
                data["child"].splice(idx,1);
            }else{
                this.del_item_to_data(id,data["child"][idx]);
            }
        }
    };

    //load and listen
    this.listen=function(name,fun){
        if(this.listen_arr.hasOwnProperty(name)){
            this.listen_arr[name] = fun;
        }
    };
    this.reload=function(){
        console.log("reload");
        this.tree_html = '<div class="five_tree">'+this.get_html(this.data,[true])+'</div>';
        $(this.target).html(this.tree_html);
        if(self.listen_arr.reloadFun!=null){
            self.listen_arr.reloadFun();
        }
    };
    this.add=function(id,pid,name){
        console.log("add");
        this.add_item_to_data(id,pid,name,this.data[0]);
        this.reload();
    };
    this.update=function(id,name){
        console.log("update");
        this.update_item_to_data(id,name,this.data[0]);
        this.reload();
    };
    this.del=function(id){
        console.log("del");
        this.del_item_to_data(id,this.data[0]);
        this.reload();
        this.activity_id = 0;
        if(self.listen_arr.resetFun!=null){
            self.listen_arr.resetFun();
        }
    };
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
        var id = self.getId(this);
        if(is_plus){//open
            self.open_item_to_data(id,self.data[0]);
            self.getItem(this).children(".child_list").removeClass('hide');
            $(this).addClass('node_sub');
            if(self.listen_arr.subFun!=null){
                //console.log("sub",id);
                self.listen_arr.subFun(id);
            }
        }else{//close
            self.close_item_to_data(id,self.data[0]);
            self.getItem(this).children(".child_list").addClass('hide');
            $(this).removeClass('node_sub');
            if(self.listen_arr.plusFun!=null){
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



