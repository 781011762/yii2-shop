<?php

echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
	'href'=>\yii\helpers\Url::to(['article-category/index']),
]).'</div>';

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textarea();
if (isset($model->logo)?$model->logo:false){//如果有图片就回显
	echo '<div>'.\yii\bootstrap\Html::img($model->logo).'</div>';
}
echo $form->field($model,'sort')->textInput(['type'=>'number']);
echo $form->field($model,'status',['inline'=>true])->radioList(['1'=>'显示','0'=>'隐藏']);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();