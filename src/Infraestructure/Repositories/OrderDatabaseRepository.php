<?php

declare(strict_types=1);

namespace Src\Infraestructure\Repositories;

use Exception;
use PDO;
use Src\Domain\Entities\Order;
use Src\Domain\Entities\OrderCustomer;
use Src\Domain\Repositories\OrderRepository;

class OrderDatabaseRepository implements OrderRepository
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

    public function create(Order $order): void
    {
        // criar a order
        $orderInserQuery = "INSERT INTO app.pedidos (pedido_id, total) VALUES (:pedido_id, :total)";
        $orderInsertQueryStmt = $this->databaseConnection->prepare($orderInserQuery);
        $orderInsertQueryStmt->bindValue(':pedido_id', $order->getId());
        $orderInsertQueryStmt->bindValue(':total', $order->getTotal());
        $orderInsertQueryStmt->execute();

        // salvar o customer
        $customerInsertQuery = "INSERT INTO app.clientes (pedido_id, nome, email, telefone) VALUES (:pedido_id, :nome, :email, :telefone)";
        $customerInsertQueryStmt = $this->databaseConnection->prepare($customerInsertQuery);
        $customerInsertQueryStmt->bindValue(':pedido_id', $order->getId());
        $customerInsertQueryStmt->bindValue(':nome', $order->getCustomerName());
        $customerInsertQueryStmt->bindValue(':email', $order->getCustomerEmail());
        $customerInsertQueryStmt->bindValue(':telefone', $order->getCustomerPhone());
        $customerInsertQueryStmt->execute();

        // salvar os itens
        foreach ($order->getItems() as $orderItem) {
            $itemsInsertQuery = "INSERT INTO app.itens_do_pedido (pedido_id, produto_id, preco, quantidade, total) VALUES (:pedido_id, :produto_id, :preco, :quantidade, :total)";
            $itemsInsertQueryStmt = $this->databaseConnection->prepare($itemsInsertQuery);
            $itemsInsertQueryStmt->bindValue(':pedido_id', $order->getId());
            $itemsInsertQueryStmt->bindValue(':produto_id', $orderItem->productId);
            $itemsInsertQueryStmt->bindValue(':preco', $orderItem->price);
            $itemsInsertQueryStmt->bindValue(':quantidade', $orderItem->quantity);
            $itemsInsertQueryStmt->bindValue(':total', $orderItem->getTotal());
            $itemsInsertQueryStmt->execute();
        }
    }

    public function getById(string $orderId): ?Order
    {
        $getOrderQuery = "
            SELECT * FROM app.pedidos p 
            JOIN app.clientes c ON c.pedido_id = p.pedido_id 
            WHERE p.pedido_id = :pedido_id
        ";
        $getOrderQueryStmt = $this->databaseConnection->prepare($getOrderQuery);
        $getOrderQueryStmt->bindValue(':pedido_id', $orderId);
        $getOrderQueryStmt->execute();

        $getOrderQueryResult = $getOrderQueryStmt->fetchObject();

        if (is_bool($getOrderQueryResult)) return null;

        $order = new Order(
            orderId: $getOrderQueryResult->pedido_id,
            customer: new OrderCustomer(
                name: $getOrderQueryResult->nome,
                email: $getOrderQueryResult->email,
                phone: $getOrderQueryResult->telefone
            )
        );

        $getOrderItemsQuery = "SELECT * from app.itens_do_pedido idp WHERE idp.pedido_id = :pedido_id";
        $getOrderItemsQueryStmt = $this->databaseConnection->prepare($getOrderItemsQuery);
        $getOrderItemsQueryStmt->bindValue(':pedido_id', $orderId);
        $getOrderItemsQueryStmt->execute();

        $getOrderItemsQueryResult = $getOrderItemsQueryStmt->fetchAll();

        foreach($getOrderItemsQueryResult as $orderItem) {
            $order->restoreItem(
                productId: $orderItem['produto_id'],
                price: $orderItem['preco'],
                quantity: $orderItem['quantidade']
            );
        }

        return $order;
    }

    public function getByIdOrFail(string $orderId): Order
    {
        $order = $this->getById($orderId);

        if (is_null($order)) {
            throw new Exception('Order not found');
        }

        return $order;
    }
}
