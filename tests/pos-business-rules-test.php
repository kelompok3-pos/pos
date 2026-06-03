<?php

/**
 * Lightweight business-rule checks for the PHP Native POS app.
 *
 * This file intentionally avoids bootstrapping the full app/database. It tests
 * calculation rules that should remain true in controller/model workflows.
 */

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function cartTotal(array $items): float
{
    return array_reduce($items, fn(float $sum, array $item): float => $sum + (float) $item['subtotal'], 0.0);
}

function calculateChange(float $paidAmount, float $totalPrice): float
{
    if ($paidAmount < $totalPrice) {
        throw new InvalidArgumentException('Payment must not be less than total.');
    }

    return $paidAmount - $totalPrice;
}

function isValidStock(int $stock): bool
{
    return $stock >= 0;
}

function canAddQuantityToCart(int $availableStock, int $currentQuantity, int $addedQuantity): bool
{
    return $addedQuantity > 0 && ($currentQuantity + $addedQuantity) <= $availableStock;
}

$cart = [
    ['name' => 'Kopi Arabica', 'quantity' => 2, 'subtotal' => 50000],
    ['name' => 'Susu Segar', 'quantity' => 1, 'subtotal' => 12000],
    ['name' => 'Air Mineral', 'quantity' => 2, 'subtotal' => 10000],
];

assertTrue(cartTotal($cart) === 72000.0, 'Cart total should equal sum of item subtotals.');
assertTrue(calculateChange(100000, cartTotal($cart)) === 28000.0, 'Change should equal paid amount minus total.');
assertTrue(isValidStock(0), 'Zero stock should be valid.');
assertTrue(!isValidStock(-1), 'Negative stock should be invalid.');
assertTrue(canAddQuantityToCart(10, 3, 2), 'Cart addition within stock should be allowed.');
assertTrue(!canAddQuantityToCart(4, 3, 2), 'Cart addition beyond stock should be rejected.');

try {
    calculateChange(50000, cartTotal($cart));
    throw new RuntimeException('Underpayment should throw an exception.');
} catch (InvalidArgumentException $e) {
    // Expected path.
}

echo "PASS: POS business-rule tests passed.\n";
