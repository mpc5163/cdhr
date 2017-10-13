<?php
namespace Home\Controller;
use Think\Controller;
use Service\Alipay as Alipays;
use Service\AlipayTransfer;
use Service\Wechat;

class TestController extends Controller {
	#打印所有为注册手机号的人的下级
	public function childs(){
		$data = M('user')->where('phone is NULL')->select();
		dump(count($data));
		echo '<hr/>';
		foreach ($data as $k => $v) {
			$childs = M('user')->where(['pid'=>$v['id']])->select();
			if(!empty($childs)){
				
				dump($v);
				dump($childs);
				echo '<hr/>';
			}
			
		}
		// dump($data);
	}
	public function score(){
		$id=390;
		$data = M('user')->where('pid=390')->select();
		foreach ($data as $k => $v) {
			$int['uid'] = 20;
			$int['source'] = $v['id'];
			$int['score'] = 3;
			$int['message'] = '用户推广';
			$int['createtime'] = time();
			$res = M('score')->add($int);
			echo $res;
		}

	}
	#签到
	public function index(){
		upGrade(1);
		
	}
	#获取录制播放地址
	public function getuu(){
		$uid = $_SESSION['userid'];
		$data = M('letv')->where('uid=%d',$uid)->select();
		$urls = [];
		foreach ($data as $k => $v) {	
			if($v['videoUrl'] == 0){
				$id = $v['id'];
				#获取播放地址
				$url = getUrls($id);
				$urls[] = $url;
				if($url){
				 M('letv')->where('id='.$id)->save(['videoUrl'=>$url]);
				}
			}
		}
		jsonpReturn('1','成功',$urls);
	}
	#上传成功
	public function Supload(){
		$lid = I('lid');
		$lid = explode(',',trim($lid,','));
		if(empty($lid)){
			jsonpReturn('0','失败');
		}
		$res = M('letv')->where(['id'=>['in',$lid]])->save(['ustatus'=>'1']);
		jsonpReturn('1','成功');
	}
	#删除回放
	public function delReplays(){
		$lid = I('lid');
		$lid = explode(',',trim($lid,','));
		$res = M('letv')->where(['id',['in',$lid]])->save(['ustatus'=>2]);
		jsonpReturn('1','删除成功');
	}
	#精彩回放展示
	public function replays(){
		$data = M('letv')->where(['ustatus'=>1])->order('id DESC')->select();
		foreach($data as $k=>$v){
			$data[$k]['endtime'] = date('Y-m-d H:i',$v['endtime']);
		}
		jsonpReturn('1','查询成功',$data);
	}
	#个人中心回放展示
	public function perReplays(){
		$id = $_SESSION['userid'];
		// dump($id);exit;
		$where['uid'] = ['=',$id];
		$where['videoUrl'] = ['!=','0'];
		$where['ustatus'] = ['=',0];
		$data = M('letv')->where("uid =$id and videoUrl !='0' and ustatus=0")->order('id DESC')->select();
		foreach($data as $k=>$v){
			$data[$k]['endtime'] = date('Y-m-d H:i',$v['endtime']);
		}
		jsonpReturn('1','查询成功',$data);
	}
	#视频录制创建任务
	public function recode(){
		$lid = I('lid');
		$letv = M('letv')->where(['id'=>$lid])->find();
		// dump($letv);exit;
				// jsonpReturn('1','查询成功',$letv);
		$data['ver'] =    C('Letv')['ver'];
        $data['userid'] = C('Letv')['userid'];
		$data['method'] = 'lecloud.cloudlive.vrs.activity.streaminfo.search';
		$data['timestamp'] = time()*1000;
        $data['activityId'] = $letv['activityid'];
		// jsonpReturn('1','查询成功',$data);
		$sign = getSign($data);
		$data['sign'] = $sign;
		// $mm = "activityId=%s";
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    $result = file_get_contents($url.$rdata);

		$mm = json_decode($result,true);

		$liveId = $mm['lives'][0]['liveId'];
		//
		$dat['ver'] =    C('Letv')['ver'];
		$dat['userid'] = C('Letv')['userid'];
		$dat['method'] = 'lecloud.cloudlive.rec.createRecTask';
		$dat['timestamp'] = time()*1000;
		$dat['liveId'] = $liveId;
        $dat['startTime'] = time()*1000;
		$dat['endTime'] = $letv['endtime']*1000;
		$sign = getSign($dat);
		$dat['sign'] = $sign;
		$taskId = LetvHttp($url,$dat,'POST',C('Letv')['headers']);
		jsonpReturn('1','录播开始',$taskId);
	}
	#安全设置
	public function serv(){
		$ser['ver'] =     C('Letv')['ver'];
	    $ser['userid'] =  C('Letv')['userid'];
	    $ser['method'] = 'lecloud.cloudlive.activity.sercurity.config';
	    $ser['timestamp'] = time()*1000;
	    $ser['activityId'] = 'A20170626000006n';
	    $sign1 = getSign($ser);
	    $ser['sign'] = $sign1;
	    $res = LetvHttp(C('Letv')['url'],$ser,'POST',C('Letv')['headers']);
	    dump($res);
	}
#查询录制结果
//获取录制视频信息接口
public function getUrl(){
	// $lid = I('lid');
	$lid = 604;
	$letv = M('letv')->where('id=%d',$lid)->find();
	dump($letv);
	$data['ver'] =    C('Letv')['ver'];
    $data['userid'] = C('Letv')['userid'];
	$data['method'] = 'lecloud.cloudlive.activity.getPlayInfo';
	$data['timestamp'] = time()*1000;;
	$data['activityId'] = $letv['activityid'];
	$sign = getSign($data);
	$data['sign'] = $sign;
	$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	$url="http://api.open.lecloud.com/live/execute?";
    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
    $result = file_get_contents($url.$rdata);
	$xx = json_decode($result,true);
	dump($xx);exit;
	$uu = 'http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu='.$xx['machineInfo'][0]["videoUnique"].'&auto_play=1&width=640&height=360&lang=zh_CN';
	return $uu;
	// 										6t4yd3wkue
	// http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu=53aecf4789&auto_play=1&width=640&height=360&lang=zh_CN

}
	//视频录制
	public function recorde(){
		$data['ver'] =    C('Letv')['ver'];
        $data['userid'] = C('Letv')['userid'];
		$data['method'] = 'lecloud.cloudlive.vrs.activity.streaminfo.search';
		$data['timestamp'] = time()*1000;;
		// $data['activityId'] = I('activityId');
        $data['activityId'] = 'A2017062100000dk';
		$sign = getSign($data);
		$data['sign'] = $sign;
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
	    $url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    $result = file_get_contents($url.$rdata);
		$mm = json_decode($result,true);
        // dump($mm);exit;
		$liveId = $mm['lives'][0]['liveId'];
		$dat['ver'] =    C('Letv')['ver'];
		$dat['userid'] = C('Letv')['userid'];
		$dat['method'] = 'lecloud.cloudlive.rec.createRecTask';
		$dat['timestamp'] = time()*1000;
		$dat['liveId'] = $liveId;
		// $dat['startTime'] = ((int)time()-20)*1000;
        $dat['startTime'] = ((int)time()+200)*1000;
		$dat['endTime'] = ((int)time()+500)*1000;
		$sign = getSign($dat);
		$dat['sign'] = $sign;
        // dump($dat);exit;
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
	//获取录制视频信息接口
	public function getPlayInfo(){
		$data['ver'] =    C('Letv')['ver'];
        $data['userid'] = C('Letv')['userid'];
		$data['method'] = 'lecloud.cloudlive.activity.getPlayInfo';
		$data['timestamp'] = time()*1000;;
		// $data['activityId']= I('activityId');
		$data['activityId'] = 'A2017062100000dk';
		/*
		activityId		string	是	直播活动ID
		machineInfo		list	是	直播对应的信息列表
		-- videoId		string	是	视频ID
		-- videoUnique	string	是	视频unique
		*/
		$sign = getSign($data);
		$data['sign'] = $sign;
		dump($data);
		$mm = "ver=%s&userid=%s&method=%s&timestamp=%s&activityId=%s&sign=%s";
		$url="http://api.open.lecloud.com/live/execute?";
	    $rdata = sprintf($mm,$data['ver'],$data['userid'],$data['method'],$data['timestamp'],$data['activityId'],$data['sign']);
	    $result = file_get_contents($url.$rdata);
		$xx = json_decode($result,true);
		dump($xx['machineInfo']);
		// http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu=9bd11a6261&auto_play=1&width=640&height=360&lang=zh_CN
		// http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu=53aecf4789&auto_play=1&width=640&height=360&lang=zh_CN

	}

	//获取订单
	public function getOrderInfo(){
		$id = I('id');
		$order = M('order')->where(['id'=>$id,'xs'=>1])->find();
		if($order){
			M('order')->where(['id'=>$id])->save(['xs'=>2]);
			jsonpReturn('1','获取成功',$order);
		}else{
			jsonpReturn('2','获取失败');
		}
		
	}
}
