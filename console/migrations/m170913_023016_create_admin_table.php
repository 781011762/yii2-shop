<?php

use yii\db\Migration;

/**
 * Handles the creation of table `admin`.
 */
class m170913_023016_create_admin_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('admin', [
            'id' => $this->primaryKey(),
	        'username' => $this->string()->notNull()->unique()->comment('管理员'),
	        'auth_key' => $this->string(32)->notNull()->comment('认证码'),
	        'password_hash' => $this->string()->notNull()->comment('密码'),
	        'password_reset_token' => $this->string()->unique()->comment('密码重置令牌'),
	        'email' => $this->string()->notNull()->unique()->comment('邮箱'),

	        'status' => $this->smallInteger()->notNull()->defaultValue(10)->comment('状态'),
	        'created_at' => $this->integer()->notNull()->comment('创建日期'),
	        'updated_at' => $this->integer()->notNull()->comment('修改日期'),
	        'last_login_time' =>$this->integer()->comment('最后登录时间'),
	        'last_login_ip' => $this->string(255)->comment('最后登录IP'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('admin');
    }
}
