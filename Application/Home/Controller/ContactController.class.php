<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class ContactController extends \Think\Controller{

    public function index(){
        if(IS_POST){
            $data['name']=I('post.name','addslashes,htmlspecialchars');
            $data['contact']=I('post.contact/d');
            $data['description']=str_replace("\n",'<br/>',I('post.description','addslashes,htmlspecialchars'));
            D('Contact')->add($data);
            $wechat=new Wechat(array(
                'token'=>C('WECHAT_TOKEN'),
                'encodingaeskey'=>C('WECHAT_AES_KEY'),
                'appid'=>C('WECHAT_APPID'),
                'appsecret'=>C('WECHAT_APPSECRET')
            ));
            $kf=explode(';',F('KF_REPLY'));
            $msg['msgtype']='text';
            $msg['text']['content']=$data['name'].'留下新消息,请到后台查看~';
            foreach($kf as &$val){
                $msg['touser']=$val;
                $wechat->sendCustomMessage($msg);
            }
            $this->success('提交成功,将返回上一页...','/Evaluate');
        }else {
            $this->display();
        }
    }

    public function view(){
        $this->assign('data',D('Contact')->field('id,name,contact,description')->select());
        $this->assign('title','联系客户');
        $this->display();
    }

    public function d(){
        $msg='删除失败,请与管理员联系~';
        if(IS_POST){
            if(D('Contact')->where('id=%d',I('post.id/d','',0))->delete())$msg='';
        }
        $this->ajaxReturn(array('msg'=>$msg));
    }
}