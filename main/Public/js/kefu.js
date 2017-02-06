$(function(){
	var KF = $(".keifu");
	var wkbox = $(".keifu_box");
	var kf_close = $(".keifu .keifu_close");
	var icon_keifu = $(".icon_keifu");
	var kH = wkbox.height();
	var kW = wkbox.width();
	var wH = $(window).height();

	//默认收起在线客服
	//wkbox.hide();
	icon_keifu.show();
	KF.css("width","26");

	KF.css({height:kH});
	icon_keifu.css("top",parseInt((kH-100)/2));
	var KF_top = (wH-kH)/2;
	if(KF_top<0) KF_top=0;
	KF.css("top",KF_top);
	$(kf_close).click(function(){
		KF.animate({width:"0"},200,function(){
			wkbox.hide();
			icon_keifu.show();
			KF.animate({width:26},300);
		});
	});
	$(icon_keifu).click(function(){
			$(this).hide();
			wkbox.show();
			KF.animate({width:kW},200);
	});
});