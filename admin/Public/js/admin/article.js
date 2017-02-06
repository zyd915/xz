// 用户激活与禁用
$(".article-toggle").click(function() {
    AjaxLoading.show();
    var obj = $(this);
    // ajax 请求
    $.get(JSV.PATH_SERVER + 'admin/Article/statusToggle', {id: $(this).val()}, function(data) {
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

// 编辑
$(".article-edit").click(function () {
    var aid = $(this).parent().attr('value');
    location.href = JSV.PATH_APP_SERVER + 'Article/articleManager?opt=edit&aid=' + aid;
});

// 删除某文章个
function delArticle(aid)
{
    layer.confirm('确定要删除此文章', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Article/ArticleManager/opt/delete', {aid: aid}, function (res) {
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




// 删除某提问
function delQuest(aid)
{
    layer.confirm('确定要删除此文章', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Quest/questManager/opt/delete', {qid: aid}, function (res) {
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
$(".quest-edit").click(function () {
    var aid = $(this).parent().attr('value');
    location.href = JSV.PATH_APP_SERVER + 'Quest/questManager?opt=edit&qid=' + aid;
});