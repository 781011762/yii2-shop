<?php
echo '<div style="text-align: right">'.\yii\bootstrap\Html::tag('a','返回列表',['class'=>'btn btn-warning',
		'href'=>\yii\helpers\Url::to(['goods-category/index']),
	]).'</div>';

$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'parent_id')->hiddenInput();
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ztree插件
echo '<div style="margin-bottom: 15px"><ul id="treeDemo" class="ztree"></ul></div>';
//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>ztree结束
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
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
					$('#goodscategory-parent_id').val(treeNode.id);
				}
			}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$ztree_mod};
        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
		zTreeObj.expandAll(true);
		//回显分类
		var node = zTreeObj.getNodeByParam('id',"{$model->parent_id}",null)
		zTreeObj.selectNode(node);
js
));
