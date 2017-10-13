<?php
namespace Admin\Controller;
use Think\Controller;

class PartnerController extends Controller {
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
    public function partner(){
        $user = M('partner');
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
        $user   = M('partner');
        $users  = $user->where("id=%d",$userid)->find();
        $this->ajaxReturn($users); 
    }

    /**
    * 查询合伙人
    */
    public function searchUser(){
        $name = I("name");
        $where['realname']=array('like',"%$name%");
        $users = M("partner")->where("isdel = 0 and isenable=1")->where($where)->select();
        $this->ajaxReturn($users);  
    }
    /**
    * 保存(新添 | 修改)的学霸信息
    */
    public function saveUser(){
        $userid     = (int)I('userid');
        $user       = M('partner');
        $data['class']      = I('class');
        $data['realname']   = I('realname');
        $data['nickname']       = I('nickname');
        $data['money']       = I('money');
        $data['phone']= I('phone');
        $data['remark']= I('remark');
        if ($userid) {
            $data['isenable']   = I('isenable');
            $res = $user->where("id=%d",$userid)->save($data);
            if ($res) {
                $this->success("更新成功!",U('Admin/Partner/partner'));
            }else{
                $this->error("更新失败!");
            }
        }else{
            $name = $user->getField("realname",true);
            if (!in_array($data['realname'],$name)) {
              $data['createtime'] = date("Y-m-d H:i:s",time());
              $res = $user->add($data);
              if ($res) {
                $this->success("添加成功!",U('Admin/Partner/partner'));
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
        $user = M('partner');
        $result = $user->where("id=%d",$id)->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }
}