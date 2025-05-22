<?php

use Src\Example;

describe('Example Tests', function () {
    test('Deve retornar Hello', function () {
        $example = new Example;
        expect($example->sayHello())->toBe('Hello');
    });
});