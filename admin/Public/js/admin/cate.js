$(document).ready(function () {
    // 多级分类
    $('.icon-th-list').click(function () {
        $(this).parent().siblings().slideToggle();
    });

    $('.can-add-cate').mouseover(function () {
        $(this).addClass('alert-info');
    }).mouseout(function () {
        $(this).removeClass('alert-info');
    });

    // 写入分类id
    $('.can-add-cate').click(function () {
        $('.can-add-cate').removeClass('alert-success');
        $(this).addClass('alert-success');
        // 为隐藏表单设置
        $("#nowSelectCid").attr('value', $(this).attr('value'));
    });

    // 添加分类
    $('#cate-add').click(function() {
        layer.open({
            type: 1,
            content: $('#div-cate-add'),
            area: ['600px', '600px']
        });
    });

    // 编辑当前分类
    $('#cate-edit').click(function() {
        var cid = $('#nowSelectCid').val();
        if (Number(cid) == 0) {
            layer.alert('还没有选择分类');
            return;
        }

        var lid = layer.load('获取中');
        $.get(JSV.PATH_CURRENT, {cid: cid}, function (res) {
            layer.close(lid);
            $("[name='name']").attr('value', res.data.name);
            $("[name='url']").attr('value', res.data.url);
            // $("[name='onleft']").attr('value', res.data.onleft);
            // $("[name='type']").attr('value', res.data.type);
            // $("[name='keyword']").attr('value', res.data.keyword);
            // $("[name='describe']").attr('value', res.data.describe);
            // $("[name='mtitle']").attr('value', res.data.mtitle);
            // if(res.data.only == 1){
            //     $('select[name=only] option[value=1]').attr('selected',true);
            // }else{
            //     $('select[name=only] option[value=0]').attr('selected',true);
            // }
            $('select[name=onleft]').val(res.data.onleft);
            $('select[name=onfoot]').val(res.data.onfoot);
            $('select[name=onleft].value:contains(1)').attr('selected',true);
            $('input[name=lsort]').val(res.data.lsort);
            $('input[name=fsort]').val(res.data.fsort);
            $('select[name=onfoot].value:contains(1)').attr('selected',true);
            // if(res.data.onnav == 1)
            // {
            //     $('select[name=onnav] option[value=1]').attr('selected',true);
            // }else{
            //     $('select[name=onnav] option[value=0]').attr('selected',true);
            // }
            layer.open({
                type: 1,
                content: $('#div-cate-edit'),
                area: ['600px', '600px']
            });
        }, 'json');
    });

    // 编辑提交按钮
    $('#btn-cate-edit').click(function () {
        var postInfo = $("[name='form-edit']").serializeArray();
        postInfo.push({name : "opt", value: "edit"});
        postInfo.push({name : "cid", value: Number($('#nowSelectCid').val())});
        var lid = layer.load('修改中');
        $.post(JSV.PATH_CURRENT, postInfo, function (res) {
            layer.close(lid);
            if (res.success == "1") {
                layer.closeAll();
                layer.msg(res.ret_msg);
                location.reload();
            } else {
                layer.msg(res.ret_msg);
            }
        }, 'json');
    });

    // 编辑提交按钮
    $('#btn-cate-add').click(function () {
        var postInfo = $("[name='form-add']").serializeArray();
        postInfo.opt = 'add';
        postInfo.push({name : "opt", value: "add"});
        postInfo.push({name : "pid", value: Number($('#nowSelectCid').val())});

        var lid = layer.load('...');
        $.post(JSV.PATH_CURRENT, postInfo, function (res) {
            layer.close(lid);
            if (res.success == "1") {
                layer.closeAll();
                layer.msg(res.ret_msg);
                location.reload();
            } else {
                layer.msg(res.ret_msg);
            }
        }, 'json');
    });

    $('#btn-cate-delete').click(function () {
        layer.confirm('确认删除分类', function () {
            var postInfo = $("[name='form-add']").serializeArray();
            postInfo.opt = 'add';
            postInfo.push({name : "opt", value: "delete"});
            postInfo.push({name : "cid", value: Number($('#nowSelectCid').val())});

            var lid = layer.load('...');
            $.post(JSV.PATH_CURRENT, postInfo, function (res) {
                layer.close(lid);
                if (res.success == "1") {
                    layer.closeAll();
                    layer.msg(res.ret_msg);
                    location.reload();
                } else {
                    layer.msg(res.ret_msg);
                }
            }, 'json');
        });
    });

});
