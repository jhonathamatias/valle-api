<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class TableProductSize extends AbstractMigration
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
        $table = $this->table('product_size');

        $table->addColumn('size', 'string')
        ->create();

        $table->insert(['size' => 'PP']);
        $table->insert(['size' => 'P']);
        $table->insert(['size' => 'M']);
        $table->insert(['size' => 'G']);
        $table->insert(['size' => 'GG']);
        $table->insert(['size' => 'XG']);
        $table->insert(['size' => 'XGG']);
        $table->insert(['size' => 'EG']);
        $table->insert(['size' => 'EGG']);

        $table->save();
    }
}
