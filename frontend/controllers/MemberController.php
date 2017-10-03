<?php

namespace frontend\controllers;

use frontend\models\LoginForm;
use frontend\models\Member;
use frontend\models\RegisterForm;
use frontend\models\SmsDemo;
use yii\captcha\CaptchaAction;
use yii\web\NotFoundHttpException;

class MemberController extends \yii\web\Controller
{
	//注册
	public function actionRegister(){
		$model = new RegisterForm();
		$model->scenario = RegisterForm::SCENARIO_REGISTER;
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post(),'');
			if ($model->validate()){//验证规则
				$model->saveMember();
				return $this->redirect(['member/login']);
			}else{
				throw new NotFoundHttpException('提交信息出错,请尽量填写正确的信息');
			}
		}
		$this->layout = false;
		return $this->render('register',['model'=>$model]);
	}
	//登录页面
	public function actionLogin(){
		$this->layout = false;
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model = new LoginForm();
			$model->load($request->post(),'');//用户提交的数据
			if ($model->validate()){//验证用户提交的信息
				//var_dump($model);exit();
				if ($model->login()){
					//通过认证 -> 跳转到首页
					return $this->redirect(['index/index']);
				}else{
					throw new NotFoundHttpException('密码或者账号错误');
				}
			}else{//验证 用户提交的信息没有通过验证->提示
				throw new NotFoundHttpException("数据验证未通过");
			}
		}
		return $this->render('login');
	}

    public function actionIndex()
    {
    	$this->layout = false;
        return $this->render('index');
    }
    //测试redis

	//测试发送短信
	public function actionSms(){
		//$this->enableCsrfValidation = false;
		//frontend\models\SmsDemo==>        @frontend\models\SmsDemo.php
		//Aliyun\Core\Config  ==> @Aliyun\Core\Config.php
		$phone = \Yii::$app->request->post('phone');
		if (!isset($phone)){
			exit;
		}
		$redis = new \Redis();
		$redis->connect("127.0.0.1");
		//判断是否能够发送短信
		//一个手机号码1分钟只能发送一条短信
		$time = $redis->get('time_'.$phone);//上次发送短信 的时间
		if($time && (time()-$time < 60)){
			//处理不能发送的情况
			echo '两次短信发送间隔必须超过1分钟';exit;
		}
		//每天只能发送20条
		//检查上次发送短信 的时间是否是今天
		if(date('Ymd',$time)<date('Ymd')){
			//最后一次不是今天发送的短信
			$redis->set('count_'.$phone,0);
		}
		$count = $redis->get('count_'.$phone);
		if($count && $count >= 20){
			echo '今天发送次数已超过20次,不能再发送短信.明天再试';exit;
		}

		$code = rand(1000,9999);
		$redis->set('code_'.$phone,$code,90);
		//保存短信发送时间
		$redis->set('time_'.$phone,time(),24*3600);
		$redis->set('count_'.$phone,++$count,24*3600);

		$demo = new SmsDemo(
			"LTAIkpS5QDhLJURK",//AK
			"mcWaNdAHuragDpPgAgFRPVBruZ44fL"//SK
		);

		//echo "SmsDemo::sendSms\n";
		$response = $demo->sendSms(
			"YMC时尚小面馆", // 短信签名
			"SMS_97980004", // 短信模板编号
			"13088038703", // 短信接收者
			Array(  // 短信模板中字段的值
				"code"=>$code,
			)
		);
		if($response->Message == 'OK'){
			echo '发送成功';
		}else{
			echo '发送失败';
		}
		//echo $code;
	}
	//验证手机验证码
	public function actionValidateSms($phone,$sms){

		$redis = new \Redis();
		$redis->connect("127.0.0.1");
		$code = $redis->get('code_'.$phone);
		//
		if($code==null || $code != $sms){
			return 'false';
		}
		//
		$redis->delete('code_'.$phone);
		return 'true';
	}

	//ajax验证用户唯一性
	public function actionValidateUser($username){
		$user = Member::findOne(['username'=>$username]);
		if ($user==null){
			return 'true';
		}else{
			return 'false';
		}
	}
	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [//验证码
				'class' => CaptchaAction::className(),
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
				'minLength' => 4,
				'maxLength' => 4,
			],
		];
	}

	public function actionStr(){
		echo date("Y-m-d",strtotime('-1 day'));
		echo strtotime('2013/12/25');
	}

	//测试redis操作
	public function actionRedis(){
		$redis = new \Redis();
		$redis->connect('127.0.0.1');
		$redis->set('name','张三');
		echo 'OK';
	}
}
