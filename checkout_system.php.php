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
    public $specialPrice;
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
    private $items = [];
    private $itemCounts = [];

    public function __construct($pricingRules) {
        $this->pricingRules = $pricingRules;
    }

    public function scan($item) {
        $this->items[] = $item;
        if (!isset($this->itemCounts[$item->sku])) {
            $this->itemCounts[$item->sku] = 0;
        }
        $this->itemCounts[$item->sku]++;
    }

    public function total() {
        $total = 0;
        $appliedSpecials = [];

        foreach ($this->items as $item) {
            if (!isset($appliedSpecials[$item->sku])) {
                $appliedSpecials[$item->sku] = 0;
            }
        }

        foreach ($this->pricingRules as $rule) {
            if ($rule->type == 'multipriced') {
                $sku = $rule->sku;
                $qty = $this->itemCounts[$sku];
                $specialQty = $rule->requiredQty;
                $specialPrice = $rule->specialPrice;

                $total += intdiv($qty, $specialQty) * $specialPrice;
                $total += ($qty % $specialQty) * $this->getUnitPrice($sku);
                $appliedSpecials[$sku] += $qty;
            } elseif ($rule->type == 'buy_n_get_1_free') {
                $sku = $rule->sku;
                $qty = $this->itemCounts[$sku];
                $specialQty = $rule->requiredQty;

                $total += (intdiv($qty, $specialQty) * ($specialQty - 1) + ($qty % $specialQty)) * $this->getUnitPrice($sku);
                $appliedSpecials[$sku] += $qty;
            } elseif ($rule->type == 'meal_deal') {
                $relatedItems = $rule->relatedItems;
                $qty = min(array_map(function($sku) {
                    return $this->itemCounts[$sku];
                }, $relatedItems));

                $total += $qty * $rule->specialPrice;

                foreach ($relatedItems as $sku) {
                    $this->itemCounts[$sku] -= $qty;
                }
            }
        }

        foreach ($this->items as $item) {
            if ($this->itemCounts[$item->sku] > 0 && !in_array($item->sku, array_keys($appliedSpecials))) {
                $total += $this->getUnitPrice($item->sku);
            }
        }

        return $total;
    }

    private function getUnitPrice($sku) {
        foreach ($this->items as $item) {
            if ($item->sku == $sku) {
                return $item->unitPrice;
            }
        }
        return 0;
    }
}
