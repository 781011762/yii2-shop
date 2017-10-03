<?php

namespace backend\models;

use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Url;

/**
 * This is the model class for table "goods_category".
 *
 * @property integer $id
 * @property integer $tree
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property string $name
 * @property integer $parent_id
 * @property string $intro
 */
class GoodsCategory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['parent_id','name'],'required'],
            [['tree','lft','rgt','depth','parent_id'], 'integer'],
            [['intro'], 'string'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    //获取商品分类的ztree数据
    public static function getZtree(){
	    $ztree_mod = GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
	    array_unshift($ztree_mod,['id'=>0,'parent_id'=>0,'name'=>'顶级分类']);
	    return json_encode($ztree_mod);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tree' => '树id',
            'lft' => '左值',
            'rgt' => '右值',
            'depth' => '层级',
            'name' => '名称',
            'parent_id' => '上级分类',
            'intro' => '简介',
        ];
    }

	public function behaviors() {
		return [
			'tree' => [
				'class' => NestedSetsBehavior::className(),
				'treeAttribute' => 'tree',
				// 'leftAttribute' => 'lft',
				// 'rightAttribute' => 'rgt',
				// 'depthAttribute' => 'depth',
			],
		];
	}

	public function transactions()
	{
		return [
			self::SCENARIO_DEFAULT => self::OP_ALL,
		];
	}

	public static function find()
	{
		return new GoodsQuery(get_called_class());
	}
	//获取首页商品分类
	public static function getGoodsCategories(){
		$redis = new \Redis();
		$redis->connect('127.0.0.1');
		$html = $redis->get('goods_categories');
		if($html===false){
			$html = '';
			$categories1 = self::find()->where(['parent_id'=>0])->all();
			foreach ($categories1 as $i=>$category1){
				$html .= '<div class="cat '.($i?'':'item1').'">';
				$html .= '<h3><a href="'.Url::to(['index/list']).'?id='.$category1->id.'">'.$category1->name.'</a><b></b></h3>';
				$html .= '<div class="cat_detail">';
				foreach ($category1->children(1)->all() as $k=>$category2){
					$html .= '<dl '.($k?'':'class="dl_1st"').'>';
					$html .= '<dt><a href="'.Url::to(['index/list']).'?id='.$category2->id.'">'.$category2->name.'</a></dt>';
					$html .= '<dd>';
					foreach ($category2->children()->all() as $category3){
						$html .= '<a href="'.Url::to(['index/list']).'?id='.$category3->id.'">'.$category3->name.'</a>';
					}
					$html .= '</dd>';
					$html .= '</dl>';
				}
				$html .= '</div>';
				$html .= '</div>';
			}
			//缓存到redis
			$redis->set('goods_categories',$html,24*3600);
		}
		return $html;
	}
	//清除redis中的表单数据
	public static function clearGoodsCategories(){
		$redis = new \Redis();
		$redis->connect('127.0.0.1');
		$redis->delete('goods_categories');
	}
}
