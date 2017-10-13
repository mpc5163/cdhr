<?php

/**
 * 生成订单二维码
 * @param int $id 订单id
 * @param $level 容错等级
 * @param $size 图片大小
 * @return 
 */

	function qrcode($id,$level=3,$size=4){
			Vendor('phpqrcode.phpqrcode');
			$url = "./Public/code/$id.png";
       $errorCorrectionLevel =intval($level) ;//容错级别 
       $matrixPointSize = intval($size);//生成图片大小 
       //生成二维码图片 
       $object = new \QRcode();
       $img = $object->png($id,$url, $errorCorrectionLevel, $matrixPointSize, 2,false);
	}

	/**
 * Jsonp方式返回数据到客户端
 * @param mixed $data 要返回的数据
 * @author jcl
 * @return array
 */
function jsonpReturn($status='',$msg='',$data=array()) {
	$data = array("status"=>$status,"msg"=>$msg,"data"=>$data);
    if(empty($type)) $type  =   'jsonp';
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler.'('.json_encode($data,$json_option).');');
}

      /**
 * Jsonp方式返回数据到客户端
 * @param mixed $data 要返回的数据
 * @author jcl
 * @return array
 */
function jsonpReturn1($status='',$msg='',$data=array()) {
      $data = array("status"=>$status,"msg"=>$msg,"data"=>$data);
    if(empty($type)) $type  =   'jsonp';
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler.'('.json_encode($data,$json_option).');');
}

      /**
 * Jsonp方式返回数据到客户端
 * @param mixed $data 要返回的数据
 * @author jcl
 * @return array
 */
function jsonpReturn2($status='',$msg='',$data=array()) {
      $data = array("status"=>$status,"msg"=>$msg,"data"=>$data);
    if(empty($type)) $type  =   'jsonp';
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler  =   isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler.'('.json_encode($data,$json_option).');');
}