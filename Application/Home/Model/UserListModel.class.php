<?php
namespace Home\Model;
class ArticleModel extends \Think\Model{

    protected $trueTableName='user_list';
    protected $pk='openid';
    protected $fields=array('openid','nickname','city','headimgurl');
}