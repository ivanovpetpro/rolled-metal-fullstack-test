<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ProductRepository;
use App\Entity\Product;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(private readonly ProductRepository $repository) {}

    #[Route(name: 'api_products_list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $qb = $this->repository->createQueryBuilder('p');

        if ($category = $request->query->get('category')) {
            $qb->andWhere('p.category = :cat')->setParameter('cat', $category);
        }
        if ($status = $request->query->get('status')) {
            $qb->andWhere('p.status = :st')->setParameter('st', $status);
        }
        if ($search = $request->query->get('q')) {
            $qb->andWhere('p.name LIKE :q OR p.sku LIKE :q')
                ->setParameter('q', '%' . $search . '%');
        }
        if ($minPrice = $request->query->get('price_min')) {
            $minPriceFloat = filter_var($minPrice, FILTER_VALIDATE_FLOAT);
            if ($minPriceFloat !== false) {
                $qb->andWhere('p.price >= :min')
                    ->setParameter('min', $minPriceFloat);
            }
        }

        if ($maxPrice = $request->query->get('price_max')) {
            $maxPriceFloat = filter_var($maxPrice, FILTER_VALIDATE_FLOAT);
            if ($maxPriceFloat !== false) {
                $qb->andWhere('p.price <= :max')
                    ->setParameter('max', $maxPriceFloat);
            }
        }

        $sort = $request->query->get('sort', 'name');
        $dir = $request->query->get('dir', 'asc');
        $qb->orderBy('p.' . $sort, $dir);

        $page = (int)($request->query->get('page', 1));
        $limit = (int)($request->query->get('limit', 20));
        $offset = ($page - 1) * $limit;
        $qb->setFirstResult($offset)->setMaxResults($limit);

        $products = $qb->getQuery()->getResult();
        $total = $this->repository->count([]);

        $data = array_map(fn(Product $p) => [
            'id' => $p->getId(),
            'sku' => $p->getSku(),
            'name' => $p->getName(),
            'category' => $p->getCategory(),
            'price' => $p->getPrice(),
            'stock' => $p->getStock(),
            'unit' => $p->getUnit(),
            'status' => $p->getStatus(),
        ], $products);

        return $this->json([
            'items' => $data,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total_items' => $total,
                'total_pages' => (int)ceil($total / $limit)
            ]
        ]);
    }
}
