<?php
/**
 * @author: helei
 * @createTime: 2016-06-08 16:29
 * @description: rsa加密算法
 * @link      https://github.com/helei112g/payment/tree/paymentv2
 * @link      https://helei112g.github.io/
 */

namespace Payment\Utils;

class RsaEncrypt
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * 设置key
     * @param $key
     * @author helei
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * RSA签名, 此处秘钥是私有秘钥
     * @param string $data 签名的数组
     * @throws \Exception
     * @return string
     * @author helei
     */
    public function encrypt($data)
    {
        if ($this->key === false) {
            return '';
        }

        $res = openssl_get_privatekey($this->key);
        if (empty($res)) {
            throw new \Exception('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        openssl_sign($data, $sign, $res);
        openssl_free_key($res);

        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA解密 此处秘钥是用户私有秘钥
     * @param string $content 需要解密的内容，密文
     * @throws \Exception
     * @return string
     * @author helei
     */
    public function decrypt($content)
    {
        if ($this->key === false) {
            return '';
        }

        /*$ss = "MIIEpQIBAAKCAQEA9/oU227GWG032U489P+w3j/uDpotlONxEcmWdnXxqF55teh9HTsBpGwxSLUQPpoJVGK4NDbv8lRcTaYUpdOAVOBQkWFYV5EP9Wx+d4Njxby4VEsrL7nA4AtRZQCSl2RZolqW9XSXIDUbRIwTX52B0lL7naFApVMTrxAg6SVee3ipySO2jjiuOfXFrIrq7kbMoX2KEEyEXNnV+1cvshVltNTDjk7LmPqkOOVelaUvEiUZKWu/s55aTHMDKQc62zyIxp+YhxEDN9MQ3IE1u1lsjkvGFRJrBK5GJ0JeaGRJ0jJk0n+jsQRZSIlahEyfeKu+TLYhbrOaZIon770RLKfdjQIDAQABAoIBAQDzzqKBGInU2RVHB2BxdY+9BFgXbUkRkQlTdsMDl3TQmIrL6lhQy/oSYfmWxcbB9grkN+dqFvWVaZITGg5E+Ay95onwVihYeBybrV8uPDV1bdZBsmI0m0piPV56krSOOrDSKoUvscEAfpZfr+VAZgkE9GfFFI0Nvv+kaJQjBoW9l/bMblRRjqVEMLemVvrdUrUDG/q76vaZub/7gHY22e9uxMBBtsbXnLWdDQSXo3WLPDaYKXOG8DK7NAXxWVzK3eu5mhVDbVFJrkRrrHQoXL3zt+26eYkRPQ+jxh/KbX9PoRgGrojLq54JU31Fu1kuxO7lwlCuiGabs+eTVUW0VcEBAoGBAPwaRfshjSuoGA2JkQEVnO5La83XrCXT5GQ+MrbtkNiJOWPMzkftP1ePp7GZ6QnJ1/mshqMajHh0VnZJAt1HPrf48nIePum/Uz1bDbYr6uVzv7poZyODNhrxpvKiI/DC4OvMuDsRaBArqQNpCIUtVto7tDjMGHQlp3NMnPUML25BAoGBAPvPet58k/Z5gv4Z0eeSlOPGpeuw4uIUAh2Gte2gy8UuzOzWzhnMAIk035KftlMQgWZ95Fqmjiek55DawnLxpup5j5ReN+uqgVXlSgFGfe4HwFQVr6vaQ+6sa6W/BflENIXcNxd4YipyChUH3g18H55JNGNLSYcRMKqm8a9tXrRNAoGBAPXpywnFGKfVOGE8J64fHw1zlJ0eHFZp8we1hFLcOcZ/VPP8+9s9meQXxUSBFcjukzaBDHRdmViGYzDO13Rx4sQimpym1Ee+0buv7hgdjuxzlDqJTNJfU4/E2U0kzFo2PqDE+ZDk0dW0QRGKiz0dlPMb+hjNPa1ObaESwqhmUn4BAoGBAPo+KcP7F+wku6ocLL6urCpkWrWMdxznd2ayXhAF8RtHND3WBsIaOxofomOLNtz7lS0uxQXVaYY6WipZTE6AmjmAjtCKhZY9PwUGtXHpBPLNXzPwzUDCyyngzaXM3Xnnby7sNw+rAhtec2iNBf5usNlTtK1Mv4hr6+pbt+l3jlstAoGAfAWSaiOEYaHfynaIxTJ2W0OeOTAvh3bNxcK2ZACSRz20uXkP6YN52JyyVl2/9b0rod2VRnLDm6PXh1rVQaq3ONpM8BMwBuBW8Qte7FQ0UuM5YlasKrxJOvTsn0Jq1rMUUiJZ7UdOPyC99gR+bMTKdoxcwniZLq7dfIoutXHS4S0=";
        $mm = str_replace('/www/wwwroot/hongrunet.ewtouch.com\private\siyao.txt', $ss, $this->key);*/
        $res = openssl_get_privatekey($this->key);
        if (empty($res)) {
            throw new \Exception('您使用的私钥格式错误，请检查RSA私钥配置');
        }

        //用base64将内容还原成二进制
        $content = base64_decode($content);
        //把需要解密的内容，按128位拆开解密
        $result  = '';
        for ($i = 0; $i < strlen($content)/128; $i++) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        //dump($result);exit;
        return $result;
    }

    /**
     * RSA验签 ，此处的秘钥，是第三方公钥
     * @param string $data 待签名数据
     * @param string $sign 要校对的的签名结果
     * @throws \Exception
     * @return bool
     * @author helei
     */
    public function rsaVerify($data, $sign)
    {
        // 初始时，使用公钥key
        $res = openssl_get_publickey($this->key);
        if (empty($res)) {
            throw new \Exception('支付宝RSA公钥错误。请检查公钥文件格式是否正确');
        }

        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        openssl_free_key($res);
        return $result;
    }
}
