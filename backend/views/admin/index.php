<?php
/* @var $this yii\web\View */
?>
<h1>管理员列表</h1>

<?php
echo \yii\bootstrap\Html::a('添加管理员',['admin/add'],['class'=>'btn btn-info']);
?>
    <table class="table table-condensed">
        <tr>
            <th>ID</th>
            <th>管理员</th>
            <th>邮箱</th>
            <th>状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
		<?php foreach ($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td><?=$model->id?></td>
                <td><?=$model->username?></td>
                <td><?=$model->email?></td>
                <td><?=$model->status==10?'启用':'未启用'?></td>
                <td><?=date("Y-m-d H:i:s",$model->created_at)?></td>
                </td>
                <td>
                    <a title="修改" href="<?=\yii\helpers\Url::to(['admin/edit','id'=>$model->id])?>" class="btn btn-default">
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
$delUrl = \yii\helpers\Url::to(['admin/del']);
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

