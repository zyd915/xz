//公共js类
//判断兄弟商会推荐是否加完
/*$(".brother").each(function(){
	alert("debug");
	if ($(this).length == 0) {
		$(".brother").text("您已加完所有商会。");
	};

});*/
//投档概率（热度条）
function applyChance(id,lvl,num){
	var startH = 5;
	var mH = 30;
	for(var i = 0; i < num; i++){
		startH++;
		mH--;
		$(id).append("<div></div>")
			 .children("div:eq("+i+")")
			 .css({"width":"3px","margin-top":mH+"px","padding-top":startH+"px","margin-left":"1px"})
			 .addClass("left bgd-bzh");
	}
	$(id).children("div:lt("+lvl+")").addClass("bg1-bzh").removeClass("bgd-bzh");
}
//表单验证
function form1(){
	$("form :input.required").each(function(){
	        var $required = $("<strong class='high'> *</strong>"); //创建元素
	            $(this).parent().append($required); //然后将它追加到文档中
	        });
	         //文本框失去焦点后
	        $('form :input').blur(function(){
	             var $parent = $(this).parent();
	             $parent.find(".formtips").remove();
	             //验证用户名
	             if( $(this).is('#username') ){
	                    if( this.value=="" || this.value.length < 3 ){
	                        var errorMsg = '请输入至少3位的用户名。';
	                        $parent.append('<span class="formtips onError">'+errorMsg+'</span>');
	                    }else{
	                        var okMsg = '输入正确。';
	                        $parent.append('<span class="formtips onSuccess">'+okMsg+'</span>');
	                    }
	             }
	             //验证密码
	             if( $(this).is('#password') ){
	                    if( this.value=="" || this.value.length < 6 ){
	                        var errorMsg = '请输入至少6位的密码。';
	                        $parent.append('<span class="formtips onError">'+errorMsg+'</span>');
	                    }else{
	                        var okMsg = '输入正确。';
	                        $parent.append('<span class="formtips onSuccess">'+okMsg+'</span>');
	                    }
	             }
	             //验证重复密码是否相同
	             if( $(this).is('#repassword') ){
	                    if( this.value=="" || this.value !== $('#password').val()){
	                        var errorMsg = '前面密码不一';
	                        $parent.append('<span class="formtips onError">'+errorMsg+'</span>');
	                    }else{
	                        var okMsg = '输入正确。';
	                        $parent.append('<span class="formtips onSuccess">'+okMsg+'</span>');
	                    }
	             }
	             //验证邮件
	             if( $(this).is('#email') ){
	                if( this.value=="" || ( this.value!="" && !/.+@.+\.[a-zA-Z]{2,4}$/.test(this.value) ) ){
	                      var errorMsg = '请输入正确的E-Mail地址。';
	                      $parent.append('<span class="formtips onError">'+errorMsg+'</span>');
	                }else{
	                      var okMsg = '输入正确。';
	                      $parent.append('<span class="formtips onSuccess">'+okMsg+'</span>');
	                }
	             }
	        }).keyup(function(){
	           $(this).triggerHandler("blur");
	        }).focus(function(){
	             $(this).triggerHandler("blur");
	        });//end blur


	        //提交，最终验证。
	         $('.submit').click(function(){
	                $("form :input.required").trigger('blur');
	                var numError = $('form .onError').length;
	                if(numError){
	                	alert("请确认注册信息是否完整？");
	                    return false;
	                }
	         });

	        //重置
	         $('#res').click(function(){
	                $(".formtips").remove();
	         });
}
//限制字符长度(正则表达式用不来)
function textArea(id,length,mode){
	this.id = id;
	this.length = length;
	this.mode = mode;
	$(id).each(function(){
		var curLength = $(this).text().length;
		if(curLength>length)
		{
			var num = $(this).text().substr(0,length);
			switch(mode)
			{
				case 1:
					var num=$(this).text().substr(0,length);
					$(this).text(num);
					break;
				case 2:
					var num=$(this).text().substr(0,length);
					$(this).text(num+"...");
					break;
				default:
					break;
			}
		}
	});
}
function bannerSlider(id,time){
    var imgLen = $(id + ' .slides').find('li').length,
        num = 0,
        strLi = '',
        timer = setInterval(callback, time * 1000);
    if(imgLen == 0){
        return;
    }else{
        for(var i=0; i<imgLen; i++){
            strLi += '<li></li>';
        }
        $(id + ' .fix-slider-nav').append(strLi);
        $(id + ' .fix-slider-nav').find('li').eq(0).addClass('active');
    }
    function callback(){
        num++;
        num == imgLen ? num = 0 : num;
        $(id + ' .slides').find('li').hide();
        $(id + ' .slides').find('li').eq(num).fadeIn();
        $(id + ' .fix-slider-nav').find('li').removeClass('active');
        $(id + ' .fix-slider-nav').find('li').eq(num).addClass('active');
    }
    $(id + ' .fix-slider-nav').find('li').each(function(index, el) {
        $(this).click(function() {
            num = index;
            $(id + ' .slides').find('li').hide();
            $(id + ' .slides').find('li').eq(index).fadeIn("slow");
            $(id + ' .fix-slider-nav').find('li').removeClass('active');
            $(id + ' .fix-slider-nav').find('li').eq(index).addClass('active');
        });
    });
    $(id + ' .slides').hover(function() {
        clearInterval(timer);
    }, function() {
        timer = setInterval(callback, time * 1000);
    });
}
//slider滑动轮播效果
function slider(id,time,acolor,bgcolor,position){
	this.id = id;//标签名
	this.time = time;//时间
	this.bgcolor = bgcolor;//背景色
	this.acolor = acolor;//焦点链接颜色
	this.position = position;//标签位置

	var num = 0;
	var auto = 1;  //等于1则自动切换，其他任意数字则不自动切换
	var maxImg = $( id+" img").length;
	a=0;
	if (maxImg == 0)
	{
		$(id).prepend("还未添加任何动态");
		$(id+" div.btn-slider").hide();
	}
	maxImg >4 ? maxImg = 4 : maxImg;
	for (i=0;i<maxImg;i++)
	{
		a++;
		$(id+" div.btn-slider").append("<a></a>");
	}

	$(id+" div.btn-slider").css("text-align",position);//btn位置
	$(id+" div.btn-slider").children().css("background-color",bgcolor);//首次btn背景
	$(id+" div.btn-slider a:eq(0)").css("background-color",acolor);//首次链接颜色
	$( id+" img:eq(0)").show();//首次展示图片

	$(id+" div.btn-slider").children().bind({
		mousedown:function(){
			num = $(this).index()-0;
			$(id+" img").hide();
			$(id+" img:eq("+num+")").fadeIn("slow");
			$(id+" div.btn-slider").children().css("background-color",bgcolor);
			$(id+" div.btn-slider a:eq("+num+")").css("background-color",acolor);
		}
	});

	if(auto ==1){//自动轮播
		var maxNum = $(id+" div.btn-slider a").length;
		function autobar(){
			num++;
			num == maxNum? num = 0 : num;
			$(id+" img").hide();
			$(id+" img:eq("+num+")").fadeIn();
			$(id+" div.btn-slider").children().css("background-color",bgcolor);
			$(id+" div.btn-slider a:eq("+num+")").css("background-color",acolor);
		}
		var barChange = setInterval(autobar,time*1000);
		//鼠标悬停暂停切换
		$(id+" div.btn-slider").children().mouseover(function(){
			clearInterval(barChange);
		});
		$(id+" div.btn-slider").children().mouseout(function(){
			barChange = setInterval(autobar,time*1000);
		});
	  }
}
//判断信息，无则显示暂缺。
function checkContent(id){
	len = $(id).nextAll().length;
	if (len== 0)
	{
		$(id).after("<dd>暂无</dd>");
	}
}
//获取添加阴影
function shade(id){
	this.id = id;
	$(id).hover(function(){
		$(this).addClass("blue-shade");
	}, function() {
		$(this).removeClass("blue-shade");
	});
}
//大气头像动画
function portraitAnimate(id,w,h){
	this.id = id;
	this.w = w;
	this.h = h;
	mw = $(id).css("width");
	mh = $(id).css("height");
	$(id).hover(function(){
		$("img", this).stop().animate({top:"30px",width:w,height:h},{queue:false,duration:160});
		$(".member-info", this).show();
	}, function() {
		$("img", this).stop().animate({top:"",width:mw,height:mh},{queue:false,duration:160});
		$(".member-info", this).hide();
	});
}
//自适应图片展示id为类名，w、h为父元素宽高
/*
如果要获取图片的真实的宽度和高度有三点必须注意
1、需要创建一个image对象：如这里的$("<img/>")
2、指定图片的src路径
3、一定要在图片加载完成后执行如.load()函数里执行
*/
function imgAuto(id,w,h){
	this.id = id;
	this.w = +w;
	this.h = +h;
	$(id).each(function(){
		var img = $(this);
		var realWidth;
		var realHeight;
		$("<img/>").attr("src", $(img).attr("src")).load(function(){
		realWidth = +this.width;
		realHeight = +this.height;
			if(realWidth >= realHeight){
				//如果真实的宽度大等于真实高度
				a = Math.ceil(realWidth*(h/realHeight));
				l = Math.ceil((a-w)/2);
				$(img).wrap("<div>").parent().css({"width":w+"px","height":h+"px"}).addClass("imgAuto-ff");
				$(img).wrap("<div>").parent().css({"width":a+"px","height":h+"px"});
				$(img).css({"width":a+"px","height":h+"px","left":"-"+l+"px"});

			}else{
				//如果真实的宽度小于真实高度
				b = Math.ceil(realHeight*(w/realWidth));
				l = Math.ceil((b-h)/2);
				$(img).wrap("<div>").parent().css({"width":w+"px","height":h+"px"}).addClass("imgAuto-ff");
				$(img).wrap("<div>").parent().css({"width":w+"px","height":b+"px"});
				$(img).css({"width":w+"px","height":b+"px","top":"-"+l+"px"});
			}
		});

	});
}
// 首页图片特效
function effect(id,minW,maxW) {
	// $(id).mouseover(function(){
	// 	$(this).toggleClass("sumImgActive");
	// });
	this.minW = minW;
	this.maxW = maxW;
	$(id).hover(function(){
		$(this).stop().animate({width:maxW},{queue:false,duration:160});
		$("i", this).removeClass("fa-2x").parent().css("line-height","78px");
		$("span", this).show();
	}, function() {
		$(this).stop().animate({width:minW},{queue:false,duration:160});
		$("i", this).addClass("fa-2x").parent().css("line-height","90px");
		$("span", this).hide();
	});
}

