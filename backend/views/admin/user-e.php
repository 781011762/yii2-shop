<h1>修改密码</h1>
<?php $form = \yii\bootstrap\ActiveForm::begin()?>
	<div class="form-group">
		<?=$form->field($model,'oldP')->textInput([
			'type'=>'password',
            'class'=>'form-control',
		    'placeholder'=>"旧密码"
		]);?>
	</div>
	<div class="form-group">
		<?=$form->field($model,'newP')->textInput([
			'type'=>'password',
			'class'=>'form-control',
			'placeholder'=>"新密码"
		]);?>
	</div>
    <div class="form-group">
        <?=$form->field($model,'newP2')->textInput([
            'type'=>'password',
            'class'=>'form-control',
            'placeholder'=>"请再次输入"
        ]);?>
    </div>
	<div class="form-group">
		<label for="exampleInputFile">File input</label>
		验证码
		<p class="help-block">Example block-level help text here.</p>
	</div>
	<button type="submit" class="btn btn-default">确认修改</button>
<?php \yii\bootstrap\ActiveForm::end()?>