<style>
.portlet-tabs > .tab-content {
padding: 10px !important;
margin: 0px;
margin-top: -50px !important;
}
.a_showdow {
    background: #eee;
    text-decoration: none;
}
.portlet-tabs > .nav-tabs > li {
    float: left;
}
.portlet > .portlet-title > .caption {
    float: right;
}
</style>

<div class="portlet box green tabbable" style="width:600px;display:none" id="ar_select_cate">
    <div class="portlet-title">
        <div class="caption"><i class="icon-reorder"></i>分类选择框</div>
    </div>
    <div class="portlet-body">
        <div class=" portlet-tabs">
            <ul class="nav nav-tabs">
                <li class="portlet_tab_select_cate" akey="country"><a>一级分类</a></li>
                <li class="portlet_tab_select_cate" akey="province"><a>二级分类</a></li>
                <li class="active portlet_tab_select_cate " akey="city"><a>三级分类</a></li>
                <li class="portlet_tab_select_cate" akey="county"><a>四级分类</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-cate-pane" id="portlet_tab_0">
                    <ul class="nav nav-tabs" id="ul-cate-country">
                    </ul>
                </div>
                <div class="tab-cate-pane active" id="portlet_tab_1">
                  <ul class="nav nav-tabs" id="ul-cate-province">
                  </ul>
                </div>
                <div class="tab-cate-pane active" id="portlet_tab_2">
                  <ul class="nav nav-tabs" id="ul-cate-city">
                  </ul>
                </div>
                <div class="tab-cate-pane" id="portlet_tab_3">
                    <ul class="nav nav-tabs" id="ul-cate-county">
                    </ul>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 获取地区信息
function getCateData(pid, ajaxCallBack, obj)
{
    console.log(obj);
    if (obj !== undefined) {
        $(obj).parent().parent().find("a").removeClass("a_showdow");
        $(obj).addClass("a_showdow");
        JSV.LAST_SELECT_CATE_ID = pid;
        // 最后一级不加载数据了
        // if ($(obj).parent().parent().parent().find('#select-confirm-cate').length > 0) {
        //     JSV.LAST_SELECT_CATE_ID = pid;
        //     return;
        // } else {
        //     showTabCate($(obj).parent().parent().parent().index() + 1);
        // }
    }

    if (ajaxCallBack == 'country') {
        $("#ul-cate-province").html('');
        $("#ul-cate-city").html('');
        $("#ul-cate-county").html('');
    } else if (ajaxCallBack == 'province') {
        $("#ul-cate-city").html('');
        $("#ul-cate-county").html('');
    } else if (ajaxCallBack == 'city') {
        $("#ul-cate-county").html('');
    }

    $.get(JSV.PATH_SERVER + 'Api/getAllcateByPid', {pid: pid}, function (res){
        if (res.data) {
            if (ajaxCallBack) {
                if (ajaxCallBack) {
                    putCateUl[ajaxCallBack](res.data);
                }
            } else {
                // 这里的pid 就是用户点击的最终选择id
                JSV.LAST_SELECT_CATE_ID = pid;
            }
        } else {
            return null;
        }

    }, 'json');

}

putCateUl = {
    'country' : function(data) {
        if (data) {
            var liStr = activeClass = '';
            for (i in data) {
                if (JSV.INIT_SELECT_CATE_COUNTRY != undefined && data[i]['cid'] == JSV.INIT_SELECT_CATE_COUNTRY[0]) {
                    activeClass =' class = "a_showdow" ';
                } else {
                    activeClass = '';
                }

                liStr += '<li><a '+ activeClass +'cid="'+data[i]['cid']+'" onclick="getCateData('+data[i]['cid']+', \'province\', this)">'+data[i]['name']+'</a></li>';
            }
            $("#ul-cate-country").html(liStr);
            $("#ul-cate-province").html('');
            // JSV.LAST_SELECT_CATE_ID = null;
        }

    },
    'province' : function(data) {
        if (data) {
            var liStr = activeClass = '';
            for (i in data) {
                if (JSV.INIT_SELECT_CATE_PROVINCE != undefined && data[i]['cid'] == JSV.INIT_SELECT_CATE_PROVINCE[0]) {
                    activeClass =' class = "a_showdow" ';
                } else {
                    activeClass = '';
                }

                liStr += '<li><a '+ activeClass +'cid="'+data[i]['cid']+'" onclick="getCateData('+data[i]['cid']+', \'city\', this)">'+data[i]['name']+'</a></li>';
            }
            $("#ul-cate-province").html(liStr);
            $("#ul-cate-city").html('');
            // JSV.LAST_SELECT_CATE_ID = null;
        }

    },
    'city' : function (data) {
        if (data) {
            var liStr = activeClass = '';
            for (i in data) {
                if (JSV.INIT_SELECT_CATE_CITY != undefined && data[i]['cid'] == JSV.INIT_SELECT_CATE_CITY[0]) {
                    activeClass =' class = "a_showdow" ';
                } else {
                    activeClass = '';
                activeClass}

                liStr += '<li><a ' + activeClass + 'cid="'+data[i]['cid']+'" onclick="getCateData('+data[i]['cid']+', \'county\',this)">'+data[i]['name']+'</a></li>';
            }
            $("#ul-cate-city").html(liStr);
            // JSV.LAST_SELECT_CATE_ID = null;
        }


    },

    'county': function (data) {
        if (data) {
            var liStr = activeClass = '';
            for (i in data) {
                if (JSV.INIT_SELECT_CATE_COUNTY != undefined && data[i]['cid'] == JSV.INIT_SELECT_CATE_COUNTY[0]) {
                    activeClass =' class = "a_showdow" ';
                } else {
                    activeClass = '';
                }
                liStr += '<li><a ' + activeClass + ' cid="'+data[i]['cid']+'" onclick="getCateData('+data[i]['cid']+', false, this)">'+data[i]['name']+'</a></li>';
            }
            $("#ul-cate-county").html(liStr);
        }
    },
};

