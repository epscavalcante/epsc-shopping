<?php

use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Src\Application\Gateways\Payment\BankSlip\BankSlipPaymentGateway;
use Src\Application\Gateways\Payment\CreditCard\CreditCardPaymentGateway;
use Src\Application\Gateways\Payment\Pix\PixPaymentGateway;
use Src\Application\UseCases\PlaceOrder\PlaceOrder;
use Src\Application\UseCases\ProcessPayment\BankSlip\BankSlipProcessPayment;
use Src\Application\UseCases\ProcessPayment\CreditCard\CreditCardProcessPayment;
use Src\Application\UseCases\ProcessPayment\Pix\PixProcessPayment;
use Src\Domain\Repositories\OrderRepository;
use Src\Domain\Repositories\PaymentRepository;
use Src\Domain\Repositories\ProductRepository;
use Src\Infraestructure\Gateways\Payment\AbacatePay\AbacatePayHttpPixPaymentGateway;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpBankSlipPaymentGateway;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpCreditCardPaymentGateway;
use Src\Infraestructure\Gateways\Payment\Asaas\AsaasHttpPixPaymentGateway;
use Src\Infraestructure\Http\Client\GuzzleHttpClientAdapter;
use Src\Infraestructure\Http\Client\HttpClient;
use Src\Infraestructure\Http\Controllers\OrderController;
use Src\Infraestructure\Logger\Logger;
use Src\Infraestructure\Logger\MonologAdapter;
use Src\Infraestructure\Repositories\OrderDatabaseRepository;
use Src\Infraestructure\Repositories\PaymentDatabaseRepository;
use Src\Infraestructure\Repositories\ProductDatabaseRepository;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(Logger::class, new MonologAdapter('epsc_shopping'));
$container->set(HttpClient::class, new GuzzleHttpClientAdapter);
$container->set(OrderRepository::class, new OrderDatabaseRepository);
$container->set(ProductRepository::class, new ProductDatabaseRepository);
$container->set(PaymentRepository::class, new PaymentDatabaseRepository);

$container->set(PlaceOrder::class, function (ContainerInterface $container) {
    return new PlaceOrder(
        productRepository: $container->get(ProductRepository::class),
        orderRepository: $container->get(OrderRepository::class),
    );
});

$container->set(PixPaymentGateway::class, function (ContainerInterface $container) {
    return new AsaasHttpPixPaymentGateway(
        logger: $container->get(Logger::class),
        httpClient: $container->get(HttpClient::class),
    );

    return new AbacatePayHttpPixPaymentGateway(
        logger: $container->get(Logger::class),
        httpClient: $container->get(HttpClient::class),
    );
});

$container->set(BankSlipPaymentGateway::class, function (ContainerInterface $container) {
    // poderia decidir qual implementação utilizar através do env
    return new AsaasHttpBankSlipPaymentGateway(
        logger: $container->get(Logger::class),
        httpClient: $container->get(HttpClient::class),
    );
});

$container->set(CreditCardPaymentGateway::class, function (ContainerInterface $container) {
    // poderia decidir qual implementação utilizar através do env
    return new AsaasHttpCreditCardPaymentGateway(
        logger: $container->get(Logger::class),
        httpClient: $container->get(HttpClient::class),
    );
});

$container->set(PixProcessPayment::class, function (ContainerInterface $container) {
    return new PixProcessPayment(
        pixPaymentGateway: $container->get(PixPaymentGateway::class),
        orderRepository: $container->get(OrderRepository::class),
        paymentRepository: $container->get(PaymentRepository::class),
    );
});

$container->set(BankSlipProcessPayment::class, function (ContainerInterface $container) {
    return new BankSlipProcessPayment(
        bankSlipPaymentGateway: $container->get(BankSlipPaymentGateway::class),
        orderRepository: $container->get(OrderRepository::class),
        paymentRepository: $container->get(PaymentRepository::class),
    );
});

$container->set(CreditCardProcessPayment::class, function (ContainerInterface $container) {
    return new CreditCardProcessPayment(
        creditCardPaymentGateway: $container->get(CreditCardPaymentGateway::class),
        orderRepository: $container->get(OrderRepository::class),
        paymentRepository: $container->get(PaymentRepository::class),
    );
});

$container->set(OrderController::class, function (ContainerInterface $container) {
    return new OrderController(
        logger: $container->get(Logger::class),
        placeOrder: $container->get(PlaceOrder::class),
        pixProcessPayment: $container->get(PixProcessPayment::class),
        bankSlipProcessPayment: $container->get(BankSlipProcessPayment::class),
        creditCardProcessPayment: $container->get(CreditCardProcessPayment::class),
    );
});

AppFactory::setContainer($container);
$app = AppFactory::create();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response, $args) {
    $body = [
        'message' => 'Hello world',
    ];
    $response->withHeader('Content-Type', 'application/json')->getBody()->write(json_encode($body));
    return $response;
});

$app->post('/orders', [OrderController::class, 'placeOrder']);
$app->post('/orders/{order_id}/pix-process-payment', [OrderController::class, 'pixProcessPayment']);
$app->post('/orders/{order_id}/bankslip-process-payment', [OrderController::class, 'bankSlipProcessPayment']);
$app->post('/orders/{order_id}/credit-card-process-payment', [OrderController::class, 'creditCardProcessPayment']);

$app->run();
