<?php
namespace console\controllers;

use yii\console\Controller;
use backend\models\Order;

class TaskController extends Controller{
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
            //iconv('输入格式','输出格式','内容');//
            //mb_convert_encoding();
            echo '清理完成'.date('Y-m-d H:i:s')."\n";
        }
		//操作  在cmd中 如数据迁移的写法一样  输入命令  yii task/clean  就开始执行了
    }
}