<?php
namespace Admin\Controller;
use Think\Controller;

class DataController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
    /**
    * 
    */
    public function index(){
        $t = time();
        $starttime = mktime(0,0,0,date("m",$t),date("d",$t),date("Y",$t));
        $endtime = mktime(23,59,59,date("m",$t),date("d",$t),date("Y",$t));

        // 累计会员总量
        $user = M('user');
        $user = $user->where("isdel = 0 and isenable=1")->select();
        $num = count($user);

        // 今日新增会员量
        $time['createtime'] = array('between',array($starttime,$endtime));
        $arr = M('user')->where("isdel = 0 and isenable=1")->where($time)->select();                       
        $numtoday = count($arr);
        // 历史累计收入总额
        $user = M('account');
        // $money1 = $user->where("message = '升级等级'")->sum('money');
        $money2 = $user->where("message = '收到红包'")->sum('money');
        $money3 = $user->where("message = '分佣金额'")->sum('money');
        /*if($money1 < 0){
            $money1 = -$money1;
        }
        $money = $money1+$money2+$money3;*/
        $money = $money2+$money3;
        // 已发放提现总额
        $user = M('account');
        $rest = $user->where("message = '余额提现'")->sum('money');
        // 会员账户月总计
        $beginThismonth=mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth=mktime(23,59,59,date('m'),date('t'),date('Y')); 
        $monthtime['createtime'] = array('between',array($beginThismonth,$endThismonth));
        $user = M('account');
        $monthmoney = $user->where($monthtime)->sum('money');
        // 会员积分累计
        $user = M('score');
        $score = $user->sum('score');
        // 今日提现总额
        $user = M('account');
        $today = $user->where("message = '余额提现'")->where($time)->sum('money');
        // echo (time());
        // 今日提现手续费总计
        $user = M('account');
        $today = $user->where("message = '余额提现'")->where($time)->sum('money');
        $todayfee = -($today *1/100);
        // 分销佣金总计
        $user = M('account');
        // $money = $user->sum('money');
        $a = $user->where("message = '分佣金额'")->sum('money');
        // echo($money);
        // 今日分销佣金总计
        $user = M('account');
        $start = strtotime(date('Y-m-d 0:0:0',time()));
        $end = strtotime(date('Y-m-d 23:59:59',time()));
        $input = $user->where('createtime>%s and createtime<%s',$start,$end)->sum('money');
        $b = $user->where($time)->sum('money');
        $this->assign("input",$input);
        $this->assign("num",$num);
        $this->assign("money",$money);
        // $this->assign("money2",$money2);
        // $this->assign("money3",$money3);
        $this->assign("rest",$rest);
        $this->assign("monthmoney",$monthmoney);
        $this->assign("numtoday",$numtoday);
        $this->assign("score",$score);
        $this->assign("today",$today);
        $this->assign("todayfee",$todayfee);
        $this->assign("a",$a);
        $this->assign("b",$b);
        
        $this->display();
        
    }
    
}