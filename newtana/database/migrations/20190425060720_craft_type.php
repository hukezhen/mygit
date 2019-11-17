<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CraftType extends Migrator
{
    const TB_NAME = 'craft_type';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('pid', 'integer', ['limit' => 10, 'comment' => '上級ID', 'default' => 0]);
        $table->addColumn('title', 'string', ['limit' => 128, 'comment' => '標題']);
        $table->addColumn('title_en', 'string', ['limit' => 128, 'comment' => '標題']);
        $table->addColumn('status', 'integer', ['limit' => 2, 'default' => 1]);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 100]);
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