<?php
namespace Home\Controller;

use Service\Wechat as Wechats;
use Think\Controller;
use Service\File;

# 前台微信控制器
class WechatController extends Controller {	
	public function http(){
		exit(http($_GET['url']));
	}
	# 账号手机绑定
	public function bind(){
		# 判断是否为提交绑定
		if(IS_POST){
			# 验证账号密码
			$phone    = I('phone');
			$password = MD5(I('password'));
			$code	  = I('code');
			$wxinfo = M('user')->where(["id"=>$_SESSION['userid']])->find();
			if ($code != $_SESSION['code']) {
			 	$this->ajaxReturn(["status"=>0,"msg"=>"验证码错误"]);
	        }
	   		$user = M('user')->where(["phone"=>$phone])->find();
	   		if (!empty($user['openid'])) {
				$this->ajaxReturn(array("status"=>0,"msg"=>"您已经绑定过了"));
			}
			if(!empty($user)){		
				if(empty($user["openid"])){  #手机用户
					if ($password != $user['password']) {
						$this->ajaxReturn(array("status"=>0,"msg"=>"密码错误"));
					}
					$daa['openid'] = $wxinfo['openid'];
					$daa['headimg'] = $wxinfo['headimg'];
					$daa['sex'] 	= $wxinfo['sex'];
					$res = M('user')->where(["phone"=>$phone])->save($daa);
					$res1 = M('user')->where(['id'=>$_SESSION['userid']])->delete();
					if($res && $res1){
						session('userid',null);
						session('home',null);
						$this->ajaxReturn(array("status"=>1,"msg"=>"绑定成功"));
					}else{
							$this->ajaxReturn(array("status"=>0,"msg"=>"绑定失败"));

						}
				}
				
			}else{ 			#非手机用户	
				qrcode($wxinfo['id']);
	            if ( $wxinfo['pid'] >0 ) {   //上级
	                $one = M('user')->field("id,pid,grade")->where(["id"=>$wxinfo['pid']])->find();
                    $onescore = M('score')->where(["source"=>$_SESSION['userid'],"uid"=>$wxinfo['pid'],"message"=>"用户推广"])->select();
                    if ( $onescore[0]["id"] >0 ) {
                    }else{
	                    $daa['uid'] = $wxinfo['pid'];
	                    $daa['source'] = $_SESSION['userid'];
	                    $daa['score'] = 5;
	                    $daa['message'] = "用户推广";
	                    $daa['createtime'] = time();
	                    M('score')->add($daa);
		                if ($one['grade'] == 1) {
	                		upGrade($one['id']);  //积分，学童自动生成学霸
		                }                    	
                    }

	                if ( !empty($one['pid']) || $one['pid'] != null) {
	                    $two = M('user')->field("id,pid,grade")->where(["id"=>$one['pid']])->find();
                    	$twoscore = M('score')->where(["source"=>$_SESSION['userid'],"uid"=>$two['id'],"message"=>"用户推广"])->select();
                        if ( $twoscore[0]["id"] >0 ) {
                        }else{
		                    // if ($two['grade'] == 1) {
		                        $daa1['uid'] = $two['id'];
		                        $daa1['source'] = $_SESSION['userid'];
		                        $daa1['score'] = 3;
		                        $daa1['message'] = "用户推广";
		                        $daa1['createtime'] = time();
		                        M('score')->add($daa1);
		                    // } 
			                if ($two['grade'] == 1) {
		                		upGrade($two['id']);  //积分，学童自动生成学霸
			                }
                        }

	                    if ( !empty($two['pid']) || $two['pid'] != null) {
	                        $three = M('user')->field("id,pid,grade")->where(["id"=>$two['pid']])->find();
                    		$threescore = M('score')->where(["source"=>$_SESSION['userid'],"uid"=>$three['id'],"message"=>"用户推广"])->select();
                            if ( $threescore[0]["id"] >0 ) {
                            }else{ 
		                        // if ($three['grade'] == 1) {
		                            $daa2['uid'] = $three['id'];
		                            $daa2['source'] = $_SESSION['userid'];
		                            $daa2['score'] = 2;
		                            $daa2['message'] = "用户推广";
		                            $daa2['createtime'] = time();
		                            M('score')->add($daa2);
		                        // }                   
				                if ($three['grade'] == 1) {
			                		upGrade($three['id']);  //积分，学童自动生成学霸
				                }	
                            }
                
	                    }                   
	                }
	            } 				
				$da['phone'] 	  = $phone;
				$da['password']  = $password;
				$da['createtime'] = time();
				$da['qrcode'] = 'http://hr.hongrunet.com/Uploads/qrcode/qrcode'.$wxinfo['id'].'.png';
				if(M('user')->where(['id'=>$_SESSION['userid']])->save($da)){				
					$_SESSION['userid'] = $wxinfo['id'];
					$_SESSION['phone'] = $phone;
					$this->ajaxReturn(array("status"=>1,"msg"=>"绑定成功"));
				}else{
					$this->ajaxReturn(array("status"=>0,"msg"=>"绑定失败"));
				}
			}	
		}else{
	  		$this -> display();
		}		
	}
	
