<?php
/* @var $this yii\web\View */
?>
	<h1>商品分类列表</h1>

<?php
echo \yii\bootstrap\Html::a('添加商品分类',['goods-category/add'],['class'=>'btn btn-info']);
?>
	<table class="table table-bordered table-responsive">
		<tr>
			<th>ID</th>
			<th>名称</th>
			<th>简介</th>
			<th>操作</th>
		</tr>
		<?php foreach ($models as $model):?>
			<tr data-id="<?=$model['id']?>">
				<td><?=$model['id']?></td>
				<td><?=str_repeat('--',$model['depth'])?><?=$model['name']?></td>
				<td><?=$model['intro']?></td>
				<td>
					<a title="修改" class="btn btn-default" href="<?=\yii\helpers\Url::to(['goods-category/edit','id'=>$model['id']])?>">
						<span class="glyphicon glyphicon-pencil"></span>
					</a>
					<a title="删除" href="javascript:;" class="btn btn-default btn-del" style="margin-left: 20px;color: red">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
				</td>
			</tr>
		<?php endforeach;?>
	</table>
<?php
$delUrl = \yii\helpers\Url::to(['goods-category/del']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
	<<<JS
    $('.btn-del').on('click',function(){
      if (confirm('确定删除吗?')){
          var tr = $(this).closest('tr');
          var id = tr.attr('data-id');
          $.post('{$delUrl}',{id:id},function(data) {
            if (data==1){
                alert('删除失败!');
            }else if(data==2){
                alert('删除失败!当前分类还有子类存在');
            }else {
                tr.hide('slow');
            }
          });
      }
    });
JS
));
