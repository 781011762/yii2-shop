<?php
use yii\web\JsExpression;
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
	'href'=>\yii\helpers\Url::to(['goods/index']),
]).'</div>';
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();//名称
echo $form->field($model,'goods_category_id')->hiddenInput(['style'=>'margin:0;padding:0;']);//商品分类ID
//如果$model->id有值 就有$model->goodsCategory->name
echo \yii\bootstrap\Html::input('','',$model->id?$model->goodsCategory->name:'',['disabled'=>'disabled','id'=>'goods_category_name']);
echo $form->field($model,'lv')->hiddenInput()->label(false);//名称

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ztree插件
echo '<div style="margin-bottom: 15px"><ul id="treeDemo" class="ztree"></ul></div>';
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ztree结束

echo $form->field($model,'brand_id')->dropDownList($sel);//品牌分类
echo $form->field($model,'market_price')->textInput(['type'=>'number']);//市场价格
echo $form->field($model,'shop_price')->textInput(['type'=>'number']);//商城价格
echo $form->field($model,'stock')->textInput(['type'=>'number']);//库存
echo $form->field($model,'is_on_sale',['inline'=>true])->radioList(['1'=>'在售','0'=>'下架']);//是否在售
echo $form->field($model,'sort')->textInput(['type'=>'number']);//排序
echo $form->field($model,'logo')->hiddenInput();//商品LOgo

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
        $("#goods-logo").val(data.fileUrl);
        //上传成功将图片回显
        $("#img").attr('src',data.fileUrl)
    }
}
EOF
		),
	]
]);
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>插件结束
echo '<div style="margin-bottom: 20px">'.\yii\bootstrap\Html::img($model->logo,['id'=>'img']).'</div>';//回显图片
echo $form->field($int_mod,'content')->widget('kucha\ueditor\UEditor');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ztree插件
/* @var $this yii\web\View */
//注册静态资源 js  css
//注册js  css文件
$this->registerCssFile('@web/ztree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/ztree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
$ztree_mod = \backend\models\GoodsCategory::getZtree();
$this->registerJs(new \yii\web\JsExpression(
	<<<js
		var treeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
				onClick:function(event, treeId, treeNode) {
					$('#goods-goods_category_id').val(treeNode.id);
					$('#goods_category_name').val(treeNode.name);
					$('#goods-lv').val(treeNode.level);
				}
			}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$ztree_mod};
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
		zTreeObj.expandAll(true);
		//回显分类
		var node = zTreeObj.getNodeByParam('id',"{$model->goods_category_id}",null)
		zTreeObj.selectNode(node);
js
));
//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<ztree结束