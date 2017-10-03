<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\ArticleCategory;
use yii\data\Pagination;

class ArticleCategoryController extends \yii\web\Controller
{
	public function actionIndex()
	{
		$query = ArticleCategory::find();
		$pager = new Pagination(['totalCount'=>$query->count(),'defaultPageSize'=>5]);
		$models = $query->limit($pager->limit)->offset($pager->offset)->all();
		return $this->render('index',['models'=>$models,'pager'=>$pager]);
	}

	public function actionAdd(){
		$model = new ArticleCategory();
		$request = \Yii::$app->request;//请求组件
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				$model->save(false);
				return $this->redirect(['article-category/index']);
			}
			var_dump($model->getErrors());exit();//
		}
		return $this->render('add',['model'=>$model]);
	}

	public function actionEdit($id){
		$model = ArticleCategory::findOne($id);
		$request = \Yii::$app->request;//请求组件
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				$model->save(false);
				return $this->redirect(['article-category/index']);
			}
			var_dump($model->getErrors());exit();//
		}
		return $this->render('add',['model'=>$model]);
	}

	public function actionDel(){
		$id = \Yii::$app->request->post('id');
		$model = ArticleCategory::findOne($id);//找到数据
		if ($model){
			$model->delete();
			$data = [
				'status'=>true
			];
		}else{
			$data = [
				'status'=>false
			];
		}
		return json_encode($data);
	}

//配置过滤器
	public function behaviors()
	{
		return [
			'rbac'=>[
				'class'=>RbacFilter::className(),
			]
		];
	}

}
