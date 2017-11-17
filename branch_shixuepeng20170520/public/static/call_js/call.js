var common = {
    $: function(id) {
        return document.getElementById(id);
    },
    $p: function(id) {
        return window.parent.document.getElementById(id);
    },
    print: function(ret, id) {
        var li = common.ctag("li");
        li.innerHTML = new Date().toLocaleTimeString() + " : " + JSON.stringify(ret);
        var ws = common.$p(id);
        if (ws)
            ws.insertBefore(li, ws.childNodes[0]);
    },
    ctag: function(tag) {
        return document.createElement(tag);
    },
    $tag: function(ele, tag) {
        return ele.getElementsByTagName(tag)
    },
    hasCls: function(ele, cls) {
        return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
    },
    addCls: function(ele, cls) {
        if (!this.hasCls(ele, cls))
            ele.className += " " + cls;
    },
    remCls: function(ele, cls) {
        if (this.hasCls(ele, cls)) {
            var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
            ele.className = ele.className.replace(reg, ' ');
        }
    },
    chaCls: function(ele, oldC, newC) {
        this.remCls(ele, oldC);
        this.addCls(ele, newC);
    },
    setStyle: function(ele, name, value) {
        // console.log(ele,name,value);
        ele.style[name] = value;
    },
    show: function(ele) {
        this.setStyle(ele, "display", "block")
    },
    hide: function(ele) {
        this.setStyle(ele, "display", "none")
    },
    active: function(ele) {
        this.remCls(ele, "disable");
        this.addCls(ele, "active");
    },
    disable: function(ele) {
        this.remCls(ele, "active");
        this.addCls(ele, "disable");
    },
    getEParam: function(key, eventStr) {
        var _p = eventStr.split("|"), _v = "";
        for (var i = 0; i < _p.length; i++) {
            if (_p[i].split("=")[0] == key) {
                _v = _p[i].split("=")[1];
                break;
            }
        }
        return _v
    }
};
/*
 * cells 页面数据填充对象集合
 */
var cells = {
    FC: common.$("fc"),// 通话1区
    /* 通话1区 */
    FcItem: {
        Dials: common.$("fc_dials"),// 拨号盘
        CallInfo: common.$("fc_callinfo"),// 通话基本信息区
        // AreaTip: common.$("fc_tip").firstChild,// 归属地提示区
        AreaTip: common.$("fc_gsd"),// 归属地提示区
        // CallTip: common.$("fc_tip").childNodes[1],// 通话状态提示区
        CallTip: common.$("fc_status"),// 通话状态提示区
        timing: common.$("fc_timing"),// 计时区
        Import: common.$("fc_import"),// 号码输入框所在容器
        ImportInput: common.$("fc_import").firstChild,// 号码输入框
        CallBt: common.$("fc_callbt"),// 拨打/接听(通话1区)按钮
        Calling: common.$("fc_calling"),// 拨打(通话1区)按钮激活时过渡
        HangBt: common.$("fc_hangbt"),// 挂断(通话1区)按钮
        Hanguping: common.$("fc_hangbt").nextSibling,// 挂断(通话1区)按钮激活时过渡
        phoneReset: common.$("phonereset"),// 复位按钮

        MultiBt: common.$("fc_multibt"),// 多方通话按钮
        KeepBt: common.$("fc_keepbt"),// 保持按钮
        AskBt: common.$("fc_askbt"),// 咨询按钮
        DevolveBt: common.$("fc_devolvebt")// 转移按钮
    }
};
/*
 * timing 计时 目前只在第一个电话区域有显示
 */
var timing = function() {
    this.obj = cells.FcItem.timing;
    this.startTime = 0;
    this.interval = false;
    this.start = function() {
        var hours = parseInt(this.startTime / 60 / 60, 10);
        var mins = parseInt(this.startTime / 60 % 60, 10);
        var secs = parseInt(this.startTime % 60, 10);
        hours = hours.toString().length < 2 ? "0" + hours : hours;
        mins = mins.toString().length < 2 ? "0" + mins : mins;
        secs = secs.toString().length < 2 ? "0" + secs : secs;
        this.obj.innerHTML = hours + ":" + mins + ":" + secs;
        this.startTime += 1;
    };
    this.stop = function() {
        if (this.interval == false)
            return;
        var t = this;
        window.clearInterval(t.interval);
        this.obj.innerHTML = "";
    };
    this.init = function(startTime) {// startTime 微秒
        this.stop();
        this.startTime = 0;
        var t = this;
        this.interval = setInterval(function() {
            t.start()
        }, 1000);
    };
};

