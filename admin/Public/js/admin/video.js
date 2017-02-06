
jQuery(document).ready(function() {
    $('#upload_ad_logo').uploadify({
        auto: true,
        //接受true or false两个值，当为true时选择文件后会自动上传；为false时只会把选择的文件增加进队列但不会上传，这时只能使用upload的方法触发上传。不设置auto时默认为true
        // buttonClass: "btn",
        //设置上传按钮的class
        buttonCursor: 'hand',
        //设置鼠标移到按钮上的开状，接受两个值'hand'和'arrow'(手形和箭头)
        // buttonImage: 'img/browse-btn.png',
        //设置图片按钮的路径（当你的按钮是一张图片时）。如果使用默认的样式，你还可以创建一个鼠标悬停状态，但要把两种状态的图片放在一起，并且默认的放上面，悬停状态的放在下面（原文好难表达啊：you can create a hover state for the button by stacking the off state above the hover state in the image）。这只是一个比较便利的选项，最好的方法还是把图片写在CSS里面。
        buttonText: '添加',
        //设置按钮文字。值会被当作html渲染，所以也可以包含html标签
        // checkExisting: '/uploadify/check-exists.php',
        //接受一个文件路径。此文件检查正要上传的文件名是否已经存在目标目录中。存在时返回1，不存在时返回0(应该主要是作为后台的判断吧)，默认为false
        debug: false,
        //开启或关闭debug模式
        fileObjName: 'pic',
        //设置在后台脚本使用的文件名。举个例子，在php中，如果这个选项设置为'the_files',你可以使用$_FILES['the_files']存取这个已经上传的文件。
        fileSizeLimit: 0,
        //设置上传文件的容量最大值。这个值可以是一个数字或者字符串。如果是字符串，接受一个单位（B,KB,MB,GB）。如果是数字则默认单位为KB。设置为0时表示不限制
        fileTypeExts: '*.*',
        //设置允许上传的文件扩展名（也就是文件类型）。但手动键入文件名可以绕过这种级别的安全检查，所以你应该始终在服务端中检查文件类型。输入多个扩展名时用分号隔开('*.jpg;*.png;*.gif')
        fileTypeDesc: 'All Files',
        //可选文件的描述。这个值出现在文件浏览窗口中的文件类型下拉选项中。（但我设置了好像没效果？原文：The description of the selectable files.  This string appears in the browse files dialog box in the file type drop down.）
        formData: {
            // timestamp: '<?php echo $timestamp;?>',
            // token: '<?php echo md5('
            // unique_salt ' . $timestamp);?>'
        },
        //通过get或post上传文件时，此对象提供额外的数据。如果想动态设置这些值，必须在onUploadStartg事件中使用settings的方法设置。在后台脚本中使用 $_GET or $_POST arrays (PHP)存取这些值.具体看这里http://www.uploadify.com/documentation/uploadify/customizing-the-server-side-upload-script/
        height: 40,
        //设置按钮的高度(单位px)，默认为30.(不要在值里写上单位，并且要求一个整数，width也一样)
        width: '100%',
        //设置按钮宽度(单位px)，默认120
        itemTemplate: false,
        //模板对象。给增加到上传队列中的每一项指定特殊的html模板。模板格式请看官网http://www.uploadify.com/documentation/uploadify/itemtemplate/
        method: 'post',
        //提交上传文件的方法，接受post或get两个值，默认为post
        multi: true,
        //设置是否允许一次选择多个文件，true为允许，false不允许
        overrideEvents: [],
        //重写事件，接受事件名称的数组作为参数。所设置的事件将可以被用户重写覆盖，比如
        preventCaching: true,
        //是否缓存swf文件。默认为true，会给swf的url地址设置一个随机数，这样它就不会被缓存。(有些浏览器缓存了swf文件就会触发不了里面的事件--by rainweb)
        progressData: 'percentage',
        //设置文件上传时显示数据，有‘percentage’ or ‘speed’两个参数(百分比和速度)
        queueID: false,
        //设置上传队列DOM元素的ID，上传的项目会增加进这个ID的DOM中。设置为false时则会自动生成队列DOM和ID。默认为false
        queueSizeLimit: 999,
        //设置每一次上传队列中的文件数量。注意并不是限制总的上传文件数量（那是uploadLimit）.如果增加进队列中的文件数量超出这个值，将会触发onSelectError事件。默认值为999
        removeCompleted: true,
        //是否移除掉队列中已经完成上传的文件。false为不移除
        removeTimeout: 3,
        //设置上传完成后删除掉文件的延迟时间，默认为3秒。如果removeCompleted为false的话，就没意义了
        requeueErrors: false,
        //设置上传过程中因为出错导致上传失败的文件是否重新加入队列中上传
        successTimeout: 30,
        //设置文件上传后等待服务器响应的秒数，超出这个时间，将会被认为上传成功，默认为30秒
        swf: JSV.PATH_SERVER + 'Public/js/uploadify/uploadify.swf',
        //swf的相对路径，必写项
        uploader: JSV.PATH_APP_SERVER + 'Video/uploadAdPic?sess=' + JSV.SESSION,
        //服务器端脚本文件路径，必写项
        uploadLimit: 10,
        //上传文件的数量。达到或超出这数量会触发onUploadError方法。默认999

        //每个文件即将上传前触发
        onUploadSuccess: function(file,data,res) {
            var obj = eval("[" + data + "]");
            var oid = $("[name='aid']").val();
            $("#img-ad-show").attr('src', obj[0]['url']);
            $("[name='gid']").attr('value', obj[0]['gid']);

        }
        //每个文件上传成功后触发 data 服务器返回数据
    });


});


// 删除某文章个
function delVideo(aid)
{
    layer.confirm('确定要删除此文章', function () {
        var lid = layer.load('...');
        $.post(JSV.PATH_APP_SERVER + 'Video/delVideo', {vid: aid}, function (res) {
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
$(".video-edit").click(function () {
    var aid = $(this).parent().attr('value');
    location.href = JSV.PATH_APP_SERVER + 'Video/editVideo/vid/' + aid;
});