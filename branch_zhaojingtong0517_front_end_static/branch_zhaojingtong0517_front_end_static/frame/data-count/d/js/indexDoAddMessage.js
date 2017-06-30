function toAddMessage(obj) {
    var shortMsgForm = $('#doAddMessages');
    var addMsgUrl = shortMsgForm.attr('action');
    var obj = $(obj), shortMsgBox = obj.prev();
    var msgName = shortMsgBox.find("input[name='name']").val();
    var msgMail = shortMsgBox.find("input[name='mail']").val();
    var msgcontent = shortMsgBox.find("textarea").val();
    var msgMailRegex = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/;
    var msgMailExec = msgMail.match(msgMailRegex);
    if(!msgMailExec) {
        alert('邮箱格式不正确~');
    } else {
        $.ajax({
            type: 'POST',
            url: addMsgUrl,
            data: "name="+msgName+"&mail="+msgMail+"&msgcontent="+msgcontent,
            success: function(msg) {
                msg = eval("("+msg+")");
                alert(msg['info']);
                shortMsgBox.find("input[name='name']").val('name:');
                shortMsgBox.find("input[name='mail']").val('mail:');
                shortMsgBox.find("textarea").val('message:');
            }
        });
    }
}
