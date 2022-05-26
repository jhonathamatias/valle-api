<?php


use Phinx\Seed\AbstractSeed;

class UsersRolesSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return [
            'UsersSeeder'
        ];
    }
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run()
    {
        $users_roles = $this->table('users_roles');

        $users_roles->insert([
            [
                'role_id' => 1,
                'user_id' => 1
            ],
            [
                'role_id' => 2,
                'user_id' => 2
            ],
            [
                'role_id' => 1,
                'user_id' => 3
            ],
            [
                'role_id' => 2,
                'user_id' => 4
            ],
            [
                'role_id' => 1,
                'user_id' => 5
            ],
            [
                'role_id' => 2,
                'user_id' => 6
            ],
        ])->save();
    }
}
