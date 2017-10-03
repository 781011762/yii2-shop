<h1>角色列表</h1>
<?php
echo \yii\bootstrap\Html::a('添加角色',['rbac/role-add'],['class'=>'btn btn-info']);
?>
<table class="table table-hover table-bordered">
	<tr class="info">
		<th>角色名</th>
		<th>描述</th>
		<th>操作</th>
	</tr>
	<?php foreach ($roles as $role):?>
		<tr data-id="<?=$role->name?>">
			<td><?=$role->name?></td>
			<td><?=$role->description?></td>
			<td>
				<a title="修改" href="<?=\yii\helpers\Url::to(['rbac/role-edit','name'=>$role->name])?>" class="btn btn-default">
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
$delUrl = \yii\helpers\Url::to(['rbac/role-remove']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
	<<<JS
    $('.btn-del').on('click',function(){
      if (confirm('确定删除吗?')){
          var tr = $(this).closest('tr');
          var id = tr.attr('data-id');
          $.post('{$delUrl}',{name:id},function(data) {
            var data = $.parseJSON(data);
            if (data.status){
                tr.hide('slow');
            }else{
                alert('删除失败!');
            }
          });
      }
    });
JS
));

