<?php

require_once __DIR__ . '/../Controller.php';

final class KasirShiftController extends Controller
{
    private ActorContext $actor;
    private ShiftRepository $shifts;

    public function __construct()
    {
        $this->actor = ActorContext::fromSession();
        $this->actor->requireRole('kasir');
        $this->shifts = new ShiftRepository(getConnection(), $this->actor);
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
        $this->shifts->open((float) ($_POST['opening_cash'] ?? 0));
        flash('success', 'Shift berhasil dibuka.');
        $this->redirect('/kasir/shift');
    }

    public function close(): void
    {
        verifyCsrf();
        $id = (int) ($_POST['id'] ?? 0);
        $shift = assertBelongsToStore('cashier_shifts', $id, $this->actor->requireStoreId());
        if ((int) $shift['kasir_id'] !== $this->actor->user_id || $shift['status'] !== 'open') {
            throw new UnauthorizedException('Shift tidak dapat ditutup.');
        }
        $this->shifts->close($id, (float) ($_POST['closing_cash'] ?? 0));
        flash('success', 'Shift berhasil ditutup.');
        $this->redirect('/kasir/shift');
    }
}
