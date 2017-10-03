<?php

namespace frontend\controllers;

use EasyWeChat\Foundation\Application;
use frontend\models\Order;
use frontend\models\OrderGoods;
use yii;
use backend\controllers\GoodsCategoryController;
use frontend\models\Address;
use frontend\models\Cart;
use frontend\models\Goods;
use backend\models\GoodsCategory;
use frontend\models\GoodsGallery;
use frontend\models\Locations;
use yii\data\Pagination;
use yii\web\Cookie;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class IndexController extends \yii\web\Controller
{
	public $enableCsrfValidation = false;
	//首页静态化
	public function actionIndex()
	{
		/*//开启ob
		ob_start();
		//页面输出
		echo '123';
		//获取ob缓存内容
		$data = ob_get_contents();
		//保存到静态页面
		file_put_contents('index.html',$data);*/
		return $this->renderPartial('index');
		//var_dump($data);
	}

	public function actionGoodsC(){
		echo GoodsCategory::getGoodsCategories();
	}
//添加收货地址
	public function actionAddress()
	{
		$request = \Yii::$app->request;
		if ($request->isPost) {
			$model = new Address();
			$model->load($request->post(), '');
			if ($model->validate()) {
				$model->save();
				return $this->redirect(['index/address']);
			} else {
				throw new NotFoundHttpException("数据验证未通过");
			}
		}
		$this->layout = false;
		return $this->render('address');
	}

	//展示已经添加的地址
	public function actionShowAddr()
	{
		$models = Address::find()->where(['member_id' => \Yii::$app->user->getId()])->asArray()->all();
		echo json_encode($models);
	}

	//删除地址
	public function actionDelAddr($id)
	{
		$model = Address::findOne($id);
		if ($model) {
			$model->delete();
			return 'true';
		} else {
			return 'false';
		}
	}

	//修改地址
	public function actionEditAddr($id)
	{
		$model = Address::find()->where(['id' => $id, 'member_id' => \Yii::$app->user->getId()])->one();
		if ($model == null) {
			throw new ForbiddenHttpException("没有找到这这条数据");
		}
		$request = \Yii::$app->request;
		if ($request->isPost) {
			$model->load($request->post(), '');
			if ($model->validate()) {
				$model->save();
				return $this->redirect(['index/address']);
			} else {
				throw new NotFoundHttpException("数据验证未通过");
			}
		}
		$this->layout = false;

		return $this->render('edit-addr', ['model' => $model]);
	}
	//商品列表
	public function actionList($id){//这是点击菜单任何 A标签都能查出数据的方法
		//第一种办法
		/*$model = GoodsCategory::findOne($id);//将GET传过来的ID去查商品分类
		if (!$model){
			throw new NotFoundHttpException("数据验证未通过");
		}
		if ($model->rgt-$model->lft!=1){//如果分类有子类分类 就查出所有的子类(包括没有子类的2级3级)
			$children = GoodsCategory::find()->where(['and',['>','lft',$model->lft],['<','rgt',$model->rgt],['tree'=>$model->tree]])->all();
		}else{
			$children = [$model];
		}
		$list = [];
		foreach ($children as $v){
			if ($v->rgt-$v->lft==1){//如果这个分类没有了子类  就去查这个类有没有商品
				$last = Goods::find()->where(['goods_category_id'=>$v->id])->all();
				if ($last){//有商品就放进数组 (2级分类是没有子类的,有也不影响,如果排序的话,就可能会乱)
					$models = array_merge($last,$list);
				}
			}
		}*/
		//第二种办法  +  分页
		$category = GoodsCategory::findOne($id);
		if ($category==null) {
			throw new NotFoundHttpException("数据验证未通过");
		}
		$query = Goods::find();
		//三种情况  1级分类 2级分类 3级分类
		if($category->depth == 2){//3级分类
			//sql: select * from goods where goods_category_id = $category_id
			$query->andWhere(['goods_category_id'=>$id]);
		}else{
			//1级分类 2级分类
			//$category id = 5
			//3级分类ID  7 8
			//SQL select *  from goods where goods_category_id  in (7,8)
			/* $ids = [];//  [7,8]
			 foreach ($category->children()->andWhere(['depth'=>2])->all() as $category3){
				 $ids[]=$category3->id;
			 }*/
			$ids = $category->children()->select('id')->andWhere(['depth'=>2])->column();
			$query->andWhere(['in','goods_category_id',$ids]);
		}
		$pager = new Pagination();
		$pager->totalCount = $query->count();
		$pager->defaultPageSize = 2;
		$models = $query->limit($pager->limit)->offset($pager->offset)->all();
		$this->layout = false;
		return $this->render('list',['models'=>$models,'pager'=>$pager]);
	}
	//搜索显示商品
	public function actionFromSearch($keyword)
	{
		$ids = \backend\models\Goods::Search($keyword);//获得查到的商品id
		$query = Goods::find()->where(['in','id',$ids]);

		$pager = new Pagination();
		$pager->totalCount = $query->count();
		$pager->defaultPageSize = 2;
		$models = $query->limit($pager->limit)->offset($pager->offset)->all();
		$this->layout = false;

		return $this->render('list',['models'=>$models,'pager'=>$pager]);
	}
	/*public function actionGallery($model){
		if (isset($model->goodsGallery)){
			return $model->goodsGallery;
		}else{
			return GoodsGallery::find()->andWhere(['goods_id'=>10])->asArray()->all();
		}
	}*/
	//商品详情页面
	public function actionGoods($id){
		$model = Goods::findOne($id);
		if ($model==null){
			throw new NotFoundHttpException("数据验证未通过");
		}
		//$galls = $this->actionGallery($model);
		//$a = $model->gallery;
		$this->layout = false;
		return $this->render('goods',['model'=>$model]);
	}
	//添加购物车页面
	public function actionAddToCart(){
		$request = \Yii::$app->request;
		$goods_id = $request->get('goods_id');
		$amount = $request->get('amount');
		if (\Yii::$app->user->isGuest){
			//检测是否已经有了购物车
			$cookies = \Yii::$app->request->cookies;
			$val = $cookies->getValue('carts');
			if ($val){
				$val = unserialize($val);
			}else{
				$val = [];
			}

			//检测购物车中已经有了该商品
			if (array_key_exists($goods_id,$val)){
				$val[$goods_id] += $amount;
			}else{
				$val[$goods_id] = $amount;
			}

			//保存数据到cookie
			$cookies = \Yii::$app->response->cookies;
			$cookie = new Cookie();
			$cookie->name = 'carts';
			$cookie->value = serialize($val);
			$cookie->expire = time()+7*24*3600;//保存7天
			$cookies->add($cookie);
		}else{//登录的用户 将购物车信息保存到数据库
			$model = Cart::find()->andWhere(['and',['goods_id'=>$goods_id],['member_id'=>\Yii::$app->user->getId()]])->one();
			if ($model){
				$model->amount += $amount;
			}else{
				$model = new Cart();
				$model->goods_id = $goods_id;
				$model->amount = $amount;
			}
			if ($model->validate()){
				$model->member_id = \Yii::$app->user->getId();
				$model->save(false);
			}else{
				throw new NotFoundHttpException("数据验证未通过");
			}
		}
		return $this->redirect(['index/cart']);
	}
	//添加成功页面
	//购物车显示页面
	public function actionCart(){
		//判断用户是否登录,
		if (\Yii::$app->user->isGuest){
			//检测是否有购物车
			$cookies = \Yii::$app->request->cookies;
			$val = $cookies->getValue('carts');
			if ($val){
				$val = unserialize($val);
			}else{
				$val = [];//没有购物车
			}
			$carts = $val;
			$models = Goods::find()->where(['in','id',array_keys($val)])->all();
			//var_dump($models);exit();
		}else{//用户登录了
			$Ca = Cart::find()->andWhere(['member_id'=>\Yii::$app->user->getId()])->all();
//var_dump($Ca);exit();
			$models = [];
			$carts = [];
			foreach ($Ca as $model){
				$models[] = Goods::findOne($model->goods_id);
				$carts[$model->goods_id] = $model->amount;
			}
			//var_dump($model->goods);exit();
		}
		return $this->renderPartial('cart',['models'=>$models,'carts'=>$carts]);//不加载默认模板
	}

	//购物车页面(点击加减)->AJAX修改购物车商品数量
	public function actionAjax(){
		// goods_id  amount  2=>1
		$goods_id = \Yii::$app->request->post('goods_id');
		$amount = Yii::$app->request->post('amount');
		if(Yii::$app->user->isGuest){
			$cookies = Yii::$app->request->cookies;
			$value = $cookies->getValue('carts');
			if($value){
				$carts = unserialize($value);
			}else{
				$carts = [];
			}

			//检查购物车中是否存在当前需要添加的商品
			if(array_key_exists($goods_id,$carts)){
				$carts[$goods_id] = $amount;
			}

			$cookies = Yii::$app->response->cookies;
			$cookie = new Cookie();
			$cookie->name = 'carts';
			$cookie->value = serialize($carts);
			$cookie->expire = time()+7*24*3600;//过期时间戳
			$cookies->add($cookie);
		}else{
			$member_id = Yii::$app->user->getId();
			$model = Cart::find()->where(['and',['member_id'=>$member_id],['goods_id'=>$goods_id]])->one();
			if ($model){
				$model->amount = $amount;
				$model->save(false);
			}
		}
	}
	//删除购物车
	public function actionCartDel($goods_id){
		if (Yii::$app->user->isGuest){
			//获得要删除的goods_id
			//获得用户的cookie中的cart购物车
				//判断是否有购物车 .没有 给$carts = []; 后面的代码就不会报错
			//删除对应的gooods数据.
				//删除数据后数据是否为空, 如果为空也没有问题
			//将cookie保存回去.
			$value = Yii::$app->request->cookies->getValue('carts');//
			if ($value){
				$carts = unserialize($value);
			}else{
				$carts =[];
			}
			//var_dump($carts);exit();
			if (array_key_exists($goods_id,$carts)){//如果该商品在数组中
				unset($carts[$goods_id]);//删除数组中的这个值
			}//如果不在不处理
			$cookie = new Cookie();
			$cookie->name = 'carts';
			$cookie->value = serialize($carts);
			$cookie->expire = time()+7*24*3600;//过期时间戳
			Yii::$app->response->cookies->add($cookie);
		}else{
			$member_id = Yii::$app->user->getId();
			$cart = Cart::find()->where(['and',['member_id'=>$member_id],['goods_id'=>$goods_id]])->one();
			if ($cart!=null){
				$cart->delete();
			}
		}
		return $this->redirect(['index/cart']);
	}

	//订单表order
	public function actionOrder(){
		if (Yii::$app->user->isGuest){
			return $this->redirect(['member/login']);
		}
		//获得数据
		$member_id = Yii::$app->user->getId();
		//商品清单
		$carts = Cart::findAll(['member_id'=>$member_id]);//获得用户购物车中的所有数据  在Carts模型中建立关系
		if (empty($carts)){//如果购物车中没有商品 , 就返回商城首页
			return $this->redirect(['index/index']);
		};
		foreach ($carts as $cart){
			if ($cart->amount > $cart->goods->stock){
				throw new NotFoundHttpException("库存不足");
			}
		}
		//收件人数据
		$address = Address::findAll(['member_id'=>$member_id]);
		//送货方式//使用在order模型中加入static属性 定义送货方式
		$shippingMethod = Order::$shippingMethod;
		//支付方式//同上
		$paymentMethod = Order::$paymentMethod;

		$request = Yii::$app->request;
		if ($request->isPost){
			$addr_id = $request->post('addr_id');//地址id
			$order = new Order();
			$order->load($request->post(),'');//获得送货方式id 和 支付方式id
			$order->member_id = $member_id;
			$addr = Address::find()->where(['and',['member_id'=>$member_id],['id'=>$addr_id]])->one();
			$order->name = $addr->consignee;
			$order->province = $addr->prov;
			$order->city = $addr->city;
			$order->area = $addr->area;
			$order->address = $addr->de_address;
			$order->tel = $addr->tel;
			$delivery = Order::$shippingMethod[$order->delivery_id];
			$order->delivery_name = $delivery[0];
			$order->delivery_price = $delivery[1];
			$payment = Order::$paymentMethod[$order->payment_id];
			$order->payment_name = $payment[0];
			$order->status = 1;
			$order->create_time = time();
			$price_totals = 0;
			foreach ($carts as $cart){
				//这个商品的总价
				$price_total = $cart->goods->shop_price*$cart->amount;//价格
				$price_totals += $price_total;//总价格
			}
			$order->total = $price_totals;

			//生成>订单商品详情表
			$transaction = Yii::$app->db->beginTransaction();//开启事物
			try {//这里有bug  订单商品详情没有记录   订单却成功了
				$order->save(false);
				foreach ($carts as $cart) {

					if ($cart->goods->stock < $cart->amount ) {
						//抛出异常
						throw new yii\db\Exception($cart->goods->name."库存不足,不能下单");
					}
					$orderGoods = new OrderGoods();
					$orderGoods->order_id = $order->id;
					$orderGoods->goods_id = $cart->goods_id;
					$orderGoods->goods_name = $cart->goods->name;
					$orderGoods->logo = $cart->goods->goodsGallery[0]->path;
					$orderGoods->price = $cart->goods->shop_price;
					$orderGoods->amount = $cart->amount;
					$orderGoods->total = $orderGoods->price * $orderGoods->amount;
						//保存订单商品详情表
					$orderGoods->save();
						//清空购物车
					$cart->delete();
				}
				//没有异常,提交事物
				$transaction->commit();
				//提交成功->
					//跳转到成功页面
				return $this->redirect(['index/flow3']);
			}catch (yii\db\Exception $e){
				//出现异常 回滚
				$transaction->rollBack();
				$order->delete();
				exit("库存不足");
			}
		}
		//var_dump($shippingMethod);exit();
		//展示表单,分配数据
		return $this->renderPartial('flow2',[
			'address'=>$address,
			'shippingMethod' => $shippingMethod,
			'paymentMethod' => $paymentMethod,
			'carts' => $carts,
		]);
	}
	//订单提交成功
	public function actionFlow3(){
		return $this->renderPartial('flow3');
	}

	//查看订单状态
	public function actionOrderRecord(){
		//准备数据
		$models = Order::find()->where(['member_id'=>Yii::$app->user->getId()])->all();
		//显示页面.分配数据
		return $this->renderPartial('order',['models'=>$models]);
	}

	//省市区 三级联动
	public function actionLocations()
	{
		$request = \Yii::$app->request;
		$models = Locations::find()->where(['parent_id' => $request->get('parent_id')])->asArray()->all();
		if ($models) {
			echo json_encode($models);
		} else {
			echo json_encode(false);
		}
	}
	//测试用户是否登录
	public function actionInfo()
	{
		phpinfo();
	}
	//ajax获取用户登录状态信息
	public function actionUserInfo(){
		$user = Yii::$app->user->identity;
		if($user){
			$isLogin = true;$name = $user->username;
		}else{
			$isLogin = false;$name = '';
		}
		//$isLogin = true;$name = '张三';//模拟登录状态(测试)
		return json_encode(['isLogin'=>$isLogin,'name'=>$name]);
	}
	//ajax获得商品点击数
	public function actionViewTimes($goods_id){
		$redis = new \Redis();
		$redis->connect("127.0.0.1");
		if (!$redis->get("times_".$goods_id)){
			$view_times = Goods::findOne($goods_id)?Goods::findOne($goods_id)->view_times:'';
			$redis->set("times_".$goods_id,$view_times);
		}
		$view_times = $redis->incr("times_".$goods_id);
		if ($view_times%10==0){
			$goods = Goods::findOne($goods_id);
			if ($goods){
				$goods->view_times = $view_times;
				$goods->save(false);
			}
			$redis->delete("times_".$goods_id);
		}
		return $view_times;
	}
	//发送邮件
	public function actionEmail()
	{
		$result = Yii::$app->mailer->compose()
			->setFrom('ymc8803@163.com')//发件人
			->setTo('ymc8803@163.com')//收件人
			->setSubject('账号提醒')
			->setHtmlBody('在中国-安徽-黄山')//设置邮件正文
			->send();
		var_dump($result);
	}
	//微信支付
	public function actionPay($order_id){
/*		$model = Order::findOne(['id'=>$order_id]);
		if ($model===null){//判断定单是否存在
			throw new yii\web\HttpException(404,"订单未找到");
		}
		if ($model->status > 1){//判断是否已支付
			throw  new yii\web\HttpException(404,"订单已支付");
		}*/
		//0.支付配置
		$app = new Application(Yii::$app->params['wechat']);
		$payment = $app->payment;
		//1.创建支付订单

		$attributes = [
			'trade_type'       => 'NATIVE', // JSAPI，NATIVE，APP...//交易类型
			'body'             => 'iPad mini 16G 白色',           //商品描述
			'detail'           => 'iPad mini 16G 白色',           //商品详情
			'out_trade_no'     => '1217752501201407033233368018',//商户订单号
			'total_fee'        => 5388, // 单位：分
			'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
			//'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
			// ...
		];
		$order = new \EasyWeChat\Payment\Order($attributes);

		//2.调统一下单API
		$result = $payment->prepare($order);
		var_dump($result);exit();
		if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
			$prepayId = $result->prepay_id;
		}
	}

	//acf过滤
	public function behaviors()
	{
		return [//以下操作只有用户登录才能访问
			'access' => [
				'class' => yii\filters\AccessControl::className(),
				'only' => ['address','show-addr','del-addr','edit-addr','order','flow3','order-record'],
				'rules' => [
					[
						//'actions' => ['address','show-addr','del-addr','edit-addr','order','flow3','order-record'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
				'denyCallback' => function(){//当没有通过规则时的操作
					return $this->redirect(['member/login']);
				}
			],
		];
	}
}
