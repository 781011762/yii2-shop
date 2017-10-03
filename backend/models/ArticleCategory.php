<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "article_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $intro
 * @property integer $sort
 * @property integer $status
 */
class ArticleCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['intro'], 'string'],
            [['sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => '名称',
			'intro' => '简介',
			'sort' => '排序',
			'status' => '状态',
		];
	}
	//建立与文章表的关系
	public function getArticle(){
		return $this->hasMany(Article::className(),["article_category_id"=>"id"])->where(["status"=>1]);
	}
}
