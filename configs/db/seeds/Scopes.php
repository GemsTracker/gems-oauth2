<?php


use Phinx\Seed\AbstractSeed;

class Scopes extends AbstractSeed
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
                'name' => 'all',
                'description' => 'Everything',
                'active' => 1,
            ]
        ];

        $table = $this->table('oauth_scopes');
        $table->insert($data)
            ->saveData();
    }
}
