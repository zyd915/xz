$("body").append('<div class="overlay"></div><div id="AjaxLoading" class="showbox"><div class="loadingWord"><img src="'+JSV.PATH_PUBLIC+'img/ajax-loading.gif"></div></div>');
var AjaxLoading = function () {
    var h = $(document).height();
    $(".overlay").css({"height": h });
    return {
        show : function() {
            $(".overlay").css({'display':'block','opacity':'0.2'});
            $(".showbox").stop(true).animate({'margin-top':'300px','opacity':'1'},200);
        },
        hide: function() {
            $(".showbox").stop(true).animate({'margin-top':'250px','opacity':'0'},400);
            $(".overlay").css({'display':'none','opacity':'0'});
        }
    };
}();
