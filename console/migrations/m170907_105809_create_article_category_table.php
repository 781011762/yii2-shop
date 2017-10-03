<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_category`.
 */
class m170907_105809_create_article_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article_category', [
            'id' => $this->primaryKey(),
	   /*     id	primaryKey
			name	varchar(50)	名称
			intro	text	简介
			sort	int(11)	排序
			status	int(2)	状态(-1删除 0隐藏 1正常)*/
	        'name' => $this->string(50),
	        'intro' => $this->text(),
	        'sort' => $this->integer(11),
	        'status' => $this->smallInteger(2),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_category');
    }
}
