$('select[name=kinds1]').change(getKinds2);
$('select[name=kinds1] option').bind('click',getKinds2);
$('select[name=kinds2]').change(getKinds3);
$('select[name=kinds2] option').bind('click',getKinds3);
function getKinds2(){
    var cid1 = $(this).val();
    $('select[name=kinds2]').html('').load(JSV.PATH_SERVER + 'admin/Product/getKinds/cid/'+cid1);
    $('select[name=cid]').html('');
}
function getKinds3(){
    var cid1 = $(this).val();
    $('select[name=cid]').html('').load(JSV.PATH_SERVER + 'admin/Product/getKinds/cid/'+cid1);
}