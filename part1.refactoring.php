<?php

enum OrderStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Shipped = 'shipped';
    case Cancelled = 'cancelled';
}

final readonly class OrderItem
{
    public function __construct(
        public string $id,
        public string $name,
        public int $quantity,
        public float $price
    ) {}
}

final class Order
{
    private OrderStatus $status;
    private float $total;

    /**
     * @param OrderItem[] $items
     */
    public function __construct(
        public readonly string $id,
        OrderStatus $status,
        float $total,
        public readonly array $items
    ) {
        $this->status = $status;
        $this->total = $total;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function changeStatus(OrderStatus $newStatus): void
    {
        $this->status = match ([$this->status, $newStatus]) {
            [OrderStatus::Draft, OrderStatus::Pending] => $newStatus,
            [OrderStatus::Pending, OrderStatus::Confirmed] => $newStatus,
            [OrderStatus::Pending, OrderStatus::Cancelled] => $newStatus,
            [OrderStatus::Confirmed, OrderStatus::Shipped] => $newStatus,
            default => throw new InvalidArgumentException(
                sprintf('Ошибка изменения статуса %s на статус %s', $this->status->value, $newStatus->value)
            ),
        };
    }
}
$items = [
    new OrderItem('PRODUCT-001', 'Продукт 1', 2, 100.00),
    new OrderItem('PRODUCT-002', 'Продукт 2', 5, 200.50),
];

$order = new Order('ORDER-001', OrderStatus::Draft, 300.50, $items);
echo "Статус заказа: " . $order->getStatus()->value . "\n";
$order->changeStatus(OrderStatus::Pending);
echo "Статус заказа: " . $order->getStatus()->value . "\n";
$order->changeStatus(OrderStatus::Confirmed);
echo "Статус заказа: " . $order->getStatus()->value . "\n";