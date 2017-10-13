<?php
namespace Admin\Controller;
use Think\Controller;

class LetvListController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
    /**
    * 佣金管理
    */
    public function index(){
        $user = M('user');
        $letv = M('letv');
        $account = M('account');
        $data = $letv->select();
        //dump($data);exit;
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $data[$k]['phone'] = $ud['phone'];
            $data[$k]['name'] = $ud['name'];
            $s =$account->where('uid = '.$v['uid'].' and message = "收到红包"')->sum('money');
            // var_dump($s);die;
            $data[$k]['money'] = $s;
        }
        $this->assign('data',json_encode($data));
        $this->display();
    }
    public function price(){
        $letv = M('letvprice');
        $data = $letv->where('type=2')->find()['price'];
        $this->assign('daf',$data);      
        $this->display();
    }
    public function save(){
        $letv = M('letvprice');
        $price = I('price');
        $res = $letv->where('type=2')->save(['price'=>$price]);
            if($res){
                $this -> ajaxReturn(['status'=>1,'msg'=>"更新成功"]);
            }else{
                $this -> ajaxReturn(['status'=>0,'msg'=>"更新失败"]);
            }
    }
     public function del(){
        $letv = M('letv');
        self::stopx(I('id'));
        $res = $letv->where('id=%d',I('id'))->delete();
            if($res){
               $this -> ajaxReturn(['status'=>1,'msg'=>"删除成功"]);
            }else{
                $this -> ajaxReturn(['status'=>0,'msg'=>"删除失败"]);
            }
    }
    //直播结束接口
    public function stopx($id){
        $letv = M('letv')->where('id=%d',I('id'))->find();
        if($letv['endTime']<time()){
            return true;
        }
        $data['ver'] =    '4.0';
        $data['userid'] = 910018;
        $data['method'] = 'lecloud.cloudlive.activity.stop';
        $data['timestamp'] = time()*1000;
        $data['activityId'] = $letv['activityId'];
        $sign = getSign($data);
        $data['sign'] = $sign;
        $url = 'http://api.open.lecloud.com/live/execute';
        $activityId = LetvHttp($url,$data,'POST',$this->header);
        $res = M('letv')->where('id=%d',I('id'))->save(['endTime'=>time()]);
        if($res){
            return true; 
       }else{
            return false;
       }    
    }
    //直播结束接口
    public function stop(){
        $letv = M('letv')->where('id=%d',I('id'))->find();
        if($letv['endTime']<time()){
            $this -> ajaxReturn(['status'=>0,'msg'=>"已停止"]);
        }
        $data['ver'] =    '4.0';
        $data['userid'] = 910018;
        $data['method'] = 'lecloud.cloudlive.activity.stop';
        $data['timestamp'] = time()*1000;
        $data['activityId'] = $letv['activityId'];
        $sign = getSign($data);
        $data['sign'] = $sign;
        $url = 'http://api.open.lecloud.com/live/execute';
        $activityId = LetvHttp($url,$data,'POST',$this->header);
        $res = M('letv')->where('id=%d',I('id'))->save(['endTime'=>time()]);
        if($res){
            $this -> ajaxReturn(['status'=>1,'msg'=>"停止成功"]); 
       }else{
            $this -> ajaxReturn(['status'=>0,'msg'=>"停止失败"]);
       }    
    }
    /**
    * 根据id获取单个用户
    */
    public function getOneUser(){
        $userid = I('id');
        $user   = M('account');
        $users  = $user->where(["id"=>$userid])->find();
        $this->ajaxReturn($users); 
    }
    
    /**
    * 模糊查询
    */
    public function searchState(){
        // die("999");
        $state = M('letv');
        $user = M('user');
        $phone = $_GET['phone'];
        $change = $_GET['change'];
        $where = [];
        // die("666");
        if((I('start')) !="" && (I('end'))!=""){
            $start = strtotime(I('start'));
            $end   = strtotime(I('end'));
            $where['createtime'] = ['between',"$start,$end"];
        }   
        if(!empty($change)){
            $where['cid'] = ['like',"%$change%"];
        }
        if(!empty($phone)){
            $users['phone'] = ['like','%'.$phone.'%'];
            $uid = $user->where($users)->select();
        } 
        if(!empty($uid)){
            foreach ($uid as $k => $v) {
                $where['uid'] = $v['id'];
                $das = $state->where($where)->select();
                // var_dump($das);
                if(!empty($das)){
                    foreach ($das as $k => $v) {
                        $data[] = $v;
                    }
                }
            }
        }else{
            $data = $state->where($where)->select();
        }   
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $data[$k]['phone'] = $ud['phone'];
            $data[$k]['name'] = $ud['name'];
        }
        $this->ajaxReturn($data);
    }
    
}