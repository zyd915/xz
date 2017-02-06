$(function(){
    $('button[name=delAttr]').bind('click',delAttr);
})

function delAttr(){
    var delNid = $(this).val();
    var lid = layer.confirm('确定删除？',function(index){
    $.post(JSV.PATH_SERVER + 'admin/Attribute/delNid',{'delNid':delNid},function(data){

        layer.msg(data.ret_msg, 2, -1);
        window.location.reload();
     //   layer.close(lid);
    },'json');

});

};