//配置layer样式
$(document).ready(function(){
	layer.config({
		extend:'extend/layer.ext.js',
		skin:'layui-layer-rim'
		//skin:'layui-layer-lan'
		//skin:'layui-layer-molv'
	});
});

var date=365,paths='/';

//输入框非空处理
function nonempty(id,msg){
	if($.trim($(id).val()) == ""){
		message(msg);
	}else{
		return true;
	}
	return false;
}

//未选择提示信息
function message(msg){
    var index=layer.alert('',{
        title:'错误信息',
        content:msg,
        closeBtn:2,
        icon:5,
        shift:6
    });
}

//下拉列表未选择判断
function snonempty(id,msg){
    if($(id)[0].selectedIndex == 0){
        message(msg);
    }else{
    	return true;
    }
    return false;
}

var index,indexLoad,indexError;
//tips提示框
function Mtips(id,msg){
    $(id).bind({
        mouseover:function(){
            index=layer.tips(msg,id,{
                tips:[3,'#5FB878'],
                time:false
            });
        },
        mouseout:function(){
            layer.close(index);
        }
    });
}
//捕获页
function CaptruePage(id,y,x,w,h,cid,can,url){
    cindex=layer.open({
        type: 1,
        title: false,
        // offset: [y,x],
        shade: 0.6,
        closeBtn: 2,
        scrollbar: false,
        area: [w, h],
        content: $(cid),
        cancel:function(){
        	if(can){
        		window.location.href=url;
        	}
        }
    });
}
//关闭页
function ClosePage(index){
	layer.close(index);
}

