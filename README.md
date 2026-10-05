# Выполненое тестовое задание
Исходник задания: https://docs.google.com/document/d/1vncD-j1tUgYNtWsKvMyQI6nfdunAvYUk/edit?usp=sharing&ouid=116139382824893984782&rtpof=true&sd=true

## Описание
Ниже — фрагмент в «старом» стиле. Приведите его к современному PHP 8.2+: сделайте код строго типизированным, статусы — перечислением, переходы статусов — через match, объект заказа — неизменяемым там, где это уместно. Коротко прокомментируйте, что и почему изменили.

## Исходный код
```php
<?php

class OrderStatus
{
    const DRAFT     = 'draft';
    const PENDING   = 'pending';
    const CONFIRMED = 'confirmed';
    const SHIPPED   = 'shipped';
    const CANCELLED = 'cancelled';
}

class Order
{
    public $id;
    public $status;
    public $total;
    public $items;

    public function __construct($id, $status, $total, $items)
    {
        $this->id     = $id;
        $this->status = $status;
        $this->total  = $total;
        $this->items  = $items;
    }

    public function changeStatus($new)
    {
        if ($this->status == OrderStatus::DRAFT && $new == OrderStatus::PENDING) {
            $this->status = $new;
        } else if ($this->status == OrderStatus::PENDING && ($new == OrderStatus::CONFIRMED || $new == OrderStatus::CANCELLED)) {
            $this->status = $new;
        } else if ($this->status == OrderStatus::CONFIRMED && $new == OrderStatus::SHIPPED) {
            $this->status = $new;
        } else {
            throw new Exception('Invalid transition');
        }
    }
}
```

## Улучшенный код
```php
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
```

## Порядок выполнения
Смотреть файл ```part1.refactoring.php```

* Вынес строковые константы в enum теперь метод changeStatus принимает только объект OrderStatus
* Добавил класс OrderItem для более корректного примера заказа и товаров и пометил его readonly. В исходном коде $items был массивом с неопределенной структурой, теперь это массив строго типизированных объектов.
* Сделал инкапсулирванными свойства $id и $items объявлены как public readonly, тк идентификатор заказа и его состав не должны меняться после создания объекта. Прямой доступ к $status и $total закрыт отмечен как private, чтобы их нельзя было изменить в обход.
* Добавлены типы string, float, int и типы для объектов OrderStatus, OrderItem[].
* Статусы в методе changeStatus сделаны через match, вместо дерева if,else
* Вместо Exception сделал InvalidArgumentException из SPL, для передачи некорректных аргументов.
* Для приватных свойств добавил методы доступа (геттеры)
* Классы пометил как final как дополнительная фича. В данной бизнес логике наследование Order не предполагается
* Добавил аннотации типов для свойств и параметров для корректной работы линтеров и анализаторов
* Реализовал пример работы с описанным классом