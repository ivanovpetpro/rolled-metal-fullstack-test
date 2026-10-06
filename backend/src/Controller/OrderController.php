<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Enum\OrderStatusEnum;
use App\Dto\CreateOrderRequest;
use App\Service\OrderService;
use App\Entity\Order;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    public function __construct(private readonly OrderService $service) {}

    #[Route(methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateOrderRequest $request): JsonResponse
    {
        $order = $this->service->createOrder($request);

        return $this->json($this->normalizeOrder($order), 201);
    }

    #[Route('/{id}', name: 'api_order_get', methods: ['GET'])]
    public function get(Order $order): JsonResponse
    {
        return $this->json($this->normalizeOrder($order));
    }

    #[Route('/{id}/status', name: 'api_order_status', methods: ['PATCH'])]
    public function status(Order $order, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['status'])) {
            return $this->json(['error' => 'Поле status обязательно.'], 400);
        }

        try {
            $this->service->updateStatus($order, OrderStatusEnum::from($data['status']));
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 422);
        }

        return $this->json(['status' => $order->getStatus()]);
    }

    private function normalizeOrder(Order $order): array
    {
        return [
            'id' => $order->getId(),
            'dealerId' => $order->getDealerId(),
            'createdAt' => $order->getCreatedAt()->format(\DateTime::ATOM),
            'status' => $order->getStatus(),
            'total' => $order->getTotalAmount(),
            'items' => array_map(fn($item) => [
                'product' => [
                    'id' => $item->getProduct()->getId(),
                    'sku' => $item->getProduct()->getSku(),
                    'name' => $item->getProduct()->getName(),
                ],
                'quantity' => $item->getQuantity(),
                'price' => $item->getPricePerUnit(),
            ], $order->getItems()->toArray()),
        ];
    }
}
