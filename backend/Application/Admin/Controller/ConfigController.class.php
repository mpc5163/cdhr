<?php
namespace Admin\Controller;
use Think\Controller;

class ConfigController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }

    /**
    * 系统设置管理
    */
    public function index(){
        $config = M('config');
        $user = M('userlevel');
        $data = $config->where()->select();
        $user = $user->where()->select();
        $m =array();
        foreach ($user as $k => $v) { 
            $m[] = $v['money'];
        }
        $a =array();
        $b =array();
        $c =array();
        $d =array();
        foreach ($data as $k => $v) { 
            $a[] = $v['student'];
            $b[] = $v['scholar'];
            $c[] = $v['lecturer'];
            $d[] = $v['partner'];
        }          
        $this->assign("m",$m);
        $this->assign("a",$a);
        $this->assign("b",$b);
        $this->assign("c",$c);
        $this->assign("d",$d);
    	$this->display();
    }

    public function letvaaa(){
        $code = I('code');
        if($code){
            $config = M('letvprice')->where('code='.$code)->select();
        }
        // dump($config);exit;
        $return = array();
        foreach($config as $k=>$v){
            if($v['num'] == '1'){
                $return['num1'][] = $v;
            }
            if($v['num'] == '2'){
                $return['num2'][] = $v;
            }
            if($v['num'] == '3'){
                $return['num3'][] = $v;
            }
            if($v['num'] == '4'){
                $return['num4'][] = $v;
            }
            if($v['num'] == '5'){
                $return['num5'][] = $v;
            }
            if($v['num'] == '6'){
                $return['num6'][] = $v;
            }
            if($v['num'] == '7'){
                $return['num7'][] = $v;
            }
            if($v['num'] == '8'){
                $return['num8'][] = $v;
            }
        }
        $this->ajaxReturn($return);     
    }

    /**
    * 保存(新添 | 修改)的系统设置信息
    */
    public function save(){
        $id       = I('thisid');
        $price    = I('thisval');
        $letv       = M('letvprice');
        $pwd = $letv->where("id=%d",$id)->getField("price"); 
        if ($pwd != I('thisval')) {
            $data['price']   = I('thisval');           
        }
        $res = $letv->where("id=%d",$id)->save($data);
        if ($res) {
            $this->success("1","更新成功!",U('Admin/config/letv'));
        }else{
            $this->error("更新失败!");
        }
          
    }
}