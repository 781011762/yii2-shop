<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
	public $name;//角色名
	public $description;//描述
	public $permissions;//权限
	const SCENARIO_ADD = 'add';

	public function rules()
	{
		return [
			[['name','description'],'required'],
			['permissions','safe'],//safe 是安全验证.
			['name','validateName','on'=>self::SCENARIO_ADD],
		];
	}

	public function validateName(){
		$auth =  \Yii::$app->authManager;
		if ($auth->getRole($this->name)){
			$this->addError('name','角色名已存在');
		}
	}

	//视图页面会用到
	public static function getPermissionItems(){//(静态方法)动态获取所有的权限选项
		$auth = \Yii::$app->authManager;
		$permissions = $auth->getPermissions();
		$items = [];
		foreach ($permissions as $permission){
			$items[$permission->name] = $permission->description;
		}
		return $items;
	}

	public function attributeLabels()
	{
		return [
			'name'=>'角色名',
			'description'=>'描述',
			'permissions'=>'权限',
		];
	}
}