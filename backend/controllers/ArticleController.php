<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;

class ArticleController extends \yii\web\Controller
{
    public function actionIndex()
    {
		$query = Article::find()->where(['>','status','-1']);
		$pager = new Pagination(['totalCount'=>$query->count(),'defaultPageSize'=>10]);
		$models = $query->limit($pager->limit)->offset($pager->offset)->all();
		return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionAdd(){
    	$model = new Article();
    	$art_d = new ArticleDetail();
    	$request = \Yii::$app->request;
    	if ($request->isPost){
    		$model->load($request->post());//填入内容
    		$art_d->load($request->post());//获得内容
    		if ($model->validate() && $art_d->validate()){
    			$model->create_time = time();
    			$model->save(false);//保存文章.
			    $art_d->article_id = $model->id;
			    $art_d->save(false);
    			return $this->redirect(['article/index']);//跳转到列表页
		    }
		    var_dump($model->getErrors());exit();//打印错误信息
	    }
    	$categorys=[];
    	foreach (ArticleCategory::find()->all() as $v){
		    $categorys[$v['id']]=$v['name'];
	    }
    	return $this->render('add',['model'=>$model,'categorys'=>$categorys,'art_d'=>$art_d]);
    }

    public function actionEdit($id){
	    $model = Article::findOne(['id'=>$id]);
	    $art_d = ArticleDetail::findOne(['article_id'=>$id]);//
	    $request = \Yii::$app->request;
	    if ($request->isPost){
		    $model->load($request->post());//填入内容
		    $art_d->load($request->post());//获得内容
		    if ($model->validate() && $art_d->validate()){
			    $model->create_time = time();
			    $model->save(false);//保存文章.
			    $art_d->save(false);
			    return $this->redirect(['article/index']);//跳转到列表页
		    }
		    var_dump($model->getErrors());exit();//打印错误信息
	    }
	    $categorys=[];
	    foreach (ArticleCategory::find()->all() as $v){
		    $categorys[$v['id']]=$v['name'];
	    }
	    return $this->render('add',['model'=>$model,'categorys'=>$categorys,'art_d'=>$art_d]);
    }

    public function actionDel(){
    	$id = \Yii::$app->request->post('id');
    	$model = Article::findOne($id);
    	if ($model){
		    $model->status = -1;
		    $model->save(false);
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
