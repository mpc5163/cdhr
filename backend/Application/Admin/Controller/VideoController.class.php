<?php
namespace Admin\Controller;
use Think\Controller;
use Service\Wechat;
use Service\Upload;
use Home\Controller\MobanController as Moban;
class VideoController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
            $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }
     public function delVideo(){
     	$id = I('id');
     	$course  = M('course');
     	$res = $course->where("isdel = 0")->where('id = '.$id)->delete();
     	if($res){
     			$this->ajaxReturn(['status'=>1,'msg'=>"删除成功"]);
    		}else{
    			$this->ajaxReturn(['status'=>0,'msg'=>"删除失败"]);
    		}
    }
     public function editVideo(){
     	$id = I('id');
     	$course  = M('course');
     	$data = $course->where("isdel = 0")->where('id = '.$id)->find();
     	$this->ajaxReturn($data);
     }
   
    public function saveVideo(){
    	$course  = M('course');
    	$data    = $_POST;
        $qiniu = new Upload();
        $data['keyId'] = $qiniu->fopss($data['keyId'])['data'];
    	$data['createtime'] = time();
        // $data['thumbnail']  = $data['thumbnail'];
    	if(!empty(I('id'))){
            if($data['thumbnail'] == $course->where("id = ".(int)I('id'))->getField('thumbnail')){
                unset($data['thumbnail'] );
            }else{
                $data['thumbnail']  = 'http://hr.hongrunet.com'.$data['thumbnail'];
            }
    		$res = $course->where("id = ".(int)I('id'))->save($data);
    		if($res){
    			$this -> ajaxReturn(['status'=>1,'msg'=>"更新成功"]);
    		}else{
    			$this -> ajaxReturn(['status'=>0,'msg'=>"更新失败"]);
    		}
    	}else{
            $data['thumbnail']  = 'http://hr.hongrunet.com'.$data['thumbnail'];
    		$res = $course->add($data);
    		if($res){
    			$this -> ajaxReturn(['status'=>1,'msg'=>"添加成功!"]);		
    		}else{
    			$this -> ajaxReturn(['status'=>0,'msg'=>"添加失败!"]);
    		}
    	}
    }
    /**
	 * 课程发布管理
	 */
	public function index(){
		$course  = M('course');
        #查询全部课程转码情况
        $qiniu = new Upload();
        $video = $course->where("keyid !=''")->select();
        foreach ($video as $key => $value) {
            if(empty($value['videofops'])){
                $res = $qiniu->seekStaus($value['keyid']);
                if($res['data']['code'] == 0){
                    $res = "http://hr3.hongrunet.com/".$res['data']['items'][0]['key'];
                    if($res !="http://hr3.hongrunet.com/"){
                       $course->where('id='.$value['id'])->save(['videofops'=>$res]); 
                    }
                }       
            }
        }
        $user = M('user');
		$class   = M('course_class');
		$data = $course->where("isdel = 0")->order("is_look ASC,id DESC")->select();
		foreach ($data as $k => $v) {
            $v['classid'] = $class->where('id = '.$v['classid'])->find()['name'];
            if($v['uid'] != 0){
                $data[$k]['grade'] = $user->where('id = '.$v['uid'])->find()['grade'];
            }else{
                $data[$k]['grade'] = 0;
            }			
		}
		$this->assign('data',json_encode($data));
		$this->display();		
	}
	
	public function pizhun(){
		$id = I('id');
		$type = I('type');
        $data = M('course')->where(['id'=>$id])->find();
        if($data['is_look'] !=0){
            $this->ajaxReturn(['code'=>1,'msg'=>'已审核']);exit;
        }
		M('course')->where(['id'=>$id])->save(['is_look'=>$type]);
        $moban = Moban::sendSms($data['uid'],$id);
		$this->ajaxReturn(['code'=>1,'msg'=>'审核成功']);
	}	
    public function search(){
        $course = M('course');
        $user = M('user');
        $change = $_GET['change'];
        $grade = $_GET['grade'];
        $title = $_GET['title'];    
        $where = [];
        if((I('start')) !="" && (I('end'))!=""){
            $start = strtotime(I('start'));
            $end   = strtotime(I('end'));
            $where['createtime'] = ['between',"$start,$end"];
        }   
        if(!empty($change)){
            $where['classid'] =$change;
        }
        if(!empty($title)){
            $where['title'] = ['like',"%$title%"];
        }
        if(!empty($grade)){
            /*$users['grade'] = $grade;
            $tid = $user->where($users)->getField('id',true);
            $where['uid'] = ['in',$tid];*/   
            $where['level'] = $grade;
        }
        $data = $course->where($where)->order('id desc')->select();
        foreach ($data as $k => $v) {
            if($v['uid'] != 0){
                $data[$k]['grade'] = $user->where('id = '.$v['uid'])->find()['grade'];
            }else{
                $data[$k]['grade'] = 0;
            }
        }
        $this->ajaxReturn($data);
    }
}
