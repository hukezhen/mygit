<?php

use think\migration\Migrator;
use think\migration\db\Column;


class ProductType extends Migrator
{
    const TB_NAME = 'product_type';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('title', 'string', ['limit' => 128, 'comment' => '标题']);
        $table->addColumn('title_en', 'string', ['limit' => 128, 'comment' => '标题(英文)', 'null' => true]);
        $table->addColumn('type', 'integer', ['limit' => 2, 'comment' => '类型 1表示系列 2品牌 3车辆类型 4车型 5表面状态 6工艺', 'default' => 1]);
        $table->addColumn('multiple', 'integer', ['limit' => 1, 'comment' => '是否多选 1是 0否', 'default' => 0]);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 100]);
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
