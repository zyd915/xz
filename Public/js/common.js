jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

$(window).ready(function() {
	$('.content_left_menu dt a').click(function() {
		if(user_accountStatus == 'N'){  // 获取登录用户状态,如果是N状态,菜单不能点击
			alert('請先激活賬戶');
			return false;
		}
		if ($(this).attr("class") != "") {
			closeAll();
			$(this).attr("class", "");
			$(this).parent().parent().find("dd").slideDown(500, function() {
				$(this).show();
			});
		}
	});
	showtime();
});

function showMenu() {
	if(user_accountStatus == 'N'){  // 获取登录用户状态,如果是N状态,菜单不能点击
		//alert('請先激活賬戶');
		return;
	}
	// 根据用户进入的主页面去显示菜单项效果
	closeAll();
	
	$('.menu_left dd a:last-child').attr("last","0");
	
	$('.content_left_menu a[name^=menu]').each(function() {
		if ($.trim($(this).attr("href"))== $('#menuPonint').attr("href")) {
			// 设置选中菜单项的样式
			$(this).parent().parent().find('dt a').attr("class", ""); // dt a
													// dt_down
			$(this).parent().show();
			/**.slideDown(500, function() {
				$(this).show();
			}); **/
			if($(this).attr("last")=='0'){
				$(this).attr("class", "ddbg_on nbb");
			}else{
				$(this).attr("class", "ddbg_on nbt"); // dd a
			}
			return;
		}
	});
	
}

// 展示全部菜单项
function showAll() {
	if(user_accountStatus == 'N'){  // 获取登录用户状态,如果是N状态,菜单不能点击
		alert('請先激活賬戶');
		return;
	}
	$('.content_left_menu dt a').attr("class", "");
	$(".content_left_menu dd").slideDown(500, function() {
		$(this).show();
	});
}

// 关闭所有菜单项
function closeAll() {
	$('.content_left_menu dt a').attr("class", "dt_down");
	$(".content_left_menu dd").slideUp(500, function() {
		$(this).hide();
	});

}

// 单独展开某部分菜单项 下标从0开始
function onlyMenuOpen(num) {
	$('.dt_down').eq(num).parent().parent().find('dd').slideDown(500,
			function() {
				$(this).show();
			});
	$('.dt_down').eq(num).attr("class", "");

}

/**
 * 显示国家时间
 * 
 * @return
 */
function showtime() {
	var today = new Date((new Date()).getTime());
	if($('#servier_time').length > 0){
		var servier_time = parseFloat( $('#servier_time').val());
		if(servier_time > 0){
			today = new Date(servier_time);
			$('#servier_time').val(today.getTime() + 1000); 
		}
	}
	var hour = today.getHours();
	var minute = today.getMinutes();
	var second = today.getSeconds();
	if (hour <= 9)
		hour = "0" + hour;
	if (minute <= 9)
		minute = "0" + minute;
	if (second <= 9)
		second = "0" + second;

	var utc = today.getTime() + (today.getTimezoneOffset() * 60000);
	var ldDate = new Date(utc + (3600000 * (1))); // 夏令时 0 冬令时 -1
	var nyDate = new Date(utc + (3600000 * (-4))); // 夏令时 -4 冬令时-5
	var tyDate = new Date(utc + (3600000 * (9))); // 日本没有
	var sxDate = new Date(utc + (3600000 * (2))); // 夏令时 1 冬令时0

	var ldhour = ldDate.getHours() <= 9 ? ("0" + ldDate.getHours()) : ldDate
			.getHours();
	var nyhour = nyDate.getHours() <= 9 ? ("0" + nyDate.getHours()) : nyDate
			.getHours();
	var tyhour = tyDate.getHours() <= 9 ? ("0" + tyDate.getHours()) : tyDate
			.getHours();
	var sxhour = sxDate.getHours() <= 9 ? ("0" + sxDate.getHours()) : sxDate
			.getHours();

	var strldtime = ldhour + ":" + minute + ":" + second;
	var strnytime = nyhour + ":" + minute + ":" + second;
	var strtytime = tyhour + ":" + minute + ":" + second;
	var strhktime = hour + ":" + minute + ":" + second;
	var strsxtime = sxhour + ":" + minute + ":" + second;

	$('#ldtime').html(strldtime);
	$('#nytime').html(strnytime);
	$('#tytime').html(strtytime);
	$('#hktime').html(strhktime);
	$('#sxtime').html(strsxtime);

	setTimeout("showtime();", 1000);
}

/**
 * 首页7天内的公告判断是否显示new图片
 */
