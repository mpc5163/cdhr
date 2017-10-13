<?php
//卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐卐//
namespace Home\Controller;

use think\Controller;
use Service\Upload;
use Service\Wechat;

class IndexController extends Controller{

    //短信接口预留
    public function _initialize(){
        $no_login = [
            '/index.php/home/index/index',
        ];
        $req = strtolower(trim($_SERVER['DOCUMENT_URI']));
        // dump($_SERVER['DOCUMENT_URI']);exit;
        if(empty(session('userid')) && !in_array($req,$no_login)){
            jsonpReturn('5','跳转首页','http://hr2.hongrunet.com/html/jyt_login.html');
        }
    }
    //🐤🐤🐤🐤🐤🐤🐤🐤首页🐤🐤🐤🐤🐤🐤🐤🐤//
    public function index(){

        //------------轮播图------------//
        $lunbo = M('lunbo');
        $top_lunbotu = $lunbo->where('isdel=0 and isenable=1')->limit(6)->select();
        $video = M('letv')->where('endtime >'.time())->order('id desc')->limit(15)->select();

        $index['top_lunbotu'] = $top_lunbotu;    //顶部轮播图
        $index['but_lunbotu'] = $video;   //底部轮播图

        $course_type = M('course_class');
        //-------------视屏类别-------------//
        $course_class = $course_type->select();
        $index['course_class'] = $course_class;

        jsonpReturn('1','查询成功',$index);
        // jsonpReturn('1','查询失败',$index);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤视频类别🐤🐤🐤🐤🐤🐤🐤🐤//
    public function course_class(){
        //------------视频类别------------//
        $id = I('get.classid');     //需要参数：类别id  ---classid
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $classname = M('course_class')->where('id='.$id)->find();
        $courses = M('course');

        $courses_info = $courses->where('isdel=0 and classid='.$id)->order('id desc')->select();
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
        $id = I('get.id');          //需要参数：视频id --id
        // $id = 2;
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $courses = M('letv');
        //视频列表信息
        $courses_info = $courses->where(['id'=>$id])->select();
        // var_dump($courses_info);die;
        // $user = user_info(session('userid'));
        // if(!$user){
        //     jsonpReturn('3','您还没有登录','');
        // }
        foreach($courses_info as $k=>$v){
            if($v['classid'] == '1'){
                $courses_info[$k]['class'] = '免费';
            } elseif($v['classid'] == '2'){
                $courses_info[$k]['class'] = '学霸专享';
            }elseif($v['classid'] == '3'){
                $courses_info[$k]['class'] = '讲师专享';
            }elseif($v['classid'] == '4'){
                $courses_info[$k]['class'] = '合伙人专享';
            }
        }

        $return['courses_info'] = $courses_info;
        // var_dump($return);die;
        //视频信息：courses_info     >>array
        //---视频id:id  ---视频链接：video  ---标题：title  ---人气：looknum  ---课程简介：content
        //讲师信息：teacher          >>array
        //---讲师id：id  ---讲师名字：name/rname  ---头像：headimg  ---座右铭：remark
        jsonpReturn('1','查询成功',$return);
    }
    //🐤🐤🐤🐤🐤🐤🐤🐤文章列表🐤🐤🐤🐤🐤🐤🐤🐤//
    public function info(){
        $classid = I('get.classid');    //需要参数：文章类别id --classid

        if(empty($classid)){
            jsonpReturn('2','参数不存在','');
        }
        $article = M('info');
        //文章信息
        $info = $article->where('isdel=0 and classid='.$classid)->order('id desc')->select();
        // $info = $article->where('isdel=0 and classid='.$classid)->select();
        //类别信息
        if($classid == '1'){
            $class_name = '干货分享';
        }elseif($classid == '2'){
            $class_name = '深度好文章';
        }elseif($classid == '3'){
            $class_name = '公司简介';
        }

        foreach($info as $k=>$v){
            $info[$k]['createtime'] = date('Y-m-d',$v['createtime']);
        }

        $return['info'] = $info;
        $return['class_name'] = $class_name;

        // dump($return);
        //文章信息：info    >>array
        //---文章id：id  ---作者：author  ---标题：title  ---人气：looknum  ---文章简介：description  ---展示图：thumbnail  时间：---createtime
        //类别信息：class_name     >>varchar
        jsonpReturn('1','查询成功',$return);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤文章详情🐤🐤🐤🐤🐤🐤🐤🐤//
    public function article(){
        $id = I('get.id');          //需要参数：文章id --id
        if(empty($id)){
            jsonpReturn('2','参数不存在','');
        }
        $article = M('info');
        //文章信息
        $info = $article->where('id='.$id)->find();

        $info['createtime'] = date('Y-m-d',$info['createtime']);

        $info['looknum'] += 1;
        $update['looknum'] = $info['looknum'];
        // $return['title'] = html_entity_decode($return['title']);
        // $return['content'] = htmlentities($return['content']);
        $article->where('id='.$id)->save($update);
        $return['info'] = $info;
        // dump($return);
        //文章信息：info     >>array
        //---文章id:id  ---标题：title  ---作者：author  ---人气：looknum  ---文章内容：content  ---创建时间：createtime
        jsonpReturn('1','查询成功',$return);
    }
    // 公司简介
    public function company(){
        $article = M('info');
        //文章信息
        $info = $article->where('classid=3')->find();
        $return['info'] = $info;

        jsonpReturn('1','查询成功',$return);
    }
    //🐤🐤🐤🐤🐤🐤🐤🐤聊天室🐤🐤🐤🐤🐤🐤🐤🐤//
    public function room(){
        $room = M('room')->select();
        foreach($room as $k=>$v){
            $id = $v['chuangjian_id'];
            $user = M('user')->where("id=$id")->find();
            $room[$k]['chuangjian_name'] = $user['name'];
            $room[$k]['chuangjian_img'] = $user['headimg'];
            $exp = explode(',',M('room')->where("chuangjian_id=".$id)->getField('user_id'));
            $room[$k]['user_num'] = count($exp);
        }
        jsonpReturn('1','查询成功',$room);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤进入聊天室🐤🐤🐤🐤🐤🐤🐤🐤//
    public function go_room(){
        $uid = session('userid');
        $id = I('id');
        $user = M('user')->where('id='.$uid)->find();
        $room = M('room')->where('id='.$id)->find();
        $user_id = explode(',',$room['user_id']);
        $guanli_id = explode(',',$room['guanli_id']);
        if($room){
            if(!in_array($uid,$user_id) && !in_array($uid,$guanli_id) && $uid != $room['chuangjian_id']){
                jsonpReturn('2','您没有权限加入此聊天室','');
            }
            $user['room_name'] = $room['room_name'];
            $user['room_id'] = $room['id'];
            jsonpReturn('1','查询成功',$user);
        }else{
            jsonpReturn('2','该聊天室已解散','');
        }
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤创建聊天室🐤🐤🐤🐤🐤🐤🐤🐤//
    public function add_room(){
        $room_name = I('room_name');
        if(isset($room_name) && !empty($room_name)){
            $yz = M('room')->where("room_name='$room_name'")->find();
            if($yz){
                jsonpReturn('2','房间名已经存在','');
            }
            $yh = M('room')->where('chuangjian_id='.session('userid'))->find();
            if($yh){
                jsonpReturn('2','您已经建立过房间了，房间名为:'.$yh['room_name'],'');
            }
            $room['chuangjian_id'] = session('userid');
            if($room['chuangjian_id']){
                $room['room_name'] = $room_name;
                $room['create_time'] = time();
                $room['room_img'] = '../lf_img/lf_roomimg.png';
                $room['user_id'] = session('userid');
                M('room')->add($room);
                jsonpReturn('1','创建房间成功','');
            }else{
                jsonpReturn('2','登录信息已过期','');
            }
        }
        jsonpReturn('2','请填写房间名','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤更改聊天室🐤🐤🐤🐤🐤🐤🐤🐤//
    public function edit_room(){
        $room_name = I('room_name');
        $id = I('id');
        $uid = session('userid');
        if(!empty($room_name) && !empty($id)){
            $yz = M('room')->where("room_name='$room_name'")->find();
            if($yz){
                jsonpReturn('2','房间名已经存在','');
            }
            $yh = M('room')->where('id='.$id)->find();
            if($yh['chuangjian_id'] != $uid){
                jsonpReturn('2','你没有权限更改此房间信息','');
            }

            $room['room_name'] = $room_name;
            M('room')->where('id='.$id)->save($room);
            jsonpReturn('1','更改房间信息成功','');

        }
        jsonpReturn('2','非法操作','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤删除群🐤🐤🐤🐤🐤🐤🐤🐤//
    public function del_room(){
        $id = I('id');
        $uid = session('userid');

        $room_info = M('room')->where('id='.$id)->find();
        if($uid != $room_info['chuangjian_id']){
            jsonpReturn('2','你没有权限解散该房间','');
        }else{
            M('room')->delete($room_info['id']);
            M('chat')->where(['room'=>$room_info['id']])->delete();
            jsonpReturn('3','该房间已被解散','');
        }
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤管理群🐤🐤🐤🐤🐤🐤🐤🐤//
    public function manage_room(){
        $id = I('id');
        if(empty($id)){
            jsonpReturn('2','非法请求','');
        }else{
            $return['room_info'] = M('room')->where('id='.$id)->find();
            $users = $return['room_info']['user_id'];
            $return['user_info'] = M('user')->where("id in ($users)")->select();
            jsonpReturn('1','查询成功',$return);
        }
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤设置房间管理员页面🐤🐤🐤🐤🐤🐤🐤🐤//
    public function admin(){
        //需要  房间id:id
        $id = I('id');
        $uid = session('userid');

        $room_info = M('room')->where('id='.$id)->find();
        $guanli_id = $room_info['guanli_id'];
        $user_id = $room_info['user_id'];
        $guanli = explode(',',$room_info['guanli_id']);
        $return['level'] = 'admin';
        if($uid != $room_info['chuangjian_id']){
            $return['level'] = 'guanli';
            if(!in_array($uid,$guanli)){
                $return['level'] = 'user';
            }
        }
        //创建人
        $return['guanli'] = array();
        $return['user'] = array();
        $return['admin'] = M('user')->where('id='.$room_info['chuangjian_id'])->find();
        if($guanli_id){
            $return['guanli'] = M('user')->where("id in ($guanli_id)")->select();
        }
        if($user_id){
            $return['user'] = M('user')->where("id in ($user_id)")->select();
            foreach($return['user'] as $k=>$v){
                if(in_array($v['id'],$guanli)){
                    unset($return['user'][$k]);
                }
                if($v['id'] == $room_info['chuangjian_id']){
                    unset($return['user'][$k]);
                }
            }
        }
        // dump($return);
        //需要房间id :id
        //admin：房主信息     >>array
        //---房主id:id   ---房主姓名：name  ---房主头像：headimg
        //guanli:管理员信息       >>二维数组
        //---管理员id:id  ---管理员姓名：name  ---管理员头像：headimg
        //user:用户信息       >>二维数组
        //---用户id:id  ---用户姓名：name  ---用户头像：headimg
        //level:我的等级      >>字符串   user-普通用户   guanli-管理员  admin-房主
        jsonpReturn('1','查询成功',$return);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤设为房间管理员🐤🐤🐤🐤🐤🐤🐤🐤//
    public function add_admin(){
        $id = I('id');
        $uid = I('uid');    //传过来的用户
        $my_id = session('userid');

        $class = M('room')->where('id='.$id)->find();
        if($my_id != $class['chuangjian_id']){
            jsonpReturn('2','没有权限那么做','');
        }
        $update['guanli_id'] = $class['guanli_id'].','.$uid;
        $update['guanli_id'] = trim($update['guanli_id'],',');
        M('room')->where('id='.$id)->save($update);
        jsonpReturn('1','添加管理员成功','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤取消房间管理员🐤🐤🐤🐤🐤🐤🐤🐤//
    public function del_admin(){
        $id = I('id');
        $uid = I('uid');    //传过来的用户
        $my_id = session('userid');

        $class = M('room')->where('id='.$id)->find();
        if($my_id != $class['chuangjian_id']){
            jsonpReturn('2','没有权限那么做','');
        }
        $guanli_all = explode(',',$class['guanli_id']);
        foreach($guanli_all as $k=>$v){
            if($v == $uid){
                unset($guanli_all[$k]);
            }
        }
        $update['guanli_id'] = implode(',',$guanli_all);
        M('room')->where('id='.$id)->save($update);
        jsonpReturn('1','取消管理员成功','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤用户踢出房间🐤🐤🐤🐤🐤🐤🐤🐤//
    public function del_user(){
        $id = I('id');
        $uid = I('uid');    //传过来的用户
        $my_id = session('userid');

        $class = M('room')->where('id='.$id)->find();
        if($my_id != $class['chuangjian_id']){
            if(!strpos($class['guanli_id'],$id)){
                jsonpReturn('2','没有权限那么做','');
            }
            if(strpos($class['guanli_id'],$uid)){
                jsonpReturn('2','没有权限那么做','');
            }
        }
        $guanli_all = explode(',',$class['guanli_id']);
        if($guanli_all){
            foreach($guanli_all as $k=>$v){
                if($v == $uid){
                    unset($guanli_all[$k]);
                }
            }
        }

        $user_all = explode(',',$class['user_id']);
        if($user_all){
            foreach($user_all as $k=>$v){
                if($v == $uid){
                    unset($user_all[$k]);
                }
            }
        }
        //======================聊天室踢出=========================//
        $tichu_name = M('user')->where('id='.$uid)->find()['name'];
        $tichu_name = urlencode($tichu_name);
        $tichu_room = 'room_'.$id;
        file_get_contents('http://123.56.31.113:666/tirenle?name='.$tichu_name.'&room='.$tichu_room);
        //========================================================//

        $update['guanli_id'] = implode(',',$guanli_all);
        $update['user_id'] = implode(',',$user_all);
        M('room')->where('id='.$id)->save($update);
        jsonpReturn('1','踢出房间成功','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤房间邀请同学页面🐤🐤🐤🐤🐤🐤🐤🐤//
    public function add_room_user(){
        $id = I('id');            //传过来的房间id
        $my_id = session('userid');

        // $user_all = getLevelUser($my_id);   //所有的同学
        $user_all = M('user')->getField("id",true);

        $room = M('room')->where('id='.$id)->find();
        $guanli = explode(',',$room['guanli_id']);
        $user = explode(',',$room['user_id']);
        $return = array();
        foreach($user_all as $k=>$v){
            if(!in_array($v,$guanli) && !in_array($v,$user) && $v != $room['chuangjian_id']){
                $return['user'][] = M('user')->where('id='.$v)->find();
            }
        }

        // dump($return);exit;
        jsonpReturn('1','查询成功',$return);
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤进行邀请同学🐤🐤🐤🐤🐤🐤🐤🐤//
    public function do_add_room_user(){
        $id = I('id');      //房间id
        $uid = I('uid');    //传过来的用户id中间用逗号隔开  如 ,1,2,3
        $my_id = session('userid');

        $class = M('room')->where('id='.$id)->find();

        $user_all = $class['user_id'].$uid;
        $update['user_id'] = array_unique(explode(',',$user_all));  //删除重复
        $update['user_id'] = implode(',',$update['user_id']);
        M('room')->where('id='.$id)->save($update);
        jsonpReturn('1','邀请同学成功','');
    }
    //获取聊天室消息
    public function get_chat(){
        $room = I('room');
        $content = M('chat')->where('room='.$room)->order('id desc')->limit(500)->select();
        foreach($content as $k=>$v){
            $content[$k]['name'] = M('user')->where('id='.$v['uid'])->getField('name');
            $content[$k]['headimg'] = M('user')->where('id='.$v['uid'])->getField('headimg');
        }
        jsonpReturn('1','查询成功',$content);
    }

    //插入聊天室消息
    public function put_chat(){
        $insert['uid'] = session('userid');
        $insert['room'] = I('room');
        $insert['content'] = I('content');
        $insert['time'] = time();
        if(I('type')){
            $insert['type'] = I('type');
        }
        M('chat')->add($insert);
        jsonpReturn('1','插入消息成功','');
    }

    //🐤🐤🐤🐤🐤🐤🐤🐤获取用户信息🐤🐤🐤🐤🐤🐤🐤🐤//
    public function my_info(){
        $uid = session('userid');
        $user_info = M('user')->where('id='.$uid)->find();
        jsonpReturn('1','查询成功',$user_info);
    }
    public function speecho(){
        $user = user_info($_SESSION['userid']);
        if($user['grade']==1){
           jsonpReturn('3','您的权限不足,请去升级！','http://hr2.hongrunet.com/html/jyt_riseFen.html'); 
        }
        if(!$user ){
            jsonpReturn('5','您还没有登录','http://hr2.hongrunet.com/html/jyt_login.html');
        }
       if($user['grade']==1 && I('fid')>1){
            jsonpReturn('4','您的权限不足，只能上传免费视频','http://hr2.hongrunet.com/html/lf_personal.html');
        }
        if($user['grade']==2 && I('fid')>2){
            jsonpReturn('4','您的权限不足，只能上传免费视频和学霸专享视频','http://hr2.hongrunet.com/html/lf_personal.html');
        }
    }
    #上传视频提交
    public function speech(){
        $user = user_info($_SESSION['userid']);
        if($user['grade']==1){
           jsonpReturn('3','您的权限不足,请去升级！','http://hr2.hongrunet.com/html/jyt_riseFen.html'); 
        }

        if(!$user ){
            jsonpReturn('5','您还没有登录','http://hr2.hongrunet.com/html/jyt_login.html');
        }
       if($user['grade']==1 && I('fid')>1){
            jsonpReturn('4','您的权限不足，只能上传免费视频','http://hr2.hongrunet.com/html/lf_personal.html');
        }
        if($user['grade']==2 && I('fid')>2){
            jsonpReturn('4','您的权限不足，只能上传免费视频和学霸专享视频','http://hr2.hongrunet.com/html/lf_personal.html');
        }
        $where['title']      = I('videoname');
        if($xy = M('course')->where($where)->find()){
            jsonpReturn('0','这个标题已存在，请更换');
        }
        $course  = M('course');
        $data['uid']        = $_SESSION['userid'];
        $data['author']     = M('user')->where('id='.$_SESSION['userid'])->find()['name'];
        $data['createtime'] = time();
        $data['phone']      = I('phone');
        $data['classid']    = I('classid');
        $data['level']      = I('fid');
        $data['checkbox']   = I('checkboxFiveInput');
        $data['video']      = $video =I('video');
        $data['title']      = I('videoname');    
        $data['content']    = I('videointro');
        $data['thumbnail']  = I('thumbnail');
        $qiniu = new Upload();
        $data['keyId']      = $qiniu->fopss(I("keyss"))['data'];
        // dump($data['keyId']);exit;
        $result = $course->add($data);
        if ($result > 0) { 
            jsonpReturn('1','提交成功','');
        }else{
            jsonpReturn('0','提交失败','');
        }   
    }
/*    public function testNotify(){
        $qiniu = new Upload();
        $id = "z2.595dd4f4e3d0041bf80cf5be";
        $res = $qiniu->seekStaus($id);
        dump($res['data']['items'][0]['key']);

    }*/
    public function notify(){
        die('1');
        $fops = json_decode($_POST,true);
        $course  = M('course');
        $data = $course->where(['isdel'=>0,'keyid'=>$fops['id']])->find();
        if($data){
            if(empty($data['videofops'])){
                $course->where('keyid='.$fops['id'])->update(['videofops'=>"http://hr3.hongrunet.com/".$fops['items'][0]['key']]);
            }
        }
    }
}

//查询用户信息   参数 ：用户id
function user_info($uid){
    $u = M('user');
    $user = $u->where('id='.$uid)->find();
    return $user;
}
