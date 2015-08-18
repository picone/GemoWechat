<?php
namespace Home\Model;
class ArticleModel extends \Think\Model{

    protected $trueTableName='article';
    protected $pk='id';
    protected $fields=array('id','title','description','picture','url','media_id','menu_id');
}