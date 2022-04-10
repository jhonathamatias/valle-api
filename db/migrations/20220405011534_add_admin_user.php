<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class AddAdminUser extends AbstractMigration
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
        $user = $this->table('users');

        $user->insert([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => password_hash('VHECNTN9P69X596D', PASSWORD_BCRYPT),
            'created_at' => '2018-01-01 00:00:00'
        ]);

        $user->save();
    }
}
