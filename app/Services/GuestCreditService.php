<?php

namespace App\Services;

use App\Models\GuestCredit;
use Illuminate\Http\Request;

class GuestCreditService
{
    protected $ip;

    public function __construct(Request $request)
    {
        $this->ip = $request->ip();
    }

    public function hasEnoughCredits(int $required): bool
    {
        $guest = GuestCredit::where('device_signature', $this->ip)->first();

        if (!$guest || $guest->credits < $required) {
            return false;
        }

        return true;
    }

    public function deductCredits(int $count): void
    {
        $guest = GuestCredit::where('device_signature', $this->ip)->first();

        if ($guest) {
            $guest->decrement('credits', $count);
        }
    }

    public function getCurrentCredits(): int
    {
        return GuestCredit::where('device_signature', $this->ip)->value('credits') ?? 0;
    }
}
