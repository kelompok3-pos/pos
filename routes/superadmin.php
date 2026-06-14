<?php

return [
    '/superadmin/dashboard' => ['SuperAdmin/SuperAdminController', 'dashboard', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN]]],
    '/superadmin/reports' => ['SuperAdmin/SuperAdminController', 'reports', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN]]],
    '/superadmin/reports/export' => ['SuperAdmin/SuperAdminController', 'exportReports', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN]]],
    '/superadmin/stores' => ['SuperAdmin/SuperAdminController', 'stores', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN]]],
    '/superadmin/audit' => ['SuperAdmin/SuperAdminController', 'audit', ['methods' => ['GET'], 'roles' => [ROLE_SUPER_ADMIN]]],
];
