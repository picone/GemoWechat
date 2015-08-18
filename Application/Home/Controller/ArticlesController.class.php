<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class ArticlesController extends \Think\Controller{
	private $wechat;

    public function _initialize(){
        import('Org.Wechat.wechat');
        $this->wechat=new Wechat(array(
            'token'=>C('WECHAT_TOKEN'),
            'encodingaeskey'=>C('WECHAT_AES_KEY'),
            'appid'=>C('WECHAT_APPID'),
            'appsecret'=>C('WECHAT_APPSECRET')
        ));
    }

    public function index(){
        if(IS_POST){
            $receive=I('post.news_id');
            $picture=I('post.picture');
            $id=I('post.menu_id/d',0);
            if($id>0){
                $addList=array();
                foreach ($receive as $key=>&$mediaId){
                    $res=F($mediaId);
                    if(!$res){
                        $data=$this->wechat->getForeverMedia($mediaId);
                        $res['digest']=$data['news_item'][0]['digest'];
                        $res['title']=$data['news_item'][0]['title'];
                        $res['url']=$data['news_item'][0]['url'];
                        unset($data);
                        $res['picture']=$picture[$key];
                        F($mediaId,$res);
                    }
                    $addList[]=array('title'=>$res['title'],'url'=>$res['url'],'picture'=>$res['picture'],'description'=>$res['digest'],'media_id'=>$mediaId,'menu_id'=>$id);
                }
                D('Article')->where('menu_id=%d',$id)->delete();
                if(D('Article')->addAll($addList)){
                    $this->assign('fail',false);
                }else{
                    $this->assign('fail',true);
                }
            }
        }
		$data=S('wechat_list');
		if(!$data){
			$res = $this->wechat->getForeverList('news',0,20);
			foreach ($res['item'] as $item) {
				$data[$item['media_id']]=array(
					'digest'=>$item['content']['news_item'][0]['digest'],
					'title'=>$item['content']['news_item'][0]['title'],
                    'url'=>$item['content']['news_item'][0]['url']
				);
                if($item['content']['news_item'][0]['show_cover_pic']==1){
                    $data[$item['media_id']]['picture']=$this->getPicUrl($item['content']['news_item'][0]['url']);
                }
                F($item['media_id'],$data[$item['media_id']]);
			}
			unset($res);
            S('wechat_list',$data,120);
		}
    	$events=D('Menu')->where('sub_id = 2')->select();
    	$this->assign('events',$events);
		$this->assign('data',$data);
    	/*$count = $this->wechat->getForeverCount();
    	$pages = floor($count['news_count']/20)+1;*/
        $this->assign('title','案例管理');
        $this->display();
    }

    public function getCur($id=0){
        $this->ajaxReturn(D('Article')->field('media_id')->where('menu_id=%d',(int)$id)->select());
    }

    private function getPicUrl($url){
        $result=F($url);
        if($result==''){
            $ch=curl_init($url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            $str=curl_exec($ch);
            $pos1=strpos($str,'var cover = "');
            if($pos1){
                $pos1+=13;
                $pos2=strpos($str,'";',$pos1);
                if($pos2){
                    $result=substr($str,$pos1,$pos2-$pos1);
                }
            }
            F($url,$result);
        }
        return $result;
    }
}