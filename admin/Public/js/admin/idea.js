// 想法显示控制
$(".idea-toggle").click(function() {
    AjaxLoading.show();
    var obj = $(this);
    // ajax 请求
    $.get(JSV.PATH_SERVER + 'admin/Idea/statusToggle', {id: $(this).val()}, function(data) {
        if (data.ret_msg == '0') {
            obj.removeClass('btn-success');
            obj.addClass('btn-danger');
            obj.html('隐藏');
        } else {
            obj.removeClass('btn-danger');
            obj.addClass('btn-success');
            obj.html('显示');
        }
        AjaxLoading.hide();
    }, 'json');
});