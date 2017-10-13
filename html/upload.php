<?php
$file = $_FILES['picname'];//得到传输的数据
//得到文件名称
$name = $file['name'];
$type = strtolower(substr($name,strrpos($name,'.')+1)); //得到文件类型，并且都转化成小写
$allow_type = array('jpg','jpeg','gif','png'); //定义允许上传的类型
//判断文件类型是否被允许上传
if(!in_array($type, $allow_type)){
  //如果不被允许，则直接停止程序运行
  exit(json_encode(['status'=>0,'message'=>'类型错误'])) ;
}
//判断是否是通过HTTP POST上传的
if(!is_uploaded_file($file['tmp_name'])){
  //如果不是通过HTTP POST上传的
  exit(json_encode(['status'=>0,'message'=>'非法请求'])) ;
}
$upload_path = "./headimg/"; //上传文件的存放路径
# 随机生成文件名
do{
	$str = '';
	$str .= date('YmdHisyju');
	for ($i=0; $i < 5; $i++) {
		$str .= rand(0,50);
	}
} while(file_exists($upload_path.$str.'.'.$type));


//开始移动文件到相应的文件夹
if(move_uploaded_file($file['tmp_name'],$upload_path.$str.'.'.$type)){
  exit(json_encode(['status'=>1,'message'=>'上传成功','url'=>'http://'.$_SERVER['HTTP_HOST'].'/headimg/'.$str.'.'.$type]));
}else{
  exit(json_encode(['status'=>0,'message'=>'上传失败'])) ;
}