/*
 * 初始化global
 */
function initGlobal() {
    global = {
        extensionInfo: {
            seat_dbid: "",
            currentState: "",
            curCaller_id: "",// 当前caller_id
            timestamp: 0
        // 当前消息时间戳
        }
    };
    console.log(global);
};
initGlobal(); // 当电话在电话工具条中区域位置变更时，需调用此函数

TQPhone = function(phoneSeatMessage) {
    var seat_dbid = phoneSeatMessage.seat_dbid;
    this.phone = global.extensionInfo[seat_dbid];
    this.seat_dbid = seat_dbid;
    this.pos = "";
    this.C();
};
TQPhone.prototype = {
    C: function() {
        // 设置电话UI区域信息等
        this.pos = cells.FcItem;
        common.addCls(cells.FC, "big");
        this.noCallMode = function() {
            common.chaCls(this.pos.CallInfo.parentNode, "firstcall", "nocall");
            common.hide(this.pos.CallInfo);
            common.show(this.pos.Import);
            console.log("noCallMode",this.pos.CallInfo.parentNode);
        };
        this.callingMode = function() {
            common.chaCls(this.pos.CallInfo.parentNode, "nocall", "firstcall");
            common.show(this.pos.CallInfo);
            common.hide(this.pos.Import);
             console.log("callingMode",this.pos);
        };
    },
    setPhone: function() {

    },
    setStatus: function() {

    },
    /* 转移按钮状态 */
    setDevolveStatus: function(_devolve) {
        switch (parseInt(_devolve, 10)) {
            case 0:
                common.disable(this.pos.DevolveBt);
                // common.active(this.pos.DevolveBt);
                console.log("转移按钮状态0disable",this.pos.DevolveBt);
                break;
            case 1:
                common.active(this.pos.DevolveBt);
                console.log("转移按钮状态 1active",this.pos.DevolveBt);
                break;
        }
        ;
    },
    /* 咨询按钮状态 */
    setAskStatus: function(_ask) {
        switch (parseInt(_ask, 10)) {
            case 0:
                common.disable(this.pos.AskBt);
                console.log("咨询按钮状态0disable",this.pos.AskBt);
                break;
            case 1:
                common.active(this.pos.AskBt);
                console.log("咨询按钮状态1active",this.pos.AskBt);
                break;
        }
        ;
    },
    /* 保持按钮状态 */
    setKeepStatus: function(_keep) {
        switch (parseInt(_keep, 10)) {
            case 0:
                common.disable(this.pos.KeepBt);
                console.log("保持按钮状态0disable",this.pos.KeepBt);
                break;
            case 1:
                common.active(this.pos.KeepBt);
                console.log("保持按钮状态1active",this.pos.KeepBt);
                break;
        }
        ;
    },
    /* 多方会话按钮状态 */
    setMultiStatus: function(_multi) {
        switch (parseInt(_multi, 10)) {
            case 0:
                common.disable(this.pos.MultiBt);
                console.log("多方会话按钮状态0disable",this.pos.MultiBt);
                break;
            case 1:
                common.active(this.pos.MultiBt);
                console.log("多方会话按钮状态1active",this.pos.MultiBt);
                break;
        }
        ;
    },
    /*
     * ringMode 响铃时处理(仅电话1区)
     */
    ringMode: function() {
        console.log("B");
        // this.setPhoneArea();
        console.log("B");
        common.hide(this.pos.Calling);
        console.log("B");
        common.show(this.pos.CallBt);
        console.log("B");
    },
    /*
     * teamCalling 内部拨打分机响铃时处理
     */
    teamExtCalling: function() {
        common.hide(this.pos.Calling);
        common.show(this.pos.CallBt);
    },
    /*
     * 内部业务电话信息提示
     */
    teamTip: function(tip, cardInfo) {
        cardInfo ? this.pos.CallInfo.innerHTML = cardInfo : "";
        this.pos.CallTip.innerHTML = tip;// 提示区
        this.setTiming(this.phone.StartTime);// 计时
    },
    /*
     * hangMode 挂机时处理
     */
    hangMode: function() {
        this.clearPhoneArea();
        common.hide(this.pos.Hanguping);
        common.show(this.pos.HangBt);
    },
    /*
     * setPhoneArea 设置号码归属地
     */
    setPhoneArea: function() {
        console.log(phoneLocalUrl);
        if (phoneLocalUrl) {
            common.show(this.pos.AreaTip);
            AreaTipObj = this.pos.AreaTip;
        }
        ;
        if (this.phone.clientEncNum || this.phone.clientNum) {
            setPhoneArea(this.phone.clientEncNum || this.phone.clientNum);
        }
        else {
            setPhoneArea(this.pos.ImportInput.value);
            this.pos.CallInfo.innerHTML = this.pos.ImportInput.value
        }
        ;
    },
    /*
     * clearPhoneArea 清除号码归属地
     */
    clearPhoneArea: function() {
        common.hide(this.pos.AreaTip);
        this.pos.AreaTip.innerHTML = "";
        this.pos.AreaTip.title = "";
    },
    /*
     * 电话信息提示
     */
    setTip: function(tip, notiming) {
        console.log("setTip: ",this.pos);
        this.pos.CallInfo.innerHTML = this.phone.clientNum || "";// 信息区显示客户号码
        this.pos.CallTip.innerHTML = tip;// 提示区
        if (notiming)
            return;
        this.setTiming(this.phone.StartTime);// 计时
    },
    /*
     * 电话状态持续时间
     */
    setTiming: function(start) {
        var start = isNaN(parseInt(start, 10)) ? 0 : parseInt(start, 10);
        InitTiming.init(start);
    }
}
var InitTiming = new timing(); // 初始化计时
/*
 * 点击拨号/取号拨号 回填接口
 */

