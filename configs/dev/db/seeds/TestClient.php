<?php


use Phinx\Seed\AbstractSeed;

class TestClient extends AbstractSeed
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
        $data = [
            [
                'id' => 1,
                'client_id' => 'test',
                'name' => 'Test Client',
                'secret' => password_hash('test123', PASSWORD_DEFAULT),
                'active' => 1,
            ]
        ];

        $table = $this->table('gems__oauth_clients');
        $table->insert($data)
            ->saveData();
    }
}
