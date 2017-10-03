<h1>商品相册</h1>
<?php
/* @var $this yii\web\View */
use yii\web\JsExpression;

$addUrl = \yii\helpers\Url::to(['goods/gall-add']);//保存图片路径的请求地址
$delUrl = \yii\helpers\Url::to(['goods/gall-del']);//保存图片路径的请求地址

?>
    <ul class="list-group-item">
        <li class="list-group-item active">
            商品名称:<?=$model->name?>&emsp;
            货号:<?=$model->sn?>
        </li>
        <li class="list-group-item" id="gall-img">
			<?php
			if (isset($gall_mods[0])){
				foreach ($gall_mods as $gall_mod){
				    echo '<div data-id="'.$gall_mod->id.'">';
					echo \yii\bootstrap\Html::img($gall_mod->path,['width'=>'50%','class'=>'img-thumbnail']);
				    echo '<button class="btn btn-danger btn-del">删除</button>';
					echo '</div>';
				}
			}else{
				echo "<span id='tipc'>暂无相册,请上传!</span>";
			}
            ?>

        </li>
        <li class="list-group-item">
			<?php
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件按钮
			//外部TAG
			echo \yii\bootstrap\Html::fileInput('test', NULL, ['id' => 'test']);
			echo \flyok666\uploadifive\Uploadifive::widget([
				'url' => yii\helpers\Url::to(['s-upload']),
				'id' => 'test',
				'csrf' => true,
				'renderTag' => false,
				'jsOptions' => [
					'formData'=>['someKey' => 'someValue'],
					'width' => 120,
					'height' => 40,
					'onError' => new JsExpression(<<<EOF
function(file, errorCode, errorMsg, errorString) {
    console.log('The file ' + file.name + ' could not be uploaded: ' + errorString + errorCode + errorMsg);
}
EOF
					),
					'onUploadComplete' => new JsExpression(<<<EOF
function(file, data, response) {
    data = JSON.parse(data);
    if (data.error) {
        console.log(data.msg);
    } else {
        //console.log(data.fileUrl);
        //将地址保存到数据库
          $.post('{$addUrl}',{goods_id:{$model->id},path:data.fileUrl},function(data) {
            var data = $.parseJSON(data);
           
    //console.debug(data);
            if (data.id){
                var html = '<div data-id="'+data.id+'">';
                html += '<img width="50%" src="'+data.path+'" alt="图片">';
                html += '<button class="btn btn-danger btn-del">删除</button>';
                html += '</div>';
       //         console.debug($("#gall-img"));
                $("#tipc").remove();
                $("#gall-img").append(html);
            }else{
                alert('保存失败!');
            }
          });
        //上传成功将图片回显
        $("#img").attr('src',data.fileUrl)
    }
}
EOF
					),
				]
			]);
			//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件结束
			?>
        </li>
    </ul>

<?php
//删除图片
$addUrl = \yii\helpers\Url::to(['goods/gall-add']);
$this->registerJs(new \yii\web\JsExpression(//注册JS代码
        <<<JS
    $('ul').on('click','.btn-del',function(){
        if (confirm('确定删除吗?')){
          var div = $(this).closest('div');
          var id = div.attr('data-id');
          $.post('{$delUrl}',{id:id},function(data) {
                if (data){
                    div.hide('slow');
                }else{
                    alert('删除失败!');
            }
          });
      }
    });
JS
));