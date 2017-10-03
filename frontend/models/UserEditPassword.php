<?php
namespace frontend\models;

use yii\base\Model;

class UserEditPassword extends Model{
	public $old_pw;
	public $new_pw;

	public function rules()
	{
		return [
			[['old_pw','new_pw'],'required'],
			['new_pw','string','min'=>6,'max'=>18],//新密码必须6-18位
			['old_pw','validatePassword'],//自定义验证规则
		];
	}
	public function attributeLabels()
	{
		return [
			'old_pw'=>'旧密码',
			'new_pw'=>'新密码',
		];
	}

	public function validatePassword()
	{//自定义验证规则
		$member = \Yii::$app->user->identity;//这个属性默认就是从数据库查出的及时数据
		if (!\Yii::$app->security->validatePassword($this->old_pw, $member->password_hash)) {
			$this->addError("old_pw",'旧密码不正确');
		}
	}
}