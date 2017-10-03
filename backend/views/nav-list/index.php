<?php
/* @var $this yii\web\View */
?>
    <h1>导航栏列表</h1>

<?php
echo \yii\bootstrap\Html::a('添加菜单',['nav-list/add'],['class'=>'btn btn-info']);
?>
    <table class="table table-condensed">
        <tr>
            <th>ID</th>
            <th>菜单名称</th>
            <th>URL/路由</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
		<?php foreach ($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td><?=$model->id?></td>
                <td><?=$model->name?></td>
                <td><?=$model->url?></td>
                <td><?=$model->sort?></td>
                <td>
                    <a title="修改" href="<?=\yii\helpers\Url::to(['nav-list/edit','id'=>$model->id])?>" class="btn btn-default">
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
$delUrl = \yii\helpers\Url::to(['nav-list/del']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
	<<<JS
    $('.btn-del').on('click',function(){
      if (confirm('确定删除吗?')){
          var tr = $(this).closest('tr');
          var id = tr.attr('data-id');
          $.post('{$delUrl}',{id:id},function(data) {
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

