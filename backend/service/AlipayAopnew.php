<?php
namespace Service;
use Service\AopClientnew;
use Service\AlipayFundTransToaccountTransferRequest;
class AlipayAopnew{
  private $appId; 
  private $rsaPrivateKey; 
  private $alipayrsaPublicKey; 
  function __construct(){ 
      $this->appId = '2017051607257320';
	  $this->rsaPrivateKey = 'MIIEpQIBAAKCAQEA9/oU227GWG032U489P+w3j/uDpotlONxEcmWdnXxqF55teh9HTsBpGwxSLUQPpoJVGK4NDbv8lRcTaYUpdOAVOBQkWFYV5EP9Wx+d4Njxby4VEsrL7nA4AtRZQCSl2RZolqW9XSXIDUbRIwTX52B0lL7naFApVMTrxAg6SVee3ipySO2jjiuOfXFrIrq7kbMoX2KEEyEXNnV+1cvshVltNTDjk7LmPqkOOVelaUvEiUZKWu/s55aTHMDKQc62zyIxp+YhxEDN9MQ3IE1u1lsjkvGFRJrBK5GJ0JeaGRJ0jJk0n+jsQRZSIlahEyfeKu+TLYhbrOaZIon770RLKfdjQIDAQABAoIBAQDzzqKBGInU2RVHB2BxdY+9BFgXbUkRkQlTdsMDl3TQmIrL6lhQy/oSYfmWxcbB9grkN+dqFvWVaZITGg5E+Ay95onwVihYeBybrV8uPDV1bdZBsmI0m0piPV56krSOOrDSKoUvscEAfpZfr+VAZgkE9GfFFI0Nvv+kaJQjBoW9l/bMblRRjqVEMLemVvrdUrUDG/q76vaZub/7gHY22e9uxMBBtsbXnLWdDQSXo3WLPDaYKXOG8DK7NAXxWVzK3eu5mhVDbVFJrkRrrHQoXL3zt+26eYkRPQ+jxh/KbX9PoRgGrojLq54JU31Fu1kuxO7lwlCuiGabs+eTVUW0VcEBAoGBAPwaRfshjSuoGA2JkQEVnO5La83XrCXT5GQ+MrbtkNiJOWPMzkftP1ePp7GZ6QnJ1/mshqMajHh0VnZJAt1HPrf48nIePum/Uz1bDbYr6uVzv7poZyODNhrxpvKiI/DC4OvMuDsRaBArqQNpCIUtVto7tDjMGHQlp3NMnPUML25BAoGBAPvPet58k/Z5gv4Z0eeSlOPGpeuw4uIUAh2Gte2gy8UuzOzWzhnMAIk035KftlMQgWZ95Fqmjiek55DawnLxpup5j5ReN+uqgVXlSgFGfe4HwFQVr6vaQ+6sa6W/BflENIXcNxd4YipyChUH3g18H55JNGNLSYcRMKqm8a9tXrRNAoGBAPXpywnFGKfVOGE8J64fHw1zlJ0eHFZp8we1hFLcOcZ/VPP8+9s9meQXxUSBFcjukzaBDHRdmViGYzDO13Rx4sQimpym1Ee+0buv7hgdjuxzlDqJTNJfU4/E2U0kzFo2PqDE+ZDk0dW0QRGKiz0dlPMb+hjNPa1ObaESwqhmUn4BAoGBAPo+KcP7F+wku6ocLL6urCpkWrWMdxznd2ayXhAF8RtHND3WBsIaOxofomOLNtz7lS0uxQXVaYY6WipZTE6AmjmAjtCKhZY9PwUGtXHpBPLNXzPwzUDCyyngzaXM3Xnnby7sNw+rAhtec2iNBf5usNlTtK1Mv4hr6+pbt+l3jlstAoGAfAWSaiOEYaHfynaIxTJ2W0OeOTAvh3bNxcK2ZACSRz20uXkP6YN52JyyVl2/9b0rod2VRnLDm6PXh1rVQaq3ONpM8BMwBuBW8Qte7FQ0UuM5YlasKrxJOvTsn0Jq1rMUUiJZ7UdOPyC99gR+bMTKdoxcwniZLq7dfIoutXHS4S0='; 

	  $this->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB'; 
  }

  /*
  *该方法为支付宝转账方法
  *osn 交易单号
  *payee_account 收款人帐号
  *amount 转账金额
  *payee_real_name 收款方真实姓名
  */
  public function AlipayFundTransToaccountTransfer($osn,$payee_account,$amount,$payee_real_name){
	    $msg = array();
	    if(!$osn){
			$msg['success'] = 2;
			$msg['text']    = '交易单号为空';
			return $msg;
		}elseif(!$payee_account){
			$msg['success'] = 2;
			$msg['text']    = '收款人帐号为空';
			return $msg;
		}elseif(!$amount || $amount<0.1){
			$msg['success'] = 2;
			$msg['text']    = '转账金额不能小于0.1';
			return $msg;
		}elseif(!$payee_real_name){
			$msg['success'] = 2;
			$msg['text']    = '收款人姓名为空';
			return $msg;
		}else{
			$aop = new AopClientnew();
			$aop->gatewayUrl         = 'https://openapi.alipay.com/gateway.do';
			$aop->appId              = $this->appId;
			$aop->rsaPrivateKey      = $this->rsaPrivateKey;
			$aop->alipayrsaPublicKey = $this->alipayrsaPublicKey;
			$aop->apiVersion         = '1.0';
			$aop->signType           = 'RSA';
			$aop->postCharset        = 'UTF-8';
			$aop->format             = 'json';
			$request = new AlipayFundTransToaccountTransferRequest();
			$request->setBizContent("{" .
			"    \"out_biz_no\":\"".$osn."\"," .
			"    \"payee_type\":\"ALIPAY_LOGONID\"," .
			"    \"payee_account\":\"".$payee_account."\"," .
			"    \"amount\":\"".$amount."\"," .
			"    \"payer_real_name\":\"承德鸿儒网络科技有限公司\"," .
			"    \"payer_show_name\":\"chengdehongru@163.com\"," .
			"    \"payee_real_name\":\"".$payee_real_name."\"," .
			"    \"remark\":\"转账\"," .
			"    \"ext_param\":\"{\\\"order_title\\\":\\\"用户提现\\\"}\"" .
			"  }");
			$result = $aop->execute($request); 
			$resultCode = $result->alipay_fund_trans_toaccount_transfer_response->code;
			@$resultMsg  = $result->alipay_fund_trans_toaccount_transfer_response->sub_msg;
		    if(!empty($resultCode)&&$resultCode == 10000){
				$msg['success'] = 1;
				$msg['text']    = '支付宝转账成功';
				return $msg;
			}else{
				$msg['success'] = 2;
				$msg['text']    = $resultMsg;
				$msg['code']    = $resultCode;
				return $msg;
			}
		}
  }
}
?>