<?php

declare(strict_types=1);

namespace Acaisia\BitField\Tests;

use Acaisia\Bitfield\Bitfield;
use Acaisia\Bitfield\Exception\InvalidBitfieldException;
use Acaisia\Bitfield\Exception\InvalidCharacterException;

class BitFieldTest extends AbstractTestCase
{
    /**
     * @dataProvider provideValidArrays
     */
    public function testValidArrays(array $given, array $expected) : void
    {
        $bitField = Bitfield::decodeFromArray($expected);
        $this->assertEquals($expected, $bitField);
    }

    public static function provideValidArrays() : array
    {
        return [
            [[0], [0]],
            [[], []],
            [[70811, 1, 7, 2, 11, 1, 61, 1, 13, 2, 10, 1, 17, 1, 2, 1], [1, 2, 3, 4, 5]],
        ];
    }

    /**
     * @dataProvider provideValidHumanReadableStrings
     */
    public function testValidHumanReadableStrings(string $given, array $expected) : void
    {
        $bitField = Bitfield::decodeFromArray($expected);
        $this->assertEquals($expected, $bitField);
    }

    public static function provideValidHumanReadableStrings() : array
    {
        return [
            ['0', [0]],
            ['', []],
            ['70811,70819-70820,70832,70894,70908-70909,70920,70938,70941', [1, 2, 3, 4, 5]],
        ];
    }

    public function testSame(): void
    {
        $this->assertEquals(
            Bitfield::decodeFromArray([70811, 1, 7, 2, 11, 1, 61, 1, 13, 2, 10, 1, 17, 1, 2, 1]),
            Bitfield::decodeFromHumanReadableString('70811,70819-70820,70832,70894,70908-70909,70920,70938,70941'),
        );
    }

    /**
     * @dataProvider provideInvalidCharacter
     */
    public function testInvalidCharacter(string $invalidString): void
    {
        $this->expectException(InvalidBitfieldException::class);
        $this->expectException(InvalidCharacterException::class);
        Bitfield::decodeFromHumanReadableString($invalidString);
    }

    public static function provideInvalidCharacter(): array
    {
        return [
            ['asdf'],
            ['11--123'],
            ['11,,123'],
            ['11.123'],
            ['11,+12323'],
            ['11,+123?23'],
            ['11-123,29,34-,,123'],
        ];
    }
}
