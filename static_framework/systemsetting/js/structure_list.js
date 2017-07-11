// +----------------------------------------------------------------------
// | 中迅传媒 [ 纯粹、极致到零 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.baidusd.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: blu10ph <blu10ph@gmail.com> <http://www.blu10ph.cn>
// +----------------------------------------------------------------------

$(document).ready(function() {
    $(".zk_1").click(function() {
        $(".zk_3").addClass('zk');
        $(".zk_1").toggleClass('zk_change1')
        $(".zk_2").toggleClass('zk');
        $(".zk_k").click(function(){
            $(this).toggleClass('zk_change');
            //					$(this).css('background','url(img/1.jpg) no-repeat 19px center');
            $(".zk_3").toggleClass('zk');
        });

    });
});
structure_tree.listen("selFun",function(id){console.log("hlselFun",id);});
structure_tree.listen("addFun",function(id){console.log("hladdFun",id);});
structure_tree.listen("editFun",function(id){console.log("hleditFun",id);});
structure_tree.listen("delFun",function(id){console.log("hldelFun",id);});
