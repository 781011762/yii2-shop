<?php

echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
	'href'=>\yii\helpers\Url::to(['article/index']),
]).'</div>';

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
echo $form->field($model,'article_category_id')->dropDownList($categorys);//分类
echo $form->field($model,'sort')->textInput(['type'=>'number']);//排序
echo $form->field($model,'status',['inline'=>true])->radioList(['1'=>'显示','0'=>'隐藏']);//状态
echo $form->field($model,'intro')->textarea();//标题
echo $form->field($art_d,'content')->textarea();//文章内容
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();