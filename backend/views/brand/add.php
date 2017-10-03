<?php
use yii\web\JsExpression;

echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
	'href'=>\yii\helpers\Url::to(['brand/index']),
]).'</div>';

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
echo $form->field($model,'logo')->hiddenInput();
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件
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
        //将地址保存到logo标签上
        $("#brand-logo").val(data.fileUrl);
        //上传成功将图片回显
        $("#img").attr('src',data.fileUrl)
    }
}
EOF
		),
	]
]);
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件结束
echo \yii\bootstrap\Html::img($model->logo,['id'=>'img']);//回显图片
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model,'status',['inline'=>true])->radioList(['1'=>'显示','0'=>'隐藏']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();