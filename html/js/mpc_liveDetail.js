//处理url
    if(location.href.split('&')[1].split('=')[0] == "from"){
        location.href = location.href.split('&')[0];
    }
//判断是否付费    
    var id = location.search.split('=')[1];
 $.ajax({
    url:"http://hr.hongrunet.com/Home/Letv/fee",
    dataType:"jsonp",
    data:{lid:id},
    jsonpCallback:'jsonpReturn20',
    success: function (msg) {
        if(msg.status == 2){
            window.location.href = "kll_payment2.html?m="+msg.data.money+"&lid="+id;
        }
     }
}); 
//加载直播详情
$.ajax({
        url : 'http://hr.hongrunet.com/home/Letv/letvDetail',
        data : {id:id,url:location.href},
        dataType : 'jsonp',
        jsonpCallback : 'jsonpReturn1',
        success : function (req){
            if(req.status==10){
                location.href = "http://hr2.hongrunet.com/html/jyt_login.html";
            }
            $('#jsid').val(req.data.js.id)
             if(req.data.jsapi_config != undefined){
                var configs = JSON.parse(req.data.jsapi_config);
                wx.config(configs);
                wx.ready(function(){
                    wx.onMenuShareTimeline({
                        title: '我在鸿儒讲堂里开启了《'+letv.title+'》直播，全民直播时代已经到来了，同学们快来加入一起分享红利吧！', // 分享标题
                        link: "http://hr2.hongrunet.com/html/lf_liveDetail.html?id="+id, // 分享链接
                        imgUrl: 'http://hr2.hongrunet.com/jyt_image/jyt_register_logo1.png', // 分享图标
                        success: function() {
                            // 用户确认分享后执行的回调函数
                            window.href = "http://hr.hongrunet.com/Home/Wechat/code.html?pid="+id;
                        },
                        cancel: function() {

                            // 用户取消分享后执行的回调函数
                            window.href = "http://hr.hongrunet.com/Home/Wechat/code.html?pid="+id;
                        }
                    });
                    wx.onMenuShareAppMessage({
                        title: '鸿儒商学讲堂', // 分享标题
                        desc: '我在鸿儒讲堂里开启了《'+letv.title+'》直播，全民直播时代已经到来了，同学们快来加入一起分享红利吧！', // 分享描述
                        link: "http://hr2.hongrunet.com/html/lf_liveDetail.html?id="+id, // 分享链接
                        imgUrl: 'http://hr2.hongrunet.com/jyt_image/jyt_register_logo1.png', // 分享图标
                        type: '', // 分享类型,music、video或link，不填默认为link
                        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                        success: function() {
                             // 用户确认分享后执行的回调函数
                            window.href= "http://hr.hongrunet.com/Home/Wechat/code.html?pid="+userid;
                        },
                        cancel: function() {

                             // 用户取消分享后执行的回调函数
                            window.href= "http://hr.hongrunet.com/Home/Wechat/code.html?pid="+userid;
                        }
                    });
                });
                wx.error(function(res){
                    console.log(res);
                    // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                });
            }
            if(req.status == 10){
                alert(req.msg);
                window.location.href="jyt_register.html";return;
            }
            var js = req.data.js;
            var letv = req.data.letv;
            var timestamp1 = Date.parse( new Date());
                timestamp1 = timestamp1/1000
            if(letv.endtime == timestamp1){
                alert("直播已结束！")
            }
            $('#guanzhu').attr('userid',js.id);
            if(req.data.type != 0){
                $('#guanzhu_type').html('已关注');
            }
            $('.js_headimg').attr('src',js.headimg);
            $('.js_name').html(js.name);  
            var player = new CloudLivePlayer();
            var playerConf = {
                activityId : letv.activityid,
                pic : letv.coverimgurl,
                auto_play : 1,
                dfull : 0,
                autoSize : 1,
            }
            player.init(playerConf,'playerid');
            var a = $('.lf_liveimg').css('height');
            console.log(a);
            if(a == '0px'){
                alert('直播开始时间为:　　　　'+req.data.letv.start);
            }
        },
        error : function (err){
            console.log(err);
        }
    });
//关注事件
 $('#guanzhu').click(function(){
        var uid = $('#guanzhu').attr('userid');
        var ajax_url = 'http://hr.hongrunet.com/home/UserCenter/clickFollow';
        var guanzhu_type = $('#guanzhu_type').html();
        if('已关注' == guanzhu_type){
            ajax_url = 'http://hr.hongrunet.com/home/UserCenter/clickNoFollow';
        }else{
            ajax_url = 'http://hr.hongrunet.com/home/UserCenter/clickFollow';
        }
        $.ajax({
            url : ajax_url,
            data : {id:uid},
            dataType : 'jsonp',
            jsonpCallback : 'jsonpReturn2',
            success : function (req){
                if(req.msg == '取消关注成功'){
                    $('#guanzhu_type').html('关注TA');
                }else{
                    $('#guanzhu_type').html('已关注');
                }
                console.log(req);
            },
            error : function (err){
                console.log(err);
            }
        })
    });
//直播聊天室
$.ajax({
        url : 'http://hr.hongrunet.com/home/index/my_info',
        data : {},
        dataType : 'jsonp',
        jsonpCallback : 'jsonpReturn',
        success : function (req){
            if(req.status == 1000 || req.status == 2000 || req.status == 3000){
                location.href = req.url;
            }
            user_info = req.data;
            hudong();
        },
        error : function (err){
            console.log(err);
        }
    })

    function hudong(){
        if(!id){
            return false;
        }
        // console.log(user_info);
        var socket = io.connect('http://47.93.27.22:666');
        var nr = '';
        var user_name = user_info.name;
        var headimg = '../images/wy_group-2.png';
        var room = 'shipin_'+id;
        socket.on('connect', function () {
            socket.emit('join', user_name, room);
        });
        $('#fabiao').click(function(){
            if(user_info.headimg){
                headimg = user_info.headimg;
            }
            nr = $('#nr').val();
            if(nr == ''){
                return false;
            }

            $('#nr').val('');
            socket.emit('foo',user_name,headimg,nr);    //像服务端发送信息
        })
        var hh = $("#scroll")[0].scrollHeight
        var content = '';
        socket.on('req',function(name,img,nr){   //监听返回消息
            content = '<li class="lf_clearFloat"><img class="lf_student" src="'+img+'" alt=""/><p class="lf_studentname" style="font-size:15px;"><span >'+name+'：</span>'+nr+'</p></li>';
            if(name == '系统消息'){
                content = '<li class="lf_clearFloat"><img class="lf_student" src="http://wx.qlogo.cn/mmopen/cmHHdPQXExXnUdmnBDMonSMtXtQnSLbsA5jeDWNVrmxpgriaqHr41N4KIOWU7TicLkTq9qdctka8F1Rk62EB7ge9BXqMWmxBUk/0" alt=""/><p class="lf_studentname" style="font-size:12px;color:red;"><span style="font-size:15px;">'+name+'：</span>'+nr+'</p></li>';
            }
            $('#content').append(content);      
            $("#scroll").scrollTop( $("#scroll")[0].scrollHeight);     
        })
        socket.on('usernum',function(usernum){  //监听在线用户人数
            $('.user_num').html(usernum);
        })
        if(location.hash){
            var hash = location.hash.split('#')[1];
            $.ajax({
                url : 'http://hr.hongrunet.com/home/Test/getOrderInfo',
                data : {id:hash},
                dataType : 'jsonp',
                jsonpCallback : 'jsonpReturn10',
                success : function (req){
                    console.log(req);
                    if(req.status == 1){
                        if(req.data.type == '8' && req.data.status == '2'){
                             socket.emit('foo','系统消息',headimg,user_name+'向主播打赏了'+req.data.money+'元');    //像服务端发送信息
                        }
                    }
                    location.hash = '';              
                },
            });
        }
    }

//打赏事件
$(".lf_shang").on("touchstart",function(){
     $(".lf_motaikuang-live").css("display","block");
     $("video").css({display:"none"});
      $(".lf_close span").on("touchstart",function(){
        $(".lf_motaikuang-live").css("display","none");
        $("video").css({display:"block"});
      });
    //打赏去支付
    $(".ds").on("touchstart",function(){
        $.ajax({
            url : 'http://hr.hongrunet.com/home/Course/das',
            data : {},
            dataType : 'jsonp',
            jsonpCallback : 'jsonpReturn2',
            success : function (req){
                if(req.status == 0){
                    alert('请先去登录');
                }
            },
            error : function (err){
                alert('网络错误');
            }
        });
        var str = $(this).attr('id');
        var money = str.replace(/ds/,"");   //打赏金额
        var jsid  = $("#jsid").val();
        var lid = location.search.split('=')[1];
        window.location.href = "kll_payment.html?m="+money+"&js="+jsid+"&type=8"+"&lid="+lid;
    });
});
//    