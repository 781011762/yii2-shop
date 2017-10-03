<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "goods".
 *
 * @property integer $id
 * @property string $name
 * @property string $sn
 * @property string $logo
 * @property integer $goods_category_id
 * @property integer $brand_id
 * @property string $market_price
 * @property string $shop_price
 * @property integer $stock
 * @property integer $is_on_sale
 * @property integer $status
 * @property integer $sort
 * @property integer $create_time
 * @property integer $view_times
 */
class Goods extends \yii\db\ActiveRecord
{
	public $lv;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'goods';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        	[['goods_category_id', 'brand_id'],'required'],
            [['goods_category_id', 'brand_id', 'stock', 'is_on_sale', 'status', 'sort', 'create_time', 'view_times'], 'integer'],
            [['market_price', 'shop_price'], 'number'],
            [['name', 'sn'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
	        [['lv'],'integer','max'=>3,'min'=>3,'tooBig'=>'必须选择3层分类','tooSmall'=>'必须选择3层分类'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '商品名称',
            'sn' => '货号',
            'logo' => 'LOGO图片',
            'goods_category_id' => '商品分类',
            'brand_id' => '品牌分类',
            'market_price' => '市场价格',
            'shop_price' => '商品价格',
            'stock' => '库存',
            'is_on_sale' => '是否在售',
            'status' => '状态',
            'sort' => '排序',
            'create_time' => '添加时间',
            'view_times' => '浏览次数',
        ];
    }

	//中文分词搜索
	public static function Search($keyword)
	{
		$cl = new SphinxClient();
		$cl->SetServer('127.0.0.1', 9312);//sphinx服务配置
//$cl->SetServer ( '10.6.0.6', 9312);
//$cl->SetServer ( '10.6.0.22', 9312);
//$cl->SetServer ( '10.8.8.2', 9312);
		$cl->SetConnectTimeout(10);  //超时
		$cl->SetArrayResult(true);//设置响应格式
// $cl->SetMatchMode ( SPH_MATCH_ANY);
		$cl->SetMatchMode(SPH_MATCH_ANY);//设置匹配模式
		$cl->SetLimits(0, 1000);             //设置查询结果范围
		$info = "$keyword";
		$res = $cl->Query($info, 'goods');//shopstore_search
//print_r($cl);
		$ids=[];
		if (isset($res['matches'])){
			//查询到了
			foreach ($res['matches'] as $match){
				$ids[] = $match['id'];
			}
		}else{
			//没有查询到
		}
		//print_r($res);
		return $ids;//返回商品id
	}

		//建立与品牌表的关系 Brand
	public function getBrand(){
		return $this->hasOne(Brand::className(),['id'=>'brand_id']);
	}
	//建立与商品分类表的关系 GoodsCategory
	public function getGoodsCategory(){
		return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
	}
	//建立与照片的关系
	public function getGoodsGallery(){
		return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
	}
}
