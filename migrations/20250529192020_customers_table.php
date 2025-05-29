<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CustomersTable extends AbstractMigration
{
    const TABLE_NAME = 'clientes';

    public function up(): void
    {
        $table = $this->table(self::TABLE_NAME);
        $table->addColumn('pedido_id', 'string', ['null' => false])
            ->addColumn('nome', 'string', ['null' => false])
            ->addColumn('email', 'string', ['null' => false])
            ->addColumn('telefone', 'string', ['null' => true])
            ->addTimestamps()
            ->create();
    }

    public function down(): void
    {
        $table = $this->table(self::TABLE_NAME);

        if ($table->exists()) {
            $table->drop()->save();
        }
    }
}
