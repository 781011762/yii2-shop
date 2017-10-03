<?php
namespace frontend\models;

use yii\base\Model;
use Yii;
use yii\web\Cookie;

class LoginForm extends Model{
	public $username;
	public $password;
	public $remember;

	public function rules()
	{
		return [
			[['username','password'],'required'],
			['remember','string'],
		];
	}

	public function login()
	{
		$member = Member::findOne(['username' => $this->username,'status'=>1]);
		if ($member) {//判断是否有这个用户
			if (Yii::$app->security->validatePassword($this->password, $member->password_hash)) {//判断密码是否正确
				$ip = Yii::$app->request->getUserIP();
				$member->last_login_ip = $ip;//保存登录的IP
				$member->last_login_time = time();
				$member->save(false);
				$user = Yii::$app->user;
				if ($this->remember) {//如果用户选择了记住我
					$user->login($member, 7 * 24 * 3600);//7天自动登录
				} else {
					$user->login($member);
				}
				//>>>>>>>>>>>>>>>>>>>cookie中是否有购物车数据
				//检测是否已经有了购物车
				$cookies = \Yii::$app->request->cookies;
				$cookie = $cookies->get('carts');
				if ($cookie!=null){
					$val = $cookie->value;
					$val = unserialize($val);
					foreach ($val as $goods_id=>$amount){
						$model = Cart::find()->where(['and',['member_id'=>\Yii::$app->user->getId()],['goods_id'=>$goods_id]])->one();
						if ($model){
							$model->amount += $amount;
						}else{
							$model = new Cart();
							$model->goods_id = $goods_id;
							$model->amount = $amount;
							$model->member_id = \Yii::$app->user->getId();
						}
						$model->save(false);

						//删除购物车cookie 避免再次登录时,加入相同的数据
						Yii::$app->response->cookies->remove('carts');
					}
				}
				return true;
			}
		}
		$this->addError('password', '账号或密码错误');
		return false;//密码或账户错误
	}
}