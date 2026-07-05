<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Services\HospitalWalletService;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        private readonly HospitalWalletService $walletService,
    ) {}

    public function index(): View
    {
        $hospital = auth('hospital')->user()->hospital->load('wallet');
        $wallet = $this->walletService->ensureWallet($hospital);
        $transactions = $this->walletService->listTransactions($hospital, 10);

        return view('hospital.account.index', compact('hospital', 'wallet', 'transactions'));
    }
}
