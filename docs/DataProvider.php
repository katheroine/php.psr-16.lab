<?php
declare(strict_types=1);

namespace PHPLab\StandardPSR16\Test\DataFixtures;

final class DataProvider
{
    /**
     * @return array<string, array{0: string}>
     */
    public function stringSizeProvider(): array
    {
        return [
            'Edge Case: Empty String' => [
                ''
            ],
            'Edge Case: Single Byte' => [
                'x'
            ],
            'Volume: 64 KB (Standard Buffer)' => [
                str_repeat('a', 64 * 1024)
            ],
            'Volume: 1 MB (Memcached Default Limit)' => [
                str_repeat('b', 1024 * 1024)
            ],
            'Volume: 16 MB (High Capacity Payload)' => [
                str_repeat('c', 16 * 1024 * 1024)
            ],
        ];
    }

    /**
     * Provides comprehensive raw byte-sequence literals for all PHP-supported encodings
     * to validate binary-safe, lossless storage architecture in PSR-16 implementations.
     *
     * @return array<string, array{0: string}>
     */
    public static function provideEncodings(): array
    {
        return [
            'UTF-8 with Polish letters' => [
                "Zażółć gęślą jaźń"
            ],
            'UTF-8 with emoji' => [
                "🦊 🔥 👨‍👩‍👧‍👦"
            ],
            'UTF-16LE (contains internal null bytes)' => [
                mb_convert_encoding("Test UTF-16", "UTF-16LE", "UTF-8")
            ],
            'ISO-8859-2 (legacy) Polish' => [
                mb_convert_encoding("Zażółć gęślą jaźń", "ISO-8859-2", "UTF-8")
            ],
            'Binary Safe - NULL byte in the middle' => [
                "Part1\0Part2"
            ],
            'Binary Safe - Random bytes' => [
                random_bytes(1024)
            ],
            'Edge case - Empty string' => [
                ""
            ],
            'Edge case - Long string (above 1MB)' => [
                str_repeat("a", 1024 * 1024)
            ],

            // PHP-compatible encodings:

            // Transport & Pseudo-Encodings
            'BASE64'                  => ['U1JDX1BFWUxPQURfV0lUSF9NQU5ZX0JZVEVTXzIwMjY='],
            'UUENCODE'                => ["+41JD7FBFWUxPQURfV0lUSF9NQU5ZX0JZVEVTXzIwMjYg\n"],
            'HTML-ENTITIES'           => ['&Idigr; &Oslash; &zwnj; &x1F98A;'],
            'Quoted-Printable'        => ['=41=50=49=20=54=65=73=74=20=3D=32=30=32=36=0D=0A'],
            '7bit'                    => ['Ascii_Standard_7Bit_Payload_Data_Only'],
            '8bit'                    => ["\x80\x8F\x9A\xA2\xB5\xC3\xD9\xE0\xFA\xFF"],

            // UCS Core Standards (Universal Coded Character Set)
            'UCS-4'                   => ["\x00\x00\x00\x41\x00\x00\x01\x14\x00\x00\x20\xAC\x00\x01\xF9\x8A"],
            'UCS-4BE'                 => ["\x00\x00\x00\x41\x00\x00\x01\x14\x00\x00\x20\xAC\x00\x01\xF9\x8A"],
            'UCS-4LE'                 => ["\x41\x00\x00\x00\x14\x01\x00\x00\xAC\x20\x00\x00\x8A\xF9\x01\x00"],
            'UCS-2'                   => ["\x00\x41\x01\x14\x20\xAC\x4E\x2D"],
            'UCS-2BE'                 => ["\x00\x41\x01\x14\x20\xAC\x4E\x2D"],
            'UCS-2LE'                 => ["\x41\x00\x14\x01\xAC\x20\x2D\x4E"],

            // UTF Family & Specialized Variants
            'UTF-32'                  => ["\x00\x00\x00\x41\x00\x00\x01\x52\x00\x01\xF6\x8A"],
            'UTF-32BE'                => ["\x00\x00\x00\x41\x00\x00\x01\x52\x00\x01\xF6\x8A"],
            'UTF-32LE'                => ["\x41\x00\x00\x00\x52\x01\x00\x00\x8A\xF6\x01\x00"],
            'UTF-16'                  => ["\xFE\xFF\x00\x41\x01\x52\xD8\x3D\xDE\x8A"],
            'UTF-16BE'                => ["\x00\x41\x01\x52\xD8\x3D\xDE\x8A"],
            'UTF-16LE'                => ["\x41\x00\x52\x01\x3D\xD8\x8A\xDE"],
            'UTF-8'                   => ["\x50\x53\x52\x2D\x31\x36\x20\xC5\xBB\xC3\xB3\xC5\x82\xC4\x87\x20\xF0\x9F\xA6\x8A"],
            'UTF-7'                   => ['PSR-16 +WloHzw- +H2A-'],
            'UTF7-IMAP'               => ['PSR-16 &WloHzw- &H2A-'],

            // ASCII & Japanese Extended Standards (Part 1)
            'ASCII'                   => ["\x41\x53\x43\x49\x49\x5F\x53\x61\x66\x65\x5F\x54\x65\x73\x74"],
            'EUC-JP'                  => ["\xA4\xC6\xA4\xB9\xA4\xC8\xB4\xCC\xBB\xFA"],
            'SJIS'                    => ["\x83\x64\x83\x53\x83\x65\x8A\xBF\x8E\x9A"],
            'eucJP-win'               => ["\xA4\xC6\xA4\xB9\xA4\xC8\xAD\xC3\xAD\xF1"],
            'EUC-JP-2004'             => ["\xA4\xC6\xA4\xB9\xA4\xC8\xAB\xAE\xF4\xA6"],
            'SJIS-Mobile#DOCOMO'      => ["\x83\x64\x83\x53\x83\x65\xF8\x60\xF8\x61"],
            'SJIS-Mobile#KDDI'        => ["\x83\x64\x83\x53\x83\x65\xF6\x59\xF6\x5A"],
            'SJIS-Mobile#SOFTBANK'    => ["\x83\x64\x83\x53\x83\x65\xF7\x41\xF7\x42"],
            'SJIS-mac'                => ["\x83\x64\x83\x53\x83\x65\x89\xAA\x8E\xEB"],
            'SJIS-2004'               => ["\x83\x64\x83\x53\x83\x65\x9E\xAC\x9F\x40"],

            // UTF Mobile Subtypes & Japanese Shift Encodings (Part 2)
            'UTF-8-Mobile#DOCOMO'     => ["\x54\x65\x73\x74\xF3\xBE\x8D\x80"],
            'UTF-8-Mobile#KDDI-A'     => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'UTF-8-Mobile#KDDI-B'     => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'UTF-8-Mobile#SOFTBANK'   => ["\x54\x65\x73\x74\xF3\xBE\x80\x81"],
            'CP932'                   => ["\x83\x64\x83\x53\x83\x65\xFA\x40\xFA\x41"],
            'SJIS-win'                => ["\x83\x64\x83\x53\x83\x65\x8C\xAF\x8E\x9A"],
            'CP51932'                 => ["\xA4\xC6\xA4\xB9\xA4\xC8\x8F\xAD\xC3"],
            'JIS'                     => ["\x1B\x24\x42\x24\x44\x24\x39\x24\x48\x1B\x28\x42"],
            'ISO-2022-JP'             => ["\x1B\x24\x42\x4E\x4D\x3A\xFA\x1B\x28\x42"],
            'ISO-2022-JP-MS'          => ["\x1B\x24\x42\xAD\xC3\xAD\xF1\x1B\x28\x42"],

            // Chinese National Standard & ISO-8859 Regional Layouts (Part 1)
            'GB18030'                 => ["\xCE\xAA\xCA\xCE\xBB\xAF\x84\x31\x95\x33"],
            'Windows-1252'            => ["\x41\x63\x63\x65\x6E\x74\x73\x3A\x20\x80\x8C\x92\x9F\xA9\xAE\xE9\xEE\xFC"],
            'Windows-1254'            => ["\x49\xFE\xEB\xFD\xFE\x20\x53\x61\xF0\x6C\xFD\x6B"],
            'ISO-8859-1'              => ["\x41\x6C\x65\x6A\x61\x6E\x64\x72\x6F\x20\xBF\xC5\xC7\xD1\xE1\xF3"],
            'ISO-8859-2'              => ["\x5A\x61\xBF\xF3\xB3\xE6\x20\x67\xEA\x9C\x6C\xB1\x20\x6A\x61\xBC\xF1"],
            'ISO-8859-3'              => ["\xC4\x60\x48\x4A\x47\x20\xE4\x60\x68\x6A\x67"],
            'ISO-8859-4'              => ["\xC1\xCC\xCE\x20\xE1\xEC\xEE\x20\xAA\xBA\xFE"],
            'ISO-8859-5'              => ["\xBB\xCE\xBB\xDF\x20\xCC\xE5\xE1\xEE\xE4\xEE\xDF\xDF\x20\xAA"],
            'ISO-8859-6'              => ["\xE3\xE4\xED\x20\xC7\xE4\xDA\xE4\xE3\x20\xE4\xE8\xEA\xEE"],
            'ISO-8859-7'              => ["\xCA\xE1\xEB\xE7\xEC\xDD\xF1\x61\x20\xCC\xE5\xF7\xF1\xDF\x61"],

            // ISO-8859 Series (Part 2) & Chinese Simplified Standard Legacy
            'ISO-8859-8'              => ["\xF9\x6C\x6F\xED\x20\xF9\xFA\xFA\xEE"],
            'ISO-8859-9'              => ["\x49\xFE\xEB\xFD\xFE\x20\x5F\x61\xFE\x69\xFE"],
            'ISO-8859-10'             => ["\xA1\xA2\xAA\xAE\xAF\xB5\xB6\xBF\xC6\xD8"],
            'ISO-8859-13'             => ["\xC6\xE6\xC5\x20\xE6\xE5\xE6\x20\xA6\xAA\xB6"],
            'ISO-8859-14'             => ["\x43\x68\x61\x63\x68\x61\x6E\x20\xAC\xAE\xAF\xFA"],
            'ISO-8859-15'             => ["\xA4\x20\xBC\x20\xBD\x20\xBE\x20\xCE\x20\xDF"],
            'ISO-8859-16'             => ["\xA1\xA2\xC3\xD0\xDF\xE3\xFA\x20\xB1\xB2"],
            'EUC-CN'                  => ["\xB2\xE2\xCA\xD4\xCE\xC4\xD4\xDA\xCE\xF4"],
            'CP936'                   => ["\xB2\xE2\xCA\xD4\xCE\xC4\xD4\xDA\xCE\xF4\x81\x40"],
            'HZ'                      => ['PSR-16 ~{2f3453~} ~{4c5a~}'],

            // Traditional Chinese, Korean & Cyrillic Layouts
            'EUC-TW'                  => ["\xC4\xE3\xBA\xC3\x20\xB1\xFA\xDF\xB4"],
            'BIG-5'                   => ["\xB3\xA1\xB8\xD5\xA4\x6A\xAC\xEC\xA5\x40"],
            'CP950'                   => ["\xB3\xA1\xB8\xD5\xA4\x6A\xAC\xEC\xFA\x40"],
            'EUC-KR'                  => ["\xB1\xE3\x20\xC5\xD7\xBD\xBA\xC6\xAE\x20\xBD\xC3\xBD\xBA\xC5\xCE"],
            'UHC'                     => ["\xB1\xE3\x20\xC5\xD7\xBD\xBA\xC6\xAE\x20\x94\x41\x94\x42"],
            'ISO-2022-KR'             => ["\x1B\x24\x29\x43\x1B\x24\x42\xB1\xE3\xC5\xD7\xBD\xBA\xC6\xAE\x1B\x28\x42"],
            'Windows-1251'            => ["\xD4\xE5\xF1\xF2\x20\xCA\xE0\xF0\xE8\xEB\xEB\xE8\xF6\xE0"],
            'CP866'                   => ["\x92\xA5\xE1\xE2\x20\x8A\xA0\xE0\xA8\xEB\xEB\xA8\xE6\xA0"],
            'KOI8-R'                  => ["\xF4\xC5\xD3\xD4\x20\xEB\xC9\xD2\xC9\xCC\xCC\xC9\xDF\xC1"],
            'KOI8-U'                  => ["\xF4\xC5\xD3\xD4\x20\xEB\xC9\xD2\xC9\xCC\xCC\xC9\xDF\xC1\xA4\xB4"],

            // Armenian, OEM West-Europe & Advanced Interactive Japanese Shifts
            'ArmSCII-8'               => ["\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA"],
            'CP850'                   => ["\x41\x63\x63\x65\x6E\x74\x73\x3A\x20\x82\x85\x8A\x8D\xA0\xA1\xA2\xA3"],
            'ISO-2022-JP-2004'        => ["\x1B\x24\x28\x51\x24\x44\x24\x39\x24\x48\x1B\x28\x42"],
            'ISO-2022-JP-MOBILE#KDDI' => ["\x1B\x24\x42\x24\x44\x24\x39\x24\x48\x1B\x24\x46\x76\x59\x1B\x28\x42"],
            'CP50220'                 => ["\x1B\x24\x42\x4E\x4D\x3A\xFA\x1B\x28\x42"],
            'CP50221'                 => ["\x1B\x28\x49\x43\x44\x45\x1B\x28\x42\x1B\x24\x42\x4E\x4D\x1B\x28\x42"],
            'CP50222'                 => ["\x1B\x24\x42\x4E\x4D\x1B\x28\x49\xB1\xB2\xB3\x1B\x28\x42"],
        ];
    }
}
