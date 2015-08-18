<?php
namespace Home\Model;
class MenuModel extends \Think\Model{

    protected $trueTableName='menu';
    protected $pk='id';
    protected $fields=array('id','name','type','val','sub_id');

    public function getMenu($sub_id=0){
        return $this->field('id,name,type,val,sub_id')->where('sub_id=%d',$sub_id)->order('id')->select();
    }

    public function getMenuName($sub_id=0){
        return $this->field('id,name as text')->where('sub_id=%d',$sub_id)->order('id')->select();
    }
}