	# 扫码后的第一个转折点
	public function code(){
        # 获取上级id
        $pid = I('pid');
		# 判断是否在微信中打开
        if(is_weixin()){
            # 查询上级信息
            $user = M('user') -> where(['id'=>$pid]) -> find();
            # 定义二维码的链接
            $url = '';
            # 判断用户是否生成过微信二维码
            if($user['wx_qrcode']!=''){
                #获取生成过的
                $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$user['wx_qrcode'];
                // $this->assign("qrcode",$url);
            }else{
                # 生成微信二维码
                $qrcode = preg_replace('!https:\/\/mp\.weixin\.qq\.com\/cgi-bin\/showqrcode\?ticket=!','',Wechats::get_Qrcode($pid));
                # 保存微信二维码
                M('user') -> where(['id'=>$pid]) -> save(['wx_qrcode'=>$qrcode]);
                # 返回二维码链接
                $url = 'http://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$qrcode;
				# 分配数据模板引擎

            }
			$this -> assign('qrcode',$url);
            # 输出二维码
			# 获取jsapi
			$jsapi_config = Wechats::get_jsapi_config(['onMenuShareTimeline','onMenuShareAppMessage'],false,false);

			# 分配JSapi配置
			$this -> assign('jsapi_config',$jsapi_config);
			$this -> assign("pid",$pid);
			# 渲染模板
			$this -> display();            
        }else{
            # 带着上级信息直接去注册页面
             redirect('http://hr2.hongrunet.com/html/jyt_register.html?pid='.$pid); 
        }
	}
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
	// 微信登录回调
	public function wechat_login(){
		$type = I('type');

		if($type){
			$userinfo = Wechats::get_user_info('http://hr.hongrunet.com/Home/Wechat/wechat_login.html?type='.$type);

		}else{
			$userinfo = Wechats::get_user_info('http://hr.hongrunet.com/Home/Wechat/wechat_login.html');
		}
			// dump($userinfo);
			// die();
		// 获取用户信息
		$type = I('type');
					// 判断用户是否已经注册过了

		if($user = M('user') -> where(['openid'=>$userinfo['openid']]) -> find()){
			if($user['isenable']!=1 || $user['isenable']!='1'){
				exit('<script>alert("此号已被拉黑，请联系客服处理");window.location.href="http://hr2.hongrunet.com/html/jyt_login.html"</script>');
	            // jsonpReturn('500','此号已被拉黑，请联系客服处理','');
	        }
			// 存储用户信息
			$_SESSION['userid'] = $user['id'];
			if ($user['phone'] !='') {
				$_SESSION['phone']  = $user['phone'];
				if($type){
					switch ($type) {
						case '1':
							redirect('http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=9');
							break;
						case '2':
							redirect('http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=3');
							break;	
						case '3':
							redirect('http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=4');
							break;
						case '4':
							redirect('http://hr2.hongrunet.com/html/lf_share.html');
							break;
						case '5':
							redirect('http://hr2.hongrunet.com/html/wy_shuangchuang.html?classid=1');
							break;
						case '6':
							redirect('http://hr2.hongrunet.com/html/lf_myAttention.html');
							break;	
						case '7':
							redirect('http://hr2.hongrunet.com/html/wy_LiveBroadcast.html');
							break;
						case '8':
							redirect('http://hr2.hongrunet.com/html/kll_myclass.html');
							break;
						case '9':
							redirect('http://hr2.hongrunet.com/html/wy_SpeechTop.html');
							break;
						case '10':
							redirect('http://hr2.hongrunet.com/html/kll_index.html');
							break;	
						case '11':
							redirect('http://hr2.hongrunet.com/html/jyt_erweima.html?id='.$user['id']);
							break;
						case '12':
							redirect('http://hr2.hongrunet.com/html/lf_kefuCenter.html');
							break;
						case '13':
							redirect('http://hr2.hongrunet.com/html/kll_index.html');
							break;
						case '14':
							redirect('http://hr2.hongrunet.com/html/lf_personal.html');
							break;	
					}
				}			
			}
			if($_SESSION['brak_url']!=''){
				redirect($_SESSION['brak_url']);
			}else{
				if ($user['phone'] == '' || $user['phone'] == null ) {
					redirect('bind');								
				}
				// 跳转到首页
				redirect('http://hr2.hongrunet.com/html/kll_index.html');
			}
		}else{

			# 获取用户信息
			# 用户数组
			$data = [];
			# 用户唯一标识
			$data['openid'] = $userinfo['openid'];
			# 性别 1=男 2=女性 0=未设置
			if ($userinfo['sex'] == 1) {
				$sex = '男';
			}else if($userinfo['sex'] == 2) {
				$sex = '女';
			}else if($userinfo['sex'] == 0){
				$sex = '未设置';
			}
			$data['sex'] = $sex;

			# 城市
			//$data['city'] = $userinfo['city'];
			# 省份
			//$data['province'] = $userinfo['province'];
			# 用户昵称
			$data['name'] = $userinfo['nickname'];
			# 国籍
			//$data['country'] = $userinfo['country'];
			
			# 下载头像到本地
			File::_download($userinfo['headimgurl'],ROOT_PATH.'/public/headimg/',$userinfo['openid'].'.jpg');

			# 头像
			$data['headimg'] = 'http://hr.hongrunet.com/public/headimg/'.$userinfo['openid'].'.jpg';

			$data['coin'] = 0;
			# 创建时间
			$data['createtime'] = time();
			# 插入用户数据

			do {
				$data['name'] = $data['name'].rand(1,100);
			} while (M('user')->where(['name'=>$data['name']])->find());

			$newid = M('user')->add($data);
			$insert['uid'] = $newid;
			$insert['pid'] = 0;
			$insert['info'] = serialize($userinfo);
			$insert['createtime'] = time();
			$rr = M('user_log')->add($insert);


			$user = M('user') -> where(['id'=>$newid]) -> find();
			if($user){
				$_SESSION['userid'] = $user['id'];
				if ($user['phone'] !='') {
					$_SESSION['phone'] = $user['phone'];					
				}else{
					redirect('bind');
				}
			}
			if($_SESSION['brak_url']!=''){
				redirect($_SESSION['brak_url']);
			}else{
				if ($user['phone'] !='') {
					$_SESSION['phone'] = $user['phone'];					
				}else{
					redirect('bind');
				}				
				// 跳转到首页
				redirect('http://hr2.hongrunet.com/html/kll_index.html');
			}
		}
	}

