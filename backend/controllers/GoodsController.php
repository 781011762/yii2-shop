<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsIntro;
use backend\models\GoodsSearchForm;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use Yii;
use yii\data\Pagination;

class GoodsController extends \yii\web\Controller
{
    public function actionIndex()
    {
	    $get = Yii::$app->request->get();
	    $where = [];
	    $where[] = 'and';
	    if (isset($get['price_min'])){$where[]=['>','shop_price',$get['price_min']];}
	    if (isset($get['price_max'])){$where[]=['<','shop_price',$get['price_max']];}
	    if (isset($get['name'])){$where[]=['like','name',$get['name']];}
	    if (isset($get['sn'])){$where[]=['like','sn',$get['sn']];}

	    $query = Goods::find()->where(['status'=>'1'])->andFilterWhere($where);
	    $pager = new Pagination(['totalCount'=>$query->count(),'defaultPageSize'=>5]);
	    $models = $query->limit($pager->limit)->offset($pager->offset)->all();
	    return $this->render('index',['models'=>$models,'pager'=>$pager,'get'=>$get]);
    }

    public function actionAdd(){
	    $model = new Goods();//商品模型
	    $int_mod = new GoodsIntro();//内容模型
	    $request = \Yii::$app->request;
	    if ($request->isPost){
	    	$model->load($request->post());
	    	$int_mod->load($request->post());
	    	if ($model->validate()&&$int_mod->validate()){
			    $day = time();
			    $sn_mod = GoodsDayCount::findOne(date('Y-m-d',$day));//查询是否存在这天的记录
			    if (!$sn_mod){
				    $sn_mod = new GoodsDayCount();
				    $sn_mod->day = date('Y-m-d',$day);
			    }
			    $sn_mod->count = ($sn_mod->count)+1;//每添加一个商品 数量加1
			    $sn_mod->save(false);
			    $model->sn = date('Ymd',$day).sprintf('%06s',$sn_mod->count);//生成货号
			    $model->create_time = time();
				$model->save(false);
				$int_mod->goods_id = $model->id;//得到商品ID
				$int_mod->save(false);//保存商品详情
				return $this->redirect(['goods/index']);
	    	}
	    	$model->addError("数据错误!");
	    }

	    $brand_mods = Brand::find()->all();//品牌数据
	    foreach ($brand_mods as $brand_mod){
	    	$sel[$brand_mod->id]=$brand_mod->name;
	    }
    	return $this->render('add',['model'=>$model,'int_mod'=>$int_mod,'sel'=>$sel]);
    }

    public function actionEdit($id){
	    $model = Goods::findOne($id);//商品模型
	    $int_mod = GoodsIntro::findOne($id);//内容模型
	    if (!$int_mod){//如果没找到 就new 一个对象
		    $int_mod = new GoodsIntro();
		    $int_mod->goods_id = $model->id;
	    }
	    $request = \Yii::$app->request;
	    if ($request->isPost){
		    $model->load($request->post());
		    $int_mod->load($request->post());
		    if ($model->validate()&&$int_mod->validate()){
			    $model->create_time = time();
			    $model->save(false);
			    $int_mod->goods_id = $model->id;//得到商品ID
			    $int_mod->save(false);//保存商品详情
			    return $this->redirect(['goods/index']);
		    }
		    $model->addError("数据错误!");
	    }
	    $brand_mods = Brand::find()->all();//品牌数据
	    foreach ($brand_mods as $brand_mod){
		    $sel[$brand_mod->id]=$brand_mod->name;
	    }
	    return $this->render('add',['model'=>$model,'int_mod'=>$int_mod,'sel'=>$sel]);
    }