//访问服务器数据
function AjaxURL(URL,DATA,fc,id,page){
	$.ajax({
        type: 'POST',
        url: URL,
        data: DATA,
        dataType: 'json',
        cache: false,
        //contentType:'application/json',
        beforeSend: function(){
            indexLoad=layer.msg('拼命加载中……', {
                time:false,
                icon: 16
            });
        },
        complete: function(){
            layer.close(indexLoad);
        },
        success: function(data,status,XHR){
        	layer.close(indexLoad);
        	if(data.ret_code == 1000){
                page();
            	fc(data,id);
            }
            if(data.ret_code == 1001){
                // if(data.ret_msg == '请先升级VIP'){
                //     fc('empty',id);
                // }
            	indexError=layer.msg('没有查到相关数据！'+data.ret_msg, {
	                time:2000,
	                icon: 16
	            });
            }
        },
        error: function(){
            layer.alert('',{
                type:0,
                title:['错误信息','color:#FF34B3;font-size:28px;font-famliy:"隶书";'],
                area:['300px','200px'],
                content:'放弃治疗吧，服务器是不会理你的，你再等下去也没什么卵用的！',
                icon:5,
                btn:['确定','取消'],
                yes:function(index,layero){
                    layer.close(index);
                },
                cancel:function(index){
                    layer.close(index);
                }
            });
        }
    });
}

function login(){

    layer.open({
        title : '登录',
        type : 1,
        closeBtn : 2,
        area : ['400px','auto'],
        content : $('#roe-login')
    });
}

// 最新资讯轮播
function news(obj){
    var len = obj.find('li').length;
    var i = 0;
    var inow = 0;
    obj.timer = setInterval(timer, 2500);
    function timer(){
        if(i == 0){
            inow = 0;
            obj.find('li').eq(0).css('position','static');
            obj.css('top', '0');
        }
        if(i == len-1){
            i = 0;
            obj.find('li').eq(0).css({
                position: 'relative',
                top: len*30
            });
        }else{
            i++;
        }
        inow++;
        obj.animate({top : -inow*30}, 400);
    }

    obj.on('mousemove',function(){
        clearInterval(obj.timer);
    });

    obj.on('mouseout',function(){
        obj.timer = setInterval(timer, 2500);
    });
}

