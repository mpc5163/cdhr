<?php
namespace Service;
# 引入鉴权类
use Qiniu\Auth;
# 引入上传类
use Qiniu\Storage\UploadManager;
#引入异步转码
use Qiniu\Processing\PersistentFop;
/**
* 文件上传类
*/
class Upload{
    #  Access Key
    protected $accessKey = "O2ssmldXdoJOcPmOqS9xIQ1z7VA0KyMV5R7UD0jA";
    # Secret Key
    protected $secretKey = "-giI8kvxNF03wLdBQudgajK9h0Ypkj6luHSPnbX3";
    # 上传空间名称
    protected $Bucket_Name = "cdhr2017";
    # 回话token
    protected $token;
    # 要上传的文件名
    protected $upLoadFileName;
    # 上传后的文件名
    protected $key;
    # 转码操作
    protected $fops = "avthumb/mp4/s/640x360/vb/1.25m";
    # 转码异步回调
    protected $notifyUrl = 'http://hr.hongrunet.com/Home/Index/notify.php';
    # 私有队列名
    protected $pipeline = 'cdhr2017';
    /**
     * [__construct 构造函数]
     */
    public function __construct(){
        # 初始化accessKey和aecreyKey和上传空间名称
        $this -> accessKey = "O2ssmldXdoJOcPmOqS9xIQ1z7VA0KyMV5R7UD0jA";
        $this -> secretKey = "-giI8kvxNF03wLdBQudgajK9h0Ypkj6luHSPnbX3";
        $this -> Bucket_Name = "cdhr2017";
        # 构建鉴权对象
        $auth = new Auth($this -> accessKey, $this -> secretKey);
        # 生成上传 Token  
        $this -> token = $auth->uploadToken($this -> Bucket_Name);

    }
    #异步转码操作
    public function fopss($key){ 
        // dump($auth);exit;
        $authss = new Auth($this -> accessKey, $this -> secretKey);
        $pfop = new PersistentFop($authss, $this -> Bucket_Name, $this -> pipeline, $this -> notifyUrl);
        //要进行转码的转码操作。 http://developer.qiniu.com/docs/v6/api/reference/fop/av/avthumb.html
        list($id, $err) = $pfop->execute($key, $this -> fops);
        if ($err != null) {
            return ['status'=>0,'data'=>$err];
        } else {
            return ['status'=>1,'data'=>$id];
        }
        
    }
    public function seekStaus($id){
        $authss = new Auth($this -> accessKey, $this -> secretKey);
        $pfop = new PersistentFop($authss, $this -> Bucket_Name, $this -> pipeline, $this -> notifyUrl);
        //查询转码的进度和状态
        list($ret, $err) = $pfop->status($id);
        if ($err != null) {
            return ['status'=>0,'data'=>$err];
        } else {
            return ['status'=>1,'data'=>$ret];
        }
    }
    public function getToken(){
        return $this->token;
    }
    public function uploadss($uptoken,$filePath){
        $uploadMgr  = new UploadManager();
        $ret = $uploadMgr->putFile($uptoken, null, $filePath);
        return "http://hr3.hongrunet.com/".$ret[0]['key'];
    }
    public function upload_one($upFile){
        if($upFile['size']<1){return false;}
        # 设置要上传的文件名
        $this -> upLoadFileName = $upFile['tmp_name'];
        # 设置上传后的文件名
        $this -> key = $this -> get_rand_File_Name().$this -> get_Suffix_Name();
        # 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        # 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($this -> token, $this -> key, $this -> upLoadFileName);
        # 判断是是否存在错误信息
        if ($err === null) {

            # 如果不存在错误信息则直接返回上传成功文件名及地址
            return "http://hr3.hongrunet.com/".$ret['key'];
        }else{

            # 如果上传失败则直接返回假
            return false;
        }
    }
    # 多文件上传
    public function uploads($upFile){
        # 统计上传文件的个数
        $count = count($upFile['name']);
        # 上传结果
        $result = [];
        # 文件队列
        $arr = [];
        # 循环处理上传
        for ($i=0; $i < $count; $i++) {
            # 处理多文件上传和单文件上传的差别
            $arr[$i]['name'] = $upFile['name'][$i];
            $arr[$i]['type'] = $upFile['type'][$i];
            $arr[$i]['tmp_name'] = $upFile['tmp_name'][$i];
            $arr[$i]['error'] = $upFile['error'][$i];
            $arr[$i]['size'] = $upFile['size'][$i];
            # 调用自己上传文件
            $result[] = $this -> upload_one($arr[$i]);
        }
        # 返回结果
        return $result;
    }
    /**
     * [get_Suffix_Name 获取上传文件的后缀名]
     * @param  [type] $upFile [上传文件资源]
     * @return [type]         [上传文件的后缀名]
     */
    protected function get_Suffix_Name($upFile){
        $suffix = substr($upFile['name'],strripos($upFile['name'],'.'));
        # 判断是否存在后缀
        if(empty($suffix)){
            $suffix = "";
        }
        # 返回文件后缀名
        return $suffix;
    }
    /**
     * [get_rand_File_Name 随机临时文件名]
     * @return [type] [随机文件名]
     */
    protected function get_rand_File_Name(){
        $str = "";
        for ($i=0; $i < 10; $i++) {
            $str .= rand(0,9);
        }
        return time().$str;
    }

}