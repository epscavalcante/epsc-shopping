<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

class ProductSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            [
                'produto_id' => Uuid::uuid4()->toString(),
                'nome' => 'Produto 1',
                'preco' => 450
            ],

            [
                'produto_id' => Uuid::uuid4()->toString(),
                'nome' => 'Produto 2',
                'preco' => 712
            ],

            [
                'produto_id' => Uuid::uuid4()->toString(),
                'nome' => 'Produto 3',
                'preco' => 789
            ],
        ];

        $productsTable = $this->table('produtos');
        $productsTable->insert($data)
            ->saveData();
    }
}