function dialNumberEx(a) {
    if (common.hasCls(cells.FcItem.CallBt, "calloutbt")) {// 拨号按钮
        if (a.length != 33) {// 不显示加密号码
            cells.FcItem.ImportInput.className = "numinput focus";
            cells.FcItem.ImportInput.value = a;
        }
        common.show(cells.FcItem.Calling);
        common.hide(cells.FcItem.CallBt);
    }
}
/*
 * 拨号盘接口
 */
function dialNumber(a) {
    if (!common.hasCls(cells.FcItem.CallInfo.parentNode, "nocall"))
        return;
    var asc = a.charCodeAt(0);
    switch (asc) {
        case 8:
            cells.FcItem.ImportInput.value =
                    cells.FcItem.ImportInput.value.substring(0, cells.FcItem.ImportInput.value.length - 1)
            break;
        case 12:
            cells.FcItem.ImportInput.value = "";
            break;
        default:
            cells.FcItem.ImportInput.value += a + "";
    }
}

/**
 * hangUpCaseNoList
 */
var hangUpCaseNoList = function() {
    return [{
        "key": "USER_BUSY",
        "value": "话机可能处于摘机状态"
    }];
};

/**
 * phoneStatusNoList
 */
var phoneStatusNoList = function() {
    return [{
        "key": "Available",
        "value": "hand_idle"
    }, {
        "key": "On Break",
        "value": "hand_busy"
    }, {
        "key": "Break",
        "value": "hand_refuse"
    }, {
        "key": "Logged-Out",
        "value": "hand_offline"
    }];
};
/**
 * 所有枚举对应关系 hangUpCaseNoList : hangup_cause对应枚举列表
 */
var allNoList = {
    "hangUpCaseNoList": hangUpCaseNoList(),// hangup对应关系
    "phoneStatusNoList": phoneStatusNoList()
// 通话过程中挂机重登客户端init方法phoneStatus
};
/**
 * 根据枚举项(property)-键值(key)获取描述(value) eg :
 * getvaluebykey("hangUpCaseNoList","USER_BUSY")
 */
var getValueByKey = function(property, key) {
    var noList = allNoList[property];
    for ( var i in noList) {
        if (noList[i].key == key) {
            console.log(noList[i].value);
            return noList[i].value;
        }
    }
    return "";
};
/**
 * 重命名函数
 * 
 * @return 返回单一入口 demo = browerify
 */
var demo = browserfly.noConflict();
console.log(demo);
var successCallBack = function() {
    console.log("初始化完成");
    console.log(demo);
    common.print("初始化完成....","ws");
};
var errorCallBack = function(ret) {
    console.log("初始化失败",ret);
    cells.FcItem["CallTip"].innerHTML = ret.errmsg;
    common.print(ret,"ws");
    // console.log(ret);
};
/**
 * 初始化配置
 * 
 * @param {Object} initOptions 初始化参数json格式
 */
demo.init(initOptions);

/**
 * 初始化配置成功回调
 * 
 * @param {Function} successCallBack 初始化成功回调函数
 */
demo.success(successCallBack);

