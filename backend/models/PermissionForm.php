<?php
namespace backend\models;

use yii\base\Model;

class PermissionForm extends Model{
	public $name;//权限名称
	public $description;//权限描述
	//场景
	const SCENARIO_ADD = 'add';

	public function rules()//验证规则
	{
		return [
			[['name','description'],'required'],
			['name','validateName','on'=>self::SCENARIO_ADD],//自定义验证规则,验证用户输入的权限名称是否存在
		];
	}

	public function validateName(){
		if (\Yii::$app->authManager->getPermission($this->name)){
			$this->addError('name','权限已存在');
		}
	}

	public function attributeLabels()
	{
		return [
			'name'=>'权限名称/路由',
			'description'=>'权限描述'
		];
	}

}