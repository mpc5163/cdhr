<?php
namespace Admin\Model;
use Think\Model\ViewModel;

class FactoryProductViewModel extends ViewModel{
    public $viewFields = array(
        'product'=>array('id'=>'productid','name','content','isenable','remark','producttypeid','factoryid','price','info','thumbnail','_as'=>'a','_type'=>'LEFT'),
        'producttype'=>array('name'=>'typename','icon','_as'=>'c','_on'=>'a.producttypeid=c.id','_type'=>'LEFT'),
        'factory'=>array('name'=>'factoryname','_as'=>'d','_on'=>'a.factoryid=d.id'),
    );
}