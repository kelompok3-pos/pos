<?php

require_once __DIR__ . '/ApiController.php';

final class CartApiController extends ApiController
{
    public function calculateCart(): void
    {
        $this->requireRole(ROLE_ADMIN, ROLE_KASIR);
        verifyCsrf();
        $calculation = (new TransactionService($this->pdo, $this->actor))->calculate($this->payload());
        $this->jsonSuccess($calculation);
    }
}
