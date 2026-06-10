<?php

class StockMovement
{
    private StockRepository $repository;

    public function __construct()
    {
        $this->repository = new StockRepository(getConnection(), ActorContext::fromSession());
    }

    public function record(int $productId, int $userId, string $type, int $quantity, int $before, int $after, string $note = ''): bool
    {
        return $this->repository->record($productId, $type, $quantity, $before, $after, $note) > 0;
    }

    public function getAll(): array
    {
        return $this->repository->history();
    }
}
