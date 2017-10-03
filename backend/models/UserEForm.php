<?php
namespace backend\models;

use yii\base\Model;
use yii\web\NotFoundHttpException;

class UserEForm extends Model{
	public $oldP;
	public $newP;
	public $newP2;

	public function rules()
	{
		return [
			[['oldP','newP','newP2'],'required'],
			['newP2','compare','compareAttribute'=>'newP','message'=>'两次输入不一致'],
			['password','validatePassword'],//自定义验证规则
		];
	}

	public function attributeLabels()
	{
		return [
			'oldP'=>'旧密码',
			'newP'=>'新密码',
			'newP2'=>'再次输入',
		];
	}

	public function validatePassword()
	{//自定义验证规则
		$member = \Yii::$app->user->identity;//这个属性默认就是从数据库查出的及时数据
		if (!\Yii::$app->security->validatePassword($this->oldP, $member->password_hash)) {
			$this->addError('oldP','密码不正确');
		}
	}

}