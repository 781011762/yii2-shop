<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Brand;
use yii\data\Pagination;
use flyok666\uploadifive\UploadAction;
use flyok666\qiniu\Qiniu;

class BrandController extends \yii\web\Controller
{
	public function actionAdd(){
		$model = new Brand();
		$request = \Yii::$app->request;//请求组件
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				$model->save(false);
				return $this->redirect(['brand/index']);
			}
			var_dump($model->getErrors());exit();//
		}
		return $this->render('add',['model'=>$model]);
	}

    public function actionIndex()
    {
	    $query = Brand::find();
	    $pager = new Pagination(['totalCount'=>$query->where(['>','status','-1'])->count(),'defaultPageSize'=>5]);
	    $models = $query->where(['>','status','-1'])->limit($pager->limit)->offset($pager->offset)->all();
	    return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionEdit($id){
	    $model = Brand::findOne($id);
	    $request = \Yii::$app->request;//请求组件
	    if ($request->isPost){
		    $model->load($request->post());
		    if ($model->validate()){
			    $model->save(false);
			    return $this->redirect(['brand/index']);
		    }
		    var_dump($model->getErrors());exit();//
	    }
	    return $this->render('add',['model'=>$model]);
    }

	public function actionDel(){
    	$id = \Yii::$app->request->post('id');
    	$model = Brand::findOne($id);
    	if ($model){
		    $model->status = -1;
		    $model->save(false);
		    return true;
    	}else{
    		return false;
	    }
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
		];
	}
	//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件结束
}
