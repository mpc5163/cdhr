<?php
#视频录制创建任务
 function recode($lid){
    //$lid= '578';
    $letv = M('letv')->where('id=%d',$lid)->find();
    $data['ver'] =    C('Letv')['ver'];
    $data['userid'] = C('Letv')['userid'];
    $data['method'] = 'lecloud.cloudlive.vrs.activity.streaminfo.search';
    $data['timestamp'] = time()*1000;
    $data['activityId'] = $letv['activityid'];
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
    $dat['endTime'] = $letv['endtime']*1000;
    $sign = getSign($dat);
    $dat['sign'] = $sign;
    $taskId = LetvHttp($url,$dat,'POST',C('Letv')['headers']);
    return $taskId;
}
#查询录制结果
    //获取录制视频信息接口
function getUrls($lid){
    $letv = M('letv')->where('id=%d',$lid)->find();
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
    if($xx['machineInfo']){
        $uu = 'http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu='.$xx['machineInfo'][0]["videoUnique"].'&auto_play=1&width=640&height=360&lang=zh_CN';
    }else{
        $uu = '';
    }
    
    return $uu;
    //                                      6t4yd3wkue
    // http://yuntv.letv.com/bcloud.html?uu=6t4yd3wkue&vu=53aecf4789&auto_play=1&width=640&height=360&lang=zh_CN

}
function upLecturer($id){
    $grade = M('user')->where('id=%d',$id)->getField('grade');
    if($grade == 1){
        return;
    }
    $order = M('order');
    $sum = 0;
    $starts = M('user')->where('id=%d',$id)->getField('xbtime');
    if(empty($starts)){
        $starts = 1497801600;
    }
    $one = explode(',', getChilden($id,1));
    foreach ($one as $k => $v) {
       $money1 = $order->where('uid=%d and status=2 and createtime>%s and message="购买用户等级"',$v,$starts)->sum('money');
       $money11 = M('coin')->where('uid=%d and coin<0 and createtime>%s',$v,$starts)->sum('coin');
        $sum += $money1+abs($money11);
    }
    $two = explode(',', getChilden($id,2));
    foreach ($two as $k => $v) {
       $money2 = $order->where('uid=%d and status=2 and createtime>%s and message="购买用户等级"',$v,$starts)->sum('money');
       $money22 = M('coin')->where('uid=%d and coin<0 and createtime>%s',$v,$starts)->sum('coin');

        $sum += $money2+abs($money22);
    }
    $three = explode(',', getChilden($id,3));
    foreach ($three as $k => $v) {
       $money3 = $order->where('uid=%d and status=2 and createtime>%s and message="购买用户等级"',$v,$starts)->sum('money');
       $money33 = M('coin')->where('uid=%d and coin<0 and createtime>%s',$v,$starts)->sum('coin');
        $sum += $money3+abs($money33);
    }
    if((int)$sum >= 97978){
        M('user')->where('id=%d',$id)->save(['grade'=>4]);
    }else if((int)$sum >= 31798 && (int)$sum < 97978){
        M('user')->where('id=%d',$id)->save(['grade'=>3]);
    }
}

//通过订单备注添加直播，并获得
function letvs($order){
    //M('order')->where('id=%d',$order['id'])->save(['status'=>2]);
    $letv = M('letv')->where('title="'.$order['remark'].'"')->find();
    $data['ver']    = C('Letv')['ver'];
    $data['userid'] = C('Letv')['userid'];
    $data['method'] = 'lecloud.cloudlive.activity.create';
    $data['timestamp'] = time()*1000;
    $data['activityName'] = $letv['title']; //直播活动名称
    $data['activityCategory'] = "012"; //012    教育
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
    preg_match('!\{(.*?)\}$!',$activityId,$arr);
    $result4 = json_decode($arr[0],true);
    $ser['ver'] =     C('Letv')['ver'];
    $ser['userid'] =  C('Letv')['userid'];
    $ser['method'] = 'lecloud.cloudlive.activity.sercurity.config';
    $ser['timestamp'] = time()*1000;
    $ser['activityId'] = $result4['activityId'];
    $ser['neededPushAuth'] = 0;
    $sign2 = getSign($ser);
    $ser['sign'] = $sign2;
    LetvHttp(C('Letv')['url'],$ser,'POST',C('Letv')['headers']);
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
    $xx = recode($letv['id']);
    file_put_contents(HTTP_URL."/test.txt", $xx);
    if($xx){
        return $ress;
    }

}
// 判断是否是微信内部浏览器
function is_weixin(){
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
            return false;
}
/**
*@param $pay_order_num  支付成功订单支付pay_order_num 订单号
* return
*/
function fxReturnMoney($pay_order_num){
    $order   = M('order');
    $info   = $order->field("uid,money,type")->where("pay_order_num='%s'",$pay_order_num)->find();
    $userid = $info['uid']; //用户id
    $money  = $info['money'];   //金额
    $type   = $info['type'];    //购买类型(1:发起直播,2:学霸,3:讲师,4:合伙人,)
    $use['is_cost'] = 1;    //首次消费
    $use['id']      = $userid;
	$use['grade'] = $type;
	if($type ==2){
        $use['xbtime'] = time();
    }
    $rr = M('user')->save($use);    //购买用户等级修改状态

    //分销
    //获取分销规则
    $config = M('config')->field("student,scholar,lecturer,partner,name")->select();
    // ----获取用户的上三级

    $one = M('user')->where('id=%d',$userid)->getField("pid");
    $user = [];
    if ( !empty($one) || $one != null || $one != 0) {
        $user[] = $one;
        $two = M('user')->where('id=%d',$one)->getField("pid");
    }

    if ( !empty($two) || $two != null || $two != 0) {
        $user[] = $two;
        $three = M('user')->where('id=%d',$two)->getField("pid");
    }
    if (!empty($three) || $three != null || $three != 0) {
        $user[] = $three;
    }
    foreach($user as $k=>$v){
        $grade = M('user')->where('id=%d',$v)->getField("grade");
        switch ($grade){
            case 1 :
            $level = 'student';
            break;
            case 2 :
            $level = 'scholar';
            break;
            case 3 :
            $level = 'lecturer';
            break;
            case 4 :
            $level = 'partner';
            break;
        }
        $return[$k]['id'] =$v;
        $return[$k]['level'] = $config[$k][$level];
    }

    foreach($return as $v){
        if(is_numeric($v['level'])){    //判断是否为学童,返积分
            $da['uid'] = $v['id'];
            $da['source'] = $userid;
            $da['score']  = $v['level'];
            $da['message']   = '分佣积分';
            $da['createtime']= time();
            $sco = M('score')->add($da);
            upGrade($v['id']);  //积分，学童自动生成学霸

        }else{
            $daa['money'] = $money * ((int)$v['level'] / 100);
            $daa['uid']   = $v['id'];
            $daa['source']= $userid;
            $daa['message']= '分佣金额';
            $daa['paymenttype']= '平台发放';
            $daa['createtime']= time();
            $acc = M('account')->add($daa);
        }
    }

    file_put_contents('test.txt',$pay_order_num.'--',FILE_APPEND);
    echo "success";return;

}


