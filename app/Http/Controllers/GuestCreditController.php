<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestCredit;

class GuestCreditController extends Controller {
    public function storeOrUpdate(Request $request) {
        $ip = $request->ip();
        $today = now()->toDateString();

        $guest = GuestCredit::firstOrCreate(
            ['device_signature' => $ip],
            ['credits' => 10, 'last_reset' => $today]
        );

        if ($guest->last_reset !== $today) {
            $guest->update([
                'credits' => 10,
                'last_reset' => $today
            ]);
        }

        return response()->json(['credits' => $guest->credits]);
    }

    public function deductCredit(Request $request) {
        $ip = $request->ip();

        $guest = GuestCredit::where('device_signature', $ip)->first();

        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], 404);
        }

        if ($guest->credits <= 0) {
            return response()->json(['error' => 'No credits left'], 403);
        }

        $guest->decrement('credits');

        return response()->json(['credits' => $guest->credits]);
    }
}
