
function recordShow(clicker,url,shower){
	$(clicker+" .index-record").click(function(){
			$.ajax({
	        url: url,
	        type: 'get',
	        async: false,
	        success: function(data) {
	            $(shower).html(data);
	        },
	        error: function() {
	           
	        }
	    });
	});
}
