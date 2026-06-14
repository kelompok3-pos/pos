<?php

return [
    '/api/products/search' => ['Api/ProductApiController', 'productSearch', ['methods' => ['GET'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/api/cart/calculate' => ['Api/CartApiController', 'calculateCart', ['methods' => ['POST'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/api/transactions/submit' => ['Api/TransactionApiController', 'submitTransaction', ['methods' => ['POST'], 'roles' => [ROLE_KASIR]]],
    '/api/stock/product-info' => ['Api/ProductApiController', 'productInfo', ['methods' => ['GET'], 'roles' => [ROLE_ADMIN, ROLE_KASIR]]],
    '/api/reports/chart-daily' => ['Api/ReportApiController', 'chartDaily', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN, ROLE_ADMIN]]],
];
