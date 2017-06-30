$(document).ready(function() {
/*
"<li data-id='<{$curStat.id}>'>";
"<h3><{$curStat.name}></h3>";
"<h4><{$curStat.address}></h4>";
"<a href='/ayu/School/toSchool/sid/<{$curStat.id}>' data-width='70' class='absolute'>";
"<span class='absobg'></span></a></li>";
*/

    
    var searchEarthOff = 0;
    /* * Search Stations * */
    var searchEarth = $('#searchEarth');
    searchEarth.click(function() {
        var searchEarthInp = $(this).prev(), searchEarthVal = searchEarthInp.val();
        if(searchEarthVal == '' || searchEarthOff == 1) {
            return;
        }
        searchEarthOff = 1;
        $.ajax({
            type:   'POST',
            url:    searchEarthStationUrl,
            data:   "keyword="+searchEarthVal,
            success: function(msg) {
				//alert(msg);
                msg = eval("("+msg+")");
                if(msg['res'] == 0) {
                    alert(msg['info']);
                    searchEarthOff = 0;
                } else if(msg['res'] == 1) {
                    var stationBox = searchEarthInp.parents('.w-100').next().find('ul');
                    stationBox.html('');
                    var searchTotal = msg['info'].length, searchResStr = "";
                    for(var i = 0; i < searchTotal; i++) {
                        searchResStr += "<li data-id='"+msg['info'][i]['id']+"'>";
                        searchResStr += "<h3>"+msg['info'][i]['name']+"</h3>";
                        searchResStr += "<h4>"+msg['info'][i]['address']+"</h4>";
                        searchResStr += "<a href='"+app_root+"/School/toSchool/sid/"+msg['info'][i]['id']+"' data-width='70' class='absolute'>";
                        searchResStr += "<span class='absobg'></span></a></li>";
                    }
                    stationBox.append(searchResStr);
                    scrollbind($(".page4 .change .box.first"));
		            scrollbind($(".page4 .change .box.last"));
                    p4ChangeChick();
                    searchEarthOff = 0;
                }
            }
        });
    });
	
	
	
	
	
	
	
	
	
    /* * Search Stations End * */




/*
<div class='w-100'>
<img src='5594f402ebf5a.png'/*tpa=http://www.zhenaijiaoyujituan.com/ayu/Public/teacher/5594f402ebf5a.png>
<p class='f-18 fbold'>
李晓晨3
</p>
<p>校区：
爱育厦门市集美中心
<br>教龄：
2年
<br>学科：
幼教
</p></div>
*/
    /* * Search Teachers * */
    var searchTeacher = $('#searchTeacher');
    searchTeacher.click(function() {
        var searchTeacherInp = $(this).prev(), searchTeacherVal = searchTeacherInp.val();
        if(searchTeacherVal == '' || searchEarthOff == 1) {
            return;
        }
        searchEarthOff = 1;
        $.ajax({
            type:   'POST',
            url:    searchTeacherUrl,
            data:   "keyword="+searchTeacherVal,
            success: function(msg) {
                msg = eval("("+msg+")");
                if(msg['res'] == 0) {
                    alert(msg['info']);
                    searchEarthOff = 0;
                } else if(msg['res'] == 1) {
                    var teacherBox = searchTeacherInp.parents('.w-100').next().find('.textArea');
                    teacherBox.html('');
                    var searchTotal = msg['info'].length, searchResStr = "";
                    for(var i = 0; i < searchTotal; i++) {
                        searchResStr += "<div class='w-100'><img src='./Public/teacher/"+msg['info'][i]['pic']+"'>";
                        searchResStr += "<p class='f-18 fbold'>"+msg['info'][i]['name']+"</p>";
                        searchResStr += "<p>校区："+msg['info'][i]['sname']+"<br>教龄："+msg['info'][i]['tage']+"年</p></div> ";
                    }
                    teacherBox.append(searchResStr);
                    scrollbind($(".page4 .change .box.first"));
		            //scrollbind($(".page4 .change .box.last"));
                    p4ChangeChick();
                    searchEarthOff = 0;
                }
            }
        });
    });
    /* * Search Teachers End * */
	
	
	
	
	
	
	//lys
	
	   $('#school_c').change(function(){
       var _val = $(this).val(),
	   		_option = $(this).find("option"),
			school_id;
		_option.each(function(i){
			if($(this).html()==_val){
				school_id = $(this).attr("data-id");
				return false;
			}
		});

        $.ajax({
            type:   'POST',
            url:    getTeacherUrl,
            data:   "school_id="+school_id,
            success: function(msg) {
                msg = eval("("+msg+")");
                if(msg['res'] == 0) {
                    alert(msg['info']);
                    searchEarthOff = 0;
                } else if(msg['res'] == 1) {
					//var searchTeacherInp = $(this).prev(), searchTeacherVal = searchTeacherInp.val();
                    //var teacherBox = searchTeacherInp.parents('.w-100').next().find('.textArea');
                    //teacherBox.html('');
					$('#teacherBox').html('');
                    var searchTotal = msg['info'].length, searchResStr = "";
                    for(var i = 0; i < searchTotal; i++) {
                        searchResStr += "<div class='w-100'><img src='./Public/teacher/"+msg['info'][i]['pic']+"'>";
                        searchResStr += "<p class='f-18 fbold'>"+msg['info'][i]['name']+"</p>";
                        searchResStr += "<p>校区："+msg['info'][i]['sname']+"<br>教龄："+msg['info'][i]['tage']+"年</p></div> ";
                    }
                    //teacherBox.append(searchResStr);
					$('#teacherBox').append(searchResStr);
                    //scrollbind($(".page4 .change .box.first"));
		            scrollbind($(".page4 .change .box.last"));
					$(".page4 .change .box.last img").load(function(){
		            	scrollbind($(".page4 .change .box.last"));
					});
                    p4ChangeChick();
                    searchEarthOff = 0;
                }
            }
        });
    });
	
	//lys	
	
	
	
	
	
});
