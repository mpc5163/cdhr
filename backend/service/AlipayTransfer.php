<?php
namespace Service;
/**
 * 支付转账操作
 * Created by PhpStorm.
 * User: helei
 * Date: 2017/4/30
 * Time: 下午5:57
 */
use Payment\Common\PayException;
use Payment\Client\Transfer;
use Payment\Config;
/**
* 
*/
class AlipayTransfer
{
	 	
	public static function querys($data){
	$aliConfig = C('Alipay');
	$default = [
	    'trans_no' => time(),
	    'payee_type' => 'ALIPAY_LOGONID',
	    'payee_account' => '15517583769',
	    'amount' => '0.01',
	    'remark' => '转账拉，有钱了',
	    'payee_real_name' => '马鹏程',
		];
	# 合并配置
	$data = array_merge($default,$data);
		try {
		    $ret = Transfer::run(Config::ALI_TRANSFER, $aliConfig, $data);
		} catch (PayException $e) {
		    return $e->errorMessage();
		    
		}

		 $res = json_encode($ret, JSON_UNESCAPED_UNICODE);
		return json_decode($res);
	}
}



