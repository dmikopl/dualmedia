<?php

namespace App\Service;

use App\Entity\Order;

class OrderCollector
{
    private $orderTotalCalculator;
    private $vatCalculator;
    private $combinedTotalCalculator;

    public function __construct(
        OrderTotalCalculator $orderTotalCalculator,
        VatCalculator $vatCalculator,
        CombinedTotalCalculator $combinedTotalCalculator
    ) {
        $this->orderTotalCalculator = $orderTotalCalculator;
        $this->vatCalculator = $vatCalculator;
        $this->combinedTotalCalculator = $combinedTotalCalculator;
    }

    public function collect(Order $order): array
    {
        $total = $this->orderTotalCalculator->calculateTotal($order);
        $vat = $this->vatCalculator->calculateVat($order);
        $combinedTotal = $this->combinedTotalCalculator->calculateCombinedTotal($total, $vat);

        return [
            'total' => $total,
            'vat' => $vat,
            'combinedTotal' => $combinedTotal,
        ];
    }
}
