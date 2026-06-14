<?php

final class Formatter
{
    public static function currency(int|float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

}
