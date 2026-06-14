<?php

require_once __DIR__ . '/ApiController.php';

final class TransactionApiController extends ApiController
{
    public function submitTransaction(): void
    {
        $this->requireRole(ROLE_KASIR);
        verifyCsrf();
        $id = (new TransactionService($this->pdo, $this->actor))->submit($this->payload());
        $this->jsonSuccess(['transaction_id' => $id], 201);
    }
}
