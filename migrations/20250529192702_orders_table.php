<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OrdersTable extends AbstractMigration
{
    const TABLE_NAME = 'pedidos';

    public function up(): void
    {
        $table = $this->table(self::TABLE_NAME, ['id' => false, 'primary_key' => ['pedido_id']]);
        $table->addColumn('pedido_id', 'string', ['null' => false])
            ->addColumn('total', 'integer', ['null' => false])
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
