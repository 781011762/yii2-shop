<?php
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
		'href'=>\yii\helpers\Url::to(['rbac/role-index']),
	]).'</div>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['placeholder'=>"如:超级管理员(或者admin...)"]);
echo $form->field($model,'description')->textInput(['placeholder'=>"如:超级管理员"]);
echo $form->field($model,'permissions',['inline'=>true])->checkboxList(\backend\models\RoleForm::getPermissionItems());
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();