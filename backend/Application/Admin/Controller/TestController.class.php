<?php
namespace Admin\Controller;
use Think\Controller;
use Service\Alipay;
use Service\AopClient;
use Payment\NotifyContext;

class TestController extends Controller{
	public function index(){
		$data['body'] = '测试商品';
		$data['subject'] = '订单名称';
		$data['order_no'] = '12345453'.time();
		$data['timeout_express'] = 1800;
		$data['amount'] = 0.01;
		$res = Alipay::create($data,'ali_wap');
		header('Location:'.$res);
		// header('location:'.$res);
	}
	# 支付宝回调页面
	public function callback(){
		$result = new NotifyContext;
		$data = ['app_id'=>Alipays::$app_id,'notify_url'=>Alipays::$notify_url,'return_url'=>Alipays::$return_url,'sign_type'=>Alipays::$sign_type,'ali_public_key'=>Alipays::$ali_public_key,'rsa_private_key'=>Alipays::$rsa_private_key];
		# 校验信息
		$result -> initNotify('ali_charge',$data);
		# 接受返回信息
		$information = $result -> getNotifyData();
	// 	if($information['trade_status']=='TRADE_SUCCESS'){
	// 		# 修改订单状态
	// 		if($order = M('orders') -> where(['pay_num'=>$information['out_trade_no']])-> first()){
	// 			M('orders') -> where(['pay_num'=>$information['out_trade_no']]) -> update(['status'=>1]);
	// 			# 查询个人信息
	// 			if($user = M('users') -> where(['id'=>$order['uid']])-> first()){
	// 				$user = $user -> toArray();
 //          # 如果不是首次消费修改用户状态
 //          if($user['is_pay'] != 1){
 //            M('users') -> where(['id'=>$user['id']]) -> update(['is_pay'=>1]);
 //          }
	// 				Goods::cms($user['id'],$order['money']);
	// 			}
	// 		}
	// 		if($order = M('zige') -> where(['pay_num'=>$information['out_trade_no']])-> first()){
	// 			M('zige') -> where(['pay_num'=>$information['out_trade_no']]) -> update(['status'=>1]);
	// 			# 查询个人信息
	// 			if($user = M('users') -> where(['id'=>$order['uid']])-> first()){
	// 				$user = $user -> toArray();
 //          # 如果不是首次消费修改用户状态
 //          if($user['is_pay'] != 1){
 //            M('users') -> where(['id'=>$user['id']]) -> update(['is_pay'=>1]);
 //          }
	// 				Goods::cms($user['id'],$order['money']);
	// 			}
	// 		}
	// 	}
	 }

}