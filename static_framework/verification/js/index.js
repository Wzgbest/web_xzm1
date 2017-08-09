$(".verification_index_index .index-record").click(function(){
	$.ajax({
        url: "/verification/index/record.html",
        type: 'get',
        async: false,
        success: function(data) {
            $('.index-record-page').html(data);
        },
        error: function() {
           
        }
    });
});
$(document).on("click",".verification_record header ul li",function(){
	$('.index-record-page').empty();
});
