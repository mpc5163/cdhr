<?php
namespace Admin\Controller;
use Think\Controller;

class LetvController extends Controller {
	private $url='http://api.open.lecloud.com/live/execute';
	private $headers = array(
						    'User-Agent' => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
						    'Content-Type'    => 'application/x-www-form-urlencoded;charset=utf-8',
						    'Referer: http://hr.hongrunet.com'
							);
	private $ver = '4.0';
	private $userid = 910018;
	private $method;
	private $timestamp;
	//
	public function test(){
		$letv = M('letv')->where('id=500')->find();
                $data['ver']    = C('Letv')['ver'];
                $data['userid'] = C('Letv')['userid'];
                $data['method'] = 'lecloud.cloudlive.activity.create';
                $data['timestamp'] = time()*1000;
                $data['activityName'] = $letv['title']; //直播活动名称
                $data['activityCategory'] = "012"; //012    教育
                //$data['startTime'] = $letv['start']*1000; //开始时间
                switch ($letv['timelength']) {
                    case '1'://40
                        $tim = (int)$letv['start']+(40*60);
                        $data['endTime'] = $tim*1000;//结束时间
                        break;
                    case '2'://1
                        $tim = (int)$letv['start']+(60*60);
                        $data['endTime'] = $tim*1000;//结束时间
                        break;
                    case '3'://1.5
                        $tim = (int)$letv['start']+(90*60);
                        $data['endTime'] = $tim*1000;//结束时间
                        break;
                    case '4'://2
                        $tim = (int)$letv['start']+(120*60);
                        $data['endTime'] = $tim*1000; //结束时间
                        break;
                    case '5'://2.5
                        $tim = (int)$letv['start']+(150*60);
                        $data['endTime'] = $tim*1000; //结束时间
                        break;
                    case '6'://3
                        $tim = (int)$letv['start']+(180*60);
                        $data['endTime'] = $tim*1000;//结束时间
                        break;
                    default:
                        break;
                }
                $data['startTime'] = $letv['start']*1000; //开始时间
                $data['playMode'] = 1; //播放模式，0：实时直播 1：流畅直播
                $data['liveNum'] = 1;//机位数量，范围为：1,2,3,4. 默认为1
                //13 流畅；16 高清；19 超清； 25   1080P；99 原画。默认按最高码率播放，如果有原画则按原画播放
                $data['codeRateTypes'] = $letv['coderatetypes'];
                $data['coverImgUrl'] = $letv['coverimgurl'];//活动封面地址，如果为空，则系统会默认一张图片
                $sign = getSign($data);
                $data['sign'] = $sign;
                $activityId = LetvHttp(C('Letv')['url'],$data,'POST',C('Letv')['headers']);
                dump($activityId);exit;
                preg_match('!\{(.*?)\}$!',$activityId,$arr);
                $result4 = json_decode($arr[0],true);
                $ds['ver'] =     C('Letv')['ver'];
                $ds['userid'] =  C('Letv')['userid'];
                $ds['method'] = 'lecloud.cloudlive.activity.getPushUrl';
                $ds['timestamp'] = time()*1000;
                $ds['activityId'] = $result4['activityId'];
                $sign1 = getSign($ds);
                $ds['sign'] = $sign1;
                $mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
                $url="http://api.open.lecloud.com/live/execute?";
                $rds = sprintf($mm,$ds['ver'],$ds['userid'],$ds['method'],$ds['timestamp'],$ds['activityId'],$ds['sign']);
                $rs = file_get_contents($url.$rds);
                $mms = json_decode($rs,true);
                $pushUrl = $mms['lives'][0]['pushUrl'];
                $ress = M('letv')->where('title="'.$order['remark'].'"')->save(['endTime'=>$tim,'pushUrl'=>$pushUrl,'activityId'=>$result4['activityId']]);
                dump($ress);exit;
                return $ress;
	}
	public function index(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.create';
		$data['timestamp'] = time()*1000;
		//直播活动名称
		//$data['activityName'] = I('activityName');
		$data['activityName'] = "地方好好";
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
		//$data['coverImgUrl'] = ROOT_PATH.I('imgUrl');
		$data['coverImgUrl'] = "http://hr.hongrunet.com/Uploads/2017-05-16/591a7aeb9e91d.jpg";
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'POST',$this->header);
		preg_match('!\{(.*?)\}$!',$activityId,$arr);
		$result = json_decode($arr[0],true);
		//dump($result['activityId']);exit;
		$playPageUrl = self::getUrl($result['activityId']);
		echo $playPageUrl;
		echo "<hr/>";
		$PushUrl     = self::getPushUrl($result['activityId']);
		dump($PushUrl['lives'][0]['pushUrl']);exit;
		//dump($result);exit;
		//数据库添加
		$tab['uid'] 			= $_SESSION['userid'];
		$tab['cid']	  			= I('cid');
		$tab['num'] 			= I('num');
		$tab['timelength'] 		= I('length');
		$tab['title'] 			= I('activityName');
		$tab['coverImgUrl']		= I('imgUrl');
		$tab['codeRateTypes'] 	= I('codeRateTypes');

