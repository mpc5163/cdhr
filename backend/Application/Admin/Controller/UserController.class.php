<?php
namespace Admin\Controller;
use Think\Controller;

class UserController extends Controller {
    /**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
    /**
    * 用户管理
    */
    public function user(){
        $user = M('user');
        $score = M('score');
        $account = M('account');
        $data = $user->where('isdel= 0')->select();
        $num =count($data);
        foreach ($data as $k => $v) {
            $data[$k]['money'] = $account->where('uid = '.$v['id'])->sum('money');
            $data[$k]['score'] = $score->where('uid = '.$v['id'])->sum('score');
            $data[$k]['bnm'] = $account->where('uid = '.$v['id'].' and message = "余额提现"')->sum('money');
            $a= $user->where('pid = '.$v['id'])->select();
            $data[$k]['person'] =count($a);
        }
        $this->assign('data',json_encode($data));
        $this->assign("num",$num);
        $this->display();       
    }

    /**
    * 根据id获取单个用户
    */
    public function getOneUser(){
        $userid = I('id');
        $user   = M('user');
        $users  = $user->where("id=%d",$userid)->find();
        if($users['pid']){
          $users['pt']= $user->where('id='.$users['pid'])->find()['phone'];  
        }else{
            $users['pt']= '';
        }
        $this->ajaxReturn($users); 
    }  
    /**
    * 模糊查询会员
    */
    public function searchUser(){
        $state = M('account');
        $user = M('user');
        $score = M('score');
        $phone = $_GET['phone'];
        $grade = $_GET['grade'];
        $name = $_GET['name'];
        $where = [];
        if((I('start')) != "" && (I('end')) != ""){
            $start = strtotime(I('start'));
            $end   = strtotime(I('end'));
            $where['createtime'] = ['between',"$start,$end"];
        }   
        if(!empty($grade)){
            $where['grade'] = ['like',"%$grade%"];
        }
        if(!empty($phone)){
            $where['phone'] = ['like','%'.$phone.'%'];
        }
        if(!empty($name)){
            $where['name'] = ['like',"%$name%"]; 
        }
        $data = $user->where($where)->select();
        // var_dump($data);
        foreach ($data as $k => $v) {
            $data[$k]['money'] = $state->where('uid = '.$v['id'])->sum('money');
            $data[$k]['score'] = $score->where('uid = '.$v['id'])->sum('score');
            $data[$k]['bnm'] = $state->where('uid = '.$v['id'].' and message = "余额提现"')->sum('money');
            $data[$k]['person'] = count($user->where('pid = '.$v['id'])->select());
        }
        $this->ajaxReturn($data);
    }

