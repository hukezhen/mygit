<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Orders extends Migrator
{
    const TB_NAME = 'orders';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('uid', 'integer', ['limit' => 10, 'comment' => '用户UID']);
        $table->addColumn('pid', 'integer', ['limit' => 10, 'comment' => '产品ID']);
        $table->addColumn('moneys', 'decimal', ['limit' => '13,2', 'comment' => '价格']);
        $table->addColumn('number', 'integer', ['limit' => 10, 'comment' => '数量', 'default' => 1]);
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
