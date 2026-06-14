<?php

final class ShiftService
{
    private ShiftRepository $shifts;

    public function __construct(private PDO $pdo, private ActorContext $actor)
    {
        $actor->requireRole(ROLE_KASIR);
        $this->shifts = new ShiftRepository($pdo, $actor);
    }

    public function openShift(int $userId, float $openingCash): int
    {
        if ($userId !== $this->actor->user_id) {
            throw new UnauthorizedException('A cashier can only open their own shift.');
        }
        $this->pdo->beginTransaction();
        try {
            $lock = $this->pdo->prepare('SELECT id FROM users WHERE id = ? AND store_id = ? FOR UPDATE');
            $lock->execute([$userId, $this->actor->requireStoreId()]);
            if (!$lock->fetchColumn()) {
                throw new UnauthorizedException('Cashier does not belong to the current store.');
            }
            $shiftId = $this->shifts->open($openingCash);
            $this->pdo->commit();
            return $shiftId;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    public function closeShift(int $shiftId, float $closingCash): bool
    {
        return $this->shifts->close($shiftId, $closingCash);
    }
}
