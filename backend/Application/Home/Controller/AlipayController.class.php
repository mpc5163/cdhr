<?php
namespace Home\Controller;

use Think\Controller;


class AlipayController extends Controller{
    //跳转定向
    public function redir(){
        if($_SESSION['userid']){
            $id = $_SESSION['userid'];
            $order = M('order')->where('uid=%d',$id)->order('id DESC')->find();
            if($order['type'] == '1'){
                $letvid = M('letv')->where('title="'.$order['remark'].'"')->find()['id'];
                header("location:http://hr2.hongrunet.com/html/lf_selectPort.html?id=$letvid");exit();
            }else if($order['type'] == '7'){
                header("location:http://hr2.hongrunet.com/html/lf_liveDetail.html?id=".$order['remark']);exit();
            }else if($order['type'] == '8'){
                $data['source'] = $order['uid'];
                $data['uid']    = $order['remark'];
                $data['message']= "收到红包";
                $data['createtime']=time();
                $data['money'] =$order['money'];
                M('account')->add($data);
                header("location:http://hr2.hongrunet.com/html/lf_liveDetail.html?id=".$order['message'].'#'.$order['id']);exit();
                
            }else if($order['type'] == '6'){
                //$xx = explode('&', $order['remark'])[1];
                $data['source'] = $order['uid'];
                $data['uid']    = M('course')->where('id=%d',$order['remark'])->getField('uid');
                $data['message']= "收到红包";
                $data['createtime']=time();
                $data['money'] =$order['money'];
                M('account')->add($data);
                header("location:http://hr2.hongrunet.com/html/lf_videoDetail.html?id=".$order['remark']);exit();
                //M('account')->add($data);
            }else{
                header('location:http://hr2.hongrunet.com/html/lf_personal.html');exit();
            }
        }
        if($_GET){
            $end = time();
            $start = (int)time()-200;
            $data = M('order')->where(['createtime'=>['between',[$start,$end]],'xs'=>3])->select();
            foreach ($data as $key => $value) {
                if($value['type'] == '8'){
                    $dat['source'] = $value['uid'];
                    $dat['uid']    = $value['remark'];
                    $dat['message']= "收到红包";
                    $dat['createtime']=time();
                    $dat['money'] =$value['money'];
                    M('account')->add($dat);       
                }else if($value['type'] == '6'){
                    $dat['source'] = $value['uid'];
                    $dat['uid']    = M('course')->where('id=%d',$value['remark'])->getField('uid');
                    $dat['message']= "收到红包";
                    $dat['createtime']=time();
                    $dat['money'] =$value['money'];
                    M('account')->add($dat);
                } 
                $res = M('order')->where('id='.$value['id'])->save(['xs'=>4]);
            }

            header('location:http://hr2.hongrunet.com/html/jyt_zhifusuc.html');exit();
        }
    }
   
}