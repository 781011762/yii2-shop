
<?php $form = \yii\bootstrap\ActiveForm::begin()?>
	<div class="form-group">
		<?=$form->field($model,'email')->textInput([
			'type'=>'email',
			'class'=>'form-control',
			'id'=>'exampleInputEmail1',
		'placeholder'=>"Email"
		]);?>
	</div>
	<div class="form-group">
		<?=$form->field($model,'password')->textInput([
			'type'=>'password',
			'class'=>'form-control',
			'id'=>'exampleInputPassword1',
			'placeholder'=>"Password"
		]);?>
	</div>
	<div class="form-group">
<?=$form->field($model,'code')->widget(\yii\captcha\Captcha::className(),[
	'captchaAction'=>'login/captcha',
	'template'=>'<div class="row"><div class="col-lg-1">{image}</div><div class="col-lg-1">{input}</div></div>'
]);?>
	</div>
	<div class="checkbox">
		<label>
			<?=$form->field($model,'remember')->checkbox()->label();?>
		</label>
	</div>
	<button type="submit" class="btn btn-default">登录</button>
<?php \yii\bootstrap\ActiveForm::end()?>