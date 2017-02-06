function delcList(cid){

    layer.confirm('确定要删除此客服吗', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Setting/cListManager/opt/delete', {cid: cid}, function (res) {
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

   var cid = $(this).parent().attr('value');
 location.href = JSV.PATH_APP_SERVER+'Setting/cListManager?opt=edit&cid='+cid;
});
