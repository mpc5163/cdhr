<?php
require './Qiniu/autoload.php';
// // exit("2");
// // 引入鉴权类
use Qiniu\Auth;

// // // 引入上传类
use Qiniu\Storage\UploadManager;
$bucket = "cdhr2017";
$filePath = $_FILES['file']['tmp_name'];
$accessKey = 'O2ssmldXdoJOcPmOqS9xIQ1z7VA0KyMV5R7UD0jA';
$secretKey = '-giI8kvxNF03wLdBQudgajK9h0Ypkj6luHSPnbX3';
//var_dump($_FILES);exit;
$auth = new Auth($accessKey, $secretKey);
if($_FILES['file']['size']>= 300*1024*1024*1024){
	echo json_encode(['status'=>0,'message'=>'您上传的文件过大，请上传小于300M的视频','url'=>""]);exit;
}
// 上传文件到七牛后， 七牛将文件名和文件大小回调给业务服务器.
$uptoken 	= $auth->uploadToken($bucket, null, 3600);
$data[] = $uptoken;
$data[] = "http://hr2.hongrunet.com".$filePath;
//echo json_encode(['status'=>1,'message'=>'视频缓存成功，可上传','url'=>$data]);


$uploadMgr  = new UploadManager();
$uploadMgr->putFile($uptoken, null, $filePath);

$ret = $uploadMgr->putFile($uptoken, null, $filePath);
echo json_encode(['status'=>1,'message'=>'视频缓存成功，可上传','url'=>"http://opzthvc7x.bkt.clouddn.com/".$ret[0]['key']]);
