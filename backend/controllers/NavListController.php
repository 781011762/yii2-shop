<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\NavList;
use yii\data\Pagination;

class NavListController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$query = NavList::find();
        $pager = new Pagination(['totalCount'=>$query->count(),'defaultPageSize'=>5]);
    	$models = $query->limit($pager->limit)->offset($pager->offset)->all();
        return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }
    //添加菜单
    public function actionAdd(){
    	$model = new NavList();
    	$request = \Yii::$app->request;
    	if ($request->isPost){
    		$model->load($request->post());
    		if ($model->validate()){
			    if ($model->url=='0'){
				    $model->url = null;
			    }
    			$model->save();
    			return $this->redirect(['nav-list/index']);
		    }
	    }
    	return $this->render('add',['model'=>$model]);
    }
    //修改菜单
	public function actionEdit($id){
		$model = NavList::findOne($id);
		//回显
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				if ($model->url=='0'){
					$model->url = null;
				}

				$model->save();
				return $this->redirect(['nav-list/index']);
			}
		}
		return $this->render('add',['model'=>$model]);
	}
	//删除菜单
	public function actionDel(){
		$request = \Yii::$app->request;
		$id = \Yii::$app->request->post('id');
		$model = NavList::findOne($id);
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
