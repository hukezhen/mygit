<?php

use think\migration\Migrator;

class Product extends Migrator
{
    const TB_NAME = 'product';

    public function up()
    {
        $table = $this->table(self::TB_NAME);
        $table->setCollation('utf8mb4_unicode_ci');
        $table->setEngine('InnoDB');
        $table->addColumn('series', 'integer', ['limit' => 10, 'comment' => '系列']);
        $table->addColumn('brand', 'integer', ['limit' => 10, 'comment' => '品牌']);
        $table->addColumn('vehicle_type', 'integer', ['limit' => 10, 'comment' => '车辆类型']);
        $table->addColumn('motorcycle_type', 'integer', ['limit' => 10, 'comment' => '车型']);
        $table->addColumn('cover', 'integer', ['limit' => 10, 'comment' => '图片']);
        $table->addColumn('recommend_img', 'integer', ['limit' => 10, 'comment' => '图片', 'null' => true]);
        $table->addColumn('part_number', 'string', ['limit' => 128, 'comment' => '零件号']);
        $table->addColumn('diameter', 'string', ['limit' => 128, 'comment' => '尺寸（直径）']);
        $table->addColumn('width', 'string', ['limit' => 128, 'comment' => '尺寸（宽度 J）']);
        $table->addColumn('et', 'string', ['limit' => 128, 'comment' => 'ET']);
        $table->addColumn('olt_hole', 'string', ['limit' => 128, 'comment' => '螺栓孔数']);
        $table->addColumn('pcd', 'string', ['limit' => 128, 'comment' => 'pcd']);
        $table->addColumn('center_hole', 'string', ['limit' => 128, 'comment' => '中心孔']);
        $table->addColumn('tire_width', 'string', ['limit' => 128, 'comment' => '推荐轮胎（宽度）']);
        $table->addColumn('tyre_flat_ratio', 'string', ['limit' => 128, 'comment' => '推荐轮胎（扁平比）']);
        $table->addColumn('rim_diameter', 'string', ['limit' => 128, 'comment' => '推荐轮胎轮辋直径(英寸)']);
        $table->addColumn('load', 'string', ['limit' => 128, 'comment' => '最大载荷']);
        $table->addColumn('surface_state', 'string', ['limit' => 128, 'comment' => '表面状态']);
        $table->addColumn('molding_process', 'string', ['limit' => 128, 'comment' => '成型工艺']);
        $table->addColumn('video_url', 'string', ['limit' => 255, 'comment' => '视频链接']);
        $table->addColumn('details', 'text', ['comment' => '详情图片', 'null' => true]);
        $table->addColumn('sales', 'integer', ['limit' => 10, 'default' => 0]);
        $table->addColumn('price', 'integer', ['limit' => 10, 'default' => 0, 'comment' => '单价']);
        $table->addColumn('sort', 'integer', ['limit' => 10, 'default' => 100]);
        $table->addColumn('stock', 'integer', ['limit' => 10, 'default' => 100]);
        $table->addColumn('recommend', 'integer', ['limit' => 1, 'default' => 0]);
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
