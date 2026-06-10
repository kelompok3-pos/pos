<?php

require_once __DIR__ . '/../Controller.php';

final class KasirDashboardController extends Controller
{
    public function index(): void
    {
        ActorContext::fromSession()->requireRole('admin');
        $this->redirect('/dashboard');
    }
}
