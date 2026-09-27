<?php

namespace App\Helpers;

class SecurityHelper
{
    /**
     * Sanitasi nilai sel spreadsheet dari potensi CSV / Formula Injection.
     * Mencegah karakter formula (=, +, -, @, tab, return) dieksekusi oleh Excel / Calc.
     */
    public static function sanitizeSpreadsheetCell(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        // Cek apakah string diawali dengan pemicu formula Excel
        if (preg_match('/^[=+\-@\t\r]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}
