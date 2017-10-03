<?php
namespace backend\controllers;

use backend\models\LoginForm;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

class LoginController extends Controller {
	//登录页面
	public function actionIndex(){
		$model = new LoginForm();
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());//用户提交的数据
			if ($model->validate()){//验证用户提交的信息
				if ($model->login()){
					//通过认证 -> 跳转到首页
					return $this->redirect(['goods/index']);
				}
			}else{//验证 用户提交的信息没有通过验证->提示
				throw new NotFoundHttpException("数据验证未通过");
			}
		}
		return $this->render('index',['model'=>$model]);
	}
	//注销登录
	public function actionLogout()
	{
		Yii::$app->user->logout();
		return $this->redirect(['login/index']);
	}
	//查看是否登录
	public function actionUserInfo(){
		var_dump(Yii::$app->user->identity);
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
			'captcha' => [//配置验证码
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
				'minLength' => 4,
				'maxLength' => 4,
			],
		];
	}
}
