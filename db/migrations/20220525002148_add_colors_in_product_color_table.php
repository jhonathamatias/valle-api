<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddColorsInProductColorTable extends AbstractMigration
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
        $table = $this->table('product_color');

        $table->insert([
            'name' => 'vermelho',
            'color' => '#f21111'
        ]);
        $table->insert([
            'name' => 'branco',
            'color' => '#ffffff'
        ]);
        $table->insert([
            'name' => 'preto',
            'color' => '#000000'
        ]);
        $table->insert([
            'name' => 'rosa',
            'color' => '#f538c6'
        ]);
        $table->insert([
            'name' => 'cinza',
            'color' => '#bdbbbc'
        ]);
        $table->insert([
            'name' => 'azul',
            'color' => '#3455eb'
        ]);
        $table->insert([
            'name' => 'verde',
            'color' => '#2abf2d'
        ]);

        $table->save();
    }
}
