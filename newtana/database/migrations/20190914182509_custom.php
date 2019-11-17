<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Custom extends Migrator
{
    const TB_NAME = 'custom';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('title', 'string', ['limit' => 255, 'comment' => '定制标题']);
        $table->addColumn('price', 'integer', ['limit' => 11, 'comment' => '基础价格']);
        $table->addColumn('state', 'string', ['limit' => 255, 'comment' => '表面状态']);
        $table->addColumn('cover', 'integer', ['limit' => 11, 'comment' => '封面图']);
        $table->addColumn('cover_path', 'string', ['limit' => 11, 'comment' => '封面图']);
        $table->addColumn('images', 'string', ['limit' => 255, 'comment' => '详情图']);
        $table->addColumn('images_path', 'string', ['limit' => 255, 'comment' => '详情图']);
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