    /**
    * 保存(新添 | 修改)的用户信息
    */
    public function saveUser(){
        $userid             = (int)I('userid');
        $user               = M('user');
        
        //$data['class']      = I('class');
        $data['rname']      = I('rname');
        $data['name']       = I('nickname');
        //$data['grade']      = I('level');
        $data['phone']      = I('tel');
        $data['remark']     = I('remark');

        #编辑用户
        if ($userid) {
            $xx = $user->where('id='.$userid)->find();
            if(I('thumbnail') == $xx['headimg']){
               
            }else{
                if(strpos(I('thumbnail'),"hongrunet")){
                    $data['headimg']    = I('thumbnail');
                }else{
                    $data['headimg']    = "http://hr.hongrunet.com".I('thumbnail');
                } 
            }
            if(I('pt')){
                $yy = $user->where('phone='.I('pt'))->find();
                if(empty($yy)){
                   $this->ajaxReturn(['status'=>0,'msg'=>'请确认导师账号信息是否正确']); 
                }else{
                    if($yy['id'] != $xx['pid']){
                        $data['pid']= $yy['id'];
                    }
                }  
            }
          /*  if(empty(I('pt'))){
                $data['pid']=
            }*/
            $data['isenable']   = I('isenable');
            $pwd = $user->where("id=%d",$userid)->getField("password"); 
            if ($pwd != I('password')) {
                $data['password']   = MD5(I('password'));           
            }
            $res = $user->where("id=%d",$userid)->save($data);
            if ($res) {
                $this->ajaxReturn(['status'=>1,'msg'=>'更新成功']);
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'更新失败']);
            }

        }else{
            if(strpos(I('thumbnail'),"hongrunet")){
                $data['headimg']    = I('thumbnail');
            }else{
                $data['headimg']    = "http://hr.hongrunet.com".I('thumbnail');
            } 
            #新加用户
            $pid = $user->where('phone='.I('pt'))->find();
            if(empty($pid)){
                $this->ajaxReturn(['status'=>0,'msg'=>'请填入正确的导师账号']);
            }
            $data['pid'] = $pid['id'];
            $name = $user->getField("rname",true);
            if (!in_array($data['rname'],$name)) {
              $data['password']   = md5(I('password'));
              $data['createtime'] = time();        
              $res = $user->add($data);
              if ($res) {
                $this->ajaxReturn(['status'=>1,'msg'=>'添加成功']);
              }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'添加失败']);;
              }                            
            }else{
                $this->ajaxReturn(['status'=>0,'msg'=>'用户已存在']);
            }


        }
    }
    /**
    * 删除指定id用户
    */
    public function deleteUser(){
        $id   = I('get.id');
        $user = M('user');
        $result = $user->where("id=%d",$id)->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }

    /**
    * 根据用户id获取用户下级
    */
    public function getOneFall(){
        $userid = I('id');
        $user   = M('user');
        $a  = $user->where(["id"=>$userid])->find();
        $users = $a['id'];
        $this->ajaxReturn($users);   
    }
    /**
    * 用户下级管理
    */
    public function fall(){
        $falluid= I('falluid');
        $state = M('account');
        $user = M('user');
        $score = M('score');
        $data = $user->where(['pid'=>$falluid])->select();
        
        foreach ($data as $k => $v) {
            $ud = $state->where('uid = '.$v['id'])->find();
            $sd = $score->where('uid = '.$v['id'])->find();
            // $data[$k]['phone'] = $ud['phone'];
            $data[$k]['money'] = $ud['money'];
            $data[$k]['score'] = $sd['score'];
            $a= $user->where('pid = '.$v['id'])->select();
            $data[$k]['person'] =count($a);
        }
        // die("666");
        $this->assign("data",json_encode($data));
        $this->display();
    }
    /**
    * 根据用户id获取粉丝
    */
    public function getOneFans(){
        $userid = I('id');
        $user   = M('user');
        $fans   = M('fans');
        $a  = $fans->where(["uid"=>$userid])->find();
        $users = $a['uid'];
        // var_dump($users);
        $this->ajaxReturn($users); 
    }
    /**
    * 粉丝管理
    */
    public function fans(){
        $fansuid= I('fansuid');
        $user = M('user');
        $fan = M('fans');
        $data  = $fan->where(["uid"=>$fansuid])->select();
        
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['gzh'])->find();
            $data[$k]['name'] = $ud['name'];
            $data[$k]['headimg'] = $ud['headimg'];
            $data[$k]['phone'] = $ud['phone'];
        }
        $this->assign("data",json_encode($data));
        $this->display();
    }
    /**
    * 根据用户id获取我的关注
    */
    public function getOneFol(){
        $userid = I('id');
        $user   = M('user');
        $fans   = M('fans');
        $a  = $fans->where(["gzh"=>$userid])->find();
        $users = $a['gzh'];
        // var_dump($users);
        $this->ajaxReturn($users); 
    }
    /**
    * 我的关注管理
    */
    public function follow(){
        $fid= I('fid');
        $user = M('user');
        $fan = M('fans');
        $data  = $fan->where(["gzh"=>$fid])->select();
        // var_dump($data);
        foreach ($data as $k => $v) {
            $ud = $user->where('id = '.$v['uid'])->find();
            $data[$k]['name'] = $ud['name'];
            $data[$k]['headimg'] = $ud['headimg'];
            $data[$k]['phone'] = $ud['phone'];
        }
        $this->assign("data",json_encode($data));
        $this->display();
    }
    /**
    * 查询我的关注
    */
    public function searchFollow(){
        $name = I("name");
        $where['name']=array('like',"%$name%");
        $follow = M("fans")->where("isdel = 0 and isenable=1")->where($where)->select();
        $this->ajaxReturn($follow);  
    }
    /**
    * 删除指定id我的关注
    */
    public function deleteFollow(){
        $id   = I('get.id');
        $follow = M('fans');
        $result = $follow->where(["id"=>$id])->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }
}