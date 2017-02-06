
$(document).ready(function () {

    $("#area-country").select_area({
        onSelected: function(res) {
            $("[name='pid']").attr('value', res.rid);
            $("[name='pshow']").html(res.name);
        },
        level: 1,

    });

    $("#area-city-edit").select_area({
        onSelected: function(res) {
            $("#div-area-edit").find("[name='rid']").attr('value', res.rid);
            $("#div-area-edit").find("[name='name']").attr('value', res.name);
            $.layer({
                offset: ['0px', ''],
                area:['700px','auto'],
                type:1,
                'page':{
                    'dom': '#div-area-edit'
                }
            });
        },
        level: 2,

    });

    // 删除
    $('#btn-area-delete').click(function () {
        $.post(JSV.PATH_CURRENT, {opt: 'delete', rid: $("#div-area-edit").find("[name='rid']").attr('value')}, function () {
            location.reload();
        });
    });

});


