<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColumnProductSizeIdInTableProducts extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('products');

        $table->addColumn('product_size_id', 'integer', ['after' => 'price'])->update();
        $table->addForeignKey('product_size_id', 'product_size', ['id'], ['constraint' => 'fk_products_product_size_id'])->update();
    }

    public function down()
    {
        $table = $this->table('products');

        $table->removeColumn('product_size_id')
            ->save();

        $table->dropForeignKey('fk_products_product_size_id')->save();
    }
}
