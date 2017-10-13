<?php
namespace Admin\Controller;
use Think\Controller;

class MainController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
    /**
     * 系统主页面
     */
    public function index(){

        $this->display();
    }

    public function sysdatainfo(){
        $this->display();
    }
}