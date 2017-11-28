//查询点击
$(".phone_call_assistant button.tq-search").click(function(){
    let uin = $(".phone_call_assistant .tq-uin").val();
    let start = $(".phone_call_assistant .tq-start-time").val();
    let end = $(".phone_call_assistant .tq-end-time").val();
    let start1 = start.split("T").join(" ");
    let end1 = end.split("T").join(" ");
    console.log(start,end,typeof start,start1);
    $(".phone_call_assistant .tq-result .u-tabList").remove();
    let data = "func_name=getPhoneRecordByUin&params[]="+uin+"&params[]=[adminuin]&params[]=&params[]=[adminpassword]&params[]=&params[]="+start1+"&params[]="+end1+"&params[]=";
    tqWebservice(data,callBackGetPhoneRecordByUin);
});
//请求
function tqWebservice(data,callback){
    $.ajax({
        url: "/index/call/tq_webservice/",
        //"func_name="+fun+"&params[]=9797871&params[]=[adminuin]&params[]=[adminpassword]"
     　 data:data,
     　 type: "POST",
     　 dataType:'json',
     　 success: function(data) {
            // console.log(data,data.data);
            let d = data.data;
            let c = loadXML(d);
            let b = c.getElementsByTagName("Size")[0].firstChild.nodeValue;
            console.log(phoneRecordHandler(c));
            let result = phoneRecordHandler(c);
            result = JSON.stringify(result);
            console.log(result);
            let fun = callback;
            fun(result);
            console.log(callback);
            // callback(result);
            // console.log(c.getElementsByTagName("Size")[0].firstChild.nodeValue);
            },
            error: function(data,status){
            console.log(status);
        }
    });
}
function phoneRecordHandler(e){
    let record = e.getElementsByTagName("RECORD")[0];
    let size = record.getElementsByTagName("Size")[0].firstChild.nodeValue;
    let b = e.getElementsByTagName("Size")[0].firstChild.nodeValue;
    console.log(size);
    let obj = new Object();
    for(let i=1;i<=size;i++){
        let obj2 = new Object();
        let id = $(record).children("ID"+i);
        // obj2.id = $(id).text();
        // console.log(obj2);
        obj2.phoneRecId = $(id).children("PhoneRecId").text();//电话记录唯一ID

        obj2.uin = $(id).children("UIN").text();//所属坐席TQ号
        obj2.nickName = $(id).children("NickName").text();//
        obj2.admin_uin = $(id).children("Admin_uin").text();//管理员TQ
        obj2.insert_time = $(id).children("Insert_time").text();//电话开始呼叫时间，utc10位
        obj2.caller_id = $(id).children("Caller_id").text();//客户侧电话
        obj2.called_id = $(id).children("Called_id").text();//座席侧电话
        //呼叫方式 1:免费电话  2:400电话 3:外呼电话 4:直线呼入
        if($(id).children("Call_style").text()=="1"){
            obj2.call_style="免费电话";
        }else if($(id).children("Call_style").text()=="2"){
            obj2.call_style="400电话";
        }else if($(id).children("Call_style").text()=="3"){
            obj2.call_style="外呼电话";
        }else if($(id).children("Call_style").text()=="4"){
            obj2.call_style="直线呼入";
        }
        obj2.call_type = $(id).children("Call_type").text();//业务类别
        obj2.deal_state = $(id).children("Deal_state").text();//处理状态 0:未处理 1:处理
        obj2.resume = $(id).children("Resume").text();//该次电话备注

        obj2.visitor_entry = $(id).children("Visitor_entry").text();//初始来源(目前仅针对免费电话有效)
        obj2.visitor_last_page = $(id).children("Visitor_last_page").text();//上个页面(目前仅针对免费电话有效)
        obj2.visitor_comes = $(id).children("Visitor_comes").text();//来访次数(目前仅针对免费电话有效)
        obj2.client_uin = $(id).children("Client_uin").text();//客户UIN(目前仅针对免费电话有效)
        obj2.client_id = $(id).children("Client_id").text();//第三方ID(目前仅针对免费电话有效)
        obj2.rand = $(id).children("Rand").text();//唯一ID(目前仅针对免费电话有效)

        obj2.is_called_phone = $(id).children("Is_called_phone").text();//是否接通  1:接通  0、2、其他:未接通

        obj2.call_type_code = $(id).children("Call_type_code").text();//电话排队策略(目前仅针对免费电话有效)

        obj2.serialno = $(id).children("Serialno").text();//电话唯一ID

        obj2.caller_ip = $(id).children("Caller_ip").text();//呼叫电话IP(目前仅针对免费电话有效)
        obj2.serial_wiseid = $(id).children("Serial_wiseid").text();//录音wiseid(目前仅针对免费电话有效)

        obj2.recordFile = $(id).children("RecordFile").text();//录音URL链接
        //电话接听时间，utc10位 
        obj2.start_time =   transformDate($(id).children("Start_time").text());
        //电话挂机时间，utc10位
        obj2.end_time = transformDate($(id).children("End_time").text());
        obj2.queuename = $(id).children("Queuename").text();//呼入队列
        obj2.third_phone_id = $(id).children("Third_phone_id").text();//第三方记录ID；值不为空：第三方电话记录
        obj2.media_id = $(id).children("Media_id").text();//中继或第三方电话记录配置的电话媒体id
        obj2.area_id = $(id).children("Area_id").text();//号码归属地，格式为86751755，86为国际码，后面加0为归属地区号如：86-0751-0755
        obj2.area_name = $(id).children("Area_name").text();//号码归属地,如北京市固定电话
        obj2.duration = $(id).children("duration").text();//通话时长；格式为00:00:11
        obj2.satisfaction = $(id).children("Satisfaction_degree").text();//电话满意度评价id；满意度节点中的id值
        obj2.seatid = $(id).children("Seatid").text();//坐席工号
        obj2.pathway = $(id).children("Pathway").text();//呼叫途径 1：呼叫中心 2：工作手机
        obj2.dnis = $(id).children("Dnis").text();//中继号码
        obj2.caller_queue_time = $(id).children("Caller_queue_time").text();//进入队列时间；utc10位
        obj2.caller_stime = $(id).children("Caller_stime").text();//呼入系统应答时间；utc10位
        obj2.hangup_side = $(id).children("Hangup_side").text();//挂机方 1：座席侧 2：客户侧
        //电话开始时间；utc10位
        obj2.phone_create_time = transformDate($(id).children("Phone_create_time").text());
        //电话结束时间；utc10位
        obj2.phone_hangup_time = transformDate($(id).children("Phone_hangup_time").text());
        obj2.fsuniqueId = $(id).children("FsuniqueId").text();//电话记录事件唯一id
        // $(".phone_call_assistant .tq-result").append($(start_time).text()+"<br>");
        obj[i] = obj2;

    }
    // console.log(obj);
    return obj;
}
/*2.6.  查询电话记录*/
//2.6.1.    按座席TQ号查询：getPhoneRecordByUin
//参数名称[uin],[adminuin],username,[adminpassword],client_id,[startTime],[endTime],is_third
function callGetPhoneRecordByUin(){
    let uin = 9797871;
    let username = "guguo001";
    let startTime = "2017-11-20";
    let endTime = "2017-11-22";
    let data = "func_name=getPhoneRecordByUin&params[]="+uin+"&params[]=[adminuin]&params[]=&params[]=[adminpassword]&params[]=&params[]="+startTime+"&params[]="+endTime+"&params[]=";
    console.log(data);
    let callback =" callBackGetPhoneRecordByUin";
    tqWebservice(data,callback);
}
function callBackGetPhoneRecordByUin(arg){
	let e = JSON.parse(arg);
	console.log(e);
	let length = Object.keys(e).length;
	for(let i = 1;i<=length;i++){
		if(e[i].is_called_phone==0){
			$(".phone_call_assistant .tq-result .m-table").append('<ul class="u-tabList"><li class="u-tabCheckbox">'+i+'</li><li class="u-tabCilentName">中迅网媒</li><li>'+e[i].seatid+'</li><li>'+e[i].caller_id+'</li><li>'+e[i].call_style+'</li><li class="u-tq-time">未接通</li><li class="u-tq-time">'+e[i].end_time+'</li><li>----</li><li>----</li><div class="clearfix"></div></ul>');
		}else{
			$(".phone_call_assistant .tq-result .m-table").append('<ul class="u-tabList"><li class="u-tabCheckbox">'+i+'</li><li class="u-tabCilentName">中迅网媒</li><li>'+e[i].seatid+'</li><li>'+e[i].caller_id+'</li><li>'+e[i].call_style+'</li><li class="u-tq-time">'+e[i].start_time+'</li><li class="u-tq-time">'+e[i].end_time+'</li><li>'+e[i].duration+'</li><li>录音</li><div class="clearfix"></div></ul>');
		}		
	}
}
//2.6.2.    按电话记录ID查询：getPhoneRecordById
//[adminuin],[adminpassword],[id],uin
function callGetPhoneRecordById(){
	//清空列表
	$(".phone_call_assistant .tq-result .u-tabList").remove();
	
    let id = 146658993;
    let data = "func_name=getPhoneRecordById&params[]=[adminuin]&params[]=[adminpassword]&params[]="+id+"&params[]=";
    tqWebservice(data,callBackGetPhoneRecordById)
}
function callBackGetPhoneRecordById(arg){
	let e = JSON.parse(arg);
	console.log(e);
	let length = Object.keys(e).length;
	for(let i = 1;i<=length;i++){
		if(e[i].is_called_phone==0){
			$(".phone_call_assistant .tq-result .m-table").append('<ul class="u-tabList"><li class="u-tabCheckbox">'+i+'</li><li class="u-tabCilentName">中迅网媒</li><li>'+e[i].seatid+'</li><li>'+e[i].caller_id+'</li><li>'+e[i].call_style+'</li><li class="u-tq-time">未接通</li><li class="u-tq-time">'+e[i].end_time+'</li><li>----</li><li>----</li><div class="clearfix"></div></ul>');
		}else{
			$(".phone_call_assistant .tq-result .m-table").append('<ul class="u-tabList"><li class="u-tabCheckbox">'+i+'</li><li class="u-tabCilentName">中迅网媒</li><li>'+e[i].seatid+'</li><li>'+e[i].caller_id+'</li><li>'+e[i].call_style+'</li><li class="u-tq-time">'+e[i].start_time+'</li><li class="u-tq-time">'+e[i].end_time+'</li><li>'+e[i].duration+'</li><li>录音</li><div class="clearfix"></div></ul>');
		}		
	}
}