<?php

declare(strict_types=1);

namespace Src\Infraestructure\Repositories;

use Exception;
use PDO;
use Src\Domain\Entities\Product;
use Src\Domain\Repositories\ProductRepository;

class ProductDatabaseRepository implements ProductRepository
{
    private PDO $databaseConnection;

    public function __construct()
    {
        $this->databaseConnection = new PDO(
            dsn: 'mysql:host=mysql;dbname=app;charset=utf8',
            username: 'root',
            password: 'root',
            options: [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    public function getById(string $productId): ?Product
    {
        $getProductQuery = "
            SELECT * FROM app.produtos p 
            WHERE p.produto_id = :produto_id
        ";
        $getProductQueryStmt = $this->databaseConnection->prepare($getProductQuery);
        $getProductQueryStmt->bindValue(':produto_id', $productId);
        $getProductQueryStmt->execute();

        $getProductQueryResult = $getProductQueryStmt->fetchObject();

        if (is_bool($getProductQueryResult)) return null;

        $product = new Product(
            productId: $getProductQueryResult->produto_id,
            name: $getProductQueryResult->nome,
            price: $getProductQueryResult->preco
        );

        return $product;
    }

    public function getByIdOrFail(string $productId): Product
    {
        $product = $this->getById($productId);

        if (is_null($product)) {
            throw new Exception('Product not found');
        }

        return $product;
    }
}
