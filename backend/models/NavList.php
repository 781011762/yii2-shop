<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nav_list".
 *
 * @property integer $id
 * @property string $name
 * @property string $url
 * @property string $parent_id
 * @property integer $deep
 */
class NavList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nav_list';
    }

	public static function getAllUrl(){//获得下拉菜单中的选项
    	$auth = Yii::$app->authManager;
    	$auth->getPermissions();
    	$allUrl = array_keys(Yii::$app->authManager->getPermissions());
    	$allU = [0=>'--请选择--'];
    	if ($allUrl){
    		foreach ($allUrl as $v){
    			$allU[$v] = $v;
		    }
	    }
	    return $allU;//
    }

    public static function getParentList(){//获得上级菜单
	    $models = NavList::find()->where(['parent_id'=>0])->all();
	    $list = [0=>'--顶级菜单--'];
	    if ($models){
		    foreach ($models as $v){
			    $list[$v['id']] = $v['name'];
		    }
	    }
	    return $list;//
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['deep','sort'], 'integer','integerOnly'=>true],
            [['name', 'url', 'parent_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '菜单名',
            'url' => '菜单地址',
            'parent_id' => '上级菜单',
            'deep' => '深度',
            'sort' => '排序',
        ];
    }
}
