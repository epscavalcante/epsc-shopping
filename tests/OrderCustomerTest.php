<?php

use Src\Domain\Entities\OrderCustomer;

describe('OrderCustomer Test', function () {
    test('Deve criar um order customer', function () {
        $customer = new OrderCustomer(
            accountId: 'account_id',
            email: 'email@email.com',
            phone: '00000000000',
        );

        expect($customer->phone)->toBe('00000000000');
        expect($customer->email)->toBe('email@email.com');
    });
});