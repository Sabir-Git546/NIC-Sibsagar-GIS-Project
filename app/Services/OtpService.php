<?php

namespace App\Services;

use App\Models\OtpVerification;

use Illuminate\Support\Facades\Hash;

class OtpService
{

    public const LOGIN =
        'login';

    public const PASSWORD_RESET =
        'password_reset';
    /**
     * Generate OTP
     */
    public function generate(
        string $userid,
        string $purpose,
        int $expiryMinutes = 10
    ): string {

        // Remove previous OTPs
        OtpVerification::where(
            'userid',
            $userid
        )
        ->where(
            'purpose',
            $purpose
        )
        ->delete();

        $otp = (string) random_int(
            100000,
            999999
        );

        OtpVerification::create([

            'userid'      => $userid,

            'purpose'     => $purpose,

            'otp_hash'    => Hash::make($otp),

            'expires_at'  => now()
                ->addMinutes(
                    $expiryMinutes
                ),

            'is_verified' => false

        ]);

        return $otp;
    }

    /**
     * Verify OTP
     */
    public function verify(
        string $userid,
        string $purpose,
        string $otp
    ): bool {

        $record =
            OtpVerification::where(
                'userid',
                $userid
            )
            ->where(
                'purpose',
                $purpose
            )
            ->latest()
            ->first();

        if (!$record) {
            return false;
        }

        if ($record->is_verified) {
            return false;
        }

        if (
            now()->gt(
                $record->expires_at
            )
        ) {
            return false;
        }

        if (
            !Hash::check(
                $otp,
                $record->otp_hash
            )
        ) {
            return false;
        }

        $record->update([

            'is_verified' => true

        ]);

        return true;
    }

    /**
     * Cleanup OTPs
     */
    public function clear(
        string $userid,
        string $purpose
    ): void {

        OtpVerification::where(
            'userid',
            $userid
        )
        ->where(
            'purpose',
            $purpose
        )
        ->delete();
    }
}