<?php
namespace Home\Controller;
use Think\Exception;

class UserController extends \Think\Controller{

    public function index(){
        if(IS_POST){
            $username = I('post.uname');
            $password = md5(I('post.pswd'));
            $user = D('User');
            $res = $user->where('username = "'.$username.'"')->find();
            if(is_array($res) && ($res['password'] === $password)){
                session('user',$username);
                $this->redirect('/Index');
            }else{
                $this->error('登录名或密码错误');
            }
        }else{
            $this->display();
        }
    }

    public function change(){
        if(IS_POST){
            try{
                $passwd=I('post.password','','');
                if($passwd!==I('post.password2','',''))throw new \Exception('两次密码不同');
                $data=D('User')->field('id,password')->where('username=\'%s\'',session('user'))->select();
                if(!isset($data[0]))throw new \Exception('用户名不存在');
                if($data[0]['password']!==md5(I('post.old_password','','')))throw new \Exception('原密码有误!');
                D('User')->where('id=%d',$data[0]['id'])->save(array('password'=>md5($passwd)));
            }catch(Exception $e){
                $this->error($e->getMessage());
            }
        }
        $this->display();
    }

    public function logout(){
        session('user',null);
        $this->success('退出成功~','/Index');
    }
}