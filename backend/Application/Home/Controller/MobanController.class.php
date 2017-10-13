<?php
namespace Home\Controller;
use Think\Controller;
use Service\Alipay as Alipays;
use Service\AlipayTransfer;
use Service\Wechat;

use Thenbsp\Wechat\Payment\Notify;
use Thenbsp\Wechat\Message\Template\Template;
use Thenbsp\Wechat\Message\Template\Sender;
use Thenbsp\Wechat\Wechat\AccessToken;
 
    #查询用户信息   参数 ：用户id
    function user_info($uid){
        $u = M('user');
        $user = $u->where('id='.$uid)->find();
        return $user;
    }
class MobanController extends Controller {
  
  #聊天室筛选
    public function sxName(){
      $id = I('id');            //传过来的房间id
      $my_id = session('userid');
      $name = I('name');
      #筛选用户id
      $sx = M('user')->where(['name'=>['LIKE',"%$name%"]])->getField("id",true);
      #用户所有ID
       // $user_all = M('user')->getField("id",true);
      #房间信息
      $room = M('room')->where('id='.$id)->find();
      
      #管理员
      $guanli = explode(',',$room['guanli_id']);
      #用户
      $user = explode(',',$room['user_id']);
      $return = array();
      foreach($sx as $k=>$v){
          if(!in_array($v,$guanli) && !in_array($v,$user) && $v != $room['chuangjian_id']){
              $return['user'][] = M('user')->where('id='.$v)->find();
          }
      }
      jsonpReturn('1','查询成功',$return);
    }
	#上传视频通知消息
      static  public function sendSMS($uid,$vid){
        # 实例化accesstoken类
        $accessToken = new AccessToken(C('wechat')['appid'],C('wechat')['secret']);
        # 定义一个模板
        # 模板参数，每一个模板 ID 都对应一组参数
        $template1 = new Template('qDcUPSXVxruvEK2Vt-XgnV_ZwGCZ9F0VUZZuNcESS20');
        $template2 = new Template('qDcUPSXVxruvEK2Vt-XgnV_ZwGCZ9F0VUZZuNcESS20');
        #上传者信息

        $user = user_info($uid);

        #视频信息
        $video = M('course')->where('id='.$vid)->find();
        #粉丝信息
        $fans = M('fans')->where('uid='.$uid)->getField('gzh',true);
        #实例化
        $sender = new Sender($accessToken);
        #发给上传者
        if($user && $user['openid']){
        $template1
          ->add('first',     '尊敬的'.$user['name'].'同学，你的课程《'.$video['title'].'》已经上传成功。')#
          ->add('keyword1',  $user['name'])#上传者
          ->add('keyword2',  '《'.$video['title'].'》')#视频名称
          ->add('keyword3',  date('Y-m-d H:i',$video['createtime']))#上传时间
          ->add('remark',    '点击查看详情', '#ff0000');#备注

        # 跳转链接
        $template1->setUrl('http://hr2.hongrunet.com/html/lf_videoDetail.html?id='.$vid);
        # 发给谁
        
            $template1->setOpenid($user['openid']);
            # 发送模板消息
            $msgid = $sender->send($template1); 
            /*try {
                $msgid = $sender->send($template1);
            } catch (\Exception $e) {
                exit($e->getMessage());
            }*/
       
        }

        
        #发给粉丝
        foreach ($fans as $k => $v) {
            $uu = user_info($v);
            if($uu && $uu['openid']){
            $template2
              ->add('first',     '亲爱的'.$uu['name'].'同学，你关注的'.$user['name'].'上传了新的课程，赶快去围观吧！')#
              ->add('keyword1',  $user['name'])#上传者
              ->add('keyword2',  '《'.$video['title'].'》')#视频名称
              ->add('keyword3',  date('Y-m-d H:i',$video['createtime']))#上传时间
              ->add('remark',    '点击查看详情', '#ff0000');#备注

            # 跳转链接
            $template2->setUrl('http://hr2.hongrunet.com/html/lf_videoDetail.html?id='.$vid);
            # 发给谁
            
               $template2->setOpenid($uu['openid']);
                # 发送模板消息
                $msgid = $sender->send($template2); 
            }
            
        }
       
      
    }
public function sendSMS1(){
        $uid = 1500;
        $vid = 460;
        # 实例化accesstoken类
        $accessToken = new AccessToken(C('wechat')['appid'],C('wechat')['secret']);
        # 定义一个模板
        # 模板参数，每一个模板 ID 都对应一组参数
        $template1 = new Template('qDcUPSXVxruvEK2Vt-XgnV_ZwGCZ9F0VUZZuNcESS20');
        $template2 = new Template('qDcUPSXVxruvEK2Vt-XgnV_ZwGCZ9F0VUZZuNcESS20');
        #上传者信息
        $user = user_info($uid);
        #视频信息
        $video = M('course')->where('id='.$vid)->find();
        #粉丝信息
        $fans = M('fans')->where('uid='.$uid)->getField('gzh',true);
        dump($fans);
        #实例化
        $sender = new Sender($accessToken);
        #发给上传者
        $template1
          ->add('first',     '尊敬的'.$user['name'].'用户，你的课程《'.$video['title'].'》已经上传成功。')#
          ->add('keyword1',  '上传者：'.$user['name'])#上传者
          ->add('keyword2',  '课程名称：《'.$video['title'].'》')#视频名称
          ->add('keyword3',  date('Y-m-d H:i',$video['createtime']))#上传时间
          ->add('remark',    '点击查看详情', '#ff0000');#备注

        # 跳转链接
        $template1->setUrl('http://hr2.hongrunet.com/html/lf_videoDetail.html?id='.$vid);
        # 发给谁
        if($user['openid']){
            $template1->setOpenid($user['openid']);
            # 发送模板消息
            $msgid = $sender->send($template1); 
            /*try {
                $msgid = $sender->send($template1);
            } catch (\Exception $e) {
                exit($e->getMessage());
            }*/
       
        }
        // exit;
        #发给粉丝
        foreach ($fans as $k => $v) {
            $uu = user_info($v);
            $template2
              ->add('first',     '亲爱的'.$uu['name'].'会员，你关注的'.$user['name'].'上传了新的课程，赶快去围观吧！')#
              ->add('keyword1',  '上传者：'.$user['name'])#上传者
              ->add('keyword2',  '课程名称：《'.$video['title'].'》')#视频名称
              ->add('keyword3',  date('Y-m-d H:i',$video['createtime']))#上传时间
              ->add('remark',    '点击查看详情', '#ff0000');#备注

            # 跳转链接
            $template2->setUrl('http://hr2.hongrunet.com/html/lf_videoDetail.html?id='.$vid);
            # 发给谁
            if($uu['openid']){
               $template2->setOpenid($uu['openid']);
                # 发送模板消息
                $msgid = $sender->send($template2); 
            }
            
        }
      
    }

}