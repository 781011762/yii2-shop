<?php

use yii\db\Migration;

/**
 * Handles the creation of table `nav_list`.
 */
class m170917_064357_create_nav_list_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('nav_list', [
            'id' => $this->primaryKey(),
	        'name' => $this->string()->comment('菜单名'),
	        'url' => $this->string()->comment('菜单地址'),
	        'parent_id' => $this->string()->comment('上级菜单'),
	        'sort' => $this->integer()->comment('排序'),
	        'deep' => $this->smallInteger()->comment('深度')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('nav_list');
    }
}
