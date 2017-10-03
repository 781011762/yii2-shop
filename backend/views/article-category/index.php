<?php
/* @var $this yii\web\View */
?>
<h1>文章分类</h1>

<?php
echo \yii\bootstrap\Html::a('添加分类',['article-category/add'],['class'=>'btn btn-info']);
?>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>简介</th>
            <th>排位先后</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
		<?php foreach ($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td><?=$model->id?></td>
                <td><?=$model->name?></td>
                <td><?=$model->intro?></td>
                <td><?=$model->sort?></td>
                <td><?=$model->status?'显示':'隐藏';?>
                </td>
                <td>
                    <a title="修改" href="<?=\yii\helpers\Url::to(['article-category/edit','id'=>$model->id])?>">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a title="删除" href="javascript:;" style="margin-left: 20px;color: red">
                        <span class="glyphicon glyphicon-remove btn-del"></span>
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
$delUrl = \yii\helpers\Url::to(['article-category/del']);
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


