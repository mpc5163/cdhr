<?php
namespace Admin\Controller;
use Think\Controller;

class ScholarController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }

    /**
    * 学霸管理
    */
    public function scholar(){
        // echo '9876';
        $user = M('scholar');
        $class= I('class');
        $users = $user->where("isdel = 0 and isenable=1")->select();
        // var_dump($users);
        $this->assign("users",json_encode($users));
        $this->display();
    }

    /**
    * 根据id获取单个学霸
    */
    public function getOneUser(){
        $userid = I('id');
        $user   = M('scholar');
        $users  = $user->where("id=%d",$userid)->find();
        $this->ajaxReturn($users); 
    }

    public function searchUser(){
        $name = I("name");
        $where['rname']=array('like',"%$name%");
        $users = M("scholar")->where("isdel = 0 and isenable=1")->where($where)->select();
        $this->ajaxReturn($users);        
    }
    /**
    * 保存(新添 | 修改)的学霸信息
    */
    public function saveUser(){
        $userid     = (int)I('userid');
        $user       = M('scholar');
        $data['class']      = I('class');
        $data['rname']   = I('rname');
        $data['nickname']       = I('nickname');
        $data['money']       = I('money');
        $data['score']       = I('score');
        $data['phone']= I('phone');
        $data['remark']= I('remark');
        if ($userid) {
            $data['isenable']   = I('isenable');
            $res = $user->where("id=%d",$userid)->save($data);
            if ($res) {
                $this->success("更新成功!",U('Admin/Scholar/scholar'));
            }else{
                $this->error("更新失败!");
            }
        }else{
            $name = $user->getField("rname",true);
            if (!in_array($data['rname'],$name)) {
                $data['createtime'] = date("Y-m-d H:i:s",time());
              $res = $user->add($data);
              if ($res) {
                $this->success("添加成功!",U('Admin/Scholar/scholar'));
              }else{
                $this->error("添加失败!");
              }                            
            }else{
                $this->error("登录名已存在!");
            }


        }
    }
    /**
    * 删除指定id厂商学霸
    */
    public function deleteUser(){
        $id   = I('get.id');
        $user = M('scholar');
        $result = $user->where("id=%d",$id)->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }
}