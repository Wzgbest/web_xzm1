fs_state = function(){
		var phoneMsg = function(args){
		    var seat_dbid = args.seat_dbid;
			var phone = global.extensionInfo[seat_dbid];
			/*if(args.call_id&&phone){
			    //绑定
    			  phone.callUI.pos.HangBt.setAttribute("call_id", args.call_id);
    			  phone.callUI.pos.CallBt.setAttribute("call_id", args.call_id);
    			  phone.callUI.pos.DevolveBt.setAttribute("call_id", args.call_id);
    			  phone.callUI.pos.AskBt.setAttribute("call_id", args.call_id);
    			  phone.callUI.pos.KeepBt.setAttribute("call_id", args.call_id);
			}*/
			
			if(args.seat_dbid&&phone){
                //绑定
                  phone.callUI.pos.phoneReset.setAttribute("seat_dbid", args.seat_dbid);
            }
			if (!phone){
			    return;
			}
			
			if(phone["timestamp"]&&args.timestamp<phone["timestamp"]){// 过期消息不处理
			    return;
			}else{
			    phone["timestamp"] = args.timestamp;
			}
			return phone ;
	  };

	  var operationUI = {
	  		idle : function(ui){
	  			console.log("电话空闲中");
	  			ui.setTip("<b style='color:#275A33'>电话空闲中</b>");
				ui.pos.CallBt.className = "bkg on";
				ui.pos.HangBt.className = "hangupdsbt bkg off hide";
				ui.pos.CallBt.title = "点击拨打";
				ui.noCallMode();
				ui.setDevolveStatus(0);
				ui.setAskStatus(0);
				ui.setKeepStatus(0);
				ui.setMultiStatus(0);
				// ui.clearPhoneArea();// 去掉归属地显示
	  		},
	  		busy : function(ui){
	  			console.log("通话中");
				ui.setTip("<b style='color:#275A33'>通话中</b>");
				ui.pos.CallBt.className = "calloutbt bkg";
				ui.pos.HangBt.className = "hangupdsbt bkg";
				ui.pos.CallBt.title = "点击拨打";
				ui.callingMode();
				ui.setDevolveStatus(1);
				ui.setAskStatus(1);
				ui.setKeepStatus(1);
				ui.setMultiStatus(1);
				// ui.clearPhoneArea();// 去掉归属地显示
	  		},
	  		refuse : function(ui){
	  			console.log("电话已拒接,可点击外呼");
	  		  ui.setTip("<b style='color:#275A33'>电话已拒接,可点击外呼</b>");
              ui.pos.CallBt.className = "calloutbt bkg";
              ui.pos.HangBt.className = "hangupdsbt bkg";
              ui.pos.CallBt.title = "点击拨打";
              ui.noCallMode();
              ui.setDevolveStatus(0);
              ui.setAskStatus(0);
              ui.setKeepStatus(0);
              ui.setMultiStatus(0);
            },
	  		offline : function(ui){
	  			console.log("电话已离线,不可接打电话");
	  			ui.setTip("<b>电话已离线,不可接打电话</b>");
				ui.pos.CallBt.className = "calldsbt bkg";
				ui.pos.HangBt.className = "hangupdsbt bkg";
				ui.pos.CallBt.title = "电话不可用,此按钮无效";
				ui.noCallMode();
				ui.setDevolveStatus(0);
				ui.setAskStatus(0);
				ui.setKeepStatus(0);
				ui.setMultiStatus(0);
				// ui.clearPhoneArea();// 去掉归属地显示
	  		},
	  		agent_create : function(ui){
	  			console.log(ui,"agent_create");
	  			ui.pos.CallBt.className = ui.phone.phoneType == 3?"answerbt bkg":"calldsbt bkg";
	  			console.log("A");
				ui.pos.HangBt.className = "hangupbt bkg";
				console.log("A");
				ui.pos.CallBt.title = ui.phone.phoneType == 3?"点击接听":"请拿起话机接听,当前电话类型不支持点击接听";
				console.log("A");
				ui.callingMode();
				console.log("A");
				ui.setDevolveStatus(0);
				console.log("A");
				ui.setAskStatus(0);
				console.log("A");
				ui.setKeepStatus(0);
				console.log("A");
				ui.setMultiStatus(0);
				console.log("A");
				ui.setTip("(&nbsp;<b>请接听</b>)"),ui.ringMode();
				console.log("A");
	  		},
	  		agent_ring : function(ui){
	  			console.log("agent_ring");
	  			ui.setTip("(&nbsp;<b>请接听</b>)");
				ui.pos.CallBt.className = ui.phone.phoneType == 3?"answerbt bkg":"calldsbt bkg";
				ui.pos.HangBt.className = "hangupbt bkg";
				ui.pos.CallBt.title = ui.phone.phoneType == 3?"点击接听":"请拿起话机接听,当前电话类型不支持点击接听";
				ui.callingMode();
				ui.setDevolveStatus(0);
                ui.setAskStatus(0);
                ui.setKeepStatus(0);
                ui.setMultiStatus(0);
				ui.ringMode();
	  		},
	  		agent_answer : function(ui){
	  			console.log("agent_answer");
	  			ui.pos.CallBt.className = "answerdsbt bkg";
				ui.pos.HangBt.className = "hangupbt bkg";
				ui.pos.CallBt.title = "通话中,此按钮无效";
				ui.callingMode();
				ui.setDevolveStatus(1);
                ui.setAskStatus(1);
                ui.setKeepStatus(1);
                ui.setMultiStatus(1);
                ui.ringMode();
	  		},
	  		agent_hangup : function(ui){
	  			console.log("agent_hangup");
	  			ui.hangMode();
	  		},
	  		caller_create :function(ui){
	  			console.log("caller_create");
	  			if(ui.phone.callType == "caller_trunk"||(ui.phone.callType=="fs_agent"&&ui.phone.preState == "agent_create"))return; // 点击外呼(直线上线)特殊处理
	  			ui.setTip("(&nbsp;<b>等待对方接听</b>)");
				ui.pos.CallBt.className = "calldsbt bkg";
				ui.pos.HangBt.className = "hangupbt bkg";
				ui.pos.CallBt.title = "等待对方接听,此按钮无效";
				ui.callingMode();
				ui.setDevolveStatus(0);
				ui.setAskStatus(0);
				ui.setKeepStatus(0);
				ui.setMultiStatus(0);
				ui.ringMode();
	  		},
	  		caller_answer : function(ui){
	  			console.log("caller_answer");
				ui.setTip("(&nbsp;<b>通话中</b>)");
				ui.pos.CallBt.className = "calldsbt bkg";
				ui.pos.HangBt.className = "hangupbt bkg";
				ui.pos.CallBt.title = "通话中,此按钮无效";
				ui.callingMode();
				ui.setDevolveStatus(1);
				ui.setAskStatus(1);
				ui.setKeepStatus(1);
				ui.setMultiStatus(1);
				ui.ringMode();
	  		},
	  		caller_hangup : function(ui){
	  			console.log("caller_hangup");
	  			// ui.hangMode();
				// this.clearPhoneArea();//去掉归属地显示
				// ui.phone.callState = ui.phone.initState;//||"idle";
				// -----ui.setStatus();
	  		},
	  		caller_ring : function(ui){
	  			console.log("caller_ring");
	  			ui.setTip("(&nbsp;<b>等待对方接听</b>)");
				ui.pos.CallBt.className = "calldsbt bkg";
				ui.pos.HangBt.className = "hangupbt bkg";
				ui.pos.CallBt.title = "等待对方接听,此按钮无效";
				ui.callingMode();
				ui.setDevolveStatus(0);
				ui.setAskStatus(0);
				ui.setKeepStatus(0);
				ui.setMultiStatus(0);
				ui.ringMode();
	  		}
	  };
	  /** * */
	eventState = StateMachine.create({
		// 闲(idle) 等待客户接听(caller_delay) 请拿起话机（agent_delay） 正在通话(agent_callBusy)
        // offline(离线)
		// agent_create 客户呼入/点击外呼,座席响铃
		// agent_answer 座席应答
		// agent_ring 座席响铃
		// agent_hangup 座席挂机
 
		// caller_create 座席呼出,客户响铃
		// caller_answer 客户应答
		// caller_hangup 客户挂机
		// caller_ring 客户响铃
		target: this,
		initial: { state: 'none' },
		events: [
                { name: 'none', from: 'none', to: 'idle' },
                { name: 'agent_create', from: 'busy',  to: 'busy' },
                { name: 'agent_create', from: 'idle',  to: 'busy' },
                { name: 'agent_create', from: 'none',  to: 'busy' },
                { name: 'agent_create', from: 'offline',  to: 'busy' },
                
                { name: 'agent_ring', from: 'idle',  to: 'busy' },
                { name: 'agent_ring', from: 'busy',  to: 'busy' },
                { name: 'agent_ring', from: 'none',  to: 'busy' },
                { name: 'agent_ring', from: 'offline',  to: 'busy' },
                
                { name: 'agent_answer', from: 'busy',  to: 'busy' },
                { name: 'agent_answer', from: 'idle',  to: 'busy' },
                { name: 'agent_answer', from: 'none',  to: 'busy' },
                { name: 'agent_answer', from: 'offline',  to: 'busy' },
                
                { name: 'agent_hangup', from: 'xx',  to: 'xx' },
                // { name: 'agent_hangup', from: 'idle', to: 'idle' },
                // { name: 'agent_hangup', from: 'offline', to: 'offline' },
                
                { name: 'caller_create', from: 'idle',  to: 'busy' },
                { name: 'caller_create', from: 'busy',  to: 'busy' },
                { name: 'caller_create', from: 'none',  to: 'busy' },
                { name: 'caller_create', from: 'offline',  to: 'busy' },
                
                { name: 'caller_ring', from: 'busy',  to: 'busy' },
                { name: 'caller_ring', from: 'idle',  to: 'busy' },
                { name: 'caller_ring', from: 'none',  to: 'busy' },
                { name: 'caller_ring', from: 'offline',  to: 'busy' },
                
                { name: 'caller_answer', from: 'busy',  to: 'busy' },
                { name: 'caller_answer', from: 'idle',  to: 'busy' },
                { name: 'caller_answer', from: 'none',  to: 'busy' },
                { name: 'caller_answer', from: 'offline',  to: 'busy' },
                
                { name: 'caller_hangup', from: 'xx', to: 'xx' },
                { name: 'caller_hangup', from: 'idle', to: 'idle' },
                
                { name: 'hand_idle', from: 'idle',  to: 'idle' },
                { name: 'hand_idle', from: 'offline',  to: 'idle' },
                { name: 'hand_idle', from: 'busy',  to: 'idle' },
                { name: 'hand_idle', from: 'none',  to: 'idle' },
                { name: 'hand_idle', from: 'refuse',  to: 'idle' },
                
                { name: 'hand_offline', from: 'idle',  to: 'offline' },
                { name: 'hand_offline', from: 'busy',  to: 'offline' },
                { name: 'hand_offline', from: 'offline',  to: 'offline' },
                { name: 'hand_offline', from: 'none',  to: 'offline' },
                { name: 'hand_offline', from: 'refuse',  to: 'offline' },
                
                { name: 'hand_busy', from: 'busy',  to: 'busy' },
                { name: 'hand_busy', from: 'offline',  to: 'busy' },
                { name: 'hand_busy', from: 'idle',  to: 'busy' },
                { name: 'hand_busy', from: 'none',  to: 'busy' },
                { name: 'hand_busy', from: 'refuse',  to: 'busy' },
                
                { name: 'hand_refuse', from: 'none',  to: 'refuse' },
                { name: 'hand_refuse', from: 'busy',  to: 'refuse' },
                { name: 'hand_refuse', from: 'idle',  to: 'refuse' },
                { name: 'hand_refuse', from: 'offline',  to: 'refuse' },
                { name: 'hand_refuse', from: 'refuse',  to: 'refuse' },
			   ],
		callbacks: {
				onagent_create : function(event, from, to,	args) {
					console.log("客户呼入/点击外呼,座席响铃");
					var phone =  phoneMsg(JSON.parse(args));
					console.log(phone);
					if(!phone)
						return;
					operationUI.agent_create(phone.callUI);
				},
				onagent_ring : function(event, from, to,args) {
					console.log("座席响铃");
					var phone =  phoneMsg(JSON.parse(args));
					console.log(phone);
					if(!phone)
						return;
					operationUI.agent_ring(phone.callUI);
				},
				onagent_answer : function(event, from, to,args) {
					console.log("座席应答");
					var phone =  phoneMsg(JSON.parse(args));
					console.log(phone);
					if(!phone)
						return;
					operationUI.agent_answer(phone.callUI);
				},
				onagent_hangup : function(event, from, to,args) {
					console.log("座席挂机");
					var phone =  phoneMsg(JSON.parse(args));
					console.log(phone);
					if(!phone)
						return;
					operationUI.agent_hangup(phone.callUI);
				},
				oncaller_create : function(event, from, to,args) {
					console.log("座席呼出,客户响铃");
					var phone =  phoneMsg(JSON.parse(args));
					if(!phone)
						return;
					operationUI.caller_create(phone.callUI);
				},
				oncaller_ring : function(event, from, to,args) {
					console.log("客户响铃");
					var phone =  phoneMsg(JSON.parse(args));
					if(!phone)
						return;
					operationUI.caller_ring(phone.callUI);
				},
				oncaller_answer : function(event, from, to,args) {
					console.log("客户应答");
					var phone =  phoneMsg(JSON.parse(args));
					if(!phone)
						return;
					operationUI.caller_answer(phone.callUI);
				},
			 	oncaller_hangup : function(event, from, to,args) {
			 		console.log("客户挂机");
			 		var phone =  phoneMsg(JSON.parse(args));
			 		if(!phone)
						return;
					operationUI.caller_hangup(phone.callUI);
			 	},
			 	onstartup : function(event, from, to, args) {

			 	},          
				onhand_idle : function(event, from, to, args) {
					console.log("空闲中");
				    var mess = JSON.parse(args);
				    var phone =  phoneMsg(mess);
				    if(!phone)
                        return;
				    if(phone["currentState"]&&phone["currentState"]==mess.status){
                        console.log("空闲中1");
                        return;
				    }else{
                        phone["currentState"] = mess.status;
                        console.log("空闲中2");
				    }
                    operationUI.idle(phone.callUI);
				},
				onhand_busy : function(event, from, to, args) {
					console.log("坐席繁忙");
				    var mess = JSON.parse(args);
					var phone =  phoneMsg(mess);    
					if(!phone)
						return;
                    if(phone["currentState"]&&phone["currentState"]==mess.status){
                        return;
                    }else{
                        phone["currentState"] = mess.status;
                    }
					operationUI.busy(phone.callUI);
				},
				onhand_offline : function(event, from, to, args) {
					console.log("座席离线");
				    var phone =  phoneMsg(JSON.parse(args));    
                    if(!phone)
                        return;
                    phone["currentState"] = "offline";
                    operationUI.offline(phone.callUI);
				},
				onhand_refuse : function(event, from, to, args) {
					console.log("座席拒接");
				    
                    var phone =  phoneMsg(JSON.parse(args));    
                    if(!phone)
                        return;
                    phone["currentState"] = "refuse";
                    operationUI.refuse(phone.callUI);
                }
		},
	    error: function(name, from, to, args, error, msg, e){
	        var mess = JSON.parse(args);
            var phone =  phoneMsg(mess);    
            if(!phone)
                return;
            if (from =="offline") // 如果是离线就不接收事件
                return; 
            if(name.indexOf("hangup")!=-1){
                eval('operationUI.'+name+'(phone.callUI)');
            }else     
                alert(name+"========from:"+from+"==========to:"+to+"=========="+e+"=========="+msg+"=========="+error);
	    }
	});
	// eventState.startup();
	return eventState;
}();