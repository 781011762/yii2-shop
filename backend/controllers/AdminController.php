<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Admin;
use backend\models\UserEForm;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;

class AdminController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$query = Admin::find();
	    $pager = new Pagination(['totalCount'=>$query->count(),'defaultPageSize'=>8]);
	    $models = $query->limit($pager->limit)->offset($pager->offset)->all();
	    return $this->render('index',['models'=>$models,'pager'=>$pager]);
    }

    public function actionAdd(){
	    $model = new Admin();
	    $model->scenario = Admin::SCENARIO_ADD;//指定场景为 SCENARIO_ADD
	    $request = \Yii::$app->request;
	    if ($request->isPost){
		    $model->load($request->post());
		    if ($model->validate()){//在模型中写重写beforSave方法将保存前的操作交给模型
			    $model->save(false);
			    $model->addRoles();//添加角色
			    return $this->redirect(['admin/index']);
		    }
		    \Yii::$app->session->setFlash("error","验证未通过");
	    }
	    return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
		$model = Admin::findOne($id);
		if ($model==null){
			throw new NotFoundHttpException("用户不存在");
		}
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				$model->save(false);
				$model->editRoles();//添加角色
				return $this->redirect(['admin/index']);
			}
			\Yii::$app->session->setFlash("error","验证未通过");
		}
		$model->roles = array_keys(\Yii::$app->authManager->getRolesByUser($id));
		return $this->render('add',['model'=>$model]);
    }

    public function actionDel(){
	    $id = \Yii::$app->request->post('id');
	    $model = Admin::findOne($id);
	    if ($model){
	    	\Yii::$app->authManager->revokeAll($id);//清除这个用户的所有身份
	    	$model->delete();
			$data = [
				'status'=>true,
			];
	    }else{
	    	$data = [
	    		'status'=>false
		    ];
	    }
	    return json_encode($data);
    }
    //用户自己修改自己的密码
    public function actionUserE(){
    	$user = \Yii::$app->user;
    	if ($user->isGuest){//判断是否是游客
    		\Yii::$app->session->setFlash('error','请先登陆');
    		return $this->redirect(['login/index']);
	    }
    	$model = new UserEForm();
    	$request = \Yii::$app->request;
    	if ($request->isPost){//
    		$model->load($request->post());
    		if ($model->validate()){//y验证用户提交的数据
			    $admin = $user->identity;
			    $admin->password = $model->newP2;
			    $admin->save();
			    return $this->redirect(['admin/User-e']);
		    }
	    }
		$user = \Yii::$app->user->identity;//取出用户信息
        return $this->render('user-e',['model'=>$model]);
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