function checkAnnouncementTopNewsImage(publish) {
	var b=false;
	var temp = publish.split(" "); 
	var dd=new Date();
	dd.setFullYear(temp[0].split("-")[0]);
	dd.setMonth(temp[0].split("-")[1]-1);
	dd.setDate(temp[0].split("-")[2]);
	var d = new Date();
	var a = (d.getTime() - dd.getTime()) / 3600 / 1000 / 24;
	if(a<7 || a==7){
		b = true;
	}
	return b;
}
(function($) {
	$.fn.Qiehuan = function(options) {
		var defaults = {
			starsrc : '', // 切换显示src
			startclass : '', // 切换变更class
			showsrc : '', // 切换显示src
			b : false
		// 是否需要变更class

		};

		var options = $.extend(defaults, options);

		$(this).bind("mouseover", function() {
			var _whtml = $(this).html();
			$(options.starsrc).each(function(i) {
				if (_whtml == $(this).html()) {
					$(options.showsrc).each(function(n) {

						if (n == i) {
							$(this).show();
							return;
						}
						$(this).hide();

					});

					if (options.b) {
						$(this).removeClass().addClass(options.startclass);
					}
					return;
				}
				$(this).removeClass();
			});
		});

	};
})(jQuery);

var regexEnum = {ONESTOP_account:"((^(?=(?=(\\w|-)*([0-9a-zA-Z]))(\\w|-)*([a-zA-Z]))(\\w|-){7,16}$))",
		ONESTOP_password:"(^(?=(?=.*\\d).*[a-zA-Z]).{8}$)|(^(?=(?=.*\\d).*[a-zA-Z]).{6,12}$)",
		GTS_account:"^[0-9]{7}$",
		GTS_password:"^[0-9]{8}$",
		GTS_phonepw:"^(?=(?=.*\\d).*[a-zA-Z]).{6,10}$",
		MT5_account:"^5{1}[0-9]{7}$",
		MT5_password:"^(?=(?=.*\\d).*[a-zA-Z]).{8}$",
		MT4_account:"^8{1}[0-9]{7}$",
		MT4_password:"^(?=(?=.*\\d).*[a-zA-Z]).{8}$",
		MTF_account:"^2{1}[0-9]{7}$",
		MTF_password:"^[0-9]{8}$",
		intege:"^-?[1-9]\\d*$",                   //整数
		num:"^([+-]?)\\d*\\.?\\d+$",			//数字
		email:"^\\w+((-\\w+)|(\\.\\w+))*\\@[A-Za-z0-9]+((\\.|-)[A-Za-z0-9]+)*\\.[A-Za-z0-9]+$", //邮件
		chinese:"^[\\u4E00-\\u9FA5\\uF900-\\uFA2D]+$",					//仅中文
		zipcode:"^\\d{6}$",						//邮编
		notempty:"^\\S+$",						//非空
		mobile: "^(13|14|15|18)[0-9]{9}$",			//手机
		username:/((^(?=(?=(\w|-)*([0-9a-zA-Z]))(\w|-)*([a-zA-Z]))(\w|-){7,16}$))|(^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$)|(^2{1}[0-9]{7}$)|(^8{1}[0-9]{7}$)|(^5{1}[0-9]{7}$)|(^[0-9]{7}$)/g

};

function validation(str,reg)   
{   
   var result=str.match(reg);   
   if(result==null) return false;   
    return true;   
}
//邮箱确认邮件
function sendConfirmEmail(successmsg,errormsgh){
	if(changeuser_Status()){
		$.ajax({url:"ajaxsendConfirmEmail.do",success:function(msg){
			if(msg.success=='200'){
				if(msg.data=='OK'){
					alert(successmsg);
				}else{
					alert(msg.data);
				}
				
			}else{
				alert(errormsgh);
			}
		}});
	}
	//ajaxsendConfirmEmail
}

/**
 * live800 弹窗口打开
 * @param lang
 */
function openLive800Chat(){
	var url="http://www.onlineservice-hk.com/k800/chatClient/chatbox.jsp?companyID=209&enterurl=http%3A%2F%2Fwww%2E24k%2Ehk%2Findex%2Ehtml&tm=1346658342836";
	window.open (url,'Live800Chatindow','height=520,width=740,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no'); 
}

//QQ窗口客服
function openQQChat(){
	var url="http://crm2.qq.com/page/portalpage/wpa.php?uin=800018282&cref=&ref=&f=1&ty=1&ap=&as=";
	window.open (url,'Live800Chatindow','height=544, width=644,top=0,left=0,toolbar=no,menubar=no,scrollbars=no, resizable=no,location=no, status=no'); 
}

function checkDeposit(){
	var b=false;
	$.ajax({url:"ajaxgCheckDepositAction.do",async:false,cache:false,dataType:"json",success:function(msg){
		if(msg.success=='200' || msg.success=='-1'){
			b=true;
		}
	},error:function(){
		b=true;
	}});
	return b;
}