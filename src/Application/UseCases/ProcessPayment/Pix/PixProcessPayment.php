<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\Pix;

use Exception;
use Src\Application\Gateways\Payment\Pix\PixPaymentGateway;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayInput;
use Src\Domain\Entities\Payment;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\PaymentRepository;

final class PixProcessPayment
{
    public function __construct(
        readonly PixPaymentGateway $pixPaymentGateway,
        readonly OrderRepository $orderRepository,
        readonly PaymentRepository $paymentRepository,
    ) {}

    public function execute(PixProcessPaymentInput $input): PixProcessPaymentOutput
    {
        $order = $this->orderRepository->getById($input->orderId);

        if (is_null($order)) {
            throw new Exception("Order not found");
        }

        // verificar se o pedido está disponivel para ser paga

        $payment = Payment::createPix(
            orderId: $order->getId(),
            amount: $order->getTotal(),
        );

        $paymentGatewayInput = new PixPaymentGatewayInput(
            amount: $payment->getAmount(),
            dueDate: $payment->getDueDate(),
            customerName: $order->getCustomerName(),
            customerDocumentValue: $order->getCustomerDocumentValue()
        );
        $paymentGatewayOutput = $this->pixPaymentGateway->process(
            input: $paymentGatewayInput
        );

        $payment->setGatewayName($paymentGatewayOutput->gatewayName);
        $payment->setGatewayTransactionId($paymentGatewayOutput->gatewayTransactionId);
        $payment->setPixQrCode($paymentGatewayOutput->qrCode);
        $payment->setPixCopyPaste($paymentGatewayOutput->copyPaste);

        $this->paymentRepository->create($payment);

        return new PixProcessPaymentOutput(
            paymentId: $payment->getId(),
            qrCode: $paymentGatewayOutput->qrCode,
            copyPaste: $paymentGatewayOutput->copyPaste,
        );
    }
}
