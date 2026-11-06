<?php

namespace App\Support;

class TotpService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random Base32 secret key.
     */
    public function generateSecret(int $length = 32): string
    {
        $secret = '';
        $maxIndex = strlen(self::BASE32_CHARS) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $maxIndex)];
        }

        return $secret;
    }

    /**
     * Decode a Base32 string to raw binary.
     */
    public function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $b32));
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $binary;
    }

    /**
     * Calculate 6-digit TOTP code for a given timestamp and period.
     */
    public function getCode(string $secret, ?int $timestamp = null, int $period = 60): string
    {
        $timestamp = $timestamp ?? time();
        $timeCounter = (int) floor($timestamp / $period);

        $binaryCounter = pack('N*', 0) . pack('N*', $timeCounter);
        $binaryKey = $this->base32Decode($secret);

        $hash = hash_hmac('sha1', $binaryCounter, $binaryKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncatedHash = substr($hash, $offset, 4);

        $code = unpack('N', $truncatedHash)[1] & 0x7FFFFFFF;

        return str_pad((string) ($code % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify the 6-digit code with a 1-minute validity limit.
     * We check both standard 60-second period and 30-second window (covering 60s)
     * to ensure compatibility across all authenticator apps (Google Authenticator, Authy, Microsoft Authenticator).
     */
    public function verify(string $secret, string $code, int $period = 60): bool
    {
        $cleanCode = preg_replace('/\D/', '', $code);
        if (strlen($cleanCode) !== 6) {
            return false;
        }

        $now = time();

        // 1. Check with 60-second period (current and previous interval)
        for ($offset = -1; $offset <= 1; $offset++) {
            $expected = $this->getCode($secret, $now + ($offset * 60), 60);
            if (hash_equals($expected, $cleanCode)) {
                return true;
            }
        }

        // 2. Also check with standard 30-second interval within 60-second window
        for ($offset = -1; $offset <= 0; $offset++) {
            $expected = $this->getCode($secret, $now + ($offset * 30), 30);
            if (hash_equals($expected, $cleanCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate 8 recovery backup codes formatted like ABCD-EF12.
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;

        for ($i = 0; $i < $count; $i++) {
            $part1 = '';
            $part2 = '';
            for ($j = 0; $j < 4; $j++) {
                $part1 .= $chars[random_int(0, $max)];
                $part2 .= $chars[random_int(0, $max)];
            }
            $codes[] = "{$part1}-{$part2}";
        }

        return $codes;
    }

    /**
     * Build standard otpauth URI for QR codes.
     */
    public function getOtpAuthUri(string $accountEmail, string $secret, string $issuer = 'HiOwy', int $period = 60): string
    {
        $encodedIssuer = rawurlencode($issuer);
        $encodedAccount = rawurlencode($accountEmail);

        return "otpauth://totp/{$encodedIssuer}:{$encodedAccount}?secret={$secret}&issuer={$encodedIssuer}&period={$period}&digits=6";
    }

    /**
     * QuickChart QR image URL for authenticator setup.
     */
    public function getQrCodeUrl(string $accountEmail, string $secret, string $issuer = 'HiOwy', int $period = 60): string
    {
        $uri = $this->getOtpAuthUri($accountEmail, $secret, $issuer, $period);

        return 'https://quickchart.io/qr?text=' . urlencode($uri) . '&size=200&ecLevel=M&margin=1';
    }
}
