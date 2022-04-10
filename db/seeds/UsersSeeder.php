<?php


use Phinx\Seed\AbstractSeed;

class UsersSeeder extends AbstractSeed
{
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
        $users = $this->table('users');

        $faker = Faker\Factory::create('pt_BR'); 

        $users->insert([
            [
                'id' => 2,
                'name' => $faker->name,
                'email' => mb_strtolower($faker->email),
                'password' => password_hash(123456, PASSWORD_BCRYPT),
                'user_type_id' => 1,
                'created_at' => date("Y-m-d H:i:s", strtotime('-10 DAY')),
            ],
            [
                'id' => 3,
                'name' => $faker->name,
                'email' => mb_strtolower($faker->email),
                'password' => password_hash(123456, PASSWORD_BCRYPT),
                'user_type_id' => 1,
                'created_at' => date("Y-m-d H:i:s", strtotime('-10 DAY')),
            ],
            [
                'id' => 4,
                'name' => $faker->name,
                'email' => mb_strtolower($faker->email),
                'password' => password_hash(123456, PASSWORD_BCRYPT),
                'user_type_id' => 1,
                'created_at' => date("Y-m-d H:i:s", strtotime('-10 DAY')),
            ],
            [
                'id' => 5,
                'name' => $faker->name,
                'email' => mb_strtolower($faker->email),
                'password' => password_hash(123456, PASSWORD_BCRYPT),
                'user_type_id' => 1,
                'created_at' => date("Y-m-d H:i:s", strtotime('-10 DAY')),
            ],
            [
                'id' => 6,
                'name' => $faker->name,
                'email' => mb_strtolower($faker->email),
                'password' => password_hash(123456, PASSWORD_BCRYPT),
                'user_type_id' => 1,
                'created_at' => date("Y-m-d H:i:s", strtotime('-10 DAY')),
            ]
        ])->save();
    }
}
