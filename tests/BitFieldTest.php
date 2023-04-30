<?php

declare(strict_types=1);

namespace Acaisia\BitField\Tests;

use Acaisia\BitField\BitField;
use Acaisia\BitField\Exception\InvalidBitFieldException;
use Acaisia\BitField\Exception\InvalidCharacterException;

class BitFieldTest extends AbstractTestCase
{
    /**
     * @dataProvider provideValidArrays
     */
    public function testValidArrays(array $given, array $expected) : void
    {
        $bitField = BitField::decodeFromArray($given);
        $this->assertEquals($expected, $bitField);
    }

    public static function provideValidArrays() : array
    {
        return [
            [[0,1], [0]],
            [[], []],
            [[70811, 1, 7, 2, 11, 1, 61, 1, 13, 2, 10, 1, 17, 1, 2, 1], [70811, 70819, 70820, 70832, 70894, 70908, 70909, 70920, 70938, 70941]],
        ];
    }

    /**
     * @dataProvider provideValidHumanReadableStrings
     */
    public function testValidHumanReadableStrings(string $given, array $expected) : void
    {
        $bitField = BitField::decodeFromHumanReadableString($given);
        $this->assertEquals($expected, $bitField);
    }

    public static function provideValidHumanReadableStrings() : array
    {
        return [
            ['5', [5]],
            ['', []],
            ['70811,70819-70820,70832,70894,70908-70909,70920,70938,70941', [70811, 70819, 70820, 70832, 70894, 70908, 70909, 70920, 70938, 70941]],
        ];
    }

    public function testSame(): void
    {
        $this->assertEquals(
            BitField::decodeFromArray([70811, 1, 7, 2, 11, 1, 61, 1, 13, 2, 10, 1, 17, 1, 2, 1]),
            BitField::decodeFromHumanReadableString('70811,70819-70820,70832,70894,70908-70909,70920,70938,70941'),
        );
    }

    /**
     * @dataProvider provideInvalidCharacter
     */
    public function testInvalidCharacter(string $invalidString): void
    {
        $this->expectException(InvalidBitFieldException::class);
        $this->expectException(InvalidCharacterException::class);
        BitField::decodeFromHumanReadableString($invalidString);
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
            ['70811,70819-70820,70832,70894,70908-70909,70920,70938,70941,'],
            ['70811,70819-70820,70832,70894,70908-70909,70920,70938,70941-'],
            ['-70811,70819-70820,70832,70894,70908-70909,70920,70938,70941'],
            [',70811,70819-70820,70832,70894,70908-70909,70920,70938,70941'],
            ['70811,70819--70820,70832,70894,70908-70909,70920,70938,70941'],
            ['70811,,70819-70820,70832,70894,70908-70909,70920,70938,70941'],
        ];
    }

    public function testSingleDoesntWork(): void
    {
        $this->expectException(InvalidBitFieldException::class);
        BitField::decodeFromArray([1234]);
    }

    public function testReverseRange(): void
    {
        $this->expectException(InvalidBitFieldException::class);
        BitField::decodeFromHumanReadableString('20,21-10,24');
    }
}
