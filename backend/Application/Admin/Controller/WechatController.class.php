<?php
namespace Admin\Controller;
use Think\Controller;
use Service\Wechat as Wechats;


class WechatController extends Controller{
	private $url = 'http://hr.hongrunet.com/Home/Wechat/wechat_login.html?type=';
	/*
	['name'=>'课程分类','two'=>
				[
				['name'=>'分享经济','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_LivList.html'],
				['name'=>'微商制造','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=8'],
				['name'=>'大咖分享','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=1'],
				['name'=>'沟通技巧','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=3'],
				['name'=>'团队建设','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=4']
				]
			],
			['name'=>'视商直播','two'=>
				[
				['name'=>'我的关注','event'=>'view','val'=>'http://hr2.hongrunet.com/html/lf_myAttention.html'],
				['name'=>'我的直播','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_LiveBroadcast.html'],
				['name'=>'我的课程','event'=>'view','val'=>'http://hr2.hongrunet.com/html/kll_myclass.html'],
				['name'=>'上传视频','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_SpeechTop.html']
				]
			],
			['name'=>'学员中心','two'=>
				[
				['name'=>'进入讲堂','event'=>'view','val'=>'http://hr2.hongrunet.com/html/wy_LivList.html'],
				['name'=>'我要推广','event'=>'view','val'=>'http://hr2.hongrunet.com/html/jyt_erweima.html'],
				['name'=>'联系我们','event'=>'view','val'=>'http://hr2.hongrunet.com/html/lf_comprofile.html'],
				['name'=>'APP下载','event'=>'view','val'=>'http://hr2.hongrunet.com/html/kll_index.html'],
				['name'=>'个人后台','event'=>'view','val'=>'http://hr2.hongrunet.com/html/lf_personal.html']
				]
			]

	*/
	//生成菜单
	public function menu(){
        $data = [
			['name'=>'课程分类','two'=>
				[
				['name'=>'演讲高手','event'=>'view','val'=>$this->url.'1'],
				['name'=>'人际沟通','event'=>'view','val'=>$this->url.'2'],
				['name'=>'团队企管','event'=>'view','val'=>$this->url.'3'],
				['name'=>'时事新闻','event'=>'view','val'=>$this->url.'4'],
				['name'=>'我的朋友圈','event'=>'view','val'=>$this->url.'5']
				]
			],
			['name'=>'视商直播','two'=>
				[
				['name'=>'我的关注','event'=>'view','val'=>$this->url.'6'],
				['name'=>'我的直播','event'=>'view','val'=>$this->url.'7'],
				['name'=>'我的课程','event'=>'view','val'=>$this->url.'8'],
				['name'=>'上传视频','event'=>'view','val'=>$this->url.'9']
				]
			],
			['name'=>'学员中心','two'=>
				[
				['name'=>'进入讲堂','event'=>'view','val'=>$this->url.'10'],
				['name'=>'我要推广','event'=>'view','val'=>$this->url.'11'],
				['name'=>'联系我们','event'=>'view','val'=>$this->url.'12'],
				// ['name'=>'APP 下载','event'=>'view','val'=>$this->url.'13'],
				['name'=>'个人后台','event'=>'view','val'=>$this->url.'14']
				]
			]
			
		];
		dump(Wechats::menu_create($data));
  	}	
	#回调方法
    public function notify(){
    	# 监听关注事件
		Wechats::addEvent('subscribe',function($result){
			$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	      	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
	       $fromUsername = $postObj->FromUserName; //请求消息的用户
	       $Event = $postObj->Event; // 事件类型
	       $toUsername = $postObj->ToUserName; //"我"的公众号id
	       $keyword = trim($postObj->Content); //消息内容
	       $time = time(); //时间戳
	       $msgtype = 'text'; //消息类型：文本
	       if($Event == 'subscribe'){
	        echo "<xml>
	            <ToUserName><![CDATA[$fromUsername]]></ToUserName>
	            <FromUserName><![CDATA[$toUsername]]></FromUserName>
	            <CreateTime>".time()."</CreateTime>
	            <MsgType><![CDATA[text]]></MsgType>
	            <Content><![CDATA[开学啦！同学你好！\n欢迎来到可以改变你命运的《鸿儒商学讲堂》在这里汇聚了各行各业的顶级讲师，将帮助你以最短的时间迈向全方位的成功。\n你的创业：需要我们的出现\n你的改变：将从这里快速实现\n你的未来：原本要比现在更加精彩。\n快点加入我们吧！用有限的时间快速充电。请点击：\n1：<a href='http://hr.hongrunet.com/Home/Wechat/wechat_login.html'>学员注册</a>\n2：<a href='http://hr.hongrunet.com/Home/Wechat/wechat_login.html?type=10'>进入讲堂</a>\n未来的你一定会感激现在的自己。加油！]]></Content>
	            </xml>";
	       }else if($postObj -> MsgType =='text'){
	        echo '<xml>
	            <ToUserName><![CDATA['.$fromUsername.']]></ToUserName>
	            <FromUserName><![CDATA['.$toUsername.']]></FromUserName>
	            <CreateTime>'.time().'</CreateTime>
	            <MsgType><![CDATA[text]]></MsgType>
	            <Content><![CDATA[有任何疑问请联系客服人员:]]></Content>
	            </xml>';
	       }
			# 判断是否存在此用户
			if(M('user') -> where(['openid'=>$result['FromUserName']]) -> find()){
				return true;
			}
			# 获取用户信息
			$userinfo = Wechats::get_openid_user_info($result['FromUserName']);
			# 用户数组
			$data = [];
			# 判断是否存在票据
			if($result['Ticket']!=''){
				# 获取上级信息
				if($puser = M('user') -> where(['wx_qrcode'=>$result['Ticket']]) -> find()){
					// 设置上级id
					$data['pid'] = $puser['id'];
					$insert['pid'] = $data['pid'];
				}
			}
			# 用户唯一标识
			$data['openid'] = $userinfo['openid'];
			# 性别 1=男 2=女性 0=未设置
			if($userinfo['sex']=='1'){
				$data['sex'] = '男';
			}else if($userinfo['sex']=='2'){
				$data['sex'] = '女';
			}else{
				$data['sex'] = '男';
			}
			# 用户昵称
			$data['name'] = $userinfo['nickname'];
			# 头像
			$data['headimg'] = $userinfo['headimgurl'];
			$data['createtime'] = time();
			# 插入用户数据
			// M('user') -> add($data);
			$ress = M('user') -> add($data);
			$insert['uid'] = $ress;
			// $insert['pid'] = $data['pid'];
			$insert['info'] = serialize($userinfo);
			$insert['createtime'] = time();
			M('user_log')->add($insert);
		});
    }


