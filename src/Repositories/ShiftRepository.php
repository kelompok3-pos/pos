<?php

final class ShiftRepository extends ScopedRepository
{
    protected string $table = 'cashier_shifts';

    public function open(float $openingCash): int
    {
        $existing = $this->findAll(['kasir_id' => $this->actor->user_id, 'status' => 'open']);
        if ($existing !== []) {
            throw new RuntimeException('Cashier already has an open shift.');
        }
        $data = [
            'kasir_id' => $this->actor->user_id,
            'opened_at' => date('Y-m-d H:i:s'),
            'opening_cash' => max(0, $openingCash),
            'total_transactions' => 0,
            'status' => 'open',
        ];
        if ($this->hasColumn('total_revenue')) {
            $data['total_revenue'] = 0;
        }
        $id = $this->insert($data);
        $_SESSION['shift_id'] = $id;
        AuditLogger::log($this->actor, 'OPEN_SHIFT', 'cashier_shifts', $id, null, $data, $this->pdo);
        return $id;
    }

    public function close(int $id, float $closingCash): bool
    {
        $shift = $this->findById($id);
        $data = [
            'closed_at' => date('Y-m-d H:i:s'),
            'closing_cash' => max(0, $closingCash),
            'status' => 'closed',
        ];
        if ($this->hasColumn('expected_cash')) {
            $expected = (float) ($shift['opening_cash'] ?? 0) + (float) ($shift['total_revenue'] ?? 0);
            $data['expected_cash'] = $expected;
            $data['cash_difference'] = $closingCash - $expected;
        }
        $closed = $this->update($id, $data);
        if ($closed) {
            unset($_SESSION['shift_id']);
            AuditLogger::log($this->actor, 'CLOSE_SHIFT', 'cashier_shifts', $id, $shift, $data, $this->pdo);
        }
        return $closed;
    }
}
