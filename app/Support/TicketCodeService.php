<?php

namespace App\Support;

use Carbon\Carbon;
use DateTimeInterface;

class TicketCodeService
{
    /**
     * 31-character alphabet without ambiguous glyphs (0, O, 1, I, L).
     */
    public const ALPHABET = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     * Generate a random ticket code using cryptographically secure random_int.
     */
    public static function generateCode(int $length = 10): string
    {
        $max = strlen(self::ALPHABET) - 1;
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= self::ALPHABET[random_int(0, $max)];
        }

        return $code;
    }

    /**
     * Normalize user input ticket code (uppercase alphanumeric only).
     */
    public static function normalize(string $rawCode): string
    {
        return strtoupper((string) preg_replace('/[^A-Za-z0-9]/', '', trim($rawCode)));
    }

    /**
     * Compute HMAC-SHA256 hash using server pepper.
     */
    public static function hash(string $code): string
    {
        $normalized = self::normalize($code);
        $pepper = (string) (config('app.ticket_pepper') ?: config('app.key'));

        return hash_hmac('sha256', $normalized, $pepper);
    }

    /**
     * Bucket submission time into broad dayparts to avoid leaking exact HH:MM.
     */
    public static function daypart(?DateTimeInterface $dateTime = null): string
    {
        $dt = $dateTime
            ? Carbon::instance($dateTime)->setTimezone('Asia/Jakarta')
            : Carbon::now('Asia/Jakarta');

        $hour = $dt->hour;

        if ($hour >= 5 && $hour < 11) {
            return 'pagi';
        }
        if ($hour >= 11 && $hour < 15) {
            return 'siang';
        }
        if ($hour >= 15 && $hour < 18) {
            return 'sore';
        }

        return 'malam';
    }
}
