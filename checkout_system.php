<?php

class Item {
    public $sku;
    public $unitPrice;

    public function __construct($sku, $unitPrice) {
        $this->sku = $sku;
        $this->unitPrice = $unitPrice;
    }
}

class SpecialPrice {
    public $type;
    public $sku;
    public $required_quantity;
    public $special_price;
    public $relatedItems;

    public function __construct($type, $sku, $required_quantity, $special_price, $relatedItems = []) {
        $this->type = $type;
        $this->sku = $sku;
        $this->required_quantity = $required_quantity;
        $this->special_price = $special_price;
        $this->relatedItems = $relatedItems;
    }
}

class Checkout {
    private $pricingRules;
    private $itemCounts = [];

    public function __construct($pricingRules) {
        $this->pricingRules = $pricingRules;
    }

    public function scan($sku) {
        if (!isset($this->itemCounts[$sku])) {
            $this->itemCounts[$sku] = 0;
        }
        $this->itemCounts[$sku]++;
    }

    public function total() {
        global $items;
        $total = 0;

        // Apply special prices first
        foreach ($this->pricingRules as $rule) {
            if ($rule->type == 'multipriced') {
                $sku = $rule->sku;
                $qty = $this->itemCounts[$sku] ?? 0;
                $specialQty = $rule->required_quantity;
                $specialPrice = $rule->special_price;

                $total += intdiv($qty, $specialQty) * $specialPrice;
                $total += ($qty % $specialQty) * $this->getUnitPrice($sku, $items);
            } elseif ($rule->type == 'buy_n_get_1_free') {
                $sku = $rule->sku;
                $qty = $this->itemCounts[$sku] ?? 0;
                $specialQty = $rule->required_quantity;

                $total += (intdiv($qty, $specialQty) * ($specialQty - 1) + ($qty % $specialQty)) * $this->getUnitPrice($sku, $items);
            } elseif ($rule->type == 'meal_deal') {
                $relatedItems = $rule->relatedItems;
                $qty = min(array_map(function($sku) {
                    return $this->itemCounts[$sku] ?? 0;
                }, $relatedItems));

                $total += $qty * $rule->special_price;

                foreach ($relatedItems as $sku) {
                    $this->itemCounts[$sku] -= $qty;
                }
            }
        }

        // Add remaining items without special prices
        foreach ($this->itemCounts as $sku => $count) {
            if ($count > 0) {
                $total += $count * $this->getUnitPrice($sku, $items);
            }
        }

        return $total;
    }

    private function getUnitPrice($sku, $items) {
        foreach ($items as $item) {
            if ($item->sku == $sku) {
                return $item->unitPrice;
            }
        }
        return 0;
    }
}
