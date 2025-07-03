<?php

declare(strict_types=1);

namespace Src\Application\UseCases\ProcessPayment\CreditCard;

use Exception;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGateway;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGatewayInput;
use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPaymentOutput;
use Src\Domain\Entities\Payment;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\PaymentRepository;

final class CreditCardProcessPayment
{
    public function __construct(
        readonly CreditCardPaymentGateway $creditCardPaymentGateway,
        readonly OrderRepository $orderRepository,
        readonly PaymentRepository $paymentRepository,
    ) {}

    public function execute(CreditCardProcessPaymentInput $input): CreditCardProcessPaymentOutput
    {
        $order = $this->orderRepository->getById($input->orderId);

        if (is_null($order)) {
            throw new Exception("Order not found");
        }

        $payment = Payment::createCreditCard(
            orderId: $order->getId(),
            amount: $order->getTotal(),
        );

        $paymentGatewayInput = new CreditCardPaymentGatewayInput(
            amount: $payment->getAmount(),
            dueDate: $payment->getDueDate(),
            customerName: $order->getCustomerName(),
            customerDocumentValue: $order->getCustomerDocumentValue(),
            creditCardName: $input->cardHolderName,
            creditCardNumber: $input->cardNumber,
            creditCardExpiryMonth: $input->cardExpiryMonth,
            creditCardExpiryYear: $input->cardExpiryYear,
            creditCardExpiryCCV: $input->cardCCV,
            creditCardHolderName: $input->holderName,
            creditCardHolderEmail: $input->holderEmail,
            creditCardHolderDocumentValue: $input->holderDocumentValue,
            creditCardHolderAddressPostalCode: $input->holderAddressPostalCode,
            creditCardHolderAddressNumber: $input->holderAddressNumber,
            creditCardHolderAddressComplement: $input->holderAddressComplement,
            creditCardHolderPhone: $input->holderPhone,
        );
        $paymentGatewayOutput = $this->creditCardPaymentGateway->process(
            input: $paymentGatewayInput
        );
        $payment->setGatewayName($paymentGatewayOutput->gatewayName);
        $payment->setGatewayTransactionId($paymentGatewayOutput->gatewayTransactionId);
        $payment->setCreditCardToken($paymentGatewayOutput->creditCardToken);
        $payment->setCreditCardBrand($paymentGatewayOutput->creditCardBrand);
        $payment->setCreditCardLastDigits($paymentGatewayOutput->creditCardLastDigits);

        $this->paymentRepository->create($payment);

        return new CreditCardProcessPaymentOutput(
            paymentId: $payment->getId(),
        );
    }
}
