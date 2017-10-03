<?php
namespace frontend\models;

use yii\base\Model;

class RegisterForm extends Model{
	public $username;
	public $password;
	public $repat_password;
	public $email;
	public $tel;
	public $captcha;//手机验证码
	public $checkcode;
	const SCENARIO_REGISTER = "register";

	public function rules()
	{
		return [
			[['username','password','email','tel'],'required'],
			['checkcode','required','on'=>self::SCENARIO_REGISTER],
			['username','string','min'=>3,'max'=>20],
			['password','match','pattern'=>'/^\w{6,20}$/'],
			['repat_password','compare','compareAttribute'=>'password','on'=>self::SCENARIO_REGISTER],
			['tel','match','pattern'=>'/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0|1|2|3|5|6|7|8|9])\d{8}$/'],
			['checkcode','captcha','captchaAction'=>'member/captcha','on'=>self::SCENARIO_REGISTER],//验证码验证
			['username','validateUsername'],//自定义验证用户名的唯一性
			['email','validateEmail'],
			['tel','validateTel'],
		];
	}

	public function validateUsername()
	{//自定义验证规则
		$user = Member::findOne(['username'=>$this->username]);
		if ($user!==null){
			$this->addError("username","用户名已存在");

		}
	}

	public function validateEmail()
	{//自定义验证规则
		$user = Member::findOne(['email'=>$this->email]);
		if ($user!==null){
			$this->addError("email","邮箱已存在");
		}
	}

	public function validateTel()
	{//自定义验证规则
		$user = Member::findOne(['tel'=>$this->tel]);
		if ($user!==null){
			$this->addError("tel","手机已存在");
		}
	}

	public function attributeLabels()
	{
		return [
			'username'=>'用户名',
			'password'=>'密码',
			'email'=>'邮箱',
			'tel'=>'手机号码',
			'checkcode'=>'验证码',
		];
	}

	public function saveMember(){
		$model = new Member();
		$model->username = $this->username;
		$model->password = $this->password;
		$model->email = $this->email;
		$model->tel = $this->tel;
		$model->save(false);
	}
}