<?php
namespace Admin\Controller;
use Think\Controller;
use Service\Alipay as Alipays;

class StateController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
            $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
    public function coin(){
        $coin = M('coin')->select();
        $user = M('user')->field('id,phone,name')->select();
        foreach ($user as $k => $v) {
            if(empty($v['phone'])){
                unset($user[$k]);
            }
        }

        foreach ($coin as $k => $v) {
            $xx = M('user')->field('name,phone')->where('id=%d',$v['uid'])->find();
            $coin[$k]['nickname'] = $xx['name'];
            $coin[$k]['phone'] = $xx['phone'];
        }
        $this->assign("user",$user);
        $this->assign('data',json_encode($coin));
        $this->display();
    }
    // public function userss(){
    //     $this->ajaxReturn($user);
    // }
    public function recharge(){
        $data['coin'] = I('coin');
        $data['uid']  = M('user')->where('phone=%s',I('userid'))->getField('id');
        $data['createtime']  = time();
        $data['remark']  = "系统充值";
        $coin = M('user')->where('id=%d',$data['uid'])->getField('coin');
        $res = M('user')->where('id=%d',$data['uid'])->save(['coin'=>((int)I('coin')+(int)$coin)]);
        $res1 = M('coin')->add($data);
        if($res && $res1){
            $this->ajaxReturn(['status'=>1]);
        }else{
            $this->ajaxReturn(['status'=>0]);
        }
    }
    public function index(){
		$state  = M('account');
		$user = M('user');
		$data = $state->select();
        $where['message'] = '购买用户等级';
        $where['status'] = 2;
        $dj = M('order')->where($where)->field('uid,id,money,message,payment,createtime,remark')->select();
        foreach ($dj as $k => $v) {
            $dj[$k]['paymenttype'] = $v['payment'];
            $dj[$k]['remark'] = $v['message'];
            array_push($data, $v);
        }
		foreach ($data as $k => $v) {
			$ud = $user->where('id = '.$v['uid'])->find();
			if(empty($ud)){
                    unset($data[$k]);
                }else{
                    $data[$k]['phone'] = $ud['phone'];
                    $data[$k]['nikname'] = $ud['name'];  
                }
		}
        $data = array_values($data);
		$this->assign('data',json_encode($data));
		$this->display();		
	}
    
    public function searchState(){
    	$state = M('account');
    	$user = M('user');
    	$phone = $_GET['phone'];
    	$reason = $_GET['reason'];
    	$paytype = $_GET['type'];
    	$start = strtotime(I('start'));
    	$end   = strtotime(I('end'));
    	if(empty(I('start'))){
    		$start = 0;
    	}
    	if(empty(I('end'))){
    		$end = time();
    	}
    	if(!empty(I('start')) && !empty(I('end'))){
    		$start = min($start,$end);
    		$end   = max($start,$end);
    	}	
        if(!empty($reason)){
            switch ($reason) {
                case '1':
                    $reason= "升级等级";          
                    break;    
                case '2':
                    $reason = "余额提现";            
                    break;
                case '3':
                    $reason = "余额充值";            
                    break;
                case '4':
                    $reason = "收到红包";            
                    break;
                case '5':
                    $reason = "校友付费";            
                    break;
                case '6':
                    $reason = "分佣金额";            
                    break;
            }
            $where['message'] = ['like',"%$reason%"];
        }
    	$where = [];

    	if(!empty($reason)){
    		$where['message'] = ['like',"%$reason%"];
    	}
    	if(!empty($paytype)){
    		$where['paymenttype'] = ['like',"%$paytype%"];
    	}
    	$where['createtime'] = ['between',"$start,$end"];
    	if(!empty($phone)){
            $users['phone'] = ['like','%'.$phone.'%'];
            $uid = $user->where($users)->getField('id',true);
            $where['uid'] = ['in',$uid];
        } 
        if($reason == '升级等级'){
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
                    $data[$k]['nikname'] = $ud['name'];  
                }
            }
        }else{
            $data = $state->where($where)->select();
            foreach ($data as $k => $v) {
                $ud = $user->where('id = '.$v['uid'])->find();
                $data[$k]['phone'] = $ud['phone'];
                $data[$k]['nikname'] = $ud['name']; 
            }
        }
        $data = array_values($data);
    	$this->ajaxReturn($data);
    }
    
	
}
