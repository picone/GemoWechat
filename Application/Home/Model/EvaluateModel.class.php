<?php
namespace Home\Model;
class EvaluateModel extends \Think\Model{

    protected $trueTableName='evaluate';
    protected $pk='id';
    protected $fields=array('id','name','days','evaluate_type');
}