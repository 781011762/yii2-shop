<?php
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
		'href'=>\yii\helpers\Url::to(['admin/index']),
	]).'</div>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();//名称
echo $form->field($model,'password')->passwordInput();//密码
echo $form->field($model,'email')->textInput(['type'=>'email']);//邮箱
echo $form->field($model,'status')->radioList([10=>'正常',0=>'禁用']);//状态
echo $form->field($model,'roles')->checkboxList($model->allRoles);//状态

echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();