	//微信支付成功回调
    public function payNotify(){
        # 监听回调通知
        Wechats::notitfy(function($notify){
        	$pay_order_num = (String) $notify['out_trade_no'];       	
        	$account = M('account');
            $order = M('order')->where(['pay_order_num'=>(String) $pay_order_num])->find();
            if($order['status'] == 2){
                return;
            }else{
                M('order')->where(['pay_order_num'=>(String) $pay_order_num])->save(['status'=>2]);
            }
            if($order['type'] == 1){
            	letvs($order);
            	return;    
            }else if($order['type'] == 2 || $order['type'] == 3 || $order['type'] == 4 ){
            	file_put_contents('tt.txt', $pay_order_num.'---',FILE_APPEND);
                //改订单状态,分佣
                fxReturnMoney($pay_order_num);
                return;
            }else if($order['type'] == 6 ) {  //打赏
            	$dsid = $order['remark'];
	            $dd['uid']    = M('course')->where(['id'=>$order['remark']])->getField('uid');
	            $dd['money']  = $order['money'];
	            $dd['source'] = $order['uid'];
	            $dd['message']= '收到红包';
	            $dd['createtime']= time();
	            $dd['paymenttype'] = '微信支付';
	            $results= $account->add($dd);
	            return;
            }else if( $order['type'] == 5 ){    //购买权限卡
                $orr['user_id'] = $order['uid'];
                $orr['add_time']= time();
                M('uservip')->add($orr);
                return;
            }else if($order['type'] == 7){
            	/*$dsid = M('letv')->where(['id'=>$order['remark']])->getField('uid');
	            $ddd['uid']    = $dsid;
	            $ddd['money']  = $order['money'];
	            $ddd['source'] = $order['uid'];
	            $ddd['message']= '观看直播';
	            $ddd['createtime']= time();
	            $ddd['paymenttype'] = '微信支付';
	            $results= $account->add($ddd); */
	            return ;

            }else if($order['type'] == 8){
                $dsid = M('letv')->where(['id'=>$order['message']])->getField('uid');
                $dddd['uid']           = $order['remark'];
                $dddd['money']         = $order['money'];
                $dddd['source']        = $order['uid'];
                $dddd['message']       = '收到红包';
                $dddd['createtime']    = time();
                $dddd['paymenttype']   = '微信支付';
                $results= $account->add($dddd);
                return;
            }
        });
    }
 


}