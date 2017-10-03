<?php
/* @var $this yii\web\View */
?>
<h1>商品管理</h1>

<?php
echo \yii\bootstrap\Html::a('添加商品',['goods/add'],['class'=>'btn btn-info']);
?>
<form href="">
        <div class="row">
            <div class="col-lg-2" style="padding: 0 0 0 15px">
                <div class="input-group">
                    <input <?=isset($get['name'])?"value='{$get["name"]}'":''?> type="text" name="name" class="form-control" placeholder="名称"/>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
            <div class="col-lg-2" style="padding: 0px">
                <div class="input-group">
                    <input <?=isset($get['sn'])?"value='{$get["sn"]}'":''?> type="number" name="sn" class="form-control" placeholder="货号"/>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
            <div class="col-lg-2" style="padding: 0 5px 0 15px;width: 200px">
                <div class="input-group">
                    <div class="input-group" style="float: left;">
                        <input <?=isset($get['price_min'])?"value='{$get["price_min"]}'":''?> type="number" name="price_min" class="form-control" placeholder="1000"/>
                        <span class="input-group-addon">.00</span>
                    </div>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
            <div class="col-lg-2" style="padding: 0 15px 0 5px;width: 200px">
                <div class="input-group">
                    <div class="input-group" style="float: left;">
                        <input <?=isset($get['price_max'])?"value='{$get["price_max"]}'":''?> type="number" name="price_max" class="form-control" placeholder="2000"/>
                        <span class="input-group-addon">.00</span>
                    </div>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
            <div class="col-lg-2" style="padding: 0px">
                <div class="input-group">
                    <button type="submit" class="btn btn-primary" style="width: 100px">搜索</button>
                </div><!-- /input-group -->
            </div><!-- /.col-lg-6 -->
        </div><!-- /.row -->
</form>
    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <th>商品名称</th>
            <th>货号</th>
            <th>LOGO图片</th>
            <th>商品价格</th>
            <th>库存</th>
            <th>是否在售</th>
            <th>排序</th>
            <th>添加时间</th>
            <th>浏览次数</th>
            <th>操作</th>
        </tr>
		<?php foreach ($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td><?=$model->id?></td>
                <td><?=$model->name?></td>
                <td><?=$model->sn?></td>
                <td><img src="<?=$model->logo?>" title="logo图片" width="100px" height="50px"></td>
                <td><?=$model->shop_price?></td>
                <td><?=$model->stock?></td>
                <td><?=$model->is_on_sale?'在售':'下架';?></td>
                <td><?=$model->sort?></td>
                <td><?=date('Y-m-d H:i:s',$model->create_time)?></td>
                <td><?=$model->view_times?></td>
                <td>
                    <a title="商品相册" href="<?=\yii\helpers\Url::to(['goods/gall-index','id'=>$model->id])?>" class="btn btn-default" style="color: blueviolet">
                        <span class="glyphicon glyphicon-camera"></span>
                    </a>
                    <a title="修改" href="<?=\yii\helpers\Url::to(['goods/edit','id'=>$model->id])?>" class="btn btn-default" >
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a title="删除" href="javascript:;" class="btn btn-default btn-del" style="color: red">
                        <span class="glyphicon glyphicon-remove"></span>
                    </a>
                </td>
            </tr>
		<?php endforeach;?>
    </table>
<?php
//分页工具条
echo \yii\widgets\LinkPager::widget([
	'pagination'=>$pager,
	'nextPageLabel'=>'下一页'
]);
$delUrl = \yii\helpers\Url::to(['goods/del']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
	<<<JS
    $('.btn-del').on('click',function(){
      if (confirm('确定删除吗?')){
          var tr = $(this).closest('tr');
          var id = tr.attr('data-id');
          $.post('{$delUrl}',{id:id},function(data) {
            if (data){
                tr.hide('slow');
            }else{
                alert('删除失败!');
            }
          });
      }
    });
JS
));

