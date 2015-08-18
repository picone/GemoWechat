<?php
namespace Home\Controller;
class AddressController extends \Think\Controller{

    public function index(){
        if(IS_POST){
            $data=D('AddressList')->where('tel=%d',I('post.tel/d'))->select();
            if(isset($data[0])){
                session('address',true);
                redirect('/Address/l');
            }else{
                $this->assign('output','您没有权限查看');
                $this->display();
            }
        }else{
            $this->display();
        }
    }

    public function l(){
        if(session('address')){
            $this->assign('data',D('AddressList')->field('name,sex,grade,platform,qq,tel,email')->select());
            $this->display();
        }else{
            redirect('/Address');
        }
    }

    public function edit(){
        $this->assign('data',D('AddressList')->field('id,name,sex,grade,platform,qq,tel,email')->select());
        //$this->display();
    }
}