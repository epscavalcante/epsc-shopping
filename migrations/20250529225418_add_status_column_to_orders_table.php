<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Src\Domain\Enums\OrderStatusEnum;

final class AddStatusColumnToOrdersTable extends AbstractMigration
{
    const TABLE_NAME = 'pedidos';

    public function up(): void
    {
        $table = $this->table(self::TABLE_NAME);
        $table->addColumn(
                columnName: 'status', 
                type: 'string', 
                options: [
                    'null' => false, 
                    'after' => 'pedido_id',
                    'default' => OrderStatusEnum::CREATED->value
                ])
            ->update();
    }

    public function down(): void
    {
        $table = $this->table(self::TABLE_NAME);
        $table->removeColumn('status')
            ->save();
    }
}
