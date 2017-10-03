<?php
namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\GoodsCategory;
use PHPUnit\Framework\Error\Notice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class GoodsCategoryController extends Controller
{
    public function actionIndex()
    {
	    $models = GoodsCategory::find()->asArray(true)->all();
	    return $this->render('index',['models'=>$models]);
    }

    public function actionAdd(){//添加分类
    	$model = new GoodsCategory();
    	$request = \Yii::$app->request;
    	if ($request->isPost){
			$model->load($request->post());
			if ($model->validate()){
				if ($model->parent_id){
					$par_mod = GoodsCategory::findOne(['id'=>$model->parent_id]);
					$model->prependTo($par_mod);//添加非顶级分类
				}else{
					$model->makeRoot();//添加顶级分类 0
				}
				\Yii::$app->session->setFlash('success','添加成功');
				return $this->redirect(['index']);
			}
			var_dump($model->getErrors());exit();
	    }
    	return $this->render('add',['model'=>$model]);
    }

    public function actionEdit($id){
	    $model = GoodsCategory::findOne(['id'=>$id]);
	    $request = \Yii::$app->request;
	    if ($request->isPost){
		    $model->load($request->post());
		    if ($model->validate()){
			    if ($model->parent_id){
				    $par_mod = GoodsCategory::findOne(['id'=>$model->parent_id]);
				    $model->prependTo($par_mod);//添加非顶级分类
			    }else{
				    $model->makeRoot();//添加顶级分类 0
			    }
			    \Yii::$app->session->setFlash('success','修改成功');
			    return $this->redirect(['index']);
		    }
		    var_dump($model->getErrors());exit();
	    }
	    return $this->render('add',['model'=>$model]);
    }

    public function actionDel(){
	    $id = \Yii::$app->request->post('id');
	    $model = GoodsCategory::findOne($id);
	    if ($model){
	    	if ($model->depth==0){
	    		if ($model->rgt-$model->lft!=1){
					return 2;//当前分类还有子类不能删除
	    		}
			    $model->deleteWithChildren();//删除顶级菜单
		    }else{
			    $model->delete();
		    }
		    GoodsCategory::clearGoodsCategories();//清除商品列表的redis缓存
		    return 3;//删除成功
	    }else{
		    return 1;//数据出错,分类不存在
	    }
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
