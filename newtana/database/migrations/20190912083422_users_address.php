<?php

use think\migration\Migrator;
use think\migration\db\Column;

class UsersAddress extends Migrator
{
    const TB_NAME = 'users_address';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('uid', 'integer', ['limit' => 11, 'comment' => 'UID']);
        $table->addColumn('name', 'string', ['limit' => 50, 'comment' => '姓名']);
        $table->addColumn('region', 'string', ['limit' => 255, 'comment' => '所在地区']);
        $table->addColumn('address', 'string', ['limit' => 255, 'comment' => '详细地址']);
        $table->addColumn('postal_code', 'integer', ['limit' => 6, 'comment' => '邮政编码']);
        $table->addColumn('status', 'integer', ['limit' => 2, 'comment' => '状态 1', 'default' => 1]);
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
