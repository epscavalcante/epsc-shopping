<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ProductsTable extends AbstractMigration
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
    public function up(): void
    {
        // create the table
        $table = $this->table('produtos', ['id' => false, 'primary_key' => ['produto_id']]);
        $table->addColumn('produto_id', 'uuid', ['null' => false])
            ->addColumn('nome', 'string', ['null' => false])
            ->addColumn('preco', 'integer', ['null' => false])
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $table = $this->table('produtos');

        if ($table->exists()) {
            $table->drop()->save();
        }
    }
}