	public function callback(){
		# 监听关注事件
		Wechats::addEvent('subscribe',function($result){
			# 判断是否存在此用户
			if(M('user') -> where(['openid'=>$result['FromUserName']]) -> first()){
				return true;
			}
			# 自动回复
			$userinfo = Wechats::get_openid_user_info($result['FromUserName']);
			# 用户数组
			$data = [];
			# 判断是否存在票据
			if($result['Ticket']!=''){
				# 获取上级信息
				if($puser = M('users') -> where(['qrcode'=>$result['Ticket']]) -> first()){
					// 设置上级id
					$data['pid'] = $puser['id'];
					$insert['pid'] = $data['pid'];
				}
			}
			# 用户唯一标识
			$data['openid'] = $userinfo['openid'];
			# 性别 1=男 2=女性 0=未设置
			$data['sex'] = $userinfo['sex'];
			# 城市
			$data['city'] = $userinfo['city'];
			# 省份
			$data['province'] = $userinfo['province'];
			# 用户昵称
			$data['nickname'] = $userinfo['nickname'];
			# 国籍
			$data['country'] = $userinfo['country'];
			# 下载头像到本地
			File::_download($userinfo['headimgurl'],ROOT_PATH.'public/headimgurl/',$userinfo['openid'].'.jpg');
			# 头像
			$data['headimgurl'] = '/headimgurl/'.$userinfo['openid'].'.jpg';
			# 创建时间
			$data['createtime'] = time();
			# 最后更新时间
			// $data['updated_at'] = $data['created_at'];
			# 插入用户数据
			$ress = M('user') -> add($data);

			$insert['uid'] = $ress;
			// $insert['pid'] = $data['pid'];
			$insert['info'] = serialize($userinfo);
			$insert['createtime'] = time();
			M('user_log')->add($insert);
		});
		if($event = M('wechat_message') -> get()){
			$event = $event -> toArray();
		}else{
			unset($event);
		}
		foreach ($event as $value) {
			Wechats::addEvent($value['event'],function($result,$value){
				if($result['Event']==$value['event'] || ($result['Content']==$value['message'] || ($value['is_blurry'] == '1' && preg_match('!.*?'.$value['message'].'.*?!',$result['Content']) == 1)) ){
					# 自动回复
					exit('<xml>
				      <ToUserName><![CDATA['.$result['FromUserName'].']]></ToUserName>
				      <FromUserName><![CDATA['.$result['ToUserName'].']]></FromUserName>
				      <CreateTime>'.time().'</CreateTime>
				      <MsgType><![CDATA[text]]></MsgType>
				      <Content><![CDATA['.$value['reply'].']]></Content>
				      </xml>');
				}
			},$value);
		}
		#监听文本消息
		Wechats::addEvent('text',function($result){
			# 自动回复
			exit('<xml>
		      <ToUserName><![CDATA['.$result['FromUserName'].']]></ToUserName>
		      <FromUserName><![CDATA['.$result['ToUserName'].']]></FromUserName>
		      <CreateTime>'.time().'</CreateTime>
		      <MsgType><![CDATA[text]]></MsgType>
		      <Content><![CDATA[您好，如果您有关于注册、分佣、推广、提现、获取积分的问题，
  请回复序号，自动解答：
   [1]如何注册
   [2]如何分佣
   [3]如何推广
   [4]提现要求
   [5]获取积分
   如果您有其他问题需要解决，可以在线留言，客服人员会尽快给您答复。
   如客服人员未能及时回复，可在工作日（08:00-17:00）拨打电话010-60305020进行人工服务。]]></Content>
		      </xml>');
		});
	}
	
}