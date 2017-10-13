<?php
namespace Home\Controller;
use Think\Controller;
use Service\Alipay as Alipays;
use Service\AlipayTransfer;
use Service\Wechat;
/*
use Thenbsp\Wechat\Payment\Notify;
use Thenbsp\Wechat\Message\Template\Template;
use Thenbsp\Wechat\Message\Template\Sender;
use Thenbsp\Wechat\Wechat\AccessToken;
              // 实例化accesstoken类
              $accessToken = new AccessToken(C('appid','wechat'),C('secret','wechat'));
              // 定义一个模板
              $template = new Template('oF897iNdF2WRqWDC27brhPrfpr3OF6y1D-UU3X9M6rs');

              // 模板参数，每一个模板 ID 都对应一组参数
              $template
                  ->add('first',     '您的订单已经消费成功，感谢您对摘多多平台和农场的支持，赏个好评吧亲！点击查看订单进行评价。')//
                  ->add('keyword1',  '摘多多商城/摘多多合作农场')
                  ->add('keyword2',  $user['nickname'])
                  ->add('keyword3',  $order['name'])
                  ->add('keyword4',  $order['updated_at'])
                  ->add('remark',    '点击查看详情', '#ff0000');

              // 跳转链接
              $template->setUrl('http://wx.17zhai.top/Order/index.html?status=2');
              // 发给谁
              $template->setOpenid($user['openid']);

              // 发送模板

              $sender = new Sender($accessToken);

              try {
                  $msgid = $sender->send($template);
              } catch (\Exception $e) {
                  exit($e->getMessage());
              }
*/
//查询用户信息   参数 ：用户id
function user_info($uid){
    $u = M('user');
    $user = $u->where('id='.$uid)->find();
    return $user;
}
function pushMsg($lid){
    $id = $_SESSION['userid'];
    $user = user_info($id);
    $gzr = M('fans')->where('uid='.$id)->field('gzh')->select();
    $data = [];
    foreach ($gzr as $k => $v) {
        $data[] = M('users')->where('id='.$v['gzh'])->find();
    }
    foreach ($data as $k1 => $v1) {

    }

}
class LetvController extends Controller {
   
