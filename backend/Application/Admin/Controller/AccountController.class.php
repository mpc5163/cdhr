<?php
namespace Admin\Controller;
use Think\Controller;

class AccountController extends Controller {
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
        $state  = M('account');
        $user = M('user');
        $data = $state->select();
       /* $where['message'] = '购买用户等级';
        $where['status'] = 2;
        $dj = M('order')->where($where)->field('uid,id,money,message,payment,createtime,remark')->select();
        foreach ($dj as $k => $v) {
            $dj[$k]['paymenttype'] = $v['payment'];
            $dj[$k]['remark'] = $v['message'];
            array_push($data, $v);
        }*/
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $end =  $state->where(['uid'=>$v['uid']])->getField('createtime');
            $data[$k]['restmoney'] = $state->where('uid = '.$v['uid'])->sum('money');
            // $data[$k]['restmoney'] = $state->where('uid = '.$v['uid'])->where('createtime<=%s',$end)->sum('money');
            $data[$k]['phone'] = $ud['phone'];
            $data[$k]['name'] = $ud['name'];
             
        }
        $data = array_values($data);
        $this->assign('data',json_encode($data));
        $this->display();
    }
    /**
    * 根据id获取单个用户
    */
    public function getOneUser(){
        $userid = I('id');
        $user   = M('account');
        $users  = $user->where("id=%d",$userid)->find();
        $this->ajaxReturn($users); 
    }
    
    /**
    * 模糊查询会员佣金
    */
    public function searchState(){
        $state = M('account');
        $user = M('user');
        $phone = $_GET['phone'];
        $change = $_GET['change'];
        $where = [];
        if(!empty(I('start')) && !empty(I('end'))){
            $start = strtotime(I('start'));
            $end   = strtotime(I('end'));
            $where['createtime'] = ['between',"$start,$end"];
        }   
        if(!empty($change)){
            switch ($change) {
                case '1':
                    $change= "升级等级";          
                    break;    
                case '2':
                    $change = "余额提现";            
                    break;
                case '3':
                    $change = "余额充值";            
                    break;
                case '4':
                    $change = "收到红包";            
                    break;
                case '5':
                    $change = "校友付费";            
                    break;
                case '6':
                    $change = "见点奖";            
                    break;
            }
            $where['message'] = ['like',"%$change%"];
        }
        if(!empty($change)){
            $where['message'] = ['like',"%$change%"];
        }
      
        if(!empty($phone)){
            $users['phone'] = ['like','%'.$phone.'%'];
            $uid = $user->where($users)->getField('id',true);
            $where['uid'] = ['in',$uid];
        } 
        if($change == '升级等级'){
            $where['message'] = '购买用户等级';
            $where['status'] = 2;
            unset($where['paymenttype']);
            $data = M('order')->where($where)->field('uid,id,money,message,payment,createtime,remark')->select();
            foreach ($data as $k => $v) {
                $data[$k]['paymenttype'] = $v['payment'];
                $data[$k]['remark'] = $v['message'];
                $ud = $user->where('id = '.$v['uid'])->find();
                if(empty($ud)){
                    unset($data[$k]);
                }else{
                    $data[$k]['phone'] = $ud['phone'];
                    $data[$k]['name'] = $ud['name'];  
                }
            }
        }else{
            $data = $state->where($where)->select();
            foreach ($data as $k => $v) {
                $ud = $user->where('id = '.$v['uid'])->find();
                $data[$k]['phone'] = $ud['phone'];
                $data[$k]['name'] = $ud['name']; 
            }
        }
        $data = array_values($data);
        $this->ajaxReturn($data);
    }
    /**
    * 删除指定id用户佣金
    */
    public function deleteUser(){
        $id   = I('get.id');
        $user = M('account');
        $result = $user->where("id=%d",$id)->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    // 见点奖
    public function award(){
        // echo(time());
        $user = M('user');
        $data  = M('account')->where('message = "见点奖"')->select();
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $data[$k]['phone'] = $ud['phone'];
            $data[$k]['name'] = $ud['name'];
            $data[$k]['grade'] = $ud['grade'];
        }
        $this->assign("data",json_encode($data));
        $this->display();
    }
    /**
    * 模糊查询会员见点奖
    */
    public function searchAward(){
        $state = M('account');
        $user = M('user');
        $phone = $_GET['phone'];
        $change = $_GET['type'];
        $where = [];
        if(!empty(I('start')) && !empty(I('end'))){
            $start = strtotime(I('start'));
            $end   = strtotime(I('end'));
            $where['createtime'] = ['between',"$start,$end"];
        }
        if(!empty($change)){
            $users['grade'] = ['like','%'.$change.'%'];
            $uid = $user->where($users)->select();
        }
        if(!empty($phone)){
            $users['phone'] = ['like','%'.$phone.'%'];
            $uid = $user->where($users)->select();
        } 
        if(!empty($uid)){
            foreach ($uid as $k => $v) {
                $where['uid'] = $v['id'];
                $das = $state->where($where)->where('message = "见点奖"')->select();
                if(!empty($das)){
                    foreach ($das as $k => $v) {
                        $data[] = $v;
                    }
                }
            }
        }else{
            $data = $state->where($where)->where('message = "见点奖"')->select();
        }   
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $data[$k]['phone'] = $ud['phone'];
            $data[$k]['name'] = $ud['name'];
            $data[$k]['grade'] = $ud['grade'];
        }
        $this->ajaxReturn($data);
    }
}