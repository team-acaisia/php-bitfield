<?php

declare(strict_types=1);

namespace Acaisia\BitField;

use Acaisia\BitField\Exception\InvalidBitFieldException;
use Acaisia\BitField\Exception\InvalidCharacterException;

/**
 * A class to decode and encode byte streams or arrays
 */
class BitField implements \Countable
{
    private array $values;

    private function __construct(array $values)
    {
        $this->values = $values;
    }

    public function count(): int
    {
        return count($this->values);
    }

    public function getFlatArray(): array
    {
        return $this->values;
    }

    /**
     * Assumes an array in RLE encoding, such as:
     * [10,2,5,3] becomes [10,11,12,17,18,19,20]
     */
    public static function decodeFromRleArray(array $array): BitField
    {
        $count = count($array);
        if ($count == 0) {
            return new self([]);
        }
        if ($count % 2 != 0) {
            throw new InvalidBitFieldException('Element count of ' . $count . ' is not possible');
        }

        $arr = [];
        $counted = [];
        $sum = 0;
        foreach ($array as $content) {
            $sum += $content;
            $counted[] = $sum;
        }

        for ($i = 0; $i < ceil(count($array)/2); $i++) {
            $from = $counted[$i*2];
            $to = $counted[($i*2)+1];

            while ($from < $to) {
                $arr[] = $from;
                $from++;
            }
        }
        return new self($arr);
    }

    /**
     * Assumes a string in human readable encoding, such as:
     * "10-14,18-20" becomes [10,11,12,13,14,18,19,20]
     */
    public static function decodeFromHumanReadableString(string $string): BitField
    {
        if (strlen($string) == 0) {
            return new self([]);
        }
        if (is_numeric($string) && (int) $string == $string) { // Keep out floats
            return new self([(int) $string]);
        }
        if (!preg_match('/^[0-9]+([0-9]+[,-]?)+[0-9]+$/', $string)) {
            throw new InvalidCharacterException('Unexpected character in human readable string');
        }

        // @todo This is highly inefficient, but works for now

        // Break up all numbers with their ending sign (, or -)
        preg_match_all('/([0-9]+[\-\,]?)/', $string, $matches);

        $result = [];
        $rangeNext = false;

        foreach ($matches[0] as $match) {
            $lastCharacter = substr($match, strlen($match)-1, 1);
            switch ($lastCharacter) {
                case '-': // Range to next
                    $rangeNext = (int) $match; // Set the next event to count to that number
                    break;
                case ',':
                default:
                    if ($rangeNext !== false) {
                        // Add from $rangeNext to this one
                        if ((int) $match < $rangeNext) {
                            throw new InvalidBitFieldException('Invalid range: ' . $rangeNext . ' to ' . $match); // Causes infinite loop
                        }
                        for ($i = $rangeNext; $i <= (int) $match; $i++) {
                            $result[] = $i;
                        }
                        $rangeNext = false;
                        break;
                    }

                    if ($lastCharacter == ',') {
                        // No previous range, just add a single number
                        $result[] = (int) $match;
                        break;
                    }

                    if (is_numeric($lastCharacter)) {
                        // Assume we have a full number, append it, and we should be done
                        $result[] = (int) $match;
                        break;
                    }
                    // This shouldn't happen due to the first check
                    throw new InvalidCharacterException('Unknown character: ' . $lastCharacter);
            }
        }
        if ($rangeNext !== false) {
            throw new InvalidBitFieldException('Unexpected end of string');
        }

        return new self($result);
    }

    /**
     * Assumes a regular sparse-filled array (not RLE encoded)
     */
    public static function fromArray(array $values): self
    {

    }

    public function toHumanReadableString(): string
    {
        $previous = null;
        foreach ($this->values as $value) {
            if ($previous == null) {
                $previous = $value;
            }

        }
        return 'no';
    }
}
