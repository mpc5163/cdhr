<?php
namespace Admin\Controller;
use Think\Controller;

class LoginController extends Controller {


	/**
	 * 登录页面
	 */
	public function login(){

		if(IS_POST){
            //取出POST请求的参数值
            $username=I('username');
            $pwd=md5(I('password'));
            $users=M('adminuser');
            $vercode=I("code");
            if(!$this->check_verify($vercode)){
                $this->error('验证码错误');
            }
            //根据用户名查询出  该用户信息
            $user=$users->where("username='%s'",$username)->find();
            //判断密码是否一致
            if($pwd==$user['password']){
                //保存当前登录用户的 ID 和名称 到Session中
                $_SESSION['userid']  =$user['id'];
                $_SESSION['username']=$user['username'];
                $_SESSION['roleid']  =$user['roleid'];
                $_SESSION['rolename']=$user['rolename'];

                $data['IP'] =  $_SERVER['REMOTE_ADDR'];
                $data['logintime'] = time();
                $result     = $users->where("username='%s'",$username)->save($data); 
                //向页面发出登陆成功的消息
                $this->success('登录成功',U('/Admin/Main/index'));
            }else{
                $this->error('用户名或密码错误');
            }
        }else{
            $this->display();
        }
	}
	/**
	 * 获取验证码
	 */
	public function verify_code(){
        ob_clean();
    	$verify=new \Think\Verify();
    	$verify->fontSize=18;
    	$verify->length=4;
    	$verify->useNoise=false;
    	$verify->useCurve=false;
    	$verify->codeSet='0123456789';
    	$verify->imageW=150;
    	$verify->imageH=40;
    	$verify->entry();
    }
    /**
     * 检验验证码
     */
    function check_verify($code, $id = ''){
    	$verify = new \Think\Verify();
		return $verify->check($code, $id);
    }
    /**
     * 系统退出
     */
    public function logout(){
        session_destroy();
        redirect(U('/Admin/Login/login'));
    }
}
