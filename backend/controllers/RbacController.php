<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Admin;
use backend\models\PermissionForm;
use backend\models\RoleForm;
use backend\models\RoleUserForm;
use yii\web\NotFoundHttpException;

class RbacController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
	//添加权限
    public function actionPermissionAdd(){
		$model = new PermissionForm();
		$model->scenario = PermissionForm::SCENARIO_ADD;//选择场景
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				$auth = \Yii::$app->authManager;//1.调用组件
				$permission = $auth->createPermission($model->name);//2.创建新权限
				$permission->description = $model->description;//添加权限描述
				$auth->add($permission);//3.保存权限到数据表
				\Yii::$app->session->setFlash('success','添加成功');
				return $this->redirect(['rbac/permission-index']);
			}
		}
		return $this->render('permission-add',['model'=>$model]);
    }
    //权限列表
    public function actionPermissionIndex(){
    	$auth = \Yii::$app->authManager;//1.调用组件
	    $permissions = $auth->getPermissions();
	    return $this->render('permission-index',['permissions'=>$permissions]);
    }
	//修改权限
	public function actionPermissionEdit($name){
    	$auth = \Yii::$app->authManager;//1.调用组件
		$permission = $auth->getPermission($name);//2.获取权限
		if ($permission == null){
			throw new NotFoundHttpException("权限不存在");
		}

		$model = new PermissionForm();
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				if ($permission->name!==$model->name){
					$permission2 = $auth->getPermission($model->name);
					if ($permission2 !== null){
						$model->addError('name','权限已存在');
					}else{
						$permission->name = $model->name;
						$permission->description = $model->description;
						$auth->update($name,$permission);//5.修改权限
						return $this->redirect(['permission-index']);
					}
				}else{
					$permission->description = $model->description;
					$auth->update($name,$permission);//5.修改权限
					return $this->redirect(['permission-index']);
				}
			}
		}
		$model->name = $permission->name;//3.回显权限
		$model->description = $permission->description;
		return $this->render('permission-add',['model'=>$model]);
	}
	//删除权限
	public function actionPermissionRemove(){
		$name = \Yii::$app->request->post('name');
		$auth = \Yii::$app->authManager;
		$permission = $auth->getPermission($name);

		if ($permission == null){
			$data = [
			'status'=>false
			];
		}else{
			$auth->remove($permission);//删除权限
			$data = [
				'status'=>true
			];
		}
		return json_encode($data);
	}

	//角色列表
	public function actionRoleIndex(){
		$roles = \Yii::$app->authManager->getRoles();//获取所有角色
		return $this->render('role-index',['roles'=>$roles]);
	}
	//添加角色
	public function actionRoleAdd(){
		$model = new RoleForm();//创建表单模型对象
		$request = \Yii::$app->request;
		if ($request->isPost){//判断请求方式
			$model->load($request->post());//加载数据
			$model->scenario = RoleForm::SCENARIO_ADD;//选择场景
			if ($model->validate()){//验证数据
				$auth = \Yii::$app->authManager;//保存角色
				$role = $auth->createRole($model->name);
				$role->description = $model->description;
				$auth->add($role);
				if ($model->permissions){//给角色分配权限
					foreach ($model->permissions as $permissionName){
						$permission = $auth->getPermission($permissionName);
						$auth->addChild($role,$permission);
					}
				}
				\Yii::$app->session->setFlash('success','添加成功');
				return $this->redirect(['rbac/role-index']);
			}
		}
		return $this->render('role-add',['model'=>$model]);//回显
	}
	//修改角色
	public function actionRoleEdit($name){
		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($name);
		if ($role==null){
			throw new NotFoundHttpException('角色不存在');
		}
		$model = new RoleForm();
		$model->name = $role->name;
		$model->description = $role->description;
		$permissions = $auth->getPermissionsByRole($role->name);
		$permission = [];
		if ($permissions){
			foreach ($permissions as $v){
				$permission[] = $v->name;
			}
			$model->permissions = $permission;
		}
		$request = \Yii::$app->request;
		if ($request->isPost){
			$model->load($request->post());
			if ($model->name !== $role->name){//是否更改角色名
				$model->scenario = RoleForm::SCENARIO_ADD;
				if ($model->validate()){
					//移出原来角色的所有权限>>>>>>>>>>如果原来没有权限
					$auth->removeChildren($role);
					//保存角色
					$role->name = $model->name;//角色名没有改变    权限没变  权限改变
					$role->description = $model->description;
					$auth->update($name,$role);
					//保存角色的权限
					if ($model->permissions){//给角色分配权限
						foreach ($model->permissions as $permissionName){
							$permission = $auth->getPermission($permissionName);
							$auth->addChild($role,$permission);
						}
					}
					return $this->redirect(['rbac/role-index']);
				}
			}else{
				if ($model->validate()){
					$role->name = $model->name;//角色名没有改变    权限没变  权限改变
					$role->description = $model->description;
					$auth->update($name,$role);
					$rePerm = array_diff_assoc($permission,$model->permissions);//要去掉的权限
					$addPerm = array_diff_assoc($model->permissions,$permission);//要增加的权限
					if ($rePerm){//给角色分配权限
						foreach ($rePerm as $v){
							$permission = $auth->getPermission($v);
							$auth->removeChild($role,$permission);
						}
					}
					if ($addPerm){
						foreach ($addPerm as $v){
							$permission = $auth->getPermission($v);
							$auth->addChild($role,$permission);
						}
					}
					\Yii::$app->session->setFlash('success','修改成功');
				}
				return $this->redirect(['rbac/role-index']);
			}
		}
		return $this->render('role-add',['model'=>$model]);
	}
	//删除角色
	public function actionRoleRemove(){
		$name = \Yii::$app->request->post('name');
		$auth = \Yii::$app->authManager;
		$role = $auth->getRole($name);
		if ($role==null){
			$data = [
				'status'=>false
			];
		}else{
			$auth->remove($role);
			$data = [
				'status'=>true
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








