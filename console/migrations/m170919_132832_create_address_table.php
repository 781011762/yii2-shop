<?php

use yii\db\Migration;

/**
 * Handles the creation of table `address`.
 */
class m170919_132832_create_address_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('address', [
            'id' => $this->primaryKey(),
	        'member_id' => $this->integer()->comment('用户'),
	        'consignee' => $this->string(50)->comment('收货人'),
	        'prov' => $this->string(10)->comment('省'),
	        'city' => $this->string(10)->comment('市'),
	        'area' => $this->string(10)->comment('区'),
			'de_address' => $this->string()->comment('详细地址'),
	        'tel' => $this->string()->comment('手机号码'),
	        'is_default'=> $this->smallInteger(2)->comment('默认地址'),
	        'create_at' => $this->integer()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('address');
    }
}
