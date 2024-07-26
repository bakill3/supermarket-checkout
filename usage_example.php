<?php

require_once 'checkout_system.php';

$items = [
    new Item('A', 50),
    new Item('B', 75),
    new Item('C', 25),
    new Item('D', 150),
    new Item('E', 200)
];

// Define special prices
$specialPrices = [
    new SpecialPrice('multipriced', 'B', 2, 125),
    new SpecialPrice('buy_n_get_1_free', 'C', 3, 0),
    new SpecialPrice('meal_deal', 'D', 1, 300, ['D', 'E'])
];

$checkout = new Checkout($specialPrices);

$checkout->scan('A');
$checkout->scan('B');
$checkout->scan('B');
$checkout->scan('C');
$checkout->scan('C');
$checkout->scan('C');
$checkout->scan('D');
$checkout->scan('E');

// total price
$total = $checkout->total();
echo "Total price: " . ($total / 100.0) . " €\n";
