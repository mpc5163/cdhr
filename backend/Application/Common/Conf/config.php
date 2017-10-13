<?php
return array(
	//'配置项'=>'配置值'
	'DEFAULT_MODULE'   =>  'Admin',    // 默认模快
    'DEFAULT_CONTROLLER'   =>   'Login',
    'DEFAULT_ACTION'       =>   'login',
	/* 数据库设置 */
    'DB_TYPE'          =>  'mysql',      // 数据库类型
    'DB_HOST'          =>  'localhost',  // 服务器地址
    'DB_NAME'          =>  'api_hongrunet',      // 数据库名
    'DB_USER'          =>  'api_hongrunet',       // 用户名
    'DB_PWD'           =>  'api_hongrunet2017',       // 密码
    'DB_PORT'          =>  '3306',       // 端口
    'DB_PREFIX'        =>  'hr_',        // 数据库表前缀
    'SHOW_PAGE_TRACE'  =>  false,        //开启ThinkPHP页面调试
   'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
   //支付宝支付配置
    "Alipay"           =>   [ # appid
         'app_id'=>'2017051607257320',
        # 商户私钥，您的原始格式RSA私钥
        'merchant_private_key'=>'MIIEpQIBAAKCAQEA9/oU227GWG032U489P+w3j/uDpotlONxEcmWdnXxqF55teh9HTsBpGwxSLUQPpoJVGK4NDbv8lRcTaYUpdOAVOBQkWFYV5EP9Wx+d4Njxby4VEsrL7nA4AtRZQCSl2RZolqW9XSXIDUbRIwTX52B0lL7naFApVMTrxAg6SVee3ipySO2jjiuOfXFrIrq7kbMoX2KEEyEXNnV+1cvshVltNTDjk7LmPqkOOVelaUvEiUZKWu/s55aTHMDKQc62zyIxp+YhxEDN9MQ3IE1u1lsjkvGFRJrBK5GJ0JeaGRJ0jJk0n+jsQRZSIlahEyfeKu+TLYhbrOaZIon770RLKfdjQIDAQABAoIBAQDzzqKBGInU2RVHB2BxdY+9BFgXbUkRkQlTdsMDl3TQmIrL6lhQy/oSYfmWxcbB9grkN+dqFvWVaZITGg5E+Ay95onwVihYeBybrV8uPDV1bdZBsmI0m0piPV56krSOOrDSKoUvscEAfpZfr+VAZgkE9GfFFI0Nvv+kaJQjBoW9l/bMblRRjqVEMLemVvrdUrUDG/q76vaZub/7gHY22e9uxMBBtsbXnLWdDQSXo3WLPDaYKXOG8DK7NAXxWVzK3eu5mhVDbVFJrkRrrHQoXL3zt+26eYkRPQ+jxh/KbX9PoRgGrojLq54JU31Fu1kuxO7lwlCuiGabs+eTVUW0VcEBAoGBAPwaRfshjSuoGA2JkQEVnO5La83XrCXT5GQ+MrbtkNiJOWPMzkftP1ePp7GZ6QnJ1/mshqMajHh0VnZJAt1HPrf48nIePum/Uz1bDbYr6uVzv7poZyODNhrxpvKiI/DC4OvMuDsRaBArqQNpCIUtVto7tDjMGHQlp3NMnPUML25BAoGBAPvPet58k/Z5gv4Z0eeSlOPGpeuw4uIUAh2Gte2gy8UuzOzWzhnMAIk035KftlMQgWZ95Fqmjiek55DawnLxpup5j5ReN+uqgVXlSgFGfe4HwFQVr6vaQ+6sa6W/BflENIXcNxd4YipyChUH3g18H55JNGNLSYcRMKqm8a9tXrRNAoGBAPXpywnFGKfVOGE8J64fHw1zlJ0eHFZp8we1hFLcOcZ/VPP8+9s9meQXxUSBFcjukzaBDHRdmViGYzDO13Rx4sQimpym1Ee+0buv7hgdjuxzlDqJTNJfU4/E2U0kzFo2PqDE+ZDk0dW0QRGKiz0dlPMb+hjNPa1ObaESwqhmUn4BAoGBAPo+KcP7F+wku6ocLL6urCpkWrWMdxznd2ayXhAF8RtHND3WBsIaOxofomOLNtz7lS0uxQXVaYY6WipZTE6AmjmAjtCKhZY9PwUGtXHpBPLNXzPwzUDCyyngzaXM3Xnnby7sNw+rAhtec2iNBf5usNlTtK1Mv4hr6+pbt+l3jlstAoGAfAWSaiOEYaHfynaIxTJ2W0OeOTAvh3bNxcK2ZACSRz20uXkP6YN52JyyVl2/9b0rod2VRnLDm6PXh1rVQaq3ONpM8BMwBuBW8Qte7FQ0UuM5YlasKrxJOvTsn0Jq1rMUUiJZ7UdOPyC99gR+bMTKdoxcwniZLq7dfIoutXHS4S0=',
        # 异步通知地址
        'notify_url'=>'http://hr.hongrunet.com/Home/UserCenter/alipayCallBack',
        # 同步跳转
        'return_url' => "http://hr.hongrunet.com/Home/Alipay/redir",
        # 编码格式
        'charset'=>'UTF-8',
        # 签名方式
        'sign_type' => 'RSA',
        # 支付宝网关
        'gatewayUrl'=>"https://openapi.alipay.com/gateway.do",
        # 支付宝私钥文件
        // 'rsa_private_key' => ROOT_PATH.'\private\siyao.txt',
        'rsa_private_key' => "MIIEpQIBAAKCAQEA9/oU227GWG032U489P+w3j/uDpotlONxEcmWdnXxqF55teh9HTsBpGwxSLUQPpoJVGK4NDbv8lRcTaYUpdOAVOBQkWFYV5EP9Wx+d4Njxby4VEsrL7nA4AtRZQCSl2RZolqW9XSXIDUbRIwTX52B0lL7naFApVMTrxAg6SVee3ipySO2jjiuOfXFrIrq7kbMoX2KEEyEXNnV+1cvshVltNTDjk7LmPqkOOVelaUvEiUZKWu/s55aTHMDKQc62zyIxp+YhxEDN9MQ3IE1u1lsjkvGFRJrBK5GJ0JeaGRJ0jJk0n+jsQRZSIlahEyfeKu+TLYhbrOaZIon770RLKfdjQIDAQABAoIBAQDzzqKBGInU2RVHB2BxdY+9BFgXbUkRkQlTdsMDl3TQmIrL6lhQy/oSYfmWxcbB9grkN+dqFvWVaZITGg5E+Ay95onwVihYeBybrV8uPDV1bdZBsmI0m0piPV56krSOOrDSKoUvscEAfpZfr+VAZgkE9GfFFI0Nvv+kaJQjBoW9l/bMblRRjqVEMLemVvrdUrUDG/q76vaZub/7gHY22e9uxMBBtsbXnLWdDQSXo3WLPDaYKXOG8DK7NAXxWVzK3eu5mhVDbVFJrkRrrHQoXL3zt+26eYkRPQ+jxh/KbX9PoRgGrojLq54JU31Fu1kuxO7lwlCuiGabs+eTVUW0VcEBAoGBAPwaRfshjSuoGA2JkQEVnO5La83XrCXT5GQ+MrbtkNiJOWPMzkftP1ePp7GZ6QnJ1/mshqMajHh0VnZJAt1HPrf48nIePum/Uz1bDbYr6uVzv7poZyODNhrxpvKiI/DC4OvMuDsRaBArqQNpCIUtVto7tDjMGHQlp3NMnPUML25BAoGBAPvPet58k/Z5gv4Z0eeSlOPGpeuw4uIUAh2Gte2gy8UuzOzWzhnMAIk035KftlMQgWZ95Fqmjiek55DawnLxpup5j5ReN+uqgVXlSgFGfe4HwFQVr6vaQ+6sa6W/BflENIXcNxd4YipyChUH3g18H55JNGNLSYcRMKqm8a9tXrRNAoGBAPXpywnFGKfVOGE8J64fHw1zlJ0eHFZp8we1hFLcOcZ/VPP8+9s9meQXxUSBFcjukzaBDHRdmViGYzDO13Rx4sQimpym1Ee+0buv7hgdjuxzlDqJTNJfU4/E2U0kzFo2PqDE+ZDk0dW0QRGKiz0dlPMb+hjNPa1ObaESwqhmUn4BAoGBAPo+KcP7F+wku6ocLL6urCpkWrWMdxznd2ayXhAF8RtHND3WBsIaOxofomOLNtz7lS0uxQXVaYY6WipZTE6AmjmAjtCKhZY9PwUGtXHpBPLNXzPwzUDCyyngzaXM3Xnnby7sNw+rAhtec2iNBf5usNlTtK1Mv4hr6+pbt+l3jlstAoGAfAWSaiOEYaHfynaIxTJ2W0OeOTAvh3bNxcK2ZACSRz20uXkP6YN52JyyVl2/9b0rod2VRnLDm6PXh1rVQaq3ONpM8BMwBuBW8Qte7FQ0UuM5YlasKrxJOvTsn0Jq1rMUUiJZ7UdOPyC99gR+bMTKdoxcwniZLq7dfIoutXHS4S0=",
        # 支付宝公钥
        'ali_public_key' => "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB"
        ],
//微信支付配置
    "Wechat"            =>   [
        # 微信的appid
                                'appid'=>'wx0e52ff4c26324db8',#
                                # 公众号的secret
                                'secret'=>'bfcf28da52d8c57b5e629ea40318498f',#
                                # 登录操作函数回调链接
                                'callback'=>'http://hr.hongrunet.com/admin/Wechat/wechat_login.html',
                                # 授权成功的回调链接
                                'login_success_callback'=>'http://hr.hongrunet.com',
                                # 微信支付key
                                'pay_key'=>'t9fc9qT18ebe5edOF1WDzZk7al7BaDKVKoj3PQfTgA2',
                                # 商户id
                                'mchid' => '1458211702',#
                                #通知回调地址
                                'notify_url'=>'http://hr.hongrunet.com/Admin/Wechat/notify.html',
                                #token定义
                                'TOKEN'=>"hongru",
                                # 微信开放平台的的appid
                                    'open_appid'=>'wxce0bb0bd03c52368',
                                # 微信开放商户平台key
                                    'open_pay_key'=>'',
                                # 微信开放平台商户id
                                    'open_mchid' => '',
    ],
 //乐视配置
"Letv"                  =>  [
                                'url' => 'http://api.open.lecloud.com/live/execute',
                                'headers'=>[
                                                'User-Agent' => 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)',
                                                'Content-Type'    => 'application/x-www-form-urlencoded;charset=utf-8',
                                                
                                            ],
                                "ver"   =>  "4.0",
                                'userid'=>  910018           
                            ]        
);
