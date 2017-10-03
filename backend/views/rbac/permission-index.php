<?php
/* @var $this yii\web\View */
$this->registerCssFile('@web/datatables/media/css/jquery.dataTables.css')
?>
<h1>权限列表</h1>
<?php
echo \yii\bootstrap\Html::a('添加权限',['rbac/permission-add'],['class'=>'btn btn-info']);
?>
    <table id="table_id_example" class="display">
        <thead>
        <tr style="background-color: #7EC4CC">
            <th>权限名称/路由</th>
            <th>描述</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
<?php foreach ($permissions as $permission):?>
        <tr data-id="<?=$permission->name?>">
            <td><?=$permission->name?></td>
            <td><?=$permission->description?></td>
            <td>
                <a title="修改" href="<?=\yii\helpers\Url::to(['rbac/permission-edit','name'=>$permission->name])?>" class="btn btn-default">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a title="删除" href="javascript:;" class="btn btn-default btn-del" style="color: red">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </td>
        </tr>
<?php endforeach;?>
        </tbody>
    </table>
<table class="table table-hover table-bordered">
	<tr class="info">
	</tr>
</table>
<?php
//注册JS文件,设置依赖
$this->registerJsFile('@web/datatables/media/js/jquery.dataTables.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs(new \yii\web\JsExpression(
        <<<JS
$(document).ready( function () {
    $('#table_id_example').DataTable();
} );
JS
));
$delUrl = \yii\helpers\Url::to(['rbac/permission-remove']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
	<<<JS
    $('.btn-del').on('click',function(){
      if (confirm('确定删除吗?')){
          var tr = $(this).closest('tr');
          var name = tr.attr('data-id');
          $.post('{$delUrl}',{name:name},function(data) {
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


