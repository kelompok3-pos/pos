<?php

return [
    '/kasir/transaction' => ['Kasir/KasirTransactionController', 'index', ['methods' => ['GET'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/add' => ['Kasir/KasirTransactionController', 'add', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/update' => ['Kasir/KasirTransactionController', 'update', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/remove' => ['Kasir/KasirTransactionController', 'remove', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/clear' => ['Kasir/KasirTransactionController', 'clear', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/checkout' => ['Kasir/KasirTransactionController', 'checkout', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/transaction/receipt' => ['Kasir/KasirTransactionController', 'receipt', ['methods' => ['GET'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/kasir/my-transactions' => ['Kasir/KasirTransactionController', 'myTransactions', ['methods' => ['GET'], 'roles' => [ROLE_KASIR]]],
    '/kasir/shift' => ['Kasir/KasirShiftController', 'index', ['methods' => ['GET'], 'roles' => [ROLE_KASIR]]],
    '/kasir/shift/open' => ['Kasir/KasirShiftController', 'open', ['methods' => ['POST'], 'roles' => [ROLE_KASIR]]],
    '/kasir/shift/close' => ['Kasir/KasirShiftController', 'close', ['methods' => ['POST'], 'roles' => [ROLE_KASIR]]],
];
