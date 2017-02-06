// 用户激活与禁用
$(".user-toggle").click(function() {
    AjaxLoading.show();
    var obj = $(this);
    // ajax 请求
    $.get(JSV.PATH_SERVER + 'admin/User/statusToggle', {id: $(this).val()}, function(data) {
        if (data.ret_msg == '0') {
            obj.removeClass('btn-success');
            obj.addClass('btn-danger');
            obj.html('禁用');
        } else {
            obj.removeClass('btn-danger');
            obj.addClass('btn-success');
            obj.html('激活');
        }
        AjaxLoading.hide();
    }, 'json');
});

// 会员卡激活与禁用
$(".vip-toggle").click(function() {
    AjaxLoading.show();
    var obj = $(this);
    // ajax 请求
    $.get(JSV.PATH_SERVER + 'admin/Vip/statusToggle', {cid: $(this).val()}, function(data) {
        if (data.ret_msg != '0') {
            obj.removeClass('btn-success');
            obj.addClass('btn-danger');
            obj.html('禁用');
        } else {
            obj.removeClass('btn-danger');
            obj.addClass('btn-success');
            obj.html('激活');
        }
        AjaxLoading.hide();
    }, 'json');
});

// 修改卡类型
function changeCardType(cid)
{
    $("[name='cid']").attr('value', cid);
    $.layer({
        'type': 1,
        'area': ['auto', 'auto'],
        'page': {
            'dom': '#changeCardType',
        },
    });

}

