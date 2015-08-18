<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class CustomController extends \Think\Controller {

    public function index(){
        $this->assign('title','用户列表');
        $this->assign('data',D('UserList')->field('openid,nickname,city,headimgurl')->select());
        $this->display();
    }

    public function updateList(){
        $msg='更新失败,请与管理员联系~';
        if(IS_POST){
            $wechat=new Wechat(array(
                'token'=>C('WECHAT_TOKEN'),
                'encodingaeskey'=>C('WECHAT_AES_KEY'),
                'appid'=>C('WECHAT_APPID'),
                'appsecret'=>C('WECHAT_APPSECRET')
            ));
            $openid=$wechat->getUserList();
            $data=array();
            foreach($openid['data']['openid'] as &$val){
                $data['user_list'][]=array('openid'=>$val);
            }
            unset($openid);
            //生成请求$data
            $data=$wechat->getUserInfoBatchget($data);
            $sql=array();
            foreach($data['user_info_list'] as &$val){
                $sql[]=array(
                    'openid'=>$val['openid'],
                    'nickname'=>$val['nickname'],
                    'city'=>$val['city'],
                    'headimgurl'=>substr($val['headimgurl'],0,-1)
                );
            }
            unset($data);
            D('UserList')->execute('TRUNCATE user_list');
            D('UserList')->addAll($sql);
            $msg='';
        }
        $this->ajaxReturn(array('msg'=>$msg));
    }
}