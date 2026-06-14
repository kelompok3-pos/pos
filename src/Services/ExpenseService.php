<?php

final class ExpenseService
{
    private ExpenseRepository $expenses;

    public function __construct(PDO $pdo, private ActorContext $actor)
    {
        $actor->requireRole(ROLE_ADMIN);
        $this->expenses = new ExpenseRepository($pdo, $actor);
    }

    public function create(array $data): int
    {
        return $this->expenses->createExpense(
            (string) ($data['category'] ?? ''),
            (float) ($data['amount'] ?? 0),
            (string) ($data['description'] ?? ''),
            (string) ($data['expense_date'] ?? '')
        );
    }

    public function update(int $id, array $data): bool
    {
        return $this->expenses->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->expenses->update($id, ['deleted_at' => date('Y-m-d H:i:s')]);
    }
}