		$tab['videoUrl'] 	= $playPageUrl['playPageUrl'];
		$tab['pushUrl']  	= $PushUrl['lives']['pushUrl']; 		
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
	//直播活动创建接口
	public function create(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.create';
		$data['timestamp'] = time()*1000;
		//直播活动名称
		//$data['activityName'] = I('activityName');
		$data['activityName'] = "地方好好";
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
		//$data['coverImgUrl'] = ROOT_PATH.I('imgUrl');
		$data['coverImgUrl'] = "http://hr.hongrunet.com/Uploads/2017-05-16/591a7aeb9e91d.jpg";
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'POST',$this->header);
		preg_match('!\{(.*?)\}$!',$activityId,$arr);
		$result = json_decode($arr[0],true);
		dump($result);exit;
		$tab['uid'] = $_SESSION['userid'];
		$tab['title'] = $data['activityName'];
		$tab['cid']	  = I('cid');
		$tab['createtime'] = time();
		$tab['codeRateTypes'] = $data['codeRateTypes'];
		$tab['coverImgUrl']	 = $data['coverImgUrl'];
		$tab['activityId'] = $result['activityId'];
		$tab['status']  = 1;
		$tab['num'] = I('num');
		$tab['timelength'] = $length;
		$tab['endTime']	   = $data['endTime'];	
		$res = M('letv')->add($tab);
		if($res){
			jsonpReturn('1','申请成功',$result['activityId']);
		}else{
			jsonpReturn('0','申请失败',$result);
		}
	}
	//直播结束接口
	public function stop(){
		$letv = M('letv')->where('id=%d',I('id'))->find();
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.stop';
		$data['timestamp'] = time()*1000;
		$data['activityId'] = $letv['activityId'];
		//$data['activityId'] = I('activityId');
		//$data['activityId'] = "A2017052300000az";
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'POST',$this->header);
		echo true;
		//dump($activityId);exit;
	}
	//直播活动播放页地址获取
	public function getUrl($id){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.playerpage.getUrl';
		$data['timestamp'] = time()*1000;
		//$data['activityId'] = I('activityId');
		//$data['activityId'] = "A2017052300000fi";
		$data['activityId'] = $id;
		$sign = getSign($data);
		$data['sign'] = $sign;
		//http://api.open.lecloud.com/live/execute?ver=4.0&userid=910018&method=lecloud.cloudlive.activity.playerpage.getUrl&timestamp=1495521703000&activityId=A2017052300000dl&sign=ef278070ea6eb664569b77e3f21d2c65
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    //echo $url.$rdata;exit;
	    $result = file_get_contents($url.$rdata);
	    $dd = json_decode($result,true);
	    return $dd;
		//dump($dd);exit;
	}
	//直播活动推流地址获取接口
	public function getPushUrl(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.getPushUrl';
		$data['timestamp'] = time()*1000;
		//$data['activityId'] = I('activityId');

		$data['activityId'] = "A2017062900000h9";
		//$data['activityId'] = $id;
		$sign = getSign($data);
		$data['sign'] = $sign;
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    // dump($url.$rdata);exit;
	    
	    //echo $url.$rdata;exit;
	    $result = file_get_contents($url.$rdata);
	    $mm = json_decode($result,true);
	    //return $mm;
		dump($result);exit;
		/*
			liveNum		int		是	机位数量
			lives		list	数组
			—machine	string	是	机位位置。1-4机位
			—status		int		是	状态。0：无信号 1：有信号
			—pushUrl	string	是	推流地址	
		*/
	}
	//直播活动流信息查询接口
	public function search(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.vrs.activity.streaminfo.search';
		$data['timestamp'] = time()*1000;;
		//$data['activityId'] = $id;
		$data['activityId'] = "A2017052300000ga";
		$sign = getSign($data);
		$data['sign'] = $sign;
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    $result = file_get_contents($url.$rdata);
		$mm = json_decode($result,true);
		dump($mm);exit;
		//$activityId = LetvHttp($this->url,$data,'GET',$this->header);
		/*activityId		string	是	活动ID
		liveNum			Int		是	机位数量
		lives			array	是	机位信息。活动最多4个机位
		-liveId			string	是	机位ID
		-machine		int		是	机位编号。1-4
		-streams		array	是	流信息
		--streamId		string	是	流ID
		--codeRateType	string	是	码率类型: 13 流畅；16 高清；19 超清；25   1080P；99 原画*/
		//dump($activityId);exit;
		//return $activityId;
	}
	//打点录制创建任务接口
	public function createRecTask(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.rec.createRecTask';
		$data['timestamp'] = time()*1000;
		//$id = I('activityId');
		//$activity = $this->search($id);
		//$data['liveId'] = $activity['liveId'];
		$data['liveId'] = '201705233000000nh';
		$data['startTime'] = time()*1000;	
		$data['endTime'] = ((int)time()+120)*1000;
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'POST',$this->header);
		dump($activityId);exit;
		/*
		返回参数
			taskId 		string	是	任务ID
		*/
	}
	//打点录制查询结果接口
	public function searchResult(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.rec.searchResult';
		$data['timestamp'] = time()*1000;;
		$id = I('activityId');
		$activity = $this->search($id);
		$data['liveId'] = $activity['liveId'];
		$data['startTime'] = '';
		$data['endTime'] = '';
		$data['offset'] = '';
		$data['size'] = '';
		/*liveId		string	否	直播ID
			taskId		string	否	任务ID
			offset		long	否	开始行数
			size		int		否	每页记录数
			startTime	string	否	开始时间，从1970开始的毫秒数
			endTime		string	否	结束时间，从1970开始的毫秒数*/
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'GET',$this->header);
	}
	//获取录制视频信息接口
	public function getPlayInfo(){
		$data['ver'] =    $this->ver;
		$data['userid'] = $this->userid;
		$data['method'] = 'lecloud.cloudlive.activity.getPlayInfo';
		$data['timestamp'] = time()*1000;;
		$data['activityId']= I('activityId');
		/*
		activityId		string	是	直播活动ID
		machineInfo		list	是	直播对应的信息列表
		-- videoId		string	是	视频ID
		-- videoUnique	string	是	视频unique
		*/
		$sign = getSign($data);
		$data['sign'] = $sign;
		$activityId = LetvHttp($this->url,$data,'GET',$this->header);

	}

}

