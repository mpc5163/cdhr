<?php
//卐卐卐卐卐卐卐卐卐卐s卐卐卐卐卐卐卐卐卐卐//
namespace Home\Controller;

use think\Controller;
use Service\Upload;
use Service\Wechat;
class ListController extends Controller{
    //🐤🐤🐤🐤🐤🐤🐤🐤视频类别🐤🐤🐤🐤🐤🐤🐤🐤//
    public function course_class(){
        //------------视频类别------------//
        $id = I('get.classid');     //需要参数：类别id  ---classid
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $classname = M('course_class')->where('id='.$id)->find();
        $courses = M('course');

        $courses_info = $courses->where('isdel=0 and classid='.$id)->select();
        foreach($courses_info as $k=>$v){
            if($v['level'] == '1'){
                $courses_info[$k]['class'] = '免费';
            } elseif($v['level'] == '2'){
                $courses_info[$k]['class'] = '学霸专享';
            }elseif($v['level'] == '3'){
                $courses_info[$k]['class'] = '讲师专享';
            }elseif($v['level'] == '4'){
                $courses_info[$k]['class'] = '合伙人专享';
            }
        }

        $return['classname'] = $classname['name'];
        $return['courses_info'] = $courses_info;
        // var_dump($return);
        // dump($return);
        //视频信息：courses_info    >>array
        //---视频id：id  ---作者：author  ---标题：title  ---人气：looknum  ---文章简介：description  ---展示图：thumbnail  时间：---createtime
        //类别信息：class_name     >>varchar
        jsonpReturn('1','查询成功',$return);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤视频详情🐤🐤🐤🐤🐤🐤🐤🐤//
    public function course(){
        $id = I('get.id');          //需要参数：视频id --id
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $courses = M('course');
        //视频信息
        $courses_info = $courses->where('isdel=0 and id='.$id)->find();
        $user = user_info(session('userid'));
        if(!$user){
            jsonpReturn('3','您还没有登录','');
        }
        if($user['grade']=='1' || $user['grade'] == '2' && $user['grade'] < $courses_info['level']){
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
        // die("5");
        // dump($return);
        //视频信息：courses_info     >>array
        //---视频id:id  ---视频链接：video  ---标题：title  ---人气：looknum  ---课程简介：content
        //讲师信息：teacher          >>array
        //---讲师id：id  ---讲师名字：name/rname  ---头像：headimg  ---座右铭：remark
        jsonpReturn('1','查询成功',$return);
    }
    //视频列表//
    public function course_list(){
        //------------视频列表------------//
        if(!session('userid')){
            jsonpReturn('5','您还没有登录','http://hr2.hongrunet.com/html/jyt_login.html');
        }
        $user = user_info(session('userid'));
        if(!$user){
            jsonpReturn('5','您还没有登录','http://hr2.hongrunet.com/html/jyt_login.html');
        }
        $courses = M('letv');
        //视频列表信息
        $courses_info = $courses->where('endtime >%s and pushurl != ""',time())->select();
        // $courses_info = array_unique($courses_info);
        foreach($courses_info as $k=>$v){
            if($v['type'] == '1'){
                $courses_info[$k]['class'] = '免费观看';
            } elseif($v['type'] == '2'){
                $courses_info[$k]['class'] = '付费观看';
            }
        }
        $return['courses_info'] = $courses_info;

        jsonpReturn('1','查询成功',$return);
    }
    //上传视频
    public function video(){
        if($_FILES['fmvid']['size'] >= 300*1024*1024*1024){
            $this -> ajaxReturn(['status'=>0,'url'=>"你上传的文件过大"]);
        }else{
            $upload =new \Service\Upload();
            $res = $upload ->upload_one($_FILES['fmvid']);
            $this -> ajaxReturn(['status'=>1,'url'=>$res]);
        }
    }
}

//查询用户信息   参数 ：用户id
function user_info($uid){
    $u = M('user');
    $user = $u->where('id='.$uid)->find();
    return $user;
}
