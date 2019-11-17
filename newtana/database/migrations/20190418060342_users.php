<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Users extends Migrator
{
    const TB_NAME = 'users';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->setId('uid');
        $table->addColumn('type', 'integer', ['limit' => 1, 'default' => 0, 'comment' => '用户账号类型 0 个人 1车企 2经销商']);
        $table->addColumn('username', 'string', ['limit' => 128, 'comment' => '账号']);
        $table->addColumn('password', 'string', ['limit' => 128, 'comment' => '密碼']);
        $table->addColumn('nickname', 'string', ['limit' => 128, 'comment' => '联系人姓名', 'null' => true]);
        $table->addColumn('company', 'string', ['limit' => 128, 'comment' => '公司名称', 'null' => true]);
        $table->addColumn('telephone', 'string', ['limit' => 128, 'comment' => '手机', 'null' => true]);
        $table->addColumn('phone', 'string', ['limit' => 128, 'comment' => '座机', 'null' => true]);
        $table->addColumn('email', 'string', ['limit' => 128, 'comment' => '邮箱', 'null' => true]);
        $table->addColumn('sales', 'integer', ['limit' => 10, 'comment' => '销量', 'default' => 0]);
        $table->addColumn('consumption', 'integer', ['limit' => 10, 'comment' => '消费金额', 'default' => 0]);
        $table->addColumn('address', 'string', ['limit' => 255, 'comment' => '地址', 'null' => true]);
        $table->addColumn('face', 'string', ['limit' => 255, 'comment' => '地址', 'null' => true]);
        $table->addColumn('status', 'integer', ['limit' => 2, 'default' => 1]);
        $table->addColumn('delete_time', 'integer', ['limit' => 10, 'null' => true]);
        $table->addColumn('create_time', 'integer', ['limit' => 11]);
        $table->addColumn('update_time', 'integer', ['limit' => 11]);
        $table->create();
        $this->execute("alter table " . self::TB_NAME . " AUTO_INCREMENT=100;");
    }

    public function down()
    {
        $this->dropTable(self::TB_NAME);
    }
}
