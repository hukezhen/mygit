<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Gallery extends Migrator
{
    const TB_NAME = 'gallery';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('types_id', 'integer', ['limit' => 10, 'comment' => '分類ID']);
        $table->addColumn('cover', 'string', ['limit' => 255, 'comment' => '封面']);
        $table->addColumn('pid', 'text', ['comment' => '封面', 'null' => true]);
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
