<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use DateTime;
use DateTimeInterface;

class Payment
{
    private ?string $gatewayName = null;
    private ?string $gatewayTransactionId = null;
    private ?string $barCode = null;
    private ?string $pixQrCode = null;
    private ?string $pixCopyPaste = null;
    private ?string $creditCardBrand = null;
    private ?string $creditCardLastDigits = null;
    private ?string $creditCardToken = null;

    public function __construct(
        private readonly string $paymentId,
        private readonly string $orderId,
        private readonly int $amount,
        private readonly DateTimeInterface $dueDate,
        private readonly string $paymentMethod,
        ?string $gatewayName = null,
        ?string $gatewayTransactionId = null,
        ?string $barCode = null,
        ?string $pixQrCode = null,
        ?string $pixCopyPaste = null,
        ?string $creditCardBrand = null,
        ?string $creditCardLastDigits = null,
        ?string $creditCardToken = null,
    ) {
        $this->gatewayName = $gatewayName;
        $this->gatewayTransactionId = $gatewayTransactionId;
        $this->pixQrCode = $pixQrCode;
        $this->pixCopyPaste = $pixCopyPaste;
        $this->barCode = $barCode;
        $this->creditCardBrand = $creditCardBrand;
        $this->creditCardLastDigits = $creditCardLastDigits;
        $this->creditCardToken = $creditCardToken;
    }

    public static function create(string $orderId, int $amount, DateTimeInterface $dueDate, string $paymentMethod, ?string $gatewayName = null, ?string $gatewayTransactionId = null, ?string $pixQrCode = null, ?string $pixCopyPaste = null, ?string $barCode = null, ?string $creditCardToken = null, ?string $creditCardBrand = null, ?string $creditCardLastDigits = null)
    {
        $paymentId = uniqid('PAYMENT_ID_', true);
        return new self(
            paymentId: $paymentId,
            orderId: $orderId,
            paymentMethod: $paymentMethod,
            amount: $amount,
            dueDate: $dueDate,
            gatewayName: $gatewayName,
            gatewayTransactionId: $gatewayTransactionId,
            barCode: $barCode,
            pixQrCode: $pixQrCode,
            pixCopyPaste: $pixCopyPaste,
            creditCardBrand: $creditCardBrand,
            creditCardLastDigits: $creditCardLastDigits,
            creditCardToken: $creditCardToken,
        );
    }

    public static function createPix(string $orderId, int $amount, ?DateTimeInterface $dueDate = null)
    {
        $dueDate = $dueDate ?? (new DateTime())->modify('+3 day');

        return self::create(
            orderId: $orderId,
            amount: $amount,
            dueDate: $dueDate,
            paymentMethod: 'PIX',
        );
    }

    public static function createBankSlip(string $orderId, int $amount, ?DateTimeInterface $dueDate = null)
    {
        $dueDate = $dueDate ?? (new DateTime())->modify('+3 day');

        return self::create(
            orderId: $orderId,
            amount: $amount,
            dueDate: $dueDate,
            paymentMethod: 'BANKSLIP',
        );
    }

    public static function createCreditCard(string $orderId, int $amount, ?DateTimeInterface $dueDate = null)
    {
        $dueDate = $dueDate ?? (new DateTime())->modify('+3 day');

        return self::create(
            orderId: $orderId,
            amount: $amount,
            dueDate: $dueDate,
            paymentMethod: 'CREDIT_CARD',
        );
    }

    public function getId(): string
    {
        return $this->paymentId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getDueDate(): DateTimeInterface
    {
        return $this->dueDate;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getGatewayName(): ?string
    {
        return $this->gatewayName;
    }

    public function setGatewayName(string $value): void
    {
        $this->gatewayName = $value;
    }

    public function getGatewayTransactionId(): ?string
    {
        return $this->gatewayTransactionId;
    }

    public function setGatewayTransactionId(string|int $transactionId): void
    {
        $this->gatewayTransactionId = $transactionId;
    }

    public function getBarCode(): ?string
    {
        return $this->barCode;
    }

    public function setBarCode(string $value): void
    {
        // só deveria ser possível se o méto de pagamento for boleto
        $this->barCode = $value;
    }

    public function getPixQrCode(): ?string
    {
        return $this->pixQrCode;
    }

    public function setPixQrCode(string $value): void
    {
        // só deveria ser possível definir o pixQrCode se o método de pagamento for PIX
        $this->pixQrCode = $value;
    }

    public function getPixCopyPaste(): ?string
    {
        return $this->pixCopyPaste;
    }

    public function setPixCopyPaste(string $value): void
    {
        $this->pixCopyPaste = $value;
    }

    public function isPixPaymentMethod(): bool
    {
        return $this->paymentMethod === 'PIX';
    }

    public function isCreditCardMethod(): bool
    {
        return $this->paymentMethod === 'CREDIT_CARD';
    }

    public function getCreditCardToken(): ?string
    {
        return $this->creditCardToken;
    }

    public function setCreditCardToken(string $value): void
    {
        // só deveria ser possível definir o creditCardToken se o método de pagamento for CREDIT_CARD
        $this->creditCardToken = $value;
    }

    public function getCreditCardBrand(): ?string
    {
        return $this->creditCardBrand;
    }

    public function setCreditCardBrand(string $value): void
    {
        // só deveria ser possível definir o creditCardBrand se o método de pagamento for CREDIT_CARD
        $this->creditCardBrand = $value;
    }

    public function getCreditCardLastDigits(): ?string
    {
        return $this->creditCardLastDigits;
    }

    public function setCreditCardLastDigits(string $value): void
    {
        // só deveria ser possível definir o creditCardLastDigits se o método de pagamento for CREDIT_CARD
        $this->creditCardLastDigits = $value;
    }
}
