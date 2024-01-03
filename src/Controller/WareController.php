<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class WareController extends AbstractController
{
    #[Route('/ware/new', name: 'app_ware')]
    public function add(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['name'], $requestData['price'], $requestData['quantity'])) {
            return $this->json(['message' => 'Invalid request format'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setName($requestData['name']);
        $product->setSku($requestData['sku'] ?? null);
        $product->setManufacturer($requestData['manufacturer'] ?? null);
        $product->setIsActive($requestData['isActive'] ?? true);
        $product->setPrice($requestData['price']);
        $product->setQuantity($requestData['quantity']);
        $product->setAddedAt(new \DateTime());

        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product added successfully'], JsonResponse::HTTP_OK);
    }
}
