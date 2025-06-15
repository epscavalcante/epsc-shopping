<?php

declare(strict_types=1);

namespace Src\Infraestructure\Http\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Src\Application\UseCases\PlaceOrder\PlaceOrder;
use Src\Application\UseCases\PlaceOrder\PlaceOrderInput;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPayment;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPayment;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPaymentInput;
use Src\Infraestructure\Logger\Logger;

class OrderController
{
    public function __construct(
        private readonly Logger $logger,
        private readonly PlaceOrder $placeOrder,
        private readonly PixProcessPayment $pixProcessPayment,
        private readonly BankSlipProcessPayment $bankSlipProcessPayment,
    ) {}

    public function placeOrder(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->logger->info('Acessou place Order');

        $data = $request->getParsedBody();

        // validation data

        $this->logger->debug('body', $data);

        $input = PlaceOrderInput::create(
            items: $data['items'],
            customerEmail: $data['customer_email'],
            customerName: $data['customer_name'],
            customerPhone: $data['customer_phone'],
        );
        $output = $this->placeOrder->execute($input);

        $this->logger->debug('Output', (array) $output);

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()
            ->write(
                json_encode([
                    'order_id' => $output->orderId
                ])
            );
        return $response;
    }

    public function pixProcessPayment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $input = new PixProcessPaymentInput(
            orderId: $args['order_id'],
        );
        $output = $this->pixProcessPayment->execute($input);
        $this->logger->debug('Output', (array) $output);

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()
            ->write(
                json_encode([
                    'payment_id' => $output->paymentId,
                    'qr_code' => $output->qrCode,
                    'qr_copy_past' => $output->copyPaste,
                ])
            );

        return $response;
    }

    public function bankSlipProcessPayment(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $input = new BankSlipProcessPaymentInput(
            orderId: $args['order_id'],
        );
        $output = $this->bankSlipProcessPayment->execute($input);
        $this->logger->debug('Output', (array) $output);

        $response->withHeader('Content-Type', 'application/json')
            ->getBody()
            ->write(
                json_encode([
                    'payment_id' => $output->paymentId,
                    'bar_Code' => $output->barCode,
                ])
            );

        return $response;
    }
}
