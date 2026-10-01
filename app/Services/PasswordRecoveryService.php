<?php

namespace App\Services;

use App\Mail\PasswordRecoveryOtp;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

class PasswordRecoveryService
{
    private const OTP_TTL_MINUTES = 10;

    private const MAX_ATTEMPTS = 5;

    public function sendCode(string $email): void
    {
        $normalizedEmail = Str::lower(trim($email));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$normalizedEmail])->first();

        if (! $user) {
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::query()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'code_hash' => Hash::make($code),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(self::OTP_TTL_MINUTES),
                'verified_at' => null,
            ]
        );

        try {
            Mail::to($user->email)->send(new PasswordRecoveryOtp($code));
        } catch (Throwable $exception) {
            Log::error('No se pudo enviar el correo de recuperación de contraseña.', [
                'exception' => $exception::class,
            ]);
        }
    }

    public function verifyCode(string $email, string $code): ?User
    {
        $user = User::query()->whereRaw('LOWER(email) = ?', [Str::lower(trim($email))])->first();

        if (! $user) {
            return null;
        }

        return DB::transaction(function () use ($user, $code) {
            $otp = PasswordResetOtp::query()->where('user_id', $user->id)->lockForUpdate()->first();

            if (! $otp || $otp->expires_at->isPast() || $otp->attempts >= self::MAX_ATTEMPTS || $otp->verified_at) {
                return null;
            }

            if (! Hash::check($code, $otp->code_hash)) {
                $otp->increment('attempts');

                return null;
            }

            $otp->forceFill(['verified_at' => now()])->save();

            return $user;
        });
    }

    public function canReset(int $userId): bool
    {
        return PasswordResetOtp::query()
            ->where('user_id', $userId)
            ->whereNotNull('verified_at')
            ->where('expires_at', '>', now())
            ->exists();
    }

    public function resetPassword(int $userId, string $password): bool
    {
        return DB::transaction(function () use ($userId, $password) {
            $otp = PasswordResetOtp::query()->where('user_id', $userId)->lockForUpdate()->first();

            if (! $otp || ! $otp->verified_at || $otp->expires_at->isPast()) {
                return false;
            }

            $user = User::query()->whereKey($userId)->lockForUpdate()->first();

            if (! $user) {
                return false;
            }

            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
            $user->tokens()->delete();
            $otp->delete();

            return true;
        });
    }
}
