<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PaymentsTable extends AbstractMigration
{
    const TABLE_NAME = 'pagamentos';

    public function up(): void
    {
        $table = $this->table(self::TABLE_NAME, ['id' => false, 'primary_key' => ['pagamento_id']]);
        //$table->addColumn('pedido_id', 'string', ['null' => false])
        $table->addColumn('pagamento_id', 'string', ['null' => false])
            ->addColumn('pedido_id', 'string', ['null' => false])
            ->addColumn('total', 'integer', ['null' => false])
            ->addColumn('valido_ate', 'string', ['null' => false])
            ->addColumn('nome_do_gateway', 'string', ['null' => true])
            ->addColumn('transacao_id_no_gateway', 'string', ['null' => true])
            ->addColumn('metodo_de_pagamento', 'string', ['null' => true])
            ->addColumn('pix_qr_code', 'text', ['null' => true])
            ->addColumn('pix_copia_e_cola', 'text', ['null' => true])
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
