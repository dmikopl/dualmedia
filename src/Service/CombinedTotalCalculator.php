<?php

namespace App\Service;

class CombinedTotalCalculator
{
    public function calculateCombinedTotal(float $total, float $vat): float
    {
        $combinedTotal = $total + $vat;
        return round($combinedTotal, 2);
    }
}
