<?php

namespace App\Utils;

class StudyObfuscator
{
    private static $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private static $multiplier = 2654435761;
    private static $inverse = 244002641;

    private static function multiply32(int $a, int $b): int
    {
        $aLow = $a & 0xFFFF;
        $aHigh = ($a >> 16) & 0xFFFF;
        $bLow = $b & 0xFFFF;
        $bHigh = ($b >> 16) & 0xFFFF;
        $mid = ($aHigh * $bLow) + ($aLow * $bHigh);
        $result = (($mid & 0xFFFF) << 16) + ($aLow * $bLow);
        return $result & 0xFFFFFFFF;
    }

    /**
     * Obfuscate a numeric ID to an unguessable string.
     */
    public static function encode(int $id): string
    {
        $hashVal = self::multiply32($id, self::$multiplier);
        $base = strlen(self::$alphabet);
        $encoded = '';
        while ($hashVal > 0) {
            $encoded = self::$alphabet[$hashVal % $base] . $encoded;
            $hashVal = intval($hashVal / $base);
        }
        return $encoded ?: self::$alphabet[0];
    }

    /**
     * Decode an obfuscated string back to its numeric ID.
     */
    public static function decode(string $str): int
    {
        $base = strlen(self::$alphabet);
        $hashVal = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $pos = strpos(self::$alphabet, $str[$i]);
            if ($pos === false) return 0;
            $hashVal = $hashVal * $base + $pos;
        }
        return self::multiply32($hashVal, self::$inverse);
    }
}