	 //直播明细//
    public function Live_list(){
        //------------视频列表------------//
        if(!session('userid')){
            jsonpReturn('5','您还没有登录','http://hr2.hongrunet.com/html/jyt_login.html');
        }
        $user = user_info(session('userid'));
        if(!$user){
            jsonpReturn('5','您还没有注册','http://hr2.hongrunet.com/html/jyt_login.html');
        }
        $courses = M('letv');
        $where['uid'] = ['eq',session('userid')];
        $where['endTime'] = ['gt',time()];
        //视频列表信息
        $courses_info = $courses->field('title,FROM_UNIXTIME(start,"%Y-%m-%d %H:%i") as start,coverimgurl,id,timelength')->where($where)->select();

        foreach($courses_info as $k=>$v){
            if($v['type'] == '1'){
                $courses_info[$k]['class'] = '免费观看';
            } elseif($v['type'] == '2'){
                $courses_info[$k]['class'] = '付费观看';
            }
        }
        jsonpReturn('1','查询成功',$courses_info);
    }
	public function duankou(){
		$id = $_SESSION['userid'];
		$data = M('letv')->where('uid=%d and endtime>%d',$id,(int)time())->order('createtime DESC')->find();
		if($data){
			jsonpReturn('1','直播进行中',['lid'=>$data['id']]);
		}else{
			jsonpReturn('0','没有直播正在进行中','');
		}
	}
	//学员付费处理
	public function payfee(){
		$id 		= $_SESSION['userid'];
        $lid        = I('lid');
        if(M('order')->where('uid=%d and remark=%s and status=2',$id,$lid)->find()){
            jsonpReturn('200');
        }
		$money  	= I('money');
		$user 		= M('user')->field("openid,id")->where("id=%d",$id)->find();
        $paymenttype = I('paymenttype');    //支付方式--(1:微信支付,2:支付宝支付,3:余额支付)
        if (!is_weixin() && $paymenttype == 1) {
            jsonpReturn('0','浏览器不支持微信支付,请选择其他支付方式');
        }
        $account = M('account');
        $order   = M('order');

        switch ($paymenttype) {
            case 4:
                    $order->startTrans(); //开启事务
                    $dattt['pay_order_num'] = orderNum();
                    $dattt['uid']    = $id;
                    $dattt['money']  = $money;
                    // $dattt['money']  = 0.01;
                    $dattt['status'] = '1';
                    $dattt['type']   = 7;
                    $dattt['message']= '直播观看付费';
                    $dattt['payment'] = '微信APP';
                    $dattt['remark'] = $lid;
                    $dattt['createtime'] = time();
                    $result = $order->add($dattt); //生成用户消费余额订单
                    if ($result) {
                        $order->commit();
                        jsonpReturn('1','',["class"=>"微信APP","oid"=>$result]);
                    }else{
                        jsonpReturn('0','下单失败,请刷新页面重试');
                    }
                    break;
            case 1:   //微信支付
            $order->startTrans(); //开启事务
            $datt['pay_order_num'] = orderNum();
            $datt['uid']     = $id;
            $datt['money']   = $money;
            $datt['status']  = '1';
            $datt['type']    = 7;
            $datt['message'] = '直播观看付费';
            $datt['payment'] = '微信';
            $datt['remark']  = $lid;
            $datt['createtime'] = time();
            $result = $order->add($datt); //生成用户消费余额订单
            if ($result) {
                $order->commit();
                # 定义下单内容
                $data = [
                'body'=>'直播观看付费',
                'total_fee'=>$money * 100,
                // 'total_fee'=>1,
                'openid'=> $user['openid'],
                'trade_type'=>'JSAPI',
                'out_trade_no'=>$datt['pay_order_num'],
                'notify_url'=>'http://hr.hongrunet.com/Admin/Wechat/payNotify.html'
                ];
                # 获取JSAPI
                $jsapi = Wechat::get_jsapi_config(['chooseWXPay'],false,false);
                $jsapi = str_replace('timeStamp','timestamp',$jsapi);
                # 获取微信支付配置
                $payConfig = Wechat::ChooseWXPay($data,false);

                foreach ((array)$payConfig as $key => $value) {
                    $payconfig = $value;
                }
                jsonpReturn('1','',array('jsapi'=>$jsapi,'payConfig'=>$payconfig,"class"=>"微信"));
            }else{
                jsonpReturn('0','下单失败,请刷新页面重试','');
            }

                break;
            case 2:   //支付宝支付
            $order->startTrans(); //开启事务
            $dat['pay_order_num'] =orderNum();   //生成唯一订单号
            $dat['uid']     = $id;
            $dat['type']    = 7;
            $dat['money']   = $money;
            $dat['status']  = '1';
            $dat['message'] = '直播观看付费';
            $dat['payment'] = '支付宝';
            $dat['remark']  = $lid;
            $dat['createtime'] = time();
            $result = $order->add($dat); //生成用户消费余额订单
            if ($result) {
                $order->commit();
                $data['order_no'] = $dat['pay_order_num'];
                $data['amount'] = $money;
                // $data['amount'] = 0.01;
                $data['body']   = '直播观看付费';
                $data['subject']= $dat['message'];
                $data['timeout_express'] = (time()+1800);
                $res = Alipays::create($data);  //提交订单
                if (is_weixin()) {
                	jsonpReturn('1','下单成功',["class"=>"支付宝","res"=>"http://hr2.hongrunet.com/html/ydy.html?res=".$res]);
            	}else{
            		jsonpReturn("1","下单成功",["class"=>"支付宝","res"=>$res]);
            	}
            }else{
                jsonpReturn('0','下单失败,请刷新页面重试',' ');
            }

                break;
            case 3:   //余额支付
            $allmoney = M('account')->where(['uid'=>$id])->getField('sum(money)');
            $mm = floatval($allmoney) - intval($money);
            if ($mm < 0 ) {
               jsonpReturn('0','余额不够,请切换其他支付方式!','');
            }
            $order->startTrans(); //开启事务
            $datttt['pay_order_num'] =orderNum();   //生成唯一订单号
            $datttt['uid']     = $id;
            $datttt['type']    = 7;
            $datttt['money']   = $money;
            $datttt['status']  = '2';
            $datttt['message'] = '直播观看付费';
            $datttt['payment'] = '余额';
            $datttt['remark']  = $lid;
            $datttt['createtime'] = time();
            $result = $order->add($datttt); //生成用户消费余额订单
            if ($result) {
                $order->commit();
            }
            // $account->startTrans(); //开启事务
           /* $data['uid']    = $id;
            $data['money']  = -$money;
            //$datt['remark']  = $lid;
            $data['message']= '直播观看付费';
            $data['createtime']= time();
            $data['paymenttype'] = '余额支付';
            $result = $account->add($data); //生成用户消费余额订单
            if ($result) {
                $account->commit(); //执行事务*/
                jsonpReturn('1','支付成功','');
           /* }else{
                $account->rollback();   //回滚事务
                jsonpReturn('0','支付失败','');
            }*/
                break;
        }
	}
    //学员付费跳转
    public function fee(){
        $lid = I('lid');
        $uid = $_SESSION['userid'];
        #判断是否登录
        if(!$uid){
            jsonpReturn("0","未登录");
        }
        #判断是否支付过
        $order = M('order')->where(['uid'=>$uid,'remark'=>$lid,"status"=>2])->find();
        if($order){
            jsonpReturn("1","已支付过");
        }else{
           $data = M('letv')->where('id='.$lid)->find();
            if($data['type']==2){
                jsonpReturn("2","成功",['money'=>$data['fee']]);
            }else{
                jsonpReturn("1","成功");
            }
        }         
    }
	public function index(){
		if(!is_weixin()){
			jsonpReturn("1","成功",'');
		}else{
			jsonpReturn("0","微信浏览器不支持直播，请更换浏览器登录",'');
		}
	}
	public function push(){
		$order = M('order')->where('id=39')->find();
		echo letvs($order);
	}
	public function pushUrl(){
		$id = I('id');
			$data = M('letv')->where('id='.$id)->field('pushurl,title,uid')->find();
            $_SESSION['jsapi_config_url'] = $_GET['url'];
        if(is_weixin()){
            $jsapi_config = Wechat::get_jsapi_config(['onMenuShareTimeline','onMenuShareAppMessage'],false,false);
        }
		if(!empty($data)){
			jsonpReturn('1','查询成功',['data'=>$data,'config'=>$jsapi_config]);
		}else{
			jsonpReturn('0','查询失败','');
		}
	}
	public function createLetv(){
		$where['num'] 		= I('num');
        $where['length'] 	= I('timelength');
        $where['code'] 		= I('codeRateTypes');
        $where['type'] 		= I('type');
        if($where['type']=='1'){
        	$info = M('letvprice')->where($where)->find()['price'];
        }else{
        	$info = M('letvprice')->where('type=2')->find()['price'];
        }
        if($info){
        	jsonpReturn("1","查询成功",$info);
        }else{
        	jsonpReturn("0","查询成功",'');
        }
	}
	public function LetvDetail(){
		if(!$_SESSION['userid']){
			jsonpReturn('10','请先去注册','');
		}
		$id  = $_SESSION['userid'];
        $_SESSION['jsapi_config_url'] = $_GET['url'];
        //$id  = 109;
		$letvid = I('get.id');
		$data = M('letv')->where('id='.$letvid)->find();        $data['start'] = date('Y-m-d H:i:s',$data['start']);
		$users = M('fans')->field('gzh')->where('uid='.$data['uid'])->select();
		$ids = [];
		foreach($users as $k=>$v){
			$ids[$k] = $v['gzh'];
		}
        //dump($ids);exit;
		if( in_array($id,$ids)){
			$type = 1;
		}else{
			$type = 0;
		}
		$user1 = M('user')->where('id='.$data['uid'])->find();
        $_SESSION['jsapi_config_url'] = $_GET['url'];
        if(is_weixin()){
            $jsapi_config = Wechat::get_jsapi_config(['onMenuShareTimeline','onMenuShareAppMessage'],false,false);
        }
	    jsonpReturn('1','查询成功',['letv'=>$data,'js'=>$user1,'type'=>$type,'jsapi_config'=>$jsapi_config]);
        // jsonpReturn('1','查询成功',['letv'=>$data,'js'=>$user1,'type'=>$type]);
	}
	public function LetvOrder(){
		$id = $_SESSION['userid'];
		$letv = M('letv');
		$letv->startTrans();
		$tab['uid'] 			= $id;
		//$tab['cid']	  			= I('cid');
		$tab['title'] 			= I('title');
		$where['title'] 		= I('title');
        $where['endtime']       = ['>',time()];
        // $where['pushUrl']       = ['!=',''];
		if($xx = M('letv')->where($where)->where('pushurl != ""')->find()){
			jsonpReturn('0','这个标题已存在，请更换');
		}
		$tab['coverImgUrl']		= I('coverImgUrl');
		$tab['codeRateTypes'] 	= I('codeRateTypes');
		$tab['num'] 			= I('num');
		$start = str_replace('T', ' ',I('start'));
		$start = strtotime($start);
		$tab['start']			= $start;
		$tab['timelength'] 		= I('timelength');
		$tab['type'] 			= I('type');
		if(I('type')==2){
			$where['num'] 		= I('num');
	        $where['length'] 	= I('timelength');
	        $where['code'] 		= I('codeRateTypes');
	        $info = M('letvprice')->where($where)->find()['price'];
	        //直播预估人数1:100,2:200,3:500,4:1000,5:2000,6:5000,7:10000,8:20000
	        switch (I('num')) {
	        		case '1':
	        			$tab['fee'] = $info/100;
	        			break;
	        		case '2':
	        			$tab['fee'] = $info/200;
	        			break;
	        		case '3':
	        			$tab['fee'] = $info/500;
	        			break;
	        		case '4':
	        			$tab['fee'] = $info/1000;
	        			break;
	        		case '5':
	        			$tab['fee'] = $info/2000;
	        			break;
	        		case '6':
	        			$tab['fee'] = $info/5000;
	        			break;
	        		case '7':
	        			$tab['fee'] = $info/10000;
	        			break;
	        		case '8':
	        			$tab['fee'] = $info/20000;
	        			break;
	        	}
		}
		$tab['createtime'] 		= time();
		$tab['status']  		= 0;
		$res = $letv->add($tab);
		if($res){
			 $letv->commit();
		}
		$tab['price'] 			= I('fee');
		$tab['paymenttype']		= I('paymenttype');
		//生成订单
        $user = M('user')->where(["id"=>$tab['uid']])->find();
		// 支付方式--(1:微信支付,2:支付宝支付,3:余额支付)
        if (!is_weixin() && $tab['paymenttype'] == 1) {
            jsonpReturn('0','浏览器不支持微信支付,请选择其他支付方式');
        }

        $account = M('account');
        $order   = M('order');
        $money   = $tab['price'];
        switch ($tab['paymenttype']) {
            case 4:
                $order->startTrans(); //开启事务
                $dattt['pay_order_num'] = orderNum();
                $dattt['uid']    = $tab['uid'];
                $dattt['money']  = $money;
                // $dattt['money']  = 0.01;
                $dattt['status'] = '1';
                $dattt['type']   = 1;
                $dattt['message']= '发起直播';
                $dattt['payment'] = '微信APP';
                $dattt['remark'] =  $tab['title'];
                $dattt['createtime'] = time();
                $result = $order->add($dattt); //生成用户消费余额订单
                if ($result) {
                    $order->commit();
                    jsonpReturn('1','',["class"=>"微信APP","oid"=>$result]);
                }else{
                    jsonpReturn('0','下单失败,请刷新页面重试');
                }
                break;
        	case 1:   //微信支付
            $order->startTrans(); //开启事务
            $datt['pay_order_num'] = orderNum();
            $datt['uid']     = $tab['uid'];
            $datt['money']   = (int)$money*100;
            $datt['status']  = '1';
            $datt['type']    = 1;
            $datt['message'] = '发起直播';
            $datt['remark']  = $tab['title'];
            $datt['payment'] = '微信';
            //$datt['remark']  = $lid;
            $datt['createtime'] = time();
            $result = $order->add($datt); //生成用户消费余额订单
            if ($result) {
                $order->commit();
                # 定义下单内容
                $data = [
                'body' 			=>'发起直播付费',
                'total_fee' 	=>(int)$money*100,
                // 'total_fee' 	=>1,
                'openid'		=> $user['openid'],
                'trade_type'	=>'JSAPI',
                'out_trade_no'	=>$datt['pay_order_num'],
                'notify_url'	=>'http://hr.hongrunet.com/Admin/Wechat/payNotify.html'
                ];
                # 获取JSAPI
                $jsapi = Wechat::get_jsapi_config(['chooseWXPay'],false,false);
                $jsapi = str_replace('timeStamp','timestamp',$jsapi);
                # 获取微信支付配置
                $payConfig = Wechat::ChooseWXPay($data,false);
                foreach ((array)$payConfig as $key => $value) {
                    $payconfig = $value;
                }
                jsonpReturn('1','',array('jsapi'=>$jsapi,'payConfig'=>$payconfig,"class"=>"微信",'lid'=>$res));
            }else{
                jsonpReturn('0','下单失败,请刷新页面重试','');
            }

                break;
            case '2':   //支付宝支付
            $order->startTrans(); //开启事务
            $dat['pay_order_num'] =orderNum();   //生成唯一订单号
            $dat['uid']     = $tab['uid'];
            $dat['type']    = 1;
            $dat['money']   = $money;
            $dat['status']  = '1';
            $dat['message'] = '发起直播';
            $dat['payment'] = '支付宝';
            $dat['remark']  = $tab['title'];
            $dat['createtime']  = time();
            $result1 = $order->add($dat); //生成用户消费余额订单
            if ($result1) {
                $order->commit();
                $data['order_no'] = $dat['pay_order_num'];
				$data['amount'] =$money ;
                // $data['amount'] =0.01 ;
                $data['body']   = '发起直播费用';
                $data['subject']= $dat['message'];
                $data['timeout_express'] = (time()+1800);
                $res = Alipays::create($data);  //提交订单
                if (is_weixin()) {
                	jsonpReturn('1','下单成功',["class"=>"支付宝","res"=>"http://hr2.hongrunet.com/html/ydy.html?res=".$res]);
            	}else{
            		jsonpReturn("1","下单成功",["class"=>"支付宝","res"=>$res]);
            	}

            }else{
                jsonpReturn('0','下单失败,请刷新页面重试',' ');
            }
                break;
            case '3':   //余额支付
            $allmoney = $account->where(['uid'=>$id])->getField('sum(money)');
            $mm = floatval($allmoney) - intval($money);
            if ($mm < 0 ) {
               jsonpReturn('0','余额不够,请切换其他支付方式!','');
            }
            $account->startTrans(); //开启事务
            $data2['uid']    		= $id;
            $data2['money']  		= -$money;
            $data2['source'] 		= $id;
            $data2['message']		= '发起直播费用';
            $data2['createtime']	= time();
            $data2['paymenttype'] 	= '余额支付';
            $data2['remark'] 		=  $tab['title'];
            $result2 = $account->add($data2); //生成用户消费余额订单
            $dd['user_id'] = $id;
            $dd['add_time'] = time();
            $result3= M('order')->add($dd); //发起直播写入表中
			$result4=letvs($data2);
			$lid = M('letv')->where('uid='.$id)->order('createtime DESC')->find();
            if ($result4>0) {
                $account->commit(); //执行事务
                jsonpReturn('1','支付成功',['lid'=>$lid['id']]);
            }else{
                $account->rollback();   //回滚事务
                jsonpReturn('0','支付失败','');
            }
                break;
            }
	}
	//结束视频
	public function  stop(){
		$data['ver'] =    C('Letv')['ver'];
		$data['userid'] = C('Letv')['userid'];
		$data['method'] = 'lecloud.cloudlive.activity.stop';
		$data['timestamp'] = time()*1000;
		$data['activityId'] = I('activityId');
		$sign = getSign($data);
		$data['sign'] = $sign;
		//dump($data);exit;
		$activityId = LetvHttp(C('Letv')['url'],$data,'POST',C('Letv')['headers']);
		M('letv')->where('activityId='.I('activityId'))->save(['endTime'=>time()*1000]);
		jsonpReturn('1','已结束','');
	}
	//视频录制
	public function recorde(){
		$data['ver'] =    C('Letv')['ver'];
        $data['userid'] = C('Letv')['userid'];
		$data['method'] = 'lecloud.cloudlive.vrs.activity.streaminfo.search';
		$data['timestamp'] = time()*1000;
		$data['activityId'] = I('activityId');
		$sign = getSign($data);
		$data['sign'] = $sign;
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    $result = file_get_contents($url.$rdata);
		$mm = json_decode($result,true);
		$liveId = $mm['lives'][0]['liveId'];
		$dat['ver'] =    C('Letv')['ver'];
		$dat['userid'] = C('Letv')['userid'];
		$dat['method'] = 'lecloud.cloudlive.rec.createRecTask';
		$dat['timestamp'] = time()*1000;
		$dat['liveId'] = $liveId;
		$dat['startTime'] = time()*1000;
		$dat['endTime'] = ((int)time()+120)*1000;
		$sign = getSign($dat);
		$dat['sign'] = $sign;
		$taskId = LetvHttp($url,$dat,'POST',C('Letv')['headers']);
		dump($taskId);exit;
		/*
		返回参数
			taskId 		string	是	任务ID
		*/
		/*activityId		string	是	活动ID
		liveNum			Int		是	机位数量
		lives			array	是	机位信息。活动最多4个机位
		-liveId			string	是	机位ID
		-machine		int		是	机位编号。1-4
		-streams		array	是	流信息
		--streamId		string	是	流ID
		--codeRateType	string	是	码率类型: 13 流畅；16 高清；19 超清；25   1080P；99 原画*/
	}
	//视频创建
	public function create(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.create';
		$data['timestamp'] = time()*1000;
		//直播活动名称
		$data['activityName'] = I('activityName');
		//$data['activityName'] = "地方好好";
		//012	教育
		$data['activityCategory'] = "012";
		//开始时间
		$data['startTime'] = time()*1000;
		//$length = I('length');
		$length = 1;
		switch ($length) {
			case '1'://40
				//结束时间
				$tim = time()+(40*60);
				$data['endTime'] = $tim*1000;
				break;
			case '2'://1
				//结束时间
				$tim = time()+(60*60);
				$data['endTime'] = $tim*1000;
				break;
			case '3'://1.5
				//结束时间
				$tim = time()+(90*60);
				$data['endTime'] = $tim*1000;
				break;
			case '4'://2
				//结束时间
				$tim = time()+(120*60);
				$data['endTime'] = $tim*1000;
				break;
			case '5'://2.5
				//结束时间
				$tim = time()+(150*60);
				$data['endTime'] = $tim*1000;
				break;
			case '6'://3
				//结束时间
				$tim = time()+(180*60);
				$data['endTime'] = $tim*1000;
				break;
			default:
				break;
		}
		//播放模式，0：实时直播 1：流畅直播
		$data['playMode'] = 1;
		//机位数量，范围为：1,2,3,4. 默认为1
		$data['liveNum'] = 1;
		//13 流畅；16 高清；19 超清； 25   1080P；99 原画。默认按最高码率播放，如果有原画则按原画播放
		//$data['codeRateTypes'] = I('codeRateTypes');
		$data['codeRateTypes'] = 13;
		//活动封面地址，如果为空，则系统会默认一张图片
		$data['coverImgUrl'] = ROOT_PATH.I('imgUrl');
		//$data['coverImgUrl'] = "http://hr.hongrunet.com/Uploads/2017-05-16/591a7aeb9e91d.jpg";
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'POST',C('Letv')['headers']);
		preg_match('!\{(.*?)\}$!',$activityId,$arr);
		$result = json_decode($arr[0],true);
		//dump($result['activityId']);exit;
		$playPageUrl = self::getUrl($result['activityId']);
		$tab['uid'] 			= $_SESSION['userid'];
		$tab['cid']	  			= I('cid');
		$tab['num'] 			= I('num');
		$tab['timelength'] 		= I('length');
		$tab['title'] 			= I('activityName');
		$tab['coverImgUrl']		= I('imgUrl');
		$tab['codeRateTypes'] 	= I('codeRateTypes');

		$tab['videoUrl'] 	= $playPageUrl['playPageUrl'];
		//$tab['pushUrl']  	= $PushUrl['lives']['pushUrl'];
		$tab['activityId']	= $result['activityId'];
		$tab['createtime'] 	= time();
		$tab['status']  		= 1;
		$tab['endTime']	   = $data['endTime']/1000;
		$res = M('letv')->add($tab);
		if($res){
			jsonpReturn('1','申请成功',$result['activityId']);
		}else{
			jsonpReturn('0','申请失败',$result);
		}
	}
	public function ydy(){
		if(is_weixin()){
			jsonpReturn('0','微信','');
		}else{
			jsonpReturn('1','浏览器','');
		}
	}


}