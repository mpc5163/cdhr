<?php
namespace Admin\Controller;
use Think\Controller;

class AdminController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
            $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }



    /**
	 * 系统用户管理
	 */
	public function index(){
		$user  = M('adminuser');
		$role  = M('role');
		$roles = $role->where("isdel = 0 and status=1")->select();
		$users = $user->where("isdel = 0 and status=1")->select();
		$count = count($roles);
		foreach ($users as $key => $value) {
			if ($value['status'] ==1) {
				$users[$key]['status'] = "可用"; 			
			}else{
				$users[$key]['status'] = "不可用"; 			
			}
			for ($i=0; $i < $count; $i++) { 
				if ($value['roleid'] == $roles[$i]['id']) {
					$users[$key]['rolename'] = $roles[$i]['rolename'];
				}
			}
			$users[$key]['logintime'] = date("Y-m-d H:i:s",$value['logintime']); 			
		}
		$this->assign("roles",$roles);
		$this->assign("users",json_encode($users));
		$this->display();
	}

	/**
	 * 根据用户ID获取用户信息
	 */
	public function getUserbyID(){
		$id    = I("id");
		$user  = M("adminuser");
		$users = $user->where("id=%d", $id)->find();
		$this->ajaxReturn($users);
	}

	/**
	 * 删除选择的用户
	 */
	public function deleteUser(){
		$id = I("id");
		$user = M("adminuser");
		$data['isdel'] = 1;
		$result = $user->where("id=%d", $id)->save($data);
		if($result){
			echo "true";
		}else{
			echo "false";
		}
	}

	/**
	 * 保存（新增）用户信息
	 */
	public function saveUser(){
		// dump($_POST);die;
		$userid    = (int)I("userid");
		$username  = I("username");
		$user      = M('adminuser');
		$password  = I('password');
		$usernames = $user->where("username ='".$username."'")->find();
		//判断登录名是否重复
		if (!empty($usernames) && $usernames['id'] != $userid) {
			$this->error("用户名存在!");
		}
			$data["username"]=I("username");
			$data["roleid"]  =I("roleid");
			$data["phone"]   = I("phone");
			if($userid){
				if ($usernames['password'] != $password) {
					$data['password'] = MD5($password);
				}
				$data["status"] = I('status');	
				$result = $user->where("id=%d", $userid)->save($data);
				if($result){
					$this->success("更新用户成功！", U("/Admin/Admin/index"));
				}else{
					$this->error("更新用户失败！");
				}
			}else{
				$data['password'] = MD5($password);
				$data['createtime'] = date("Y-m-d H:i:s",time());
				$result = $user->add($data);
				if($result){
					$this->success("添加用户成功！", U("/Admin/Admin/index"));
				}else{
					$this->error("添加用户失败！");
				}
			}
	}

	 
	/**
	 * 修改密码页面
	 */
	public function changepwd(){
		$this->display();
	}
	/**
	 * 验证并保存修改后的新密码
	 */
	public function savePWD(){
		$id = I("userid");
		$pwd = md5(I("password"));	
		$newpwd = I("newpassword");
		$confpwd = I("confpassword");
		$user = M("adminuser");
		$users = $user->where("id=%d", $id)->find();
		if($users["password"] != $pwd){
			$this->error("输入原始密码错误！");
		}
		$data["password"] = md5($newpwd);
		$result = $user->where("id=%d", $id)->save($data);
		if($result){
			$this->success("更新密码成功！",U('/Admin/Admin/index'));
		}else{
			$this->error('更新密码失败！');
		}
	}
}