    public function actionDel(){
	    $id = \Yii::$app->request->post('id');
	    $model = Goods::findOne($id);
	    if ($model){
		    $model->status = 0;
		    $model->save(false);
		    return true;
	    }else{
		    return false;
	    }
    }
    //展示相册
	public function actionGallIndex($id){
    	$model = Goods::findOne($id);
		$gall_mods = GoodsGallery::find()->where(['goods_id'=>$model->id])->all();
		if (!$gall_mods){
			$gall_mods = new GoodsGallery();
		}
		return $this->render('gall',['model'=>$model,'gall_mods'=>$gall_mods]);
	}
	//添加图片
	public function actionGallAdd(){
		$model = new GoodsGallery();
		$request = Yii::$app->request;
		$model->goods_id = $request->post('goods_id');
		$model->path = $request->post('path');
		if ($model->validate()){
			$model->save(false);
			$data = [
				"m"=>true,
				"id"=>$model->id,
				"goods_id"=>$model->goods_id,
				"path"=>"$model->path",
			];
		}else{
			$data = [
				"m"=>false
			];
		}
		return json_encode($data);
	}
	//删除图片
	public function actionGallDel(){
		$model = GoodsGallery::findOne(Yii::$app->request->post('id'));
		if ($model){
			$model->delete();
			return true;
		}
		return false;
	}

	//配置过滤器
	public function behaviors()
	{
		return [
			'rbac'=>[
				'class'=>RbacFilter::className(),
				'except'=>['s-upload'],
			]
		];
	}

	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>上传图片的插件
	public function actions() {
		return [
			's-upload' => [
				'class' => UploadAction::className(),
				'basePath' => '@webroot/upload',//保存路径
				'baseUrl' => '@web/upload',
				'enableCsrf' => true, // default//开启跨站请求(攻击)验证
				'postFieldName' => 'Filedata', // default
				//BEGIN METHOD
				//'format' => [$this, 'methodName'],
				//END METHOD
				//BEGIN CLOSURE BY-HASH
				'overwriteIfExist' => true,
				/*'format' => function (UploadAction $action) {
					$fileext = $action->uploadfile->getExtension();
					$filename = sha1_file($action->uploadfile->tempName);
					return "{$filename}.{$fileext}";
				},*/
				//END CLOSURE BY-HASH
				//BEGIN CLOSURE BY TIME
				'format' => function (UploadAction $action) {
					$fileext = $action->uploadfile->getExtension();//获得后缀
					$filehash = sha1(uniqid() . time());
					$p1 = substr($filehash, 0, 2);
					$p2 = substr($filehash, 2, 2);
					return "{$p1}/{$p2}/{$filehash}.{$fileext}";
				},
				//END CLOSURE BY TIME
				'validateOptions' => [//验证规则
					'extensions' => ['jpg', 'png'],
					'maxSize' => 1 * 1024 * 1024, //file size
				],
				'beforeValidate' => function (UploadAction $action) {
					//throw new Exception('test error');
				},
				'afterValidate' => function (UploadAction $action) {},
				'beforeSave' => function (UploadAction $action) {},
				'afterSave' => function (UploadAction $action) {
					//$action->getFilename(); // "image/yyyymmddtimerand.jpg"
					//$action->getWebUrl(); //  "baseUrl + filename, /upload/image/yyyymmddtimerand.jpg"
					//$action->output['fileUrl'] = $action->getWebUrl();//输出图片的路径
					//$action->getSavePath(); // "/var/www/htdocs/upload/image/yyyymmddtimerand.jpg"
					$qiniu = new Qiniu(\Yii::$app->params['qiniuyun']);
					$key = $action->getFilename();
					$filename = $action->getSavePath();
					//上传文件到七牛云,同时指定一个KEY(相当于索引,或者目录)
					$qiniu->uploadFile($filename,$key);
					$url = $qiniu->getLink($key);
					$action->output['fileUrl'] = $url;//输出图片的路径
				},
			],
			'upload' => [//百度UEditor 插件<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
				'class' => 'kucha\ueditor\UEditorAction',
				'config' => [
					"imageUrlPrefix"  => Yii::getAlias("@web"),//图片访问路径前缀
					"imagePathFormat" => "/upload/ueditor/img/{yyyy}{mm}{dd}/{time}{rand:6}", //上传保存路径
                    "imageRoot" => Yii::getAlias("@webroot"),
                ],
			],
		];
	}
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件结束


}
