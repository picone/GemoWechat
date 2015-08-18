<?php
namespace Home\Model;
class AddressListModel extends \Think\Model{

    protected $trueTableName='address_list';
    protected $pk='id';
    protected $fields=array('id','name','sex','grade','platform','qq','tel','email');
}