function ObjectToArray($array) {
    if(is_object($array)) {
        $array = (array)$array;
     } if(is_array($array)) {
         foreach($array as $key=>$value) {
             $array[$key] = ObjectToArray($value);
             }
     }
     return $array;
}
/**
 * 获取指定级别后的所有用户
 * @param $uid char 要查询下级的用户id
 * @param $num int   要查的级别后
 * @return 查询指定级别后的用户下级
 */
function getThreeEnd($uid,$n = ''){
    static $user = [];
    if($n){
        $threeChilden = '';
        for($i = 1;$i <= $n;$i++){
            $threeChilden .= getChilden($uid,$i).',';
        }
        $threeChilden = explode(',',trim($threeChilden,','));
    }
    if(!in_array($uid, $user)) {
        array_push($user, $uid);
    }

    $where['pid'] = ['in',"$uid"];
    $userChilden = M('user')->field('id')->where($where)->select();
    $userChilden = array_column($userChilden, 'id');

    $user = array_unique(array_merge($user, $userChilden));

            // dump($user);exit;
    foreach($userChilden as $user_id) {
        getThreeEnd($user_id);
    }

    $threeChildenEnd = array_diff($user,$threeChilden);
    array_shift($threeChildenEnd);
    return $threeChildenEnd;
}

/**
 * 获取指定级别下级
 * @param $uid char 要查询下级的用户集合id；如'1,2,3'
 * @param $num int   要查询的级别
 * @return 查询级别的用户下级
 */
function getChilden($uid,$num = 1){

    $where['pid'] = ['IN',"$uid"];
    $user1 = M('user')->where($where)->select();
    $users_id = '';
    foreach($user1 as $k=>$v){
        $users_id .= $v['id'].',';
    }
    $users_id = trim($users_id,',');    //一级下级
    for($i = 1;$i < $num;$i++){
        $users_id = getChilden($users_id,$num-1);
        return $users_id;
    }
    return $users_id;
}

// 公用函数库
/**
 * 模拟提交参数，支持https提交 可用于各类api请求
 * @param string $url ： 提交的地址
 * @param array $data :POST数组
 * @param string $method : POST/GET，默认GET方式
 * @return mixed
 */
function http($url, $data='', $method='GET'){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        exit(curl_error($ch));
    }

    curl_close($ch);
    // 返回结果集
    return $result;
}
function LetvHttp($url, $data='', $method='GET',$header=['User-Agent'=>'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)']){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // dump($ch);exit;
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        exit(curl_error($ch));
    }
    curl_close($ch);
    // 返回结果集
    return $result;
}

/*发送短信验证码
auth:mpc
$mobile:手机号
$code :验证码
*/
function NewSms($Mobile){
    
      $str = "1234567890123456789012345678901234567890";
      $str = str_shuffle($str);
      $code= substr($str,3,6);
    $data = "username=%s&password=%s&mobile=%s&content=%s";
    $url="http://120.55.248.18/smsSend.do?";
    $name = "HongRu";
    $pwd  = md5("hJ9eO9dT");
    $pass = md5($name.$pwd);
    $to   =  $Mobile;
    $content = "您的验证码是：".$code."，切勿将验证码泄露于他人。【鸿儒网络】";
    $content = urlencode($content);
    $rdata = sprintf($data, $name, $pass, $to, $content);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    $result = curl_exec($ch);
    curl_close($ch);
    // dump($result);ex
    return ['code' => $code, 'data' => $result, 'msg' =>'1235645'];
}

//乐视sign获取
function getSign($data){
    //1，按照key值升序
   ksort($data);
    //2,拼接key、value值
    $str = '';
    foreach ($data as $k => $v) {
        $str .=$k.$v;
    }
    $da2 = trim($str);
    //3,拼接secretkey
    $secretkey = 'da07bd14f35d9b825139ce719939266d';
    $da3 = $da2.$secretkey;
    //md5加密
    $sign = md5($da3);
    return $sign;
}
