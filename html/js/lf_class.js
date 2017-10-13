/*
* @Author: Administrator
* @Date:   2017-04-27 19:32:53
* @Last Modified by:   Administrator
* @Last Modified time: 2017-05-26 20:16:19
*/

'use strict';
$(function(){
	if ($(".footer")) {
		$(".content").css("height", window.screen.availHeight - $(".header").height() - $(".footer").height());
	} else {
		$(".content").css("height", window.screen.availHeight - $(".header").height());
	}
	$(".content").css("top", Math.floor($(".header").height()));
	if ($(".footer")) {
		$(".lf_teshu-content").css("height", document.body.clientHeight - $(".header").height() - $(".footer").height());
	} else {
		$(".lf_teshu-content").css("height", document.body.clientHeight - $(".header").height());
	}
	$(".lf_teshu-content").css("top", $(".header").height() - 1);
	
    /*我的关注页面点击弹出模态框*/
    /*$(".lf_iconTC").off("touchstart").on("touchstart",function(){
          var that = $(this);
          $(".lf_motaikuang").css("display","block");
          $(".lf_cancel").off("touchstart").on("touchstart",function(){
               $(".lf_motaikuang").css("display","none");
                $(".lf_confirm").unbind();
          });
          $(".lf_confirm").off("touchstart").on("touchstart",function(){
               $(".lf_motaikuang").css("display","none");
               that.parent(".lf_fans-item").css("display","none");
               //console.log($(this));
          });

    });*/
     /*我的粉丝页面*/
    /* $(".lf_attention").on("touchstart",function(){
          $(this).css("color","#757575");
          $(this).find("i").removeClass('icon-jiaguanzhu').addClass('icon-yiguanzhu');
          $(this).find("span").text("已关注");
     });*/
     /*提现记录页面*/
     var lis = $(".lf_txitems .lf_tx-item").length;
     //console.log(lis);
     $(".lf_summary-wrap").find("p").text("￥"+lis*100+".00");
     /*直播详情页面 点击弹出模态框*/
    $(".lf_livefooter .lf_shang").on("touchstart",function(){
           $(".lf_motaikuang-live").css("display","block");
          $(".lf_motaikuang-live .lf_close span").on("touchstart",function(){
              $(".lf_motaikuang-live").css("display","none");
          })
    });
    /*设置管理员*/
     var $adminBox = $(".lf_administratorList");
     var adminArray = [
          {
               img:"../lf_img/lf_admin02.png",
               admin:"B从前有座山",
               icon:"icon-guanliyuan",
               txt:"管理"
          },
          {
               img:"../lf_img/lf_admin03.png",
               admin:"Cjon Snow",
               icon:"icon-guanliyuan",
               txt:"管理"
          },
           {
               img:"../lf_img/lf_admin04.png",
               admin:"D偏执狂",
               icon:"",
               txt:""
          },
          {
               img:"../lf_img/lf_portrait.png",
               admin:"E阿呆不呆",
               icon:"",
               txt:""
          },
           {
               img:"../lf_img/lf_admin05.png",
               admin:"FAaron",
               icon:"",
               txt:""
          },
           {
               img:"../lf_img/lf_admin06.png",
               admin:"G奔跑的蜗牛",
               icon:"",
               txt:""
          },
          
           {
               img:"../lf_img/lf_admin01.png",
               admin:"G安安和静静",
               icon:"",
               txt:""
          }
     ]
     function adminCon(_array){
          $.each(_array,function(index,value){
               var $img = $("<img>").attr("src",value.img);
               var $span1 = $("<span>").addClass('lf_setName').text(value.admin);
               var $i = $("<i>").addClass('iconfont'+' '+value.icon);
               var $span3 = $("<span>").text(value.txt);
               var $span2 = $("<span>").addClass('lf_setAdmin');
               $span2.append($i);
               $span2.append($span3);
               var $ul = $("<ul>").addClass("lf_seting");
               var $li2 = $("<li>").text("设为管理员");
               var $li3 = $("<li>").text("踢出房间");
               var $li4 = $("<li>").text("取消管理员");
               $ul.append($li2);
               $ul.append($li3);
               $ul.append($li4);
               var $li = $("<li>").addClass('lf_adminList lf_adminList1');
               $li.append($img);
               $li.append($span1);
               $li.append($span2);
               $li.append($ul);
               $adminBox.append($li);
                     
          });

     }
    adminCon(adminArray);  
    /*邀请同学*/
    var $classBox = $(".lf_classmateList");
    var classArray = [
          {
               img:"../lf_img/lf_admin01.png",
               admin:"A从前有座山",
          },
          {
               img:"../lf_img/lf_admin02.png",
               admin:"B从前有座山",
          },
          {
               img:"../lf_img/lf_admin03.png",
               admin:"Cjon Snow",
          },
           {
               img:"../lf_img/lf_admin04.png",
               admin:"D偏执狂",
          },
          {
               img:"../lf_img/lf_portrait.png",
               admin:"E阿呆不呆",
          },
           {
               img:"../lf_img/lf_admin05.png",
               admin:"FAaron",
          },
           {
               img:"../lf_img/lf_admin06.png",
               admin:"G奔跑的蜗牛",
          },
          
           {
               img:"../lf_img/lf_admin01.png",
               admin:"G安安和静静",
          }
     ];
    
     function classCon(_array){
          $.each(_array,function(index,value){
               var $img = $("<img>").attr("src",value.img);
               var $span1 = $("<span>").addClass('lf_setName').text(value.admin);
               var $span2 = $("<span>").addClass('iconfont icon-not_selected lf_xuanze');
               var $li = $("<li>").addClass('lf_adminList lf_adminList2');
               $li.append($img);
               $li.append($span1);
               $li.append($span2);
               $classBox.append($li);
                     
          });

     }
    classCon(classArray);  
    $(".lf_adminList2").on("touchstart",function(){
        $(this).find($(".lf_xuanze")).toggleClass("icon-xuanzhong1");
    });
    
    //设为管理员
    $(".lf_adminList1").on("touchstart",function(){
         $(".lf_adminList1").css(
               {
                    background:"url('../lf_img/lf_setlibg.png') no-repeat;",
                    backgroundSize:"100% 100%"
               }
          );
         $(".lf_adminList1").eq($(this).index()).css(
               {
                    background:"url('../lf_img/lf_setlibg2.png') no-repeat;",
                    backgroundSize:"100% 100%"
               }
          );
         $(".lf_adminList1").find("ul").css("display","none");
         $(".lf_adminList1").eq($(this).index()).find("ul").css("display","block");
         
    });
    /*同学聊天室*/
    var $chatBox = $(".lf_chatRoom");
     var chatArray = [
          {
               img:"../lf_img/lf_roomimg.png",
               p_con:"什么是正确的人际交往，正确的人际交往",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_videoPortrait.png",
               tea:"小菲老师"
          },
           {
               img:"../lf_img/lf_roomimg.png",
               p_con:"职场人最有效的表达方式,最有效的表达方式...",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_teacher02.png",
               tea:"奇点"
          },
          {
               img:"../lf_img/lf_roomimg.png",
               p_con:"什么是正确的人际交往，正确的人际交往",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_videoPortrait.png",
               tea:"小菲老师"
          },
           {
               img:"../lf_img/lf_roomimg.png",
               p_con:"职场人最有效的表达方式,最有效的表达方式...",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_teacher02.png",
               tea:"奇点"
          },
          {
               img:"../lf_img/lf_roomimg.png",
               p_con:"什么是正确的人际交往，正确的人际交往",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_videoPortrait.png",
               tea:"小菲老师"
          },
           {
               img:"../lf_img/lf_roomimg.png",
               p_con:"职场人最有效的表达方式,最有效的表达方式...",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_teacher02.png",
               tea:"奇点"
          },
          {
               img:"../lf_img/lf_roomimg.png",
               p_con:"什么是正确的人际交往，正确的人际交往",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_videoPortrait.png",
               tea:"小菲老师"
          },
           {
               img:"../lf_img/lf_roomimg.png",
               p_con:"职场人最有效的表达方式,最有效的表达方式...",
               p_num:"8888人已观看",
               pic:"../lf_img/lf_teacher02.png",
               tea:"奇点"
          }
          
     ]
     function chatCon(_array){
          $.each(_array,function(index,value){
               var $img = $("<img>").attr("src",value.img);
               var _div = $("<div>").addClass('lf_zhezhao lf_clearFloat');
               var $img2 = $("<img>").attr("src",value.pic);
               var $span = $("<span>").text(value.tea);
               _div.append($img2).append($span);
               var $div = $("<div>").addClass('lf_chatRoomCon');
               var $p1 = $("<p>").addClass('lf_descriptionCon').text(value.p_con);   
               var $p2 = $("<p>").addClass('lf_yiguankan').text(value.p_num); 
               $div.append($p1).append($p2);
               var $li = $("<li>").addClass('lf_chatRoomlist');
               $li.append($img).append(_div).append($div); 
               $chatBox.append($li);
               $li.on("touchstart",function(){
                  window.location.href='../html/wy_chat.html';
               })
          });

     }
    chatCon(chatArray);  
    /*积分互转页面*/
    $(".lf_integralTop .lf_integralitem").on("touchstart",function(){
     
        $(".lf_integralitem").removeClass('lf_integralitemon');
        $(".lf_integralitem").eq($(this).index()).addClass('lf_integralitemon');
        $(".lf_integralBot>div").css("display","none");
        $(".lf_integralBot>div").eq($(this).index()).css("display","block");
    });

})