/**
 * 初始化配置错误回调
 * 
 * @param {Function} errorCallBack 初始化失败回调函数，输出失败原因
 */
demo.error(errorCallBack);
/*
 * operation 电话操作
 */
var operation = {
    /*
     * callBt 电话拨打(接听)按钮点击函数
     */
    callBt:function(obj){
        // var callInput = obj.parentNode.previousSibling.firstChild;
        // var callNumber = callInput.value;
        var callInput = document.getElementById("callInput");
        var callNumber = callInput.value;
        if(callNumber.length>6&&callNumber.indexOf("****")!=-1&&callNumber.indexOf("*****")==-1&&clientEncNum!=false){
            callNumber = clientEncNum;
            console.log(callNumber);
        }else{
            callNumber=="点此输入号码"?callNumber="":"";
            if(callNumber&&callNumber.length != 33&&this.phoneRule.test(callNumber)){
                callNumber = callNumber.replace(this.phoneRule,'');
                callInput.value = callNumber;
            };
            if(!callNumber){alert("请输入电话号码");callInput.focus();return};
             console.log(callNumber);
        }
        var makeCallOption = {
              phone: callNumber, // 电话号码
              error: function(ret) {
                  cells.FcItem.CallTip.innerHTML = "<b style='color:#275A33'>拨打错误,"+ret.errmsg+"</b>";
                  console.log(JSON.stringify(ret),ret);
              },
              success: function(ret) {
                  cells.FcItem.CallTip.innerHTML = "<b style='color:#275A33'>正在拨打,请稍后...</b>";
                  console.log(cells);
                  
                  // console.log($(obj));
                  // console.log($(obj).next());
                  // common.show(obj.nextSibling);
                  // console.log(obj);
                  // common.hide(obj);
              }
        };
        var makeCallCallBack = function(ret, jsonObject) {
            console.log(ret); 
            common.print(ret,"ws");
        };
        $(".phone-box h1.call-number").text(callNumber);
        // console.log($(".phone-box h1.call-number"));
        demo.invokeEvent("makecall", makeCallOption, makeCallCallBack);   
    },

    /*
     * hangBt 电话挂断按钮点击函数
     */
    hangBt:function(obj){// obj:拨打按钮对象
        if(common.hasCls(obj,"hangupdsbt")){
            return
        }else{
            console.log("hangBt");
            this.hangup(obj);
            // common.show(obj.nextSibling);
            // common.hide(obj);
        }
    },
    hangup:function(obj){
        console.log("hangup");
        var hangupOption = {
            error: function(ret) {
                console.log(ret);
                common.print(ret,"ws");
            },
            success: function(ret) {
                console.log(ret);
                common.print(ret,"ws");
            }
        };
        var hangupCallBack = function(ret, jsonObject) {
            common.print(ret,"ws");
        };
        demo.invokeEvent("hangup", hangupOption, hangupCallBack); 
    },
    /*
     * devolveBt 转移按钮点击函数
     */
    devolveBt:function(obj){
        if(common.hasCls(cells.FcItem.DevolveBt,"disable")){
            return;
        }
        var dest=prompt("请输入坐席工号或者按键标识");
        if(dest==null||dest==""){
            alert("请输入坐席工号或者按键标识!");
            return;
        }
        var transferOption = {
          dest: dest,
          error: function(ret) {
              common.print(ret,"ws"); 
          },
          success: function(ret) {
              common.print(ret,"ws");
          }
      };
      var transferCallBack = function(ret, jsonObject) {
          common.print(ret,"ws");
      };
      demo.invokeEvent("transfer", transferOption, transferCallBack);
    },
    /*
     * askBt 咨询
     */
    askBt:function(obj){
        if(common.hasCls(cells.FcItem.AskBt,"disable")){
            return;
        }
        var dest=prompt("请输入坐席工号或者按键标识");
        if(dest==null||dest==""){
            alert("请输入坐席工号或者按键标识!");
            return;
        }
        var consultOption = {
            dest: dest,
            error: function(ret) {
                common.print(ret,"ws");
            },
            success: function(ret) {
                common.print(ret,"ws");
            }
        };
        var consultCallBack = function(ret, jsonObject) {
            common.print(ret,"ws");
        };
        demo.invokeEvent("consult", consultOption, consultCallBack);
    },
    /*
     * keepBt 保持
     */
    keepBt:function(obj,key){
        if(common.hasCls(cells.FcItem.KeepBt,"disable")){
            return;
        }
         var holdOption = {
              error: function(ret) {
                  common.print(ret,"ws");
              },
              success: function(ret) {
                  common.print(ret,"ws");
              }
          };
          var holdCallBack = function(ret, jsonObject) {
              common.print(ret,"ws");
          };
          demo.invokeEvent("hold", holdOption, holdCallBack);
    },
    /*
     * multiBt 多方通话按钮点击函数
     */
    multiBt:function(callerId){

    },
    /*
     * keydownFn 号码输入框按键操作
     */
    keydownFn:function(e,obj){
        var e = ("undefined"!=typeof(event))?event:window.event;
        e = e?e:arguments.callee.caller.arguments[0]
        if(!e)return;
        switch(e.keyCode){
            case 27:// Esc
                obj.value = ""; 
                break;
            case 13:// enter
                this.callBt(obj.parentNode.nextSibling.firstChild);
                break;
        }
    },
    phoneRule: new RegExp("[^\\d#|\*]","g"),
    mobileRule: new RegExp("[^\\d]","g"),
    /*
     * keyupFn 号码输入框按键操作
     */
    keyupFn:function(obj){
        this.phoneRule.test(obj.value) ? obj.value = obj.value.replace(this.phoneRule,''):"";
    },
    /*
     * onfocusFn 号码输入框聚焦操作
     */
    onfocusFn:function(obj){
        obj.value.indexOf("*")!=-1?obj.value = "":"";
    },
    
    /*
     * phoneResetBt 电话复位按钮点击函数
     */
    phoneResetBt:function(obj){     
        cells.FcItem.CallTip.innerHTML = "正在复位,请稍后...";
        var seat_dbid = obj.getAttribute("seat_dbid");
        if(global.extensionInfo[seat_dbid]){
            delete global.extensionInfo[seat_dbid];
        }
        demo.reinit(initOptions);
    }
};
/**
 * 监听座席状态
 * 
 * @param {string} "seatState" 座席状态标识,固定值
 * @param {Function} 监听成功的回调函数
 */
