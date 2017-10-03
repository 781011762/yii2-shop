<?php
/* @var $this yii\web\View */
?>
<h1>品牌管理</h1>

<?php
echo \yii\bootstrap\Html::a('添加品牌',['brand/add'],['class'=>'btn btn-info']);
?>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>ID</th>
            <th>LOGO</th>
            <th>名称</th>
            <th>排位先后</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
		<?php foreach ($models as $model):?>
            <tr data-id="<?=$model->id?>">
                <td><?=$model->id?></td>
                <td><img src="<?=$model->logo?>" title="头像" width="150px" height="60px" class="img-rounded"></td>
                <td><?=$model->name?></td>
                <td><?=$model->sort?></td>
                <td><?=$model->status?'显示':'隐藏';?>
                </td>
                <td>
                    <a class="btn btn-default" title="修改" href="<?=\yii\helpers\Url::to(['brand/edit','id'=>$model->id])?>">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>
                    <a class="btn btn-default btn-del" title="删除" href="javascript:;" style="margin-left: 20px;color: red">
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
$delUrl = \yii\helpers\Url::to(['brand/del']);
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