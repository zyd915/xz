// 使用配置
$('document').ready(function () {
	// 点击
    $('.cart-show').bind('click', myHandlerFunctionCart);
    var first = true;
    function myHandlerFunctionCart(e) {
        if (first) {
        	var offset = $('.cart-show').offset();
			var top = offset.top + $('.cart-show').height();
			var left = offset.left ;
		$(".jsorder").css({display:'block', left: left, top: top});
        } else {
			$(".jsorder").css({display:'none'});
        }
        first = !first; // Invert `first`
    }

    // 加入购物车
    $(".jsorderadd").click(function () {
        MoveBox(this);
        $.post(JSV.PATH_APP_SERVER + 'Cart/manager/opt/add', $('#form-jsorder').serialize(), function (res) {
            if (res.success == '1') {
                window.location.reload();
            }
        }, 'json');
    });

    // 删除产品
    $(".cart-del").click(function () {
        var cid = $(this).attr('cid');
        $.post(JSV.PATH_APP_SERVER + 'Cart/manager/opt/delete', {cid: cid}, function (res) {
            window.location.reload();
        }, 'json');
    });
});

function MoveBox(obj) {
	var divTop = $(obj).offset().top;
	var divLeft = $(obj).offset().left;
	$(obj).css({
		"position": "absolute",
		"z-index": "500",
		"left": divLeft + "px",
		"top": divTop + "px"
	});
	$(obj).animate({
		"left": ($("#collectBox").offset().left - $("#collectBox").width()) + "px",
		"top": ($(document).scrollTop()) + "px",
		"width": "80px",
		"height": "30px"
	},
	500,
	function() {
		$(obj).animate({
			"left": $("#collectBox").offset().left + "px",
			"top": $("#collectBox").offset().top + "px",
			"width": "50px",
			"height": "25px"
		},500).fadeTo(0, 0.1).hide(0);
	});
}