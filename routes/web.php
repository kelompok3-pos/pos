<?php

return [
    '/' => ['HomeController', 'landing', ['methods' => ['GET']]],
    '/dashboard' => ['HomeController', 'index', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN, ROLE_ADMIN]]],
    '/login' => ['AuthController', 'loginForm', ['methods' => ['GET']]],
    '/login/authenticate' => ['AuthController', 'login', ['methods' => ['POST']]],
    '/logout' => ['AuthController', 'logout', ['methods' => ['POST'], 'roles' => [ROLE_SUPER_ADMIN, ROLE_ADMIN, ROLE_KASIR]]],
];
