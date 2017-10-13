<?php
namespace Home\Controller;

use Service\Wechat as Wechats;
use Think\Controller;
use Service\File;

# 前台微信控制器
class CourseController extends Controller {
	#视频详情
    public function course(){
        $id = I('get.id');          //需要参数：视频id --id
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $courses = M('course');
        //视频信息
        $courses_info = $courses->where('isdel=0 and id='.$id)->find();
        $_SESSION['jsapi_config_url'] = $_GET['url'];
        if(is_weixin()){
            $jsapi_config = Wechats::get_jsapi_config(['onMenuShareTimeline','onMenuShareAppMessage'],false,false); 
        }
        if(!$_SESSION['userid']){
            if($courses_info['level']==1){
             //观看人数
               $update['looknum'] = $courses_info['looknum']+1;
                $courses->where('id='.$id)->save($update);
                $courses_info['looknum'] += 1;
                $teacher = user_info($courses_info['uid']);
                $return['courses_info'] = $courses_info;
                $return['teacher'] = $teacher;     
                $return['type'] = 0;
                // dump($return);exit;
                //视频信息：courses_info     >>array
                //---视频id:id  ---视频链接：video  ---标题：title  ---人气：looknum  ---课程简介：content
                //讲师信息：teacher          >>array
                //---讲师id：id  ---讲师名字：name/rname  ---头像：headimg  ---座右铭：remark
                 jsonpReturn('1','查询成功',$return);
            }else{
                jsonpReturn('10','请先去注册','');
            }
            
        }else{
            $user = user_info(session('userid'));
            if(!$user){
                jsonpReturn('3','您还没有登录','');
            }
            if(($user['grade']=='1' && $user['grade'] < $courses_info['level']) || ($user['grade'] == '2' && $user['grade'] < $courses_info['level'])){
                $month_time = 60*60*24*30;
                $time = time() - $month_time;
                $vip = M('uservip');
                $uservip = $vip->where("user_id={$user['id']} and add_time>".$time)->find();
                if(!$uservip){
                    jsonpReturn('2','您的权限不足','');
                }
            }
            //观看人数+1
            $update['looknum'] = $courses_info['looknum']+1;
            $courses->where('id='.$id)->save($update);
            $courses_info['looknum'] += 1;
            $teacher = user_info($courses_info['uid']);
            $return['courses_info'] = $courses_info;
            $return['teacher'] = $teacher;
            $users = M('fans')->field('gzh')->where('uid='.$teacher['id'])->select();
            $ids = [];
            foreach($users as $k=>$v){
                $ids[$k] = $v['gzh'];
            }
            if( in_array(session('userid'),$ids)){
                $type = 1;
            }else{
                $type = 0;
            }
            $return['type'] = $type;
            $return['jsapi_config'] = $jsapi_config;
            // $return['access_tocken'] = $access_tocken;
            // dump($return);exit;
            //视频信息：courses_info     >>array
            //---视频id:id  ---视频链接：video  ---标题：title  ---人气：looknum  ---课程简介：content
            //讲师信息：teacher          >>array
            //---讲师id：id  ---讲师名字：name/rname  ---头像：headimg  ---座右铭：remark
            jsonpReturn('1','查询成功',$return);
        }
        
	}
	public function das(){
		if($_SESSION['userid']){
			jsonpReturn('1','成功','');
		}else{
			jsonpReturn('0','失败','');
		}
	}

}
//查询用户信息   参数 ：用户id
function user_info($uid){
    $u = M('user');
    if($uid == 0){
        $user = $u->where('id=30')->find();
    }else{
        $user = $u->where('id='.$uid)->find();
    }
    

    return $user;
}