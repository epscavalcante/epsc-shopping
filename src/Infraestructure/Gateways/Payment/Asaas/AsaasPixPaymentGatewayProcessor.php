<?php

declare(strict_types=1);

namespace Src\Infraestructure\Gateways\Payment\Asaas;

use Exception;
use Src\Application\Gateways\Payment\Pix\PixPaymentGatewayProcessor;
use Src\Infraestructure\Logger\Logger;

class AsaasPixPaymentGatewayProcessor implements PixPaymentGatewayProcessor
{
    public function __construct(
        private readonly Logger $logger,
        private readonly AsaasPixPaymentGateway $pixPaymentGateway,
        private readonly ?PixPaymentProcessor $nextPixPaymentProcessor = null,
    ) {}

    public function handle(PixPaymentGatewayInput $input): PixPaymentGatewayOutput
    {
        $this->logger->info('Processing Payment with Asaas');

        try {
            throw new Exception('Errro mocado');
            return $this->pixPaymentGateway->process($input);
        } catch (\Throwable $e) {
            if ($this->nextPixPaymentProcessor) {
                $this->logger->notice('Error - Process Payment with Asaas');
                return $this->nextPixPaymentProcessor->handle($input);
            }

            $this->logger->error(
                message: $e->getMessage(),
            );

            throw new \RuntimeException("Todos os gateways falharam", 0, $e);
        }
    }
}
