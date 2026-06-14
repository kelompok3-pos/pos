<?php

$routes = array_merge(
    require BASE_PATH . '/routes/web.php',
    require BASE_PATH . '/routes/admin.php',
    require BASE_PATH . '/routes/kasir.php',
    require BASE_PATH . '/routes/superadmin.php',
    require BASE_PATH . '/routes/api.php',
);
