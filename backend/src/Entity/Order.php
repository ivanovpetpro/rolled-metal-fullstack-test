<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\OrderStatusEnum;

#[ORM\Entity]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $status;

    #[ORM\Column]
    private int $dealerId;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private string $totalAmount = '0.00';

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->status = OrderStatusEnum::DRAFT;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(OrderItem $item): self
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setOrder($this);
        }

        return $this;
    }

    public function removeItem(OrderItem $item): self
    {
        if ($this->items->removeElement($item)) {
            if ($item->getOrder() === $this) {
                $item->setOrder(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getStatus(): OrderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OrderStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDealerId(): int
    {
        return $this->dealerId;
    }

    public function setDealerId(int $dealerId): self
    {
        $this->dealerId = $dealerId;
        return $this;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): self
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function canTransitionTo(OrderStatusEnum $newStatus): bool
    {
        $current = $this->status;
        return match ($current) {
            OrderStatusEnum::DRAFT => in_array($newStatus, [OrderStatusEnum::PROCESSING, OrderStatusEnum::CANCELLED], true),
            OrderStatusEnum::PROCESSING => in_array($newStatus, [OrderStatusEnum::CONFIRMED, OrderStatusEnum::CANCELLED], true),
            OrderStatusEnum::CONFIRMED => in_array($newStatus, [OrderStatusEnum::SHIPPED, OrderStatusEnum::CANCELLED], true),
            OrderStatusEnum::SHIPPED, OrderStatusEnum::CANCELLED => false,
        };
    }
}
