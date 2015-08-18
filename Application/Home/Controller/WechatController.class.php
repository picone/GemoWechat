<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class WechatController extends \Think\Controller {
    private $wechat;

    public function _initialize(){
        import('Org.Wechat.wechat');
        $this->wechat=new Wechat(array(
            'token'=>C('WECHAT_TOKEN'),
            'encodingaeskey'=>C('WECHAT_AES_KEY')
        ));
    }

    public function index(){
        $this->wechat->valid();
        switch($this->wechat->getRev()->getRevType()){
            case Wechat::MSGTYPE_EVENT:
                $event=$this->wechat->getRevEvent();
                $event=$event['key'];
                if($event==''){
                    $this->wechat->text(F('WELCOME_MSG'))->reply();
                }else{
                    $data=M('Article')->field('title,description,picture,url')->join('menu ON menu.id=menu_id')->where('type=\'click\' AND val=\'%s\'',$event)->select();
                    if(count($data)>0){
                        $news=array();
                        foreach($data as &$val){
                            $news[]=array('Title'=>$val['title'],'Description'=>$val['description'],'PicUrl'=>$val['picture'],'Url'=>$val['url']);
                        }
                        $this->wechat->news($news)->reply();
                    }else{
                        $this->wechat->text(F('NO_CASE_MSG'))->reply();
                    }
                }
                break;
        }
    }
}