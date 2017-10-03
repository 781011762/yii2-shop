<?php
namespace backend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\Order;
use backend\models\SphinxClient;
use yii\data\Pagination;
use yii\web\Controller;

class SysController extends Controller{
    //生成首页静态页
    public function actionIndexStatic(){
        GoodsCategory::clearGoodsCategories();//清除redis缓存
    	$data = $this->renderPartial('@frontend/views/index/index.php');
        //var_dump($data);
        file_put_contents(\Yii::getAlias('@frontend/web/html/index.html'),$data);
        echo '首页静态化完成';
    }

    //手动清理超时未支付订单(24小时)
    public function actionClean(){
        //设置脚本执行时间(不终止)
        set_time_limit(0);
        //当前时间 - 创建时间 > 24小时   ---> 创建时间 <  当前时间 - 24小时
        //超时未支付订单
        //sql: update order set status=0 where status = 1 and create_time < time()-24*3600
        while (true){
            \frontend\models\Order::updateAll(['status'=>0],'status = 1 and create_time < '.(time()-24*3600));
            //每隔一秒执行一次
            sleep(1);
            echo '清理完成'.date('Y-m-d H:i:s');
        }
    }

}