<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class OrderItemsTable extends AbstractMigration
{
    const TABLE_NAME = 'itens_do_pedido';

    public function up(): void
    {
        $table = $this->table(self::TABLE_NAME, ['id' => true]);
        $table->addColumn('pedido_id', 'string', ['null' => false])
            ->addColumn('produto_id', 'string', ['null' => false])
            ->addColumn('quantidade', 'integer', ['null' => false])
            ->addColumn('preco', 'integer', ['null' => false])
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
