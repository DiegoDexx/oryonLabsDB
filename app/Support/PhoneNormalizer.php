<?php

namespace App\Support;

class PhoneNormalizer
{
    /**
     * Normalizes a phone number to E.164 format (+34..., +44...).
     * Returns null if input is empty or invalid.
     */
    public static function normalize(?string $phone, string $defaultCountry = '34'): ?string
    {
        if ($phone === null) return null;

        $trimmed = trim($phone);
        if ($trimmed === '') return null;

        $hasPlus = str_starts_with($trimmed, '+');

        $digits = preg_replace('/[^0-9]/', '', $trimmed);
        if ($digits === '') return null;

        // 00XX... → +XX...
        if (str_starts_with($digits, '00')) {
            return '+' . substr($digits, 2);
        }

        // Already had + → keep prefix as-is
        if ($hasPlus) {
            return '+' . $digits;
        }

        // 34XXXXXXXXX (11 digits) → Spanish full number
        if (str_starts_with($digits, '34') && strlen($digits) === 11) {
            return '+' . $digits;
        }

        // 44... (12+ digits) → UK full number
        if (str_starts_with($digits, '44') && strlen($digits) >= 12) {
            return '+' . $digits;
        }

        // Local Spanish mobile/landline: 9 digits starting with 6, 7, 8, or 9
        if (strlen($digits) === 9 && in_array($digits[0], ['6', '7', '8', '9'])) {
            return '+34' . $digits;
        }

        // Local UK number: starts with 0, 10–11 digits
        if (str_starts_with($trimmed, '0') && strlen($digits) >= 10) {
            return '+44' . ltrim($digits, '0');
        }

        // Fallback: prepend default country prefix
        return '+' . $defaultCountry . $digits;
    }
}