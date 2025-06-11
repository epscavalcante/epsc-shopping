<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\AbacatePay;

use Src\Infraestructure\Gateways\Payment\AbacatePay\AbacatePayPixPaymentGateway;
use Src\Infraestructure\Gateways\Payment\PixPaymentGatewayInput;
use Src\Infraestructure\Gateways\Payment\PixPaymentGatewayOutput;
use Src\Infraestructure\Gateways\Payment\PixPaymentProcessor;
use Src\Infraestructure\Logger\Logger;

final class AbacatePayPixPaymentGatewayProcessor implements PixPaymentProcessor
{
    public function __construct(
        private readonly AbacatePayPixPaymentGateway $pixPaymentGateway,
        private readonly Logger $logger,
        private readonly ?PixPaymentProcessor $nextPixPaymentProcessor = null,
    ) {}

    public function handle(PixPaymentGatewayInput $input): PixPaymentGatewayOutput
    {
        $this->logger->info('Processing Payment with AbacatePay');

        try {
            return $this->pixPaymentGateway->process($input);
        } catch (\Throwable $e) {
            if ($this->nextPixPaymentProcessor) {
                $this->logger->notice('Error - Process Payment with AbacatePay');
                return $this->nextPixPaymentProcessor->handle($input);
            }

            $this->logger->error(
                message: $e->getMessage(),
            );

            throw new \RuntimeException("Todos os gateways falharam", 0, $e);
        }
    }
}
