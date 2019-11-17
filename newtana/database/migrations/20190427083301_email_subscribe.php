<?php

use think\migration\Migrator;
use think\migration\db\Column;

class EmailSubscribe extends Migrator
{
    const TB_NAME = 'email_subscribe';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('email', 'string', ['limit' => 10, 'comment' => '邮箱']);
        $table->addColumn('ip', 'string', ['limit' => 10, 'comment' => 'IP']);
        $table->addColumn('status', 'integer', ['limit' => 2, 'comment' => '1表示下单成功 2表示支付成功 3表示已发货 4表示已完成', 'default' => 1]);
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
