<?php
namespace Home\Controller;
class SettingController extends \Think\Controller {

    public function index(){
        if(IS_POST){
            F('EVALUATE_PRICE',I('post.price/d','',500));
            F('WELCOME_MSG',I('post.welcome','','欢迎关注歌莫信息ฅ•❛ั ω ❛ั•ฅ~'));
            F('NO_CASE_MSG',I('post.no_case','','还没有相关案例=_=...'));
            F('KF_REPLY',I('post.kf_reply','',''));
        }
        $this->assign('title','杂项设置');
        $this->assign('evaluate_price',F('EVALUATE_PRICE'));
        $this->assign('welcome_msg',F('WELCOME_MSG'));
        $this->assign('no_case_msg',F('NO_CASE_MSG'));
        $this->assign('kf_reply',F('KF_REPLY'));
        $this->display();
    }
}