// 地区
function addData(data,obj,name){
    if(data.ret_code == 1000){
        var html = '';
        for(var i=0; i<data.total_lines; i++){
            html += '<option value="'+data.data[i].region_name+'">'+data.data[i].region_name+'</option>';
        }
        obj.find('select[name="'+name+'"]').append(html);
    }
}

//院校、专业录取分数线条件判断
function panduan(id,name1,name2,name3,name4){
    //$(id).submit(function(event) {
        name1 = $(id).find('select[name="'+name1+'"]').val();
        name2 = $(id).find('select[name="'+name2+'"]').val();
        name3 = $(id).find('select[name="'+name3+'"]').val();
        name4 = $(id).find('select[name="'+name4+'"]').val();
        if(name1 == -1 || name2 == -1 || name3 == -1 || name4 == -1 ){
            layer.msg('请选择正确的选项。');
            return false;
        }
    //});
}

//专业录取分数线
function zylqsearchfn(name,url,value,content,contname){
    $('#roe-fraction').find('select[name="'+name+'"]').change(function() {
        var dat = $(this).val();
        if(dat != -1){
            ajaxdata(url,'POST',name+'='+dat,zylqfsx1);
            function zylqfsx1(data){
            //$.post(url,name+'='+dat,function(data){
                if(data.ret_code == 1000){
                    var html = '<option value="-1">'+contname+'</option>';
                    for(var i=0; i<data.total_lines; i++){
                        html += '<option value="'+data.data[i][value]+'">'+data.data[i][content]+'</option>';
                    }
                    $('#roe-fraction').find('select[name="'+value+'"]').html(html);
                }else{
                    $('#roe-fraction').find('select[name="'+value+'"]').html('<option value="-1">'+contname+'</option>');
                    layer.msg('很抱歉，没有查询到'+contname+'相关数据。');
                }
            //},'json');
            }
        }
    });
}

//根据地区查院校名称
function yxnamesearch(url){
    $('#roe-school').find('select[name="yxaddress"],select[name="km"],select[name="pc"]').change(function() {
        var km = $('#roe-school').find('select[name="km"]').val();
        var pc = $('#roe-school').find('select[name="pc"]').val();
        var yxaddress = $('#roe-school').find('select[name="yxaddress"]').val();
        var address = $('#roe-school').find('select[name="address"]').val();
        if(km != -1 && pc != -1 && yxaddress != -1){
            ajaxdata(url,'POST','area='+yxaddress+'&km='+km+'&pc='+pc,areaYxname);
            function areaYxname(data){
            //$.post(url,'area='+yxaddress+'&km='+km+'&pc='+pc,function(data){
                if(data.ret_code == 1000){
                    var html = '<option value="-1">院校名称</option>';
                    for(var i=0; i<data.total_lines; i++){
                        html += '<option value="'+data.data[i].cid+'">'+data.data[i].name+'</option>';
                    }
                    $('#roe-school').find('select[name="cid"]').html(html);
                }else{
                    $('#roe-school').find('select[name="cid"]').html('<option value="-1">院校名称</option>');
                    layer.msg('很抱歉，没有查询到院校名称相关数据。');
                }
            //},'json');
            }
        }
    });
}
function ajaxdata(URL,method,dat,callback){
    $.ajax({
        url : URL,type : method,data : dat,dataType : "json",
        beforeSend: function(){
            Load=layer.msg('加载中',{
                time:false,
                icon: 16
            });
        },
        complete: function(){
            layer.close(Load);
        },
        success: function(data){
            layer.close(Load);
            callback(data);
        },
        error: function(jqXHR,textStatus,errorThrown){
            layer.close(Load);
            layer.alert('很抱歉！没有查询到符合条件的招生信息；请更改条件后重新查询。',{icon:5});
        }
    });
}

//清除缓存
function delCookie(){
    $.cookie('zydUpdate',null,{expires:-1,path:paths});
    $.cookie('zyFormName',null,{expires:-1,path:paths});
    $.cookie('zyFormA',null,{expires:-1,path:paths});
    $.cookie('zyFormB',null,{expires:-1,path:paths});
    $.cookie('zyFormC',null,{expires:-1,path:paths});
}
function kms(km){
    switch(km){
        case '理科':
            return 0;
        case '文科':
            return 1;
    }
}
function kmnum(km){
    switch(km){
        case '0':
            return '理科';
        case '1':
            return '文科';
    }
}
