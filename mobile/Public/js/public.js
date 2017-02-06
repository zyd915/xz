(function (doc, win) {
    var docEl = doc.documentElement,
    resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
    recalc = function () {
        var clientWidth = docEl.clientWidth;
        if (!clientWidth) return;
        docEl.style.fontSize = 100 * (clientWidth / 750) + 'px';
    };
    if (!doc.addEventListener) return;
    win.addEventListener(resizeEvt, recalc, false);
    doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);

function bannerSlider(obj, time){
    var len = obj.find('li').size(),
        num = 0,
        i = 0,
        width = obj.find('li').eq(0).width(),
        downTime = 0,
        downLeft = 0,
        downX = 0,
        sliderNav = '';
    if(len < 2) {
        return;
    }else{
        var ol = $("<ol />", {id:"sliderNav", css:{position:'absolute',bottom: 4,left: '50%'} });
        for(var j=0; j<len; j++){
            sliderNav += '<li></li>';
        }
        ol.append(sliderNav);
        obj.append(ol);
        $('#sliderNav').css( 'margin-left',-$('#sliderNav').width()/2 ).find('li').first().addClass('active');
    }
    obj.timer = setInterval(callback, time * 1000);
    function callback(){
        if (i == 0) {
            num = 0;
            obj.find('ul').css('left',0).find('li').eq(0).css({'position':'static',left:0});
        }
        if (i == len-1) {
            i = 0;
            obj.find('li').eq(0).css({position:'relative',left: len*width});
        }else{
            i++;
        }
        num++;
        $('#sliderNav').find('li').removeClass('active').eq(i).addClass('active');
        obj.find('ul').animate({left: -num*width + 'px'}, 300);
    }
    obj.on('touchstart', 'ul', function(e){
        clearInterval(obj.timer);
        downLeft = $(this).position().left;
        downTime = Date.now();
        downX = e.touches[0].pageX;
    })
    .on('touchmove', 'ul', function(e){
        e.preventDefault();
        if($(this).position().left > 0){
            $(this).find('li').last().css({position:'relative',left: -len*width});
        }else if($(this).position().left < obj.width()-obj.find('ul').width()){
            obj.find('li').eq(0).css({position:'relative',left: len*width});
        }
        $(this).css('left',e.touches[0].pageX-downX+downLeft);
    })
    .on('touchend', 'ul', function(e){
        if(e.changedTouches[0].pageX < downX){
            if(num != len-1){
                if(Math.abs(e.changedTouches[0].pageX - downX) > 80 || Date.now() - downTime < 300 && Math.abs(e.changedTouches[0].pageX - downX) > 25){
                    num++;
                    i++;
                }
                obj.find('ul').animate({left: -num*width}, 300);
                $('#sliderNav').find('li').removeClass('active').eq(i).addClass('active');
            }else{
                if(Math.abs(e.changedTouches[0].pageX - downX) > 80 || Date.now() - downTime < 300 && Math.abs(e.changedTouches[0].pageX - downX) > 25){
                    num++;
                    obj.find('ul').animate({left: -num*width}, 300, function(){
                        obj.find('ul').css('left',0).find('li').eq(0).css({'position':'static',left:0});
                        i=0;
                        num=0;
                        $('#sliderNav').find('li').removeClass('active').eq(i).addClass('active');
                    });
                }else{
                    obj.find('ul').animate({left: -num*width}, 300, function(){
                        obj.find('li').eq(0).css({position:'static'});
                    });
                }
            }
        }else if(e.changedTouches[0].pageX > downX){
            if( num != 0){
                if(Math.abs(e.changedTouches[0].pageX - downX) > 80 || Date.now() - downTime < 300 && Math.abs(e.changedTouches[0].pageX - downX) > 25){
                    num--;
                    i--;
                }
                obj.find('ul').animate({left: -num*width}, 300);
                $('#sliderNav').find('li').removeClass('active').eq(i).addClass('active');
            }else{
                if(Math.abs(e.changedTouches[0].pageX - downX) > 80 || Date.now() - downTime < 300 && Math.abs(e.changedTouches[0].pageX - downX) > 25){
                    num--;
                    obj.find('ul').animate({left: -num*width + 'px'}, 300,function(){
                        obj.find('ul').css('left',obj.width()-obj.find('ul').width()).find('li').last().css({'position':'static'});
                        i = num = len-1;
                        $('#sliderNav').find('li').removeClass('active').eq(i).addClass('active');
                    });
                }else{
                    obj.find('ul').animate({left: -num*width}, 300, function(){
                        obj.find('li').last().css({position:'static'});
                    });
                }
            }
        }
        obj.timer = setInterval(callback, time * 1000);
    });
}

//请求服务器数据
function ajaxdata(URL,method,dat,callback){
    var Load;
    $.ajax({
        url : URL,
        type : method,
        data : dat,
        dataType : "json",
        beforeSend: function(){
            Load = layer.open({type: 2});
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
            layer.open({
                content: '抱歉，出错了！',
                time: 2 //2秒后自动关闭
            });
        }
    });
}

// 设置Cookie
function setCookie(key, value, t) {
    var oDate = new Date();
    oDate.setDate( oDate.getDate() + t );
    document.cookie = key + '=' + value + ';expires=' + oDate.toGMTString();
}
// 取出Cookie的值
function getCookie(key) {
    var arr1 = document.cookie.split('; ');
    for (var i=0; i<arr1.length; i++) {
        var arr2 = arr1[i].split('=');
        if ( arr2[0] == key ) {
            return decodeURI(arr2[1]);
        }
    }
}
function removeCookie(key) {
    setCookie(key, '', -1);
}

function tdmc(mc){
    if(typeof mc == 'string'){
        if(mc == ''){
            return '-';
        }else{
            return mc;
        }
    }else if(typeof mc == 'number'){
        return mc;
    }else {
        return '<a href="javascript:;" class="vipCard">限VIP用户查看</a>';
    }
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
function pcs(pc){
    switch(pc){
        case '0': return '提前批';
        case '1': return '本科一批';
        case '2': return '本科二批';
        case '3': return '专科一批';
        case '4': return '专科二批';
    }
}
