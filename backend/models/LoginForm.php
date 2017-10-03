<?php

namespace backend\models;

use yii\base\Model;
use Yii;
use yii\web\Cookie;

class LoginForm extends Model
{
	public $email;
	public $password;
	public $remember;
	public $code;

	public function rules()//用户登录时的验证规则
	{
		return [
			[['email', 'password',], 'required'],
			['remember', 'string'],
			['code','captcha','captchaAction'=>'login/captcha'],//验证码
		];
	}

	public function attributeLabels()
	{
		return [
			'email' => '邮箱',
			'password' => '密码',
			'remember' => '记住我',
			'code' => '验证码'
		];
	}

	public function login()
	{
		$member = Admin::findOne(['email' => $this->email,'status'=>10]);
		if ($member) {//判断是否有这个用户
			if (Yii::$app->security->validatePassword($this->password, $member->password_hash)) {//判断密码是否正确
				$ip = Yii::$app->request->getUserIP();
				$member->last_login_ip = $ip;//保存登录的IP
				$member->last_login_time = time();
				$member->save(false);
				$user = Yii::$app->user;
				if ($this->remember == 1) {//如果用户选择了记住我
					$user->login($member, 7 * 24 * 3600);//7天自动登录
				} else {
					$user->login($member);
				}
				return true;
			}
		}
		$this->addError('password', '账号或密码错误');
		return false;//密码或账户错误
	}
}