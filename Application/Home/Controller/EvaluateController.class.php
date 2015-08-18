<?php
namespace Home\Controller;
class EvaluateController extends \Think\Controller{

    public function index(){
        $this->assign('evaluate',D('Evaluate')->field('GROUP_CONCAT(evaluate.name) as name,GROUP_CONCAT(days) as days,evaluate_type.name as type,evaluate_type')->join('evaluate_type ON evaluate.evaluate_type=evaluate_type.id')->group('type')->order('evaluate_type.id')->select());
        $this->assign('price',F('EVALUATE_PRICE'));
        $this->display();
    }

    public function edit(){
        $this->display();
        $data=D('Evaluate')->field('GROUP_CONCAT(evaluate.name) as name,GROUP_CONCAT(days) as days,evaluate_type.name as type,evaluate_type')->join('evaluate_type ON evaluate.evaluate_type=evaluate_type.id')->group('type')->order('evaluate_type.id')->select();
        $res=array();
        foreach($data as &$val){
            $name=explode('.',$val['name']);
            $id=explode(',',$val['id']);
        }
    }

    public function getAll(){
        $data=D('Evaluate')->field('GROUP_CONCAT(evaluate.name) as name,GROUP_CONCAT(days) as days,evaluate_type.name as type,evaluate_type')->join('evaluate_type ON evaluate.evaluate_type=evaluate_type.id')->group('type')->order('evaluate_type.id')->select();
        foreach($data as &$val){
            print_r($val);
        }
        $this->ajaxReturn(array('id'=>0,'text'=>'估价模块','children'=>$data));
    }
}