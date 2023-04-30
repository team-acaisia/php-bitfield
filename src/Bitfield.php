<?php

declare(strict_types=1);

namespace Acaisia\Bitfield;

/**
 * A class to decode and encode byte streams or arrays
 */
class Bitfield
{
    /**
     * Assumes an array in RLE encoding, such as:
     * [10,2,5,3] becomes [10,11,12,17,18,19,20]
     */
    public static function decodeFromArray(array $array): array
    {
        return [];
    }

    /**
     * Assumes a string in human readable encoding, such as:
     * "10-14,18-20" becomes [10,11,12,13,14,18,19,20]
     */
    public static function decodeFromHumanReadableString(string $string): array
    {
        return [];
    }
}
