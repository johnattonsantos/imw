<?php

namespace App\Support;

class CpfCnpj
{
    public static function normalize(?string $value): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $value));
    }

    public static function isValidCpfOrCnpj(?string $value): bool
    {
        $document = self::normalize($value);

        if (preg_match('/^\d{11}$/', $document)) {
            return self::isValidCpf($document);
        }

        return self::isValidCnpj($document);
    }

    public static function isValidCpf(?string $value): bool
    {
        $cpf = preg_replace('/\D/', '', (string) $value);

        if (!preg_match('/^\d{11}$/', $cpf)) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $digit = 0;

            for ($c = 0; $c < $t; $c++) {
                $digit += (int) $cpf[$c] * (($t + 1) - $c);
            }

            $digit = ((10 * $digit) % 11) % 10;

            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    public static function isValidCnpj(?string $value): bool
    {
        $cnpj = self::normalize($value);

        if (!preg_match('/^[A-Z0-9]{12}\d{2}$/', $cnpj)) {
            return false;
        }

        if (count(array_unique(str_split($cnpj))) === 1) {
            return false;
        }

        $firstDigit = self::calculateCnpjDigit(substr($cnpj, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);
        $secondDigit = self::calculateCnpjDigit(substr($cnpj, 0, 12) . $firstDigit, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]);

        return substr($cnpj, -2) === $firstDigit . $secondDigit;
    }

    public static function format(?string $value): string
    {
        $document = self::normalize($value);

        if (preg_match('/^\d{11}$/', $document)) {
            return substr($document, 0, 3) . '.' . substr($document, 3, 3) . '.' . substr($document, 6, 3) . '-' . substr($document, 9, 2);
        }

        if (preg_match('/^[A-Z0-9]{14}$/', $document)) {
            return substr($document, 0, 2) . '.' . substr($document, 2, 3) . '.' . substr($document, 5, 3) . '/' . substr($document, 8, 4) . '-' . substr($document, 12, 2);
        }

        return (string) $value;
    }

    private static function calculateCnpjDigit(string $base, array $weights): string
    {
        $sum = 0;

        foreach (str_split($base) as $index => $character) {
            $sum += (ord($character) - 48) * $weights[$index];
        }

        $remainder = $sum % 11;
        $digit = $remainder < 2 ? 0 : 11 - $remainder;

        return (string) $digit;
    }
}
