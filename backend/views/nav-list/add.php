<?php
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
		'href'=>\yii\helpers\Url::to(['nav-list/index']),
	]).'</div>';

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'url')->dropDownList(\backend\models\NavList::getAllUrl());
echo $form->field($model,'parent_id')->dropDownList(\backend\models\NavList::getParentList());
echo $form->field($model,'name')->textInput();
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();