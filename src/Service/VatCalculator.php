<?php

namespace App\Service;

use App\Entity\Order;

class VatCalculator
{
    public function calculateVat(Order $order): float
    {
        $items = $order->getOrderItems();
        $vat = 0;

        foreach ($items as $item) {
            $vat += round($item->getProduct()->getPrice() * $item->getQuantity() * 0.23, 2);
        }

        return $vat;
    }
}
