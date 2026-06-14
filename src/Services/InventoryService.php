<?php

final class InventoryService
{
    private ProductRepository $products;
    private StockRepository $movements;

    public function __construct(private PDO $pdo, private ActorContext $actor)
    {
        $actor->requireRole(ROLE_ADMIN);
        $this->products = new ProductRepository($pdo, $actor);
        $this->movements = new StockRepository($pdo, $actor);
    }

    public function adjust(int $productId, int $delta, string $reason): bool
    {
        if ($productId <= 0 || $delta === 0) {
            throw new InvalidArgumentException('Invalid stock adjustment.');
        }
        $this->pdo->beginTransaction();
        try {
            $product = $this->products->findActiveById($productId);
            if ($product === null) {
                throw new NotFoundException('Product not found.');
            }
            $before = (int) $product['stock'];
            if (!$this->products->adjustStock($productId, $delta)) {
                throw new RuntimeException('Stock cannot be negative.');
            }
            $this->movements->record(
                $productId,
                $delta > 0 ? 'in' : 'out',
                abs($delta),
                $before,
                $before + $delta,
                $reason
            );
            $this->pdo->commit();
            return true;
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
