<?php
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
		'href'=>\yii\helpers\Url::to(['rbac/permission-index']),
	]).'</div>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['placeholder'=>"如:admin/index"]);
echo $form->field($model,'description')->textInput(['placeholder'=>"如:查看管理员列表"]);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();