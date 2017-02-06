jQuery(document).ready(function() {
});
// 删除某个产品
function delSet(sid)
{
    layer.confirm('确定要删除此产品', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Setting/setManager/opt/delete', {sid: sid}, function (res) {
            layer.close(lid);
            if (res.success == "1") {
                layer.msg(res.ret_msg);
                location.reload();
            } else {
                layer.msg('删除失败');
            }
        }, 'json');
    });


}

// 编辑
$(".product-edit").click(function () {

   var sid = $(this).parent().attr('value');
 location.href = JSV.PATH_APP_SERVER+'Setting/setManager?opt=edit&sid='+sid;
});
