<?php
namespace Home\Controller;
use Org\Wechat\Wechat;
class IndexController extends \Think\Controller {
    public function index(){
        $res=array();
        // UPTIME
        if($str=@file('/proc/uptime')) {
            $str=explode(' ', implode('', $str));
            $res['uptime']=$this->getDay(trim($str[0]));
        }
        // MEMORY
        if($str = @file('/proc/meminfo')) {
            $str = implode('', $str);
            preg_match_all('/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s', $str, $buf);
            preg_match_all('/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s',$str,$buffers);
            $res['memTotal'] = round($buf[1][0] / 1024, 2);
            $res['memFree'] = round($buf[2][0] / 1024, 2);
            $res['memBuffers'] = round($buffers[1][0] / 1024, 2);
            $res['memCached'] = round($buf[3][0] / 1024, 2);
            $res['memUsed'] = $res['memTotal'] - $res['memFree'];
            $res['memPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memUsed'] / $res['memTotal'] * 100, 2) : 0;
            $res['memRealUsed'] = $res['memTotal'] - $res['memFree'] - $res['memCached'] - $res['memBuffers']; //真实内存使用
            $res['memRealFree'] = $res['memTotal'] - $res['memRealUsed']; //真实空闲
            $res['memRealPercent'] = (floatval($res['memTotal']) != 0) ? round($res['memRealUsed'] / $res['memTotal'] * 100, 2) : 0; //真实内存使用率
            $res['memCachedPercent'] = (floatval($res['memCached']) != 0) ? round($res['memCached'] / $res['memTotal'] * 100, 2) : 0; //Cached内存使用率
            $res['swapTotal'] = round($buf[4][0] / 1024, 2);
            $res['swapFree'] = round($buf[5][0] / 1024, 2);
            $res['swapUsed'] = round($res['swapTotal'] - $res['swapFree'], 2);
            $res['swapPercent'] = (floatval($res['swapTotal']) != 0) ? round($res['swapUsed'] / $res['swapTotal'] * 100, 2) : 0;
        }
        // LOAD AVG
        if($str=@file('/proc/loadavg')){
            $str=explode(' ',implode('',$str));
            $str=array_chunk($str,4);
            $res['loadAvg']=implode(' ',$str[0]);
        }
        //数据库信息统计
        $data=(new \Think\Model())->query('SHOW STATUS');
        $sql=array();
        foreach($data as &$val){
            switch($val['variable_name']){
                case 'Uptime':
                    $sql['uptime']=$this->getDay($val['value']);
                    break;
                case 'Queries':
                    $sql['query']=$val['value'];
                    break;
                case 'Qcache_hits':
                    $sql['qc_hits']=$val['value'];
                    break;
                case 'Qcache_inserts':
                    $sql['qc_inserts']=$val['value'];
                    break;
                case 'Threads_connected':
                    $sql['connection']=$val['value'];
                    break;
            }
        }
        $sql['hit_rate']=round((($sql['qc_hits']-$sql['qc_inserts'])/$sql['qc_hits'])*100,2);
        unset($data);
        //微信数据统计
        $wechat_data=S('wechat');
        if(!$wechat_data){
            import('Org.Wechat.wechat');
            $yesterday=date('Y-m-d',NOW_TIME-86400);
            $wechat=new Wechat(array(
                'token'=>C('WECHAT_TOKEN'),
                'encodingaeskey'=>C('WECHAT_AES_KEY'),
                'appid'=>C('WECHAT_APPID'),
                'appsecret'=>C('WECHAT_APPSECRET')
            ));
            $tmp=$wechat->getDatacube('user','summary',$yesterday,$yesterday);
            $wechat_data['user']=$tmp[0];
            $tmp=$wechat->getDatacube('interface','summary',$yesterday);
            $wechat_data['interface']=$tmp[0];
            unset($tmp);
            S('wechat',$wechat_data,3600);
        }
        $this->assign('system',$res);
        $this->assign('sql',$sql);
        $this->assign('wechat',$wechat_data);
        $this->display();
    }

    private function getDay($str){
        $res='';
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if ($days!=0) $res=$days . '天';
        if ($hours!=0) $res.= $hours . '小时';
        $res.= $min . '分钟';
        return $res;
    }
}