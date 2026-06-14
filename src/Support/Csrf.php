<?php

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            self::regenerate();
        }
        return (string) $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . Escaper::escape(self::token()) . '">';
    }

    public static function verify(): void
    {
        $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
            http_response_code(403);
            exit('CSRF token tidak valid. Silakan refresh halaman dan coba lagi.');
        }
    }

    public static function regenerate(): void
    {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
