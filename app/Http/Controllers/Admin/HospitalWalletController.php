<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WalletTransactionRequest;
use App\Models\Hospital;
use App\Services\HospitalWalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HospitalWalletController extends Controller
{
    public function __construct(
        private readonly HospitalWalletService $walletService,
    ) {}

    public function index(Hospital $hospital): View
    {
        $this->authorize('manageWallet', $hospital);

        $wallet = $this->walletService->ensureWallet($hospital);
        $transactions = $this->walletService->listTransactions($hospital);

        return view('admin.hospitals.wallet', compact('hospital', 'wallet', 'transactions'));
    }

    public function store(WalletTransactionRequest $request, Hospital $hospital): RedirectResponse
    {
        $this->authorize('manageWallet', $hospital);

        $admin = auth('admin')->user();

        try {
            match ($request->action) {
                'deposit' => $this->walletService->deposit(
                    $hospital,
                    (float) $request->amount,
                    $request->reason,
                    $admin,
                ),
                'deduct' => $this->walletService->deduct(
                    $hospital,
                    (float) $request->amount,
                    $request->reason,
                    $admin,
                ),
                'adjust' => $this->walletService->adjust(
                    $hospital,
                    (float) $request->amount,
                    $request->reason,
                    $admin,
                ),
            };
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'تمت العملية بنجاح.');
    }
}
