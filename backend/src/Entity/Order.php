<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use App\Enum\OrderStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\OrderItem;

#[ORM\Entity]
class Order
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(enumType: OrderStatusEnum::class)]
    private OrderStatusEnum $status;

    #[ORM\Column]
    private int $dealer_id;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2)]
    private float $totalAmount = 0;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist'])]
    private ArrayCollection $items;
}
