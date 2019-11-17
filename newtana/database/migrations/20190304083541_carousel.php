<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Carousel extends Migrator
{
    const TB_NAME = 'carousel';

    public function change()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('type', 'integer', ['limit' => 1, 'comment' => '1首页 2轮廓', 'default' => 1]);
        $table->addColumn('cover', 'integer', ['limit' => 10, 'comment' => '封面']);
        $table->addColumn('title', 'string', ['limit' => 255, 'comment' => '标题']);
        $table->addColumn('url', 'string', ['limit' => 255, 'comment' => '跳转链接']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'comment' => '排序', 'default' => 100]);
        $table->addColumn('status', 'integer', ['limit' => 1, 'default' => 1, 'comment' => '状态']);
        $table->addColumn('create_time', 'integer', ['limit' => 10, 'comment' => '创建时间']);
        $table->addColumn('update_time', 'integer', ['limit' => 10, 'comment' => '更新时间']);
        $table->create();
        $this->execute("alter table " . self::TB_NAME . " AUTO_INCREMENT=100;");
    }

    public function down()
    {
        $this->dropTable(self::TB_NAME);
    }
}
