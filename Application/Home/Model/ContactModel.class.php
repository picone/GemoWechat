<?php
namespace Home\Model;
class ContactModel extends \Think\Model{

    protected $trueTableName='contact';
    protected $pk='id';
    protected $fields=array('id','name','contact','description');
}