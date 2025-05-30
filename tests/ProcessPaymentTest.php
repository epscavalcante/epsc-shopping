<?php

use Src\Application\UseCases\ProcessPayment\ProcessPayment;
use Src\Application\UseCases\ProcessPayment\ProcessPaymentInput;
use Src\Application\UseCases\ProcessPayment\ProcessPaymentOutput;

describe('Process Payment Tests', function () {

    it('Deve processar o pagamento de um pedido via PIX', function () {

        $processPaymentInput = new ProcessPaymentInput(
            orderId: 'order_id',
            paymentMethod: 'PIX'
        );
        $processPayment = new ProcessPayment();
        $processPaymentOuput = $processPayment->execute($processPaymentInput);
        expect($processPaymentOuput)->toBeInstanceOf(ProcessPaymentOutput::class);
        expect($processPaymentOuput->paymentId)->toBeString();

    });

});