<?php
namespace Admin\Controller;
use Think\Controller;

class ScoreController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }

    /**
    * 用户积分
    */
    public function score(){
        $score = M('score');
        $users = M('user');
        $data = $score->select();
        foreach ($data as $k => $v) {
            $ud = $users->where('id = '.$v['uid'])->find();  
            $data[$k]['name'] = $ud['name'];
            $data[$k]['phone'] = $ud['phone'];
            if($v['source']){
                $data[$k]['sname'] = $users->where('id = '.$v['source'])->find()['name'];
            }
            $end =  $score->where(['id'=>$v['id']])->getField('createtime');
            $data[$k]['account'] = $score->where(['uid'=>$v['uid']])->where('createtime<=%s',$end)->sum('score');
        }        
        $this->assign("data",json_encode($data));
    	$this->display();
    }

    /**
    * 查询会员的积分
    */
    public function searchScore(){
        $score = M('score');
        $user = M('user');
        $phone = $_GET['phone'];
        $reason = $_GET['reason'];
        $start = strtotime(I('start'));
        $end   = strtotime(I('end'));
        if(empty($start)){
            $start = 0;
        }
        if(empty($end)){
            $end = time();
        }
        if(!empty($start) && !empty($end)){
            $start = min($start,$end);
            $end   = max($start,$end);
        }   
        $where = [];
        if(!empty($reason)){
            $where['message'] = ['like',"%$reason%"];
        }
       if(!empty($phone)){
            $users['phone'] = ['like','%'.$phone.'%'];
            $uid = $user->where($users)->select();
        } 
        $where['createtime'] = ['between',"$start,$end"];
        if(!empty($uid)){
            foreach ($uid as $k => $v) {
                $where['uid'] = $v['id'];
                $das = $score->where($where)->select();
                if(!empty($das)){
                    foreach ($das as $k => $v) {
                        $data[] = $v;
                    }
                }
            }
        }else{
        $data = $score->where($where)->select();
        }
         foreach ($data as $k => $v) {
             $ud = $user->where('id = '.$v['uid'])->find();  
             $data[$k]['name'] = $ud['name'];
             $data[$k]['phone'] = $ud['phone'];
             $data[$k]['sname'] = $user->where('id = '.$v['source'])->find()['name'];
             $data[$k]['account'] = $score->where(['uid'=>$v['uid']])->sum('score');
         }
        $this->ajaxReturn($data);  
    }

   
   
}