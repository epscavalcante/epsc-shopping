<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\BankSlip;

use Exception;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGateway;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGatewayInput;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPaymentOutput;
use Src\Domain\Entities\Payment;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\PaymentRepository;

final class BankSlipProcessPayment
{
    public function __construct(
        readonly BankSlipPaymentGateway $bankSlipPaymentGateway,
        readonly OrderRepository $orderRepository,
        readonly PaymentRepository $paymentRepository,
    ) {}

    public function execute(BankSlipProcessPaymentInput $input): BankSlipProcessPaymentOutput
    {
        $order = $this->orderRepository->getById($input->orderId);

        if (is_null($order)) {
            throw new Exception("Order not found");
        }

        $payment = Payment::createBankSlip(
            orderId: $order->getId(),
            amount: $order->getTotal(),
        );

        $paymentGatewayInput = new BankSlipPaymentGatewayInput(
            amount: $payment->getAmount(),
            dueDate: $payment->getDueDate(),
            customerName: $order->getCustomerName(),
            customerDocumentValue: $order->getCustomerDocumentValue()
        );
        $paymentGatewayOutput = $this->bankSlipPaymentGateway->process(
            input: $paymentGatewayInput
        );

        $payment->setGatewayName($paymentGatewayOutput->gatewayName);
        $payment->setGatewayTransactionId($paymentGatewayOutput->gatewayTransactionId);
        $payment->setBarCode($paymentGatewayOutput->barCode);

        $this->paymentRepository->create($payment);

        return new BankSlipProcessPaymentOutput(
            paymentId: $payment->getId(),
            barCode: $paymentGatewayOutput->barCode,
        );
    }
}
