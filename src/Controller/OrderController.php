<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use App\Service\OrderCollector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order', name: 'app_order')]
class OrderController extends AbstractController
{
    #[Route('/create', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request, EntityManagerInterface $entityManager, OrderCollector $orderCollector): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $requiredFields = ['customerName', 'customerEmail', 'orderItems'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field])) {
                return $this->json(['error' => "Missing required field: $field"], Response::HTTP_BAD_REQUEST);
            }
        }

        $order = new Order();
        $order->setOrderDate(new \DateTime());
        $order->setCustomerName($requestData['customerName']);
        $order->setCustomerEmail($requestData['customerEmail']);
        $order->setStreet($requestData['street']);
        $order->setHouseNumber($requestData['houseNumber']);
        $order->setApartmentNumber($requestData['apartmentNumber'] ?? null);
        $order->setPostalCode($requestData['postalCode']);
        $order->setCity($requestData['city']);
        $order->setStatus('new');
        $order->setCurrency('PLN');

        if (!isset($requestData['orderItems']) || !is_array($requestData['orderItems'])) {
            return $this->json(['error' => 'Missing or invalid orderItems field'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($requestData['orderItems'] as $item) {
            if (!isset($item['productId']) || !isset($item['quantity'])) {
                return $this->json(['error' => 'Missing productId or quantity in orderItems'], Response::HTTP_BAD_REQUEST);
            }

            $productId = $item['productId'];
            $quantity = $item['quantity'];

            $product = $entityManager->getRepository(Product::class)->find($productId);

            if (!$product) {
                return $this->json(['error' => 'Product not found. Id ' . $productId], Response::HTTP_BAD_REQUEST);
            }

            if (!$product->getPrice()) {
                return $this->json(['error' => 'No product price. Id ' . $productId], Response::HTTP_BAD_REQUEST);
            }

            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $order->addOrderItem($orderItem);
            if ($product->getQuantity() - $quantity < 0) {
                return $this->json(['error' => 'Not enough products in stock. Id ' . $product->getId()], Response::HTTP_BAD_REQUEST);
            }
            $product->setQuantity($product->getQuantity() - $quantity);
            $entityManager->persist($product);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        $orderInfo = $orderCollector->collect($order);
        $orderData = [
            'id' => $order->getId(),
            'orderDate' => $order->getOrderDate()->format('Y-m-d H:i:s'),
            'orderInfo' => $orderInfo ?? null,
            'orderCurrency' => $order->getCurrency() ?? null,
            'status' => $order->getStatus(),
        ];

        return $this->json($orderData, Response::HTTP_OK);
    }


    #[Route('/search/{orderId}', name: 'get_order', methods: ['GET'])]
    public function getOrder(int $orderId, EntityManagerInterface $entityManager, OrderCollector $orderCollector): JsonResponse
    {
        $order = $entityManager->getRepository(Order::class)->find($orderId);

        if (!$order) {
            return $this->json(['error' => 'Order not found'], Response::HTTP_NOT_FOUND);
        }
        $orderInfo = $orderCollector->collect($order);
        $orderData = [
            'id' => $order->getId(),
            'orderDate' => $order->getOrderDate()->format('Y-m-d H:i:s'),
            'orderInfo' => $orderInfo ?? null,
            'orderCurrency' => $order->getCurrency() ?? null,
            'status' => $order->getStatus(),
        ];

        return $this->json($orderData, Response::HTTP_OK);
    }
}
