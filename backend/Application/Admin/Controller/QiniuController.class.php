<?php
namespace Admin\Controller;

use Think\Controller;
use Service\Upload;
class QiniuController extends Controller{
	public function index(){
        if($_FILES['fmvid']['size'] >= 800*1024*1024){
        	$this -> ajaxReturn(['status'=>0,'url'=>"你上传的文件过大"]);
        }else{
	        $upload =new \Service\Upload();
	        $res = $upload ->upload_one($_FILES['fmvid']);
	        $this -> ajaxReturn(['status'=>1,'url'=>$res]);
        }
	}
	public function token(){
		$upload =new \Service\Upload();
		$this -> ajaxReturn(['data'=>$upload->getToken()]);
	}
	public function tokens(){
		$upload =new \Service\Upload();
		jsonpReturn1('1','',$upload->getToken());
	}

}