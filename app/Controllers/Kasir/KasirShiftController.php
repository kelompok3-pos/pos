<?php

require_once __DIR__ . '/../Controller.php';

final class KasirShiftController extends Controller
{
    private ActorContext $actor;
    private ShiftRepository $shifts;
    private ShiftService $shiftService;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->actor->requireRole(ROLE_KASIR);
        $pdo = getConnection();
        $this->shifts = new ShiftRepository($pdo, $this->actor);
        $this->shiftService = new ShiftService($pdo, $this->actor);
    }

    public function index(): void
    {
        $this->view('kasir/shift/index', [
            'title' => 'Shift Kasir',
            'shifts' => $this->shifts->findAll(['kasir_id' => $this->actor->user_id]),
        ]);
    }

    public function open(): void
    {
        verifyCsrf();
        $this->shiftService->openShift($this->actor->user_id, (float) ($_POST['opening_cash'] ?? 0));
        flash('success', 'Shift berhasil dibuka.');
        $this->redirect('/kasir/shift');
    }

    public function close(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $this->shiftService->closeShift($id, (float) ($_POST['closing_cash'] ?? 0));
        flash('success', 'Shift berhasil ditutup.');
        $this->redirect('/kasir/shift');
    }
}
