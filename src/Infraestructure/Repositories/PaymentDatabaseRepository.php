<?php

declare(strict_types=1);

namespace Src\Infraestructure\Repositories;

use DateTime;
use DateTimeInterface;
use Exception;
use PDO;
use Src\Domain\Entities\Payment;
use Src\Domain\Repositories\PaymentRepository;

class PaymentDatabaseRepository implements PaymentRepository
{
    private PDO $databaseConnection;

    public function __construct()
    {
        $this->databaseConnection = new PDO(
            dsn: 'mysql:host=mysql;dbname=app;charset=utf8',
            username: 'root',
            password: 'root',
            options: [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    public function create(Payment $payment): void
    {
        $orderInserQuery = "
        INSERT INTO app.pagamentos 
        (pagamento_id, pedido_id, total, valido_ate, nome_do_gateway, transacao_id_no_gateway, metodo_de_pagamento, pix_qr_code, pix_copia_e_cola, codigo_de_barra_do_boleto, cartao_bandeira, cartao_token, cartao_ultimos_digitos) 
        VALUES (:pagamento_id, :pedido_id, :total, :valido_ate, :nome_do_gateway, :transacao_id_no_gateway, :metodo_de_pagamento, :pix_qr_code, :pix_copia_e_cola, :codigo_de_barra_do_boleto, :cartao_bandeira, :cartao_token, :cartao_ultimos_digitos)";
        $orderInsertQueryStmt = $this->databaseConnection->prepare($orderInserQuery);
        $orderInsertQueryStmt->bindValue(':pagamento_id', $payment->getId());
        $orderInsertQueryStmt->bindValue(':pedido_id', $payment->getOrderId());
        $orderInsertQueryStmt->bindValue(':total', $payment->getAmount());
        $orderInsertQueryStmt->bindValue(':valido_ate', $payment->getDueDate()->format(DateTimeInterface::ISO8601_EXPANDED));
        $orderInsertQueryStmt->bindValue(':nome_do_gateway', $payment->getGatewayName());
        $orderInsertQueryStmt->bindValue(':transacao_id_no_gateway', $payment->getGatewayTransactionId());
        $orderInsertQueryStmt->bindValue(':metodo_de_pagamento', $payment->getPaymentMethod());
        $orderInsertQueryStmt->bindValue(':codigo_de_barra_do_boleto', $payment->getBarCode());
        $orderInsertQueryStmt->bindValue(':pix_qr_code', $payment->getPixQrCode());
        $orderInsertQueryStmt->bindValue(':pix_copia_e_cola', $payment->getPixCopyPaste());
        $orderInsertQueryStmt->bindValue(':cartao_bandeira', $payment->getCreditCardBrand());
        $orderInsertQueryStmt->bindValue(':cartao_token', $payment->getCreditCardToken());
        $orderInsertQueryStmt->bindValue(':cartao_ultimos_digitos', $payment->getCreditCardLastDigits());
        $orderInsertQueryStmt->execute();
    }

    public function getById(string $paymentId): ?Payment
    {
        $getPayment = "
            SELECT * FROM app.pagamentos p 
            WHERE p.pagamento_id = :pagamento_id
        ";
        $getPaymentStmt = $this->databaseConnection->prepare($getPayment);
        $getPaymentStmt->bindValue(':pagamento_id', $paymentId);
        $getPaymentStmt->execute();
        
        $getPaymentQueryResult = $getPaymentStmt->fetchObject();

        if (is_bool($getPaymentQueryResult)) return null;

        $payment = new Payment(
            paymentId: $getPaymentQueryResult->pagamento_id,
            orderId: $getPaymentQueryResult->pedido_id,
            paymentMethod: $getPaymentQueryResult->metodo_de_pagamento,
            amount: (int) $getPaymentQueryResult->total,
            dueDate: new DateTime($getPaymentQueryResult->valido_ate),
            gatewayName: $getPaymentQueryResult->nome_do_gateway,
            gatewayTransactionId: $getPaymentQueryResult->transacao_id_no_gateway,
            barCode: $getPaymentQueryResult->codigo_de_barra_do_boleto,
            pixQrCode: $getPaymentQueryResult->pix_qr_code,
            pixCopyPaste: $getPaymentQueryResult->pix_copia_e_cola,
            creditCardToken: $getPaymentQueryResult->cartao_token,
            creditCardBrand: $getPaymentQueryResult->cartao_bandeira,
            creditCardLastDigits: $getPaymentQueryResult->cartao_ultimos_digitos,
        );

        return $payment;
    }

    public function getByIdOrFail(string $paymentId): Payment
    {
        $payment = $this->getById($paymentId);

        if (is_null($payment)) {
            throw new Exception('Pagamento not found');
        }

        return $payment;
    }
}
