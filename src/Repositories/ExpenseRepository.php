<?php

final class ExpenseRepository extends ScopedRepository
{
    protected string $table = 'expenses';

    public function createExpense(string $category, float $amount, string $description, string $date): int
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Expense amount must be positive.');
        }
        $data = [
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'expense_date' => $date,
        ];
        $data[$this->hasColumn('user_id') ? 'user_id' : 'created_by'] = $this->actor->user_id;
        return $this->insert($data);
    }
}