// 点击切换
$(".portlet_tab_select_cate").click(function () {
    showTabCate($(this).index());
});


// 显示索引值
function showTabCate(index, hide, level)
{
    if (level) {
        if (index > level - 1) {
            return false;
        }
    }

    $(".portlet_tab_select_cate").removeClass('active');
    $(".portlet_tab_select_cate").eq(index).addClass('active');
    // $(".tab-cate-pane").removeClass('active');

    if (hide) {
        $(".portlet_tab_select_cate :gt("+index+")").hide();
        $(".tab-cate-pane :gt("+index+")").remove();

        var buttonStr = '<div class="text-center"><button type="btn" class="btn btn-success select-confirm-cate" id="select-confirm-cate">确定选择</button></div>';

        $(".tab-cate-pane").eq(index).append(buttonStr);

    }

    putCateUl[$(".portlet_tab_select_cate").eq(index).attr('akey')](false);

    $(".tab-cate-pane").eq(index).addClass('active').show();

}

// 包装地址选择器插件
(function($){
    $.fn.extend({
        select_cate: function (options) {
            var lid, opts, level;
            var defaults = {
                // 地址选中完毕时调用
                onSelected: function (res) {
                    // alert('ok');
                },
                // 初始化
                onInit: function (res) {

                },
                // 参数
                'cid': 0,
                // 选择级数
                level: 3,
            }

            opts = $.extend(defaults, options);

            level = opts.level;
            // 隐藏多余的地址栏
            var tabMaxIndex = level - 1;
            showTabCate(tabMaxIndex, true);
            if (opts.cid) {
               showTabCate(tabMaxIndex);
            } else {
                showTabCate(0);
            }

            if (opts.cid) {
                $.get(JSV.PATH_SERVER + 'Api/getAllcateBySid', {sid: opts.cid}, function (res) {
                        // 地区初始化赋值
                        var init_select = [];
                        var resCopy = res.data;
                        console.log(res.data);
                        for (var i = 0; i < opts.level; i++) {
                            init_select.push([resCopy.cid, resCopy.name]);
                            if (resCopy.parent) {
                                resCopy = resCopy.parent;
                            } else {
                                break;
                            }

                        }
                        var l = init_select.length;
                        for (var j = 0; j < l; j++) {
                            if (j == 0) {
                                JSV.INIT_SELECT_CATE_COUNTRY = init_select.pop();
                            } else if (j == 1) {
                                JSV.INIT_SELECT_CATE_PROVINCE = init_select.pop();
                            } else if (j == 2) {
                                JSV.INIT_SELECT_CATE_CITY = init_select.pop();
                            } else if (j == 3) {
                                JSV.INIT_SELECT_CATE_COUNTY = init_select.pop();
                            }
                        }

                        opts.onInit(res.data);


                    }, 'json');
            }
            $("#select-confirm-cate").click(function () {
                if (JSV.LAST_SELECT_CATE_ID) {
                    var sid = JSV.LAST_SELECT_CATE_ID;
                    layer.closeAll();
                    $.get(JSV.PATH_SERVER + 'Api/getAllcateBySid', {sid: sid}, function (res) {
                        opts.onSelected(res.data);
                    }, 'json');
                    // JSV.LAST_SELECT_CATE_ID = null;
                    //JSV.INIT_SELECT_CATE_COUNTY = null;
                    //JSV.INIT_SELECT_CATE_CITY = null;
                    //JSV.INIT_SELECT_CATE_PROVINCE = null;
                    //JSV.INIT_SELECT_CATE_COUNTRY = null;
                } else {
                    layer.msg('必须选择一个分类');
                }
            });
            $(this).click(function() {
                // 第一次执行
                getCateData(0, 'country');
                showTabCate(0);
                if (JSV.INIT_SELECT_CATE_COUNTRY != undefined && JSV.INIT_SELECT_CATE_COUNTRY[0]) {
                    getCateData(JSV.INIT_SELECT_CATE_COUNTRY[0], 'province');
                    if (JSV.INIT_SELECT_CATE_PROVINCE != undefined && JSV.INIT_SELECT_CATE_PROVINCE[0]) {
                        getCateData(JSV.INIT_SELECT_CATE_PROVINCE[0], 'city');
                        if (JSV.INIT_SELECT_CATE_CITY != undefined && JSV.INIT_SELECT_CATE_CITY[0]) {
                            getCateData(JSV.INIT_SELECT_CATE_CITY[0], 'county');
                        }
                    }
                }


                lid = layer.open({
                    area:['650px','50%'],
                    type:1,
                    title:false,
                    closeBtn:[1,true],
                    border:[0],
                    content:$('#ar_select_cate')
                });
            });
        }
    })
})(jQuery);
/**
 *usage

$("#select-confirm-cate-test").select_cate({
    onSelected: function(res) {
        alert(res.name);
    }
});

*/

</script>