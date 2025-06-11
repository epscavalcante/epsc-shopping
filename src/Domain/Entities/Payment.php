<?php

declare(strict_types=1);

namespace Src\Domain\Entities;

use DateTime;
use DateTimeInterface;

class Payment
{
    private ?string $gatewayName = null;
    private ?string $gatewayTransactionId = null;
    private ?string $pixQrCode = null;
    private ?string $pixCopyPaste = null;

    public function __construct(
        private readonly string $paymentId,
        private readonly string $orderId,
        private readonly int $amount,
        private readonly DateTimeInterface $dueDate,
        private readonly string $paymentMethod,
        ?string $gatewayName = null,
        ?string $gatewayTransactionId = null,
        ?string $pixQrCode = null,
        ?string $pixCopyPaste = null,
    ) {
        $this->gatewayName = $gatewayName ?? null;
        $this->gatewayTransactionId = $gatewayTransactionId ?? null;
        $this->pixQrCode = $pixQrCode ?? null;
        $this->pixCopyPaste = $pixCopyPaste ?? null;
    }

    public static function create(string $orderId, int $amount, DateTimeInterface $dueDate, string $paymentMethod, ?string $gatewayName = null, ?string $gatewayTransactionId = null, ?string $pixQrCode = null, ?string $pixCopyPaste = null)
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
            pixQrCode: $pixQrCode,
            pixCopyPaste: $pixCopyPaste,
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

    public function getPixQrCode(): ?string
    {
        return $this->pixQrCode;
    }

    public function setPixQrCode(string $value): void
    {
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
}
