/*
* @Author: CLS
* @Date:   2017-04-19 11:15:35
* @Last Modified by:   Administrator
* @Last Modified time: 2017-05-27 14:28:37
*/

'use strict';
// 定义基础字体在640下大小是16px;

function IsPC() {//判断是否为pc
    var userAgentInfo = navigator.userAgent;
    var Agents = ["Android", "iPhone","SymbianOS", "Windows Phone", "iPad", "MeeGo", "BB10"];
    var flag = true;
    for (var v = 0; v < Agents.length; v++) {
        if (userAgentInfo.indexOf(Agents[v]) > 0) {
            flag = false;
            break;
        }
    }
    return flag;
}
if (sessionStorage.userPhoneWidth) {//防止元素在有虚拟键盘弹出时返回产生bug,将手机端宽高临时存于本地(存宽)
    var screenX = sessionStorage.userPhoneWidth;
} else {
    var screenX = window.innerWidth ? window.innerWidth : document.body.clientWidth;//获取页面宽
    sessionStorage.userPhoneWidth = screenX;
}
if (sessionStorage.userPhoneHeight) {//存高
    var screenY = sessionStorage.userPhoneHeight;
} else {
    var screenY = window.innerHeight ? window.innerHeight : document.body.clientHeight;//获取页面高
    sessionStorage.userPhoneHeight = screenY;
}
var oldScreenY = screenY;

$(function(){
    $("html").css("font-size", screen.width/640 * 16);
    (function () {//input框获取焦点时状态
        var boxChangeTopIn = 0;
        if (IsPC()) {
            return false;
        }
        $("input").on("focus", function () {
            console.log($(this).attr("type"));
            if($(this).attr("type") == "checkbox" || $(this).attr("type") == "button" || $(this).attr("type") == "date"){
                //alert('000');

                return false;
            }
            var box = $(this);
            if (box.offset().top < oldScreenY * 0.3) {
                return false;
            }
            setTimeout(function () {
                var newScreenY = window.innerHeight ? window.innerHeight : document.body.clientHeight;//获取焦点后页面高
                var boxBttom = oldScreenY - box.offset().top;//元素据下边距距离
                var boxNewShow = oldScreenY - newScreenY;//虚拟键盘可视高度大小
                boxChangeTopIn = $(".content").css("margin-top");
                $(".content").css("transition", "all 0.2s");
                $(".content").css("margin-top", (-1 * (box.offset().top - oldScreenY * 0.3)));
            }, 300);
        }).on("blur", function () {
            $(".content").css("margin-top", boxChangeTopIn);
        });
    })();


})


// 控制内容盒子大小
window.onload = function () {
    var screenY = window.innerHeight ? window.innerHeight : document.body.clientHeight;//获取页面高
    var screenX = window.innerWidth ? window.innerWidth : document.body.clientWidth;//获取页面宽
    if ($(".footer").height()) {
        $(".content").css("height", screenY - $(".header").height() - $(".footer").height());
    } else {
        $(".content").css("height", screenY - $(".header").height());
    };
    if ($(".header").height()) {
        $(".content").css("top", Math.floor($(".header").height()));
    } else {
        $(".content").css("top", 0);
    };

};
