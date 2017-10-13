<?php
namespace Admin\Controller;
use Think\Controller;

class ArticleController extends Controller {
	/**
     * Session过期重新定位到登录页面
     */
    public function _initialize(){
        if (!isset($_SESSION['userid'])){
           $this->error('你还没有登录,请重新登录', U('/Admin/Login/login'));
        }
    }

    /**
    * 文章管理
    */
    public function article(){
        $user = M('info');
        $class= I('class');
        $users = $user->where("isdel = 0")->order('id desc')->select();
        // var_dump($users);
        $this->assign("users",json_encode($users));
        $this->display();
    }

    /**
    * 根据id获取单个文章
    */
    public function getOneUser(){
        $userid = I('id');
        $user   = M('info');
        // var_dump($user);
        $users  = $user->where("id=%d",$userid)->find();
        // var_dump($users);die;
        $this->ajaxReturn($users); 
    }

    /**
    * 查询文章
    */
    public function searchState(){
        $state = M('info');
        $name = $_GET['name'];
        $paytype = I('grade');
        $start = strtotime(I('start'));
        $end   = strtotime(I('end'));
        if(empty(I('start'))){
            $start = 0;
        }
        if(empty(I('end'))){
            $end = time();
        }
        if(!empty(I('start')) && !empty(I('end'))){
            $start = min($start,$end);
            $end   = max($start,$end);
        }   
        $where = [];
        // if(!empty($reason)){
        //     $where['message'] = ['like',"%$reason%"];
        // }
        if(!empty($paytype)){
            $where['classid'] = ['like',"%$paytype%"];
        }
        if(!empty($name)){
            $where['title'] = ['like',"%$name%"];
        }   
        $where['createtime'] = ['between',"$start,$end"];
        $users = $state->where($where)->select();
        $this->ajaxReturn($users);
    }
    /**
    * 保存(新添 | 修改)的文章信息
    */
    public function saveUser(){
        $userid     = (int)I('userid');
        $user       = M('info');
        $data['title']   = I('title');
        $data['classid']   = I('classid');
        $data['author']       = I('author');
        $data['content']       = I('content');
        $data['description']       = I('description');
        $data['thumbnail']= I('thumbnail');
        if ($userid) {
            // $data['isenable']   = I('isenable');
            $res = $user->where("id=%d",$userid)->save($data);
            if ($res) {
                $this->success("更新成功!",U('Admin/Article/article'));
            }else{
                $this->error("更新失败!");
            }
        }else{
            $name = $user->getField("title",true);
            if (!in_array($data['title'],$name)) {
              $data['createtime'] = time();
              $res = $user->add($data);
              if ($res) {
                $this->success("添加成功!",U('Admin/Article/article'));
              }else{
                $this->error("添加失败!");
              }                            
            }else{
                $this->error("标题已存在!");
            }
        }
    }
    /**
    * 删除指定id厂商文章
    */
    public function deleteUser(){
        $id   = I('get.id');
        $user = M('info');
        if($user->where("id=%d",$id)->getField('classid') == 3){
            echo "false";return;
        }
        $result = $user->where("id=%d",$id)->delete();
        if($result){
            echo "true";
        }else{
            echo "false";
        }
    }
    public function getInfo(){
        $userid = I('id');
        $user   = M('info');
        $users  = $user->where("id=%d",$userid)->find();   
        $this->assign("data",$users);
        $this->display('addaticle');     
    }
    //文本编辑器
    public function ueditor(){
        $data = new \Org\Util\Ueditor();
        echo $data->output();
    }
}