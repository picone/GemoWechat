<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class MenuController extends \Think\Controller{

    public function index(){
        $this->assign('title','菜单管理');
        $this->display();
    }

    public function getMenu(){
        $menu=D('Menu')->getMenuName();
        foreach($menu as &$item){
            $sub_menu=D('Menu')->getMenuName($item['id']);
            if(isset($sub_menu[0])){
                $item['children']=$sub_menu;
            }
            unset($sub_menu);
        }
        $this->ajaxReturn(array('id'=>0,'text'=>'菜单管理','state'=>array('opened'=>true,'selected'=>true),'children'=>$menu));
    }

    public function c(){
        $msg='创建失败,请于管理员联系~';
        if(IS_POST){
            $data['name']=I('post.name');
            $data['type']=I('post.type');
            $data['val']=I('post.val');
            $data['sub_id']=I('post.id/d',0);
            if(D('Menu')->data($data)->add()){
                $msg='';
            }
        }
        $this->ajaxReturn(array('msg'=>$msg));
    }

    public function u(){
        $msg='保存失败,请于管理员联系~';
        if(IS_POST){
            $data['name']=I('post.name');
            $data['type']=I('post.type');
            $data['val']=I('post.val');
            if(D('Menu')->where('id=%d',I('post.id/d',0))->save($data)==1){
                $msg='';
            }
        }
        $this->ajaxReturn(array('msg'=>$msg));
    }

    public function r($id='0'){
        $data=D('Menu')->field('name,type,val')->where('id=%d',(int)$id)->select();
        $this->ajaxReturn($data[0]);
    }

    public function d(){
        $msg='删除失败,请于管理员联系~';
        if(IS_POST){
            $id=I('post.id/d',-1);
            if(D('Menu')->where('id=%d OR sub_id=%d',$id,$id)->delete()){
                $msg='';
            }
        }
        $this->ajaxReturn(array('msg'=>$msg));
    }

    public function updateMenu(){
        import('Org.Wechat.wechat');
        $wechat=new Wechat(array(
            'token'=>C('WECHAT_TOKEN'),
            'encodingaeskey'=>C('WECHAT_AES_KEY'),
            'appid'=>C('WECHAT_APPID'),
            'appsecret'=>C('WECHAT_APPSECRET')
        ));
        if(IS_POST){
            $menu=D('Menu')->getMenu();
            $result=array();
            foreach($menu as &$val){
                $temp=$this->toMenu($val);
                $sub_menu=D('Menu')->getMenu($val['id']);
                if(isset($sub_menu[0])){
                    $t=array();
                    foreach($sub_menu as &$sub){
                        $t[]=$this->toMenu($sub);
                    }
                    $temp['sub_button']=$t;
                    unset($t);
                }
                $result['button'][]=$temp;
                unset($temp);
            }
            $wechat->createMenu($result);
        }
        $msg=$wechat->errMsg;
        if($msg=='no access')$msg='';
        $this->ajaxReturn(array('msg'=>$msg));
    }

    private function toMenu($val){
        $result=array();
        $result['name']=$val['name'];
        $result['type']=$val['type'];
        if($val['val']!==''){
            switch($val['type']){
                case Wechat::EVENT_MENU_CLICK:
                    $result['key']=$val['val'];
                    break;
                case Wechat::EVENT_MENU_VIEW:
                    $result['url']=$val['val'];
                    break;
                case Wechat::EVENT_MENU_MEDIA_ID:
                case Wechat::EVENT_MENU_VIEW_LIMITED:
                    $result['media_id']=$val['val'];
                    break;
            }
        }
        return $result;
    }
}