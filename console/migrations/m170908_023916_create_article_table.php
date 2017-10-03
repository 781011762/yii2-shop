<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article`.
 */
class m170908_023916_create_article_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey(),
	       /* name	varchar(50)	名称
			intro	text	简介
			article_category_id	int()	文章分类id
			sort	int(11)	排序
			status	int(2)	状态(-1删除 0隐藏 1正常)
			create_time	int(11)	创建时间*/
	        'name' => $this->string(50),
	        'intro' => $this->text(),
	        'article_category_id' => $this->integer(),
	        'sort' => $this->integer(11),
	        'status' => $this->integer(2),
	        'create_time' => $this->integer(11),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article');
    }
}
