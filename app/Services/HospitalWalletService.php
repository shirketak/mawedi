<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\WalletTransactionType;
use App\Models\Admin;
use App\Models\Hospital;
use App\Models\HospitalWallet;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class HospitalWalletService
{
    public function __construct(
        private readonly AuditLogService $auditLogService,
    ) {}

    public function ensureWallet(Hospital $hospital): HospitalWallet
    {
        return HospitalWallet::firstOrCreate(
            ['hospital_id' => $hospital->id],
            [
                'balance' => 0,
                'total_deposits' => 0,
                'total_deductions' => 0,
            ],
        );
    }

    public function listTransactions(Hospital $hospital, int $perPage = 20): LengthAwarePaginator
    {
        return WalletTransaction::query()
            ->where('hospital_id', $hospital->id)
            ->with('performedBy')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function deposit(Hospital $hospital, float $amount, string $reason, ?Model $performer = null): WalletTransaction
    {
        return $this->applyTransaction($hospital, WalletTransactionType::Deposit, $amount, $reason, $performer);
    }

    public function deduct(Hospital $hospital, float $amount, string $reason, ?Model $performer = null): WalletTransaction
    {
        return $this->applyTransaction($hospital, WalletTransactionType::Deduction, $amount, $reason, $performer);
    }

    public function adjust(Hospital $hospital, float $newBalance, string $reason, ?Model $performer = null): WalletTransaction
    {
        if ($newBalance < 0) {
            throw new InvalidArgumentException('الرصيد لا يمكن أن يكون سالباً.');
        }

        return DB::transaction(function () use ($hospital, $newBalance, $reason, $performer) {
            $wallet = $this->ensureWallet($hospital);
            $oldBalance = (float) $wallet->balance;
            $difference = $newBalance - $oldBalance;

            $wallet->update([
                'balance' => $newBalance,
                'total_deposits' => $difference > 0
                    ? $wallet->total_deposits + $difference
                    : $wallet->total_deposits,
                'total_deductions' => $difference < 0
                    ? $wallet->total_deductions + abs($difference)
                    : $wallet->total_deductions,
            ]);

            $transaction = WalletTransaction::create([
                'hospital_id' => $hospital->id,
                'hospital_wallet_id' => $wallet->id,
                'type' => WalletTransactionType::Adjustment,
                'amount' => abs($difference),
                'balance_before' => $oldBalance,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'performed_by_type' => $performer?->getMorphClass(),
                'performed_by_id' => $performer?->getKey(),
            ]);

            $this->auditLogService->log(
                AuditAction::WalletAdjustment,
                $hospital,
                ['balance' => $oldBalance],
                ['balance' => $newBalance, 'reason' => $reason],
                $performer instanceof Admin ? $performer : null,
            );

            return $transaction;
        });
    }

    public function deductBookingFee(Hospital $hospital, float $amount, int $bookingId): ?WalletTransaction
    {
        if ($amount <= 0) {
            return null;
        }

        return DB::transaction(function () use ($hospital, $amount, $bookingId) {
            $wallet = $this->ensureWallet($hospital);

            if ((float) $wallet->balance < $amount) {
                return null;
            }

            $oldBalance = (float) $wallet->balance;
            $newBalance = $oldBalance - $amount;

            $wallet->update([
                'balance' => $newBalance,
                'total_deductions' => $wallet->total_deductions + $amount,
            ]);

            return WalletTransaction::create([
                'hospital_id' => $hospital->id,
                'hospital_wallet_id' => $wallet->id,
                'type' => WalletTransactionType::BookingFee,
                'amount' => $amount,
                'balance_before' => $oldBalance,
                'balance_after' => $newBalance,
                'reason' => 'رسوم حجز ناجح',
                'meta' => ['booking_id' => $bookingId],
            ]);
        });
    }

    private function applyTransaction(
        Hospital $hospital,
        WalletTransactionType $type,
        float $amount,
        string $reason,
        ?Model $performer = null,
    ): WalletTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('المبلغ يجب أن يكون أكبر من صفر.');
        }

        return DB::transaction(function () use ($hospital, $type, $amount, $reason, $performer) {
            $wallet = $this->ensureWallet($hospital);
            $oldBalance = (float) $wallet->balance;

            $newBalance = match ($type) {
                WalletTransactionType::Deposit => $oldBalance + $amount,
                WalletTransactionType::Deduction => $oldBalance - $amount,
                default => $oldBalance,
            };

            if ($newBalance < 0) {
                throw new InvalidArgumentException('الرصيد غير كافٍ.');
            }

            $wallet->update([
                'balance' => $newBalance,
                'total_deposits' => $type === WalletTransactionType::Deposit
                    ? $wallet->total_deposits + $amount
                    : $wallet->total_deposits,
                'total_deductions' => $type === WalletTransactionType::Deduction
                    ? $wallet->total_deductions + $amount
                    : $wallet->total_deductions,
            ]);

            $transaction = WalletTransaction::create([
                'hospital_id' => $hospital->id,
                'hospital_wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $oldBalance,
                'balance_after' => $newBalance,
                'reason' => $reason,
                'performed_by_type' => $performer?->getMorphClass(),
                'performed_by_id' => $performer?->getKey(),
            ]);

            $auditAction = $type === WalletTransactionType::Deposit
                ? AuditAction::WalletDeposit
                : AuditAction::WalletDeduction;

            $this->auditLogService->log(
                $auditAction,
                $hospital,
                ['balance' => $oldBalance],
                ['balance' => $newBalance, 'amount' => $amount, 'reason' => $reason],
                $performer instanceof Admin ? $performer : null,
            );

            return $transaction;
        });
    }
}
