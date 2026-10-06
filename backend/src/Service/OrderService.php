<?php

namespace App\Service;

use App\Dto\CreateOrderRequest;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Enum\OrderStatusEnum;
use App\Enum\ProductStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator
    ) {}


    public function createOrder(CreateOrderRequest $request): Order
    {
        $violations = $this->validator->validate($request);
        if (count($violations) > 0) {
            throw new \InvalidArgumentException((string) $violations);
        }

        $this->em->beginTransaction();
        try {
            $order = new Order();
            $order->setDealerId($request->dealerId);

            $total = 0;

            foreach ($request->items as $itemData) {
                /** @var Product|null $product */
                $product = $this->em->getRepository(Product::class)->find($itemData->productId);

                /*if (!$product) {
                    throw new \InvalidArgumentException("Товар с ID {$itemData->productId} не найден.");
                }
                if ($product->getStatus() !== ProductStatusEnum::ACTIVE) {
                    throw new \InvalidArgumentException("Товар {$product->getName()} недоступен для заказа.");
                }
                if ($product->getStock() < $itemData->quantity) {
                    throw new \InvalidArgumentException("Недостаточно остатка для товара {$product->getName()}. Запрошено: {$itemData->quantity}, в наличии: {$product->getStock()}.");
                }*/

                $product->setStock($product->getStock() - $itemData->quantity);
                $this->em->persist($product);

                $item = new OrderItem();
                $item->setOrder($order);
                $item->setProduct($product);
                $item->setQuantity($itemData->quantity);
                $item->setPricePerUnit($product->getPrice());
                $order->addItem($item);

                $total += $product->getPrice() * $itemData->quantity;
            }

            $order->setTotalAmount($total);
            $this->em->persist($order);
            $this->em->flush();
            $this->em->commit();

            return $order;
        } catch (\Throwable $e) {
            $this->em->rollback();
            throw $e;
        }
    }

    public function updateStatus(Order $order, OrderStatusEnum $newStatus): void
    {
        if (!$order->canTransitionTo($newStatus)) {
            throw new \InvalidArgumentException(
                sprintf("Переход из статуса '%s' в '%s' невозможен.", $order->getStatus()->value, $newStatus->value)
            );
        }

        $order->setStatus($newStatus);
        $this->em->flush();
    }
}
