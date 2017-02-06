// 产品激活与禁用
$(".product-toggle").click(function() {
    AjaxLoading.show();
    var obj = $(this);
    // ajax 请求
    $.get(JSV.PATH_SERVER + 'admin/Product/statusToggle', {id: $(this).val()}, function(data) {
        if (data.ret_msg == '0') {
            obj.removeClass('btn-success');
            obj.addClass('btn-danger');
            obj.html('下架');
        } else {
            obj.removeClass('btn-danger');
            obj.addClass('btn-success');
            obj.html('上架');
        }
        AjaxLoading.hide();
    }, 'json');
});

// 编辑
$(".product-edit").click(function () {
    var pid = $(this).parent().attr('value');
    location.href = JSV.PATH_APP_SERVER + 'Product/productManager?opt=edit&pid=' + pid;
});

// 编辑
$(".link-edit").click(function () {
    var pid = $(this).parent().attr('value');
    location.href = JSV.PATH_APP_SERVER + 'Setting/addLink?lid=' + pid;
});

// 删除某个产品
function delLink(pid)
{
    layer.confirm('确定要删除', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Setting/linkManager/opt/delete', {lid: pid}, function (res) {
            layer.close(lid);
            if (res.success == "1") {
                layer.msg('删除成功');
                location.reload();
            } else {
                layer.msg('删除失败');
            }
        }, 'json');
    });

}


