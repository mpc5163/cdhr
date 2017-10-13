<?php
namespace Home\Controller;
use Think\Controller;
use Service\Wechat;

class BaseController extends Controller {


    /**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        # 判断是否在微信端访问
        if(is_weixin()){
            # 判断是否登录了
            if(!isset($_SESSION['userid'])){
                # 让前端跳转到微信授权url
                $this -> jsonpReturn(['status'=>2000,'url'=>Wechat::get_user_info('http://'.$_SERVER['HTTP_HOST'].'/Home/Wechat/wechat_login.html')]);
            }else if($_SESSION['phone']==''){
                # 让前端跳转到绑定页面
                $this -> jsonpReturn(['status'=>1000,'url'=>'http://'.$_SERVER['HTTP_HOST'].'/Home/Wechat/bind.html']);
            }
        }else{
            # 判断是否登录了
            if (!isset($_SESSION['userid'])){
                # 让前端跳转到前端的登录页面
                $this -> jsonpReturn(['status'=>3000,'msg'=>'请重新登录']);
            }
        }

    }
}
