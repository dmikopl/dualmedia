<?php

namespace App\Service;

use App\Entity\Order;

class OrderTotalCalculator
{
    public function calculateTotal(Order $order): float
    {
        $items = $order->getOrderItems();
        $total = 0;

        foreach ($items as $item) {
            $total += $item->getProduct()->getPrice() * $item->getQuantity();
        }

        return $total;
    }
}

