<?php
namespace Home\Model;
class MenuModel extends \Think\Model{

    protected $trueTableName='user';
    protected $pk='id';
    protected $fields=array('id','username','password');
}