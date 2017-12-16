console.log("首页测试");
//  设置
$(".c-target-set").click(function () {
	// body...
	console.log("首页测试");
	loadPage("/datacount/index/target_set","indexfr");
});
$(".datacount_index .back").click(function () {
	// body...
	console.log("回去");
	loadPage("/datacount/index/summary","indexfr");
});


function echartInit(){
	let val = $(".datacount_summary .echart-container select").val();
	console.log(val);
	let data = {
		"x":["上上上月","上上月","上月","本月","下月"],
		"series":[[50,200,360,100,100],[30,250,300,120,10],[60,20,360,10,100],[600,260,160,310,400]]
	};
	myChart.setOption({
        xAxis: {
            data: data.x
        },
        series: [{
            // 根据名字对应到相应的系列
            name: '有效通话',
            data: data.series[0]
        },{
            // 根据名字对应到相应的系列
            name: '商机',
            data: data.series[1]
        },{
            // 根据名字对应到相应的系列
            name: '拜访',
            data: data.series[2]
        },{
            // 根据名字对应到相应的系列
            name: '成单',
            data: data.series[3]
        }]
    });
	
}
echartInit();

function foo(){
	$.ajax({
	    type: 'POST',
	    // url: ,
	    data: val,
	    success: function(data) {
	        if (data.status) {
	            layer.msg(data.message, {icon: 1});
	            if(callback){
	                callback();
	            }
	        } else {
	            layer.msg(data.message, {icon: 2});
	        }
	    },
	    error: function() {
	        console.log('保存失败，未连接到服务器！');
	    }
	});
}