demo.monitorEvent("seatState", function(message, jsonObject) {
    console.log(message,jsonObject);
    common.print(message,"ws");
    var seat_dbid = message.phoneseat.seat_dbid;
    var phoneSeatMessage = message.phoneseat;
    var timestamp = phoneSeatMessage.timestamp;
    var status = phoneSeatMessage.status;
    console.log(seat_dbid,phoneSeatMessage,timestamp,status);
    // 初始化一次
    if(!global.extensionInfo[seat_dbid]){
        console.log("初始化一次");
        global.extensionInfo[seat_dbid] = {
           "seat_dbid": seat_dbid
       };
       global.extensionInfo[seat_dbid].callUI = new TQPhone(phoneSeatMessage); 
       console.log("初始化一次");
    }
    var seat_status_value = getValueByKey("phoneStatusNoList", status);
    console.log(seat_status_value);
    console.log("eventState." + seat_status_value + "('" + JSON.stringify(phoneSeatMessage) + "')");
    eval("eventState." + seat_status_value + "('" + JSON.stringify(phoneSeatMessage) + "')");
    
    /*
     * eval("eventState.hand_busy('" +
     * '{"phoneseat":{"lastchange":1508322086,"seat_dbid":28921,"status":"On-Break","timestamp":1508377698733035}}' +
     * "')"); eval("eventState.caller_answer" + "('" +
     * '{"call_id":"a951473e-b3e7-11e7-96f4-5521a76fa011","call_state":"caller_answer","seat_dbid":28921,"timestamp":1509319335273044}' +
     * "')");
     */
});
/**
 * 监听电话事件
 * 
 * @param {string} "callEvent" 电话事件标识,固定值
 * @param {Function} 监听成功的回调函数
 */
demo.monitorEvent("callEvent", function(message, jsonObject) {
    console.log("callEvent",message,jsonObject);
    common.print(message,"ws");
    var call_state = message.call_event.call_state;
    console.log("eventState." + call_state + "('" + JSON.stringify(message.call_event) + "')");
    eval("eventState." + call_state + "('" + JSON.stringify(message.call_event) + "')");
});
/**
 * 监听弹屏事件
 * 
 * @param {string} "callTip" 监听弹屏标识,固定值
 * @param {Function} 监听成功的回调函数
 */
demo.monitorEvent("callTip", function(message, jsonObject) {
    console.log("该事件在座席响铃事件（call_state：agent_ring）时推送callTip",message,jsonObject);
    common.print(message,"ws");
})

