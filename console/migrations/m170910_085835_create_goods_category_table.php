<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_category`.
 */
class m170910_085835_create_goods_category_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods_category', [
            'id' => $this->primaryKey(),
	        'tree' => $this->integer()->notNull()->comment("树ID"),
	        'lft' => $this->integer()->notNull(),
	        'rgt' => $this->integer()->notNull(),
	        'depth' => $this->integer()->notNull()->comment("分层"),
	        'name' => $this->string()->notNull()->comment("分类名称"),
	        'parent_id' =>$this->integer()->notNull()->comment("上级分类"),
	        'intro' => $this->text()->comment('简介')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_category');
    }
}
