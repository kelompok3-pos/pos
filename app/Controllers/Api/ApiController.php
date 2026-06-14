<?php

require_once __DIR__ . '/../Controller.php';

abstract class ApiController extends Controller
{
    protected ActorContext $actor;
    protected PDO $pdo;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->pdo = getConnection();
    }

    protected function payload(): array
    {
        $payload = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($payload)) {
            throw new InvalidArgumentException('Payload JSON tidak valid.');
        }
        return $payload;
    }

    protected function jsonSuccess(array $data, int $status = 200): void
    {
        $this->respond(['success' => true, 'data' => $data], $status);
    }

    protected function jsonError(string $message, int $status): void
    {
        $this->respond(['success' => false, 'message' => $message], $status);
    }

    protected function requireRole(string ...$roles): void
    {
        $this->actor->requireRole(...$roles);
    }

    private function respond(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
