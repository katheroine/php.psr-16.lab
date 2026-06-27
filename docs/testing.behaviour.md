# PSR-16 behaviour testing

## Cached data

1. [REQUIRED] Test that the cache accepts the following data types.

Implementing libraries MUST support all *serializable PHP data types*, including:

* *Strings* - Character strings of arbitrary size in any PHP-compatible encoding.
* *Integers* - All integers of any size supported by PHP, up to 64-bit signed.
* *Floats* - All signed floating point values.
* *Booleans* - `True` and `False`.
* *Null* - The `null` value (although it will not be distinguishable from a *cache miss* when reading it back out).
* *Arrays* - Indexed, associative and multidimensional arrays of arbitrary depth.
* *Objects* - Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`. Objects MAY leverage PHP's `Serializable` interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

1.1. Strings

1.1.1 Size

Character strings of arbitrary size [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
    /**
     * @return array<string, array{0: string}>
     */
    public function stringSizeProvider(): array
    {
        return [
            'Empty String'                             => [''],
            'Single Byte'                              => ['x'],
            'Volume 64 KB (Standard Buffer)'           => [str_repeat('a', 64 * 1024)],
            'Volume 1 MB (Memcached Default Limit)'    => [str_repeat('b', 1024 * 1024)],
            'Volume 16 MB (High Capacity Payload)'     => [str_repeat('c', 16 * 1024 * 1024)],
        ];
    }
```

1.1.2. Encoding

[...] in any PHP-compatible encoding.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

The [`mb_list_encodings` function](https://www.php.net/manual/en/function.mb-list-encodings.php) can be used to generate a list of the available encodings.

```shell
php -r 'print_r(mb_list_encodings()); echo "\n";'
```

This is the result for `PHP 8.4.12`:

```php
Array
(
    [0] => BASE64
    [1] => UUENCODE
    [2] => HTML-ENTITIES
    [3] => Quoted-Printable
    [4] => 7bit
    [5] => 8bit
    [6] => UCS-4
    [7] => UCS-4BE
    [8] => UCS-4LE
    [9] => UCS-2
    [10] => UCS-2BE
    [11] => UCS-2LE
    [12] => UTF-32
    [13] => UTF-32BE
    [14] => UTF-32LE
    [15] => UTF-16
    [16] => UTF-16BE
    [17] => UTF-16LE
    [18] => UTF-8
    [19] => UTF-7
    [20] => UTF7-IMAP
    [21] => ASCII
    [22] => EUC-JP
    [23] => SJIS
    [24] => eucJP-win
    [25] => EUC-JP-2004
    [26] => SJIS-Mobile#DOCOMO
    [27] => SJIS-Mobile#KDDI
    [28] => SJIS-Mobile#SOFTBANK
    [29] => SJIS-mac
    [30] => SJIS-2004
    [31] => UTF-8-Mobile#DOCOMO
    [32] => UTF-8-Mobile#KDDI-A
    [33] => UTF-8-Mobile#KDDI-B
    [34] => UTF-8-Mobile#SOFTBANK
    [35] => CP932
    [36] => SJIS-win
    [37] => CP51932
    [38] => JIS
    [39] => ISO-2022-JP
    [40] => ISO-2022-JP-MS
    [41] => GB18030
    [42] => GB18030-2022
    [43] => Windows-1252
    [44] => Windows-1254
    [45] => ISO-8859-1
    [46] => ISO-8859-2
    [47] => ISO-8859-3
    [48] => ISO-8859-4
    [49] => ISO-8859-5
    [50] => ISO-8859-6
    [51] => ISO-8859-7
    [52] => ISO-8859-8
    [53] => ISO-8859-9
    [54] => ISO-8859-10
    [55] => ISO-8859-13
    [56] => ISO-8859-14
    [57] => ISO-8859-15
    [58] => ISO-8859-16
    [59] => EUC-CN
    [60] => CP936
    [61] => HZ
    [62] => EUC-TW
    [63] => BIG-5
    [64] => CP950
    [65] => EUC-KR
    [66] => UHC
    [67] => ISO-2022-KR
    [68] => Windows-1251
    [69] => CP866
    [70] => KOI8-R
    [71] => KOI8-U
    [72] => ArmSCII-8
    [73] => CP850
    [74] => ISO-2022-JP-2004
    [75] => ISO-2022-JP-MOBILE#KDDI
    [76] => CP50220
    [77] => CP50221
    [78] => CP50222
)
```

```php
    /**
     * Provides comprehensive raw byte-sequence literals for all PHP-supported encodings
     * to validate binary-safe, lossless storage architecture in PSR-16 implementations.
     *
     * @return array<string, array{0: string}>
     */
    public static function provideEncodings(): array
    {
        return [
            'UTF-8 with Polish letters'                 => ["Zażółć gęślą jaźń"],
            'UTF-8 with emoji'                          => ["🦊 🔥 👨‍👩‍👧‍👦"],
            'UTF-16LE (contains internal null bytes)'   => [mb_convert_encoding("Test UTF-16", "UTF-16LE", "UTF-8")],
            'ISO-8859-2 (legacy) Polish'                => [mb_convert_encoding("Zażółć gęślą jaźń", "ISO-8859-2", "UTF-8")],
            'Binary Safe - NULL byte in the middle'     => ["Part1\0Part2"],
            'Binary Safe - Random bytes'                => [random_bytes(1024)],
            'Empty string'                              => [""],
            'Long string (above 1MB)'                   => [str_repeat("a", 1024 * 1024)],

            // PHP-compatible encodings:

            // Transport & Pseudo-Encodings
            'Encoding BASE64'                           => ['U1JDX1BFWUxPQURfV0lUSF9NQU5ZX0JZVEVTXzIwMjY='],
            'Encoding UUENCODE'                         => ["+41JD7FBFWUxPQURfV0lUSF9NQU5ZX0JZVEVTXzIwMjYg\n"],
            'Encoding HTML-ENTITIES'                    => ['&Idigr; &Oslash; &zwnj; &x1F98A;'],
            'Encoding Quoted-Printable'                 => ['=41=50=49=20=54=65=73=74=20=3D=32=30=32=36=0D=0A'],
            'Encoding 7bit'                             => ['Ascii_Standard_7Bit_Payload_Data_Only'],
            'Encoding 8bit'                             => ["\x80\x8F\x9A\xA2\xB5\xC3\xD9\xE0\xFA\xFF"],

            // UCS Core Standards (Universal Coded Character Set)
            'Encoding UCS-4'                            => ["\x00\x00\x00\x41\x00\x00\x01\x14\x00\x00\x20\xAC\x00\x01\xF9\x8A"],
            'Encoding UCS-4BE'                          => ["\x00\x00\x00\x41\x00\x00\x01\x14\x00\x00\x20\xAC\x00\x01\xF9\x8A"],
            'Encoding UCS-4LE'                          => ["\x41\x00\x00\x00\x14\x01\x00\x00\xAC\x20\x00\x00\x8A\xF9\x01\x00"],
            'Encoding UCS-2'                            => ["\x00\x41\x01\x14\x20\xAC\x4E\x2D"],
            'Encoding UCS-2BE'                          => ["\x00\x41\x01\x14\x20\xAC\x4E\x2D"],
            'Encoding UCS-2LE'                          => ["\x41\x00\x14\x01\xAC\x20\x2D\x4E"],

            // UTF Family & Specialized Variants
            'Encoding UTF-32'                           => ["\x00\x00\x00\x41\x00\x00\x01\x52\x00\x01\xF6\x8A"],
            'Encoding UTF-32BE'                         => ["\x00\x00\x00\x41\x00\x00\x01\x52\x00\x01\xF6\x8A"],
            'Encoding UTF-32LE'                         => ["\x41\x00\x00\x00\x52\x01\x00\x00\x8A\xF6\x01\x00"],
            'Encoding UTF-16'                           => ["\xFE\xFF\x00\x41\x01\x52\xD8\x3D\xDE\x8A"],
            'Encoding UTF-16BE'                         => ["\x00\x41\x01\x52\xD8\x3D\xDE\x8A"],
            'Encoding UTF-16LE'                         => ["\x41\x00\x52\x01\x3D\xD8\x8A\xDE"],
            'Encoding UTF-8'                            => ["\x50\x53\x52\x2D\x31\x36\x20\xC5\xBB\xC3\xB3\xC5\x82\xC4\x87\x20\xF0\x9F\xA6\x8A"],
            'Encoding UTF-7'                            => ['PSR-16 +WloHzw- +H2A-'],
            'Encoding UTF7-IMAP'                        => ['PSR-16 &WloHzw- &H2A-'],

            // ASCII & Japanese Extended Standards (Part 1)
            'Encoding ASCII'                            => ["\x41\x53\x43\x49\x49\x5F\x53\x61\x66\x65\x5F\x54\x65\x73\x74"],
            'Encoding EUC-JP'                           => ["\xA4\xC6\xA4\xB9\xA4\xC8\xB4\xCC\xBB\xFA"],
            'Encoding SJIS'                             => ["\x83\x64\x83\x53\x83\x65\x8A\xBF\x8E\x9A"],
            'Encoding eucJP-win'                        => ["\xA4\xC6\xA4\xB9\xA4\xC8\xAD\xC3\xAD\xF1"],
            'Encoding EUC-JP-2004'                      => ["\xA4\xC6\xA4\xB9\xA4\xC8\xAB\xAE\xF4\xA6"],
            'Encoding SJIS-Mobile#DOCOMO'               => ["\x83\x64\x83\x53\x83\x65\xF8\x60\xF8\x61"],
            'Encoding SJIS-Mobile#KDDI'                 => ["\x83\x64\x83\x53\x83\x65\xF6\x59\xF6\x5A"],
            'Encoding SJIS-Mobile#SOFTBANK'             => ["\x83\x64\x83\x53\x83\x65\xF7\x41\xF7\x42"],
            'Encoding SJIS-mac'                         => ["\x83\x64\x83\x53\x83\x65\x89\xAA\x8E\xEB"],
            'Encoding SJIS-2004'                        => ["\x83\x64\x83\x53\x83\x65\x9E\xAC\x9F\x40"],

            // UTF Mobile Subtypes & Japanese Shift Encodings (Part 2)
            'Encoding UTF-8-Mobile#DOCOMO'              => ["\x54\x65\x73\x74\xF3\xBE\x8D\x80"],
            'Encoding UTF-8-Mobile#KDDI-A'              => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'Encoding UTF-8-Mobile#KDDI-B'              => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'Encoding UTF-8-Mobile#SOFTBANK'            => ["\x54\x65\x73\x74\xF3\xBE\x80\x81"],
            'Encoding CP932'                            => ["\x83\x64\x83\x53\x83\x65\xFA\x40\xFA\x41"],
            'Encoding SJIS-win'                         => ["\x83\x64\x83\x53\x83\x65\x8C\xAF\x8E\x9A"],
            'Encoding CP51932'                          => ["\xA4\xC6\xA4\xB9\xA4\xC8\x8F\xAD\xC3"],
            'Encoding JIS'                              => ["\x1B\x24\x42\x24\x44\x24\x39\x24\x48\x1B\x28\x42"],
            'Encoding ISO-2022-JP'                      => ["\x1B\x24\x42\x4E\x4D\x3A\xFA\x1B\x28\x42"],
            'Encoding ISO-2022-JP-MS'                   => ["\x1B\x24\x42\xAD\xC3\xAD\xF1\x1B\x28\x42"],

            // Chinese National Standard & ISO-8859 Regional Layouts (Part 1)
            'Encoding GB18030'                          => ["\xCE\xAA\xCA\xCE\xBB\xAF\x84\x31\x95\x33"],
            'Encoding Windows-1252'                     => ["\x41\x63\x63\x65\x6E\x74\x73\x3A\x20\x80\x8C\x92\x9F\xA9\xAE\xE9\xEE\xFC"],
            'Encoding Windows-1254'                     => ["\x49\xFE\xEB\xFD\xFE\x20\x53\x61\xF0\x6C\xFD\x6B"],
            'Encoding ISO-8859-1'                       => ["\x41\x6C\x65\x6A\x61\x6E\x64\x72\x6F\x20\xBF\xC5\xC7\xD1\xE1\xF3"],
            'Encoding ISO-8859-2'                       => ["\x5A\x61\xBF\xF3\xB3\xE6\x20\x67\xEA\x9C\x6C\xB1\x20\x6A\x61\xBC\xF1"],
            'Encoding ISO-8859-3'                       => ["\xC4\x60\x48\x4A\x47\x20\xE4\x60\x68\x6A\x67"],
            'Encoding ISO-8859-4'                       => ["\xC1\xCC\xCE\x20\xE1\xEC\xEE\x20\xAA\xBA\xFE"],
            'Encoding ISO-8859-5'                       => ["\xBB\xCE\xBB\xDF\x20\xCC\xE5\xE1\xEE\xE4\xEE\xDF\xDF\x20\xAA"],
            'Encoding ISO-8859-6'                       => ["\xE3\xE4\xED\x20\xC7\xE4\xDA\xE4\xE3\x20\xE4\xE8\xEA\xEE"],
            'Encoding ISO-8859-7'                       => ["\xCA\xE1\xEB\xE7\xEC\xDD\xF1\x61\x20\xCC\xE5\xF7\xF1\xDF\x61"],

            // ISO-8859 Series (Part 2) & Chinese Simplified Standard Legacy
            'Encoding ISO-8859-8'                       => ["\xF9\x6C\x6F\xED\x20\xF9\xFA\xFA\xEE"],
            'Encoding ISO-8859-9'                       => ["\x49\xFE\xEB\xFD\xFE\x20\x5F\x61\xFE\x69\xFE"],
            'Encoding ISO-8859-10'                      => ["\xA1\xA2\xAA\xAE\xAF\xB5\xB6\xBF\xC6\xD8"],
            'Encoding ISO-8859-13'                      => ["\xC6\xE6\xC5\x20\xE6\xE5\xE6\x20\xA6\xAA\xB6"],
            'Encoding ISO-8859-14'                      => ["\x43\x68\x61\x63\x68\x61\x6E\x20\xAC\xAE\xAF\xFA"],
            'Encoding ISO-8859-15'                      => ["\xA4\x20\xBC\x20\xBD\x20\xBE\x20\xCE\x20\xDF"],
            'Encoding ISO-8859-16'                      => ["\xA1\xA2\xC3\xD0\xDF\xE3\xFA\x20\xB1\xB2"],
            'Encoding EUC-CN'                           => ["\xB2\xE2\xCA\xD4\xCE\xC4\xD4\xDA\xCE\xF4"],
            'Encoding CP936'                            => ["\xB2\xE2\xCA\xD4\xCE\xC4\xD4\xDA\xCE\xF4\x81\x40"],
            'Encoding HZ'                               => ['PSR-16 ~{2f3453~} ~{4c5a~}'],

            // Traditional Chinese, Korean & Cyrillic Layouts
            'Encoding EUC-TW'                           => ["\xC4\xE3\xBA\xC3\x20\xB1\xFA\xDF\xB4"],
            'Encoding BIG-5'                            => ["\xB3\xA1\xB8\xD5\xA4\x6A\xAC\xEC\xA5\x40"],
            'Encoding CP950'                            => ["\xB3\xA1\xB8\xD5\xA4\x6A\xAC\xEC\xFA\x40"],
            'Encoding EUC-KR'                           => ["\xB1\xE3\x20\xC5\xD7\xBD\xBA\xC6\xAE\x20\xBD\xC3\xBD\xBA\xC5\xCE"],
            'Encoding UHC'                              => ["\xB1\xE3\x20\xC5\xD7\xBD\xBA\xC6\xAE\x20\x94\x41\x94\x42"],
            'Encoding ISO-2022-KR'                      => ["\x1B\x24\x29\x43\x1B\x24\x42\xB1\xE3\xC5\xD7\xBD\xBA\xC6\xAE\x1B\x28\x42"],
            'Encoding Windows-1251'                     => ["\xD4\xE5\xF1\xF2\x20\xCA\xE0\xF0\xE8\xEB\xEB\xE8\xF6\xE0"],
            'Encoding CP866'                            => ["\x92\xA5\xE1\xE2\x20\x8A\xA0\xE0\xA8\xEB\xEB\xA8\xE6\xA0"],
            'Encoding KOI8-R'                           => ["\xF4\xC5\xD3\xD4\x20\xEB\xC9\xD2\xC9\xCC\xCC\xC9\xDF\xC1"],
            'Encoding KOI8-U'                           => ["\xF4\xC5\xD3\xD4\x20\xEB\xC9\xD2\xC9\xCC\xCC\xC9\xDF\xC1\xA4\xB4"],

            // Armenian, OEM West-Europe & Advanced Interactive Japanese Shifts
            'Encoding ArmSCII-8'                        => ["\xB1\xB2\xB3\xB4\xB5\xB6\xB7\xB8\xB9\xBA"],
            'Encoding CP850'                            => ["\x41\x63\x63\x65\x6E\x74\x73\x3A\x20\x82\x85\x8A\x8D\xA0\xA1\xA2\xA3"],
            'Encoding ISO-2022-JP-2004'                 => ["\x1B\x24\x28\x51\x24\x44\x24\x39\x24\x48\x1B\x28\x42"],
            'Encoding ISO-2022-JP-MOBILE#KDDI'          => ["\x1B\x24\x42\x24\x44\x24\x39\x24\x48\x1B\x24\x46\x76\x59\x1B\x28\x42"],
            'Encoding CP50220'                          => ["\x1B\x24\x42\x4E\x4D\x3A\xFA\x1B\x28\x42"],
            'Encoding CP50221'                          => ["\x1B\x28\x49\x43\x44\x45\x1B\x28\x42\x1B\x24\x42\x4E\x4D\x1B\x28\x42"],
            'Encoding CP50222'                          => ["\x1B\x24\x42\x4E\x4D\x1B\x28\x49\xB1\xB2\xB3\x1B\x28\x42"],
        ];
    }
```

1.2. Integers

1.2.1. Size

All integers of any size supported by PHP, up to 64-bit signed.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
    public static function provideIntegers(): array
    {
        return [
            'Zero Boundary'                             => [0],
            'Standard Positive Integer'                 => [42],
            'Standard Negative Integer'                 => [-100],
            'PHP_INT_MAX (64-bit)'                      => [PHP_INT_MAX],  // 9223372036854775807
            'PHP_INT_MIN (64-bit)'                      => [PHP_INT_MIN],  // -9223372036854775808
        ];
    }
```

1.3. Floats

All signed floating point values.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
    public static function provideFloats(): array
    {
        return [
            'Zero Float Boundary'                       => [0.0],
            'Negative Zero Float'                       => [-0.0],
            'Standard Positive Float'                   => [3.1415926535898],
            'Standard Negative Float'                   => [-2.71828],
            'Small Fractional Value'                    => [0.000000000000001],
            'Large Float Representation'                => [1.23e10],
            'PHP_FLOAT_MAX'                             => [PHP_FLOAT_MAX],
            'PHP_FLOAT_MIN'                             => [PHP_FLOAT_MIN],
        ];
    }
```

1.4. Booleans and null

`True` and `False`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

The `null` value (although it will not be distinguishable from a *cache miss* when reading it back out).

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
    public static function provideBooleanAndNull(): array
    {
        return [
            'Boolean True'                              => [true],
            'Boolean False'                             => [false],
            'Null'                                      => [null],
        ];
    }
```

1.5. Arrays

Indexed, associative and multidimensional arrays of arbitrary depth.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
    public static function provideArrays(): array
    {
        return [
            'Empty Array' => [
                []
            ],

            'Sequential Integer Keys' => [
                [10, 20, 30, 40, 50]
            ],

            'Associative String Keys' => [
                [
                    'config_key_1' => 'value_1',
                    'config_key_2' => 'value_2',
                ]
            ],

            'Strict Type Integrity (Mixed Values)' => [
                [
                    'integer_type' => 42,
                    'float_type'   => 3.1415,
                    'bool_true'    => true,
                    'bool_false'   => false,
                    'null_type'    => null,
                    'string_utf8'  => 'Zażółć gęślą jaźń',
                ]
            ],

            'Numeric String Keys (Type Juggling Danger)' => [
                [
                    '10' => 'string_key_10',
                    '01' => 'string_key_01', // PHP castuje '10' do int, ale '01' zostaje stringiem!
                ]
            ],

            'Sparse Array (Non-contiguous Indices)' => [
                [
                    0   => 'first',
                    5   => 'sixth',
                    100 => 'hundredth'
                ]
            ],

            'Deeply Nested Multidimensional Array (Depth 5)' => [
                [
                    'level1' => [
                        'level2' => [
                            'level3' => [
                                'level4' => [
                                    'level5' => 'deep_value'
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            'Mixed Numeric and String Keys' => [
                [
                    'status' => 'active',
                    0        => 'meta_0',
                    'tags'   => ['php', 'psr16'],
                    1        => 'meta_1'
                ]
            ],
        ];
    }
```

1.6. Objects

Any object that supports lossless serialization and deserialization such that `$o == unserialize(serialize($o))`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

```php
function provideObjects(): array
    {
        $someStandardClass = new \stdClass();
        $someStandardClass->property = "dynamic_value";

        $someDateTime = new \DateTimeImmutable('2026-06-24 22:00:00', new \DateTimeZone('UTC'));

        return [
            // 'Standard DTO (Private & Public Props)' => [
            //     new \StandardDto('PSR-16 Test', 2026, 'secret_bit_stream')
            // ],
            // 'Legacy Magic Methods (__sleep/__wakeup)' => [
            //     new \LegacyMagicObject()
            // ],
            // 'Modern Serialization (__serialize)' => [
            //     $obj = new ModernSerializableObject(),
            //     $obj->payload = ['engine' => 'redis']
            // ],
            'PHP Engine Internal stdClass' => [
                $someStandardClass
            ],
            'PHP Core DateTime Engine' => [
                $someDateTime
            ]
        ];
    }
```

Objects MAY leverage PHP's `Serializable` interface, `__sleep()` or `__wakeup()` magic methods, or similar language functionality if appropriate.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

2. [REQUIRED] Test if the cached data is returned exactly the same as passed with both value and type and do not show the effects of the serialization done.

All data passed into the *implementing library* MUST be returned exactly as passed with both value and type type, (that is, it is an error to return `(string) 5` if `(int) 5` was the value saved).

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

*Implementing libraries* MAY use PHP's `serialize()`/`unserialize()` functions internally.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

3. [REQUIRED] Test both cases - caching null data and cache miss, give the same result.

If it is not possible to return the exact saved value for any reason, *implementing libraries* MUST respond with a *cache miss* rather than corrupted data.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#14-data)

## Cache key

1. [REQUIRED] Test key can consist of the allowed characters put together in any order

Implementing libraries MUST support *keys* consisting of the characters `A-Z`, `a-z`, `0-9`, `_`, and `.` in any order [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

```php
    /**
     * Provides keys guaranteed as proper
     * what is compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     *
     * @return array
     */
    public static function properKeysProvider(): array
    {
        return [
            ['somekey'],
            ['some_key'],
            ['some-key'],
            ['some.key'],
            ['SOMEkey'],
            ['123key'],
            ['key3'],
            ['SOME-key_3'],
            [str_repeat('a', 64)],
        ];
    }
```

2. [REQUIRED] Test key can be encoded in UTF-8

[...] in *UTF-8 encoding* [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

```php
    /**
     * Provides multi-byte UTF-8 keys guaranteed as proper
     * what is compliant with the PSR-16 specification rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     *
     * @return array
     */
    public static function multibyteUtf8KeysProvider(): array
    {
        return [
            ['Zażółć'], // Polish characters (multi-byte)
            ['你好'], // Chinese characters
            ['🚀key'], // emoji (4 bytes),
            [str_repeat('ą', 64)], // 64 multi-byte characters
        ];
    }
```

```php
            'UTF-8 with Polish letters'                 => ["Zażółć gęślą jaźń"],
            'UTF-8 with emoji'                          => ["🦊 🔥 👨‍👩‍👧‍👦"],

            'Encoding UTF-8'                            => ["\x50\x53\x52\x2D\x31\x36\x20\xC5\xBB\xC3\xB3\xC5\x82\xC4\x87\x20\xF0\x9F\xA6\x8A"],

            'Encoding UTF-8-Mobile#DOCOMO'              => ["\x54\x65\x73\x74\xF3\xBE\x8D\x80"],
            'Encoding UTF-8-Mobile#KDDI-A'              => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'Encoding UTF-8-Mobile#KDDI-B'              => ["\x54\x65\x73\x74\xF3\xBE\x8B\x94"],
            'Encoding UTF-8-Mobile#SOFTBANK'            => ["\x54\x65\x73\x74\xF3\xBE\x80\x81"],
```

3. [REQUIRED] Test key can consist of allowed UTF-8 characters up to 64 characters length

[...]  and a length of up to `64` characters.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

```php
            [str_repeat('a', 64)],
            [str_repeat('ą', 64)], // 64 multi-byte characters
```

4. [Optional] Test key can contain additional characters other than strictly allowed ones

*Implementing libraries* MAY support additional characters [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

5. [Optional] Test key can be encodded in the additional encodings

[...] and encodings [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

6. [Optional] Test key can be longer than 64 characters

[...] or longer lengths [...]

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

```php
    /**
     * Longer keys accepted
     * what is compliant with the PSR-16 specification (optional part of the) rule:
     *
     * Implementing libraries MUST support keys consisting
     * of the characters A-Z, a-z, 0-9, _, and .
     * in any order in UTF-8 encoding and a length of up to 64 characters.
     *
     * @return array
     */
    public static function longerKeysProvider(): array
    {
        return [
            [str_repeat('a', 65)],
            [str_repeat('a', 66)],
            [str_repeat('a', 70)],
            [str_repeat('ą', 65)],
            [str_repeat('ą', 66)],
            [str_repeat('ą', 70)],
        ];
    }
```

7. [REQUIRED] Test key are returned exactly the same as passed as a string and do not show the effects of the possible escaping

*Libraries* are responsible for their own escaping of *key strings* as appropriate, but MUST be able to return the original unmodified *key string*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

8. [REQUIRED] Test key cannot contain the forbidden characters

The following characters are reserved for future extensions and MUST NOT be supported by implementing libraries: `{}()/\@:`

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

8.1. Single character being forbidden

```php
    /**
     * Provides key not allowed characters
     * what is compliant with the PSR-16 specification rule:
     *
     * The following characters are reserved for future extensions
     * and MUST NOT be supported by implementing libraries: {}()/\@:
     *
     * @return array
     */
    public static function keyForbiddenCharactersProvider(): array
    {
        return [
            ['{'],
            ['}'],
            ['('],
            [')'],
            ['/'],
            ['\\'],
            ['@'],
            [':'],
        ];
    }
```

8.2. Key consisting forbidden character among others

```php
    /**
     * Provides key containing not allowed characters
     * what is compliant with the PSR-16 specification rule:
     *
     * The following characters are reserved for future extensions
     * and MUST NOT be supported by implementing libraries: {}()/\@:
     *
     * @return array
     */
    public static function keyWithForbiddenCharactersProvider(): array
    {
        return [
            ['some{key'],
            ['some}key'],
            ['some(key'],
            ['some)key'],
            ['some/key'],
            ['some\\key'],
            ['some@key'],
            ['some:key'],
        ];
    }
```

## Cache storing time

1. [REQUIRED] Test if the TTL is not supported it is silently ignored as an user input.

If the underlying implementation does not support TTL, the user-specified TTL MUST be silently ignored.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

2. [REQUIRED] Test if the TTL is supported it will be respected.

An item with a 300 second *TTL* stored at `1:30:00` will have an expiration of `1:35:00`.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

Testing Time-To-Live (TTL) compliance in a deterministic way is notoriously tricky because time moves forward while the test executes. If the programmers rely on the host system's real-time clock (time() or microtime()), they introduce flaky tests (tests that pass or fail randomly due to execution delays, CPU spikes, or network latency in CI/CD pipelines).

To verify exact timestamp alignment without actually sleeping the PHP process for 5 minutes, the concrete cache implementation must depend on a Clock abstraction (such as the standard [`Psr\Clock\ClockInterface`](https://www.php-fig.org/psr/psr-20/) introduced in PSR-20) rather than calling native time functions directly.

***Setting Up the Virtual Clock Mock***

```php
declare(strict_types=1);

namespace Psr16Lab\Test\Mock;

use Psr\Clock\ClockInterface;

final class MutableMockClock implements ClockInterface
{
    private \DateTimeImmutable $now;

    public function __construct(string $initialTime)
    {
        $this->now = new \DateTimeImmutable($initialTime, new \DateTimeZone('UTC'));
    }

    public function now(): \DateTimeImmutable
    {
        return $this->now;
    }

    public function changeTime(string $newTime): void
    {
        $this->now = new \DateTimeImmutable($newTime, new \DateTimeZone('UTC'));
    }
}
```

***Implementing the PSR-16 TTL Compliance Test***

This test explicitly locks the scenario to the timestamps provided in the PSR-16 specification.

```php
declare(strict_types=1);

namespace Psr16Lab\Test\Integration;

use Psr16Lab\Test\Mock\MutableMockClock;
use PHPUnit\Framework\TestCase;

final class CacheTtlComplianceTest extends TestCase
{
    private MutableMockClock $clock;
    private \Psr\SimpleCache\CacheInterface $cache;

    protected function setUp(): void
    {
        // 1. Initialize the mock clock to the specification's baseline time
        $this->clock = new MutableMockClock('2026-06-27 01:30:00');

        // 2. Inject the mock clock into your cache engine architecture
        // (Your implementation must utilize $this->clock->now() internally to set expiration)
        $this->cache = new YourConcreteCacheImplementation($this->clock);
    }

    public function testSpecificationTtlBoundaryWindow(): void
    {
        $key = 'spec_ttl_compliance_key';
        $value = 'cache_payload_data';
        $ttl = 300; // 300 seconds = 5 minutes

        // State A: Time is 01:30:00. Item is stored.
        $this->assertTrue($this->cache->set($key, $value, $ttl));
        $this->assertTrue($this->cache->has($key));
        $this->assertSame($value, $this->cache->get($key));

        // State B: Time warps to 01:34:59 (1 second BEFORE official expiration)
        // The data MUST still be valid and present.
        $this->clock->changeTime('2026-06-27 01:34:59');
        $this->assertTrue($this->cache->has($key), 'Item expired prematurely before the 300-second TTL window closed.');
        $this->assertSame($value, $this->cache->get($key));

        // State C: Time warps to 01:35:00 (The exact expiration timestamp)
        // Depending on whether your logic checks `>=` or `>`, the exact second is the death boundary.
        // To be safe per specification terms, at 01:35:00 the item has reached expiration.
        $this->clock->changeTime('2026-06-27 01:35:00');
        $this->assertFalse($this->cache->has($key), 'Item is still visible at the exact expiration timestamp.');
        $this->assertNull($this->cache->get($key), 'Cache miss should return null once expiration timestamp is crossed.');
    }
}
```

***Using the Symfony PSR-20 Clock implementation***

```php
declare(strict_types=1);

namespace Psr16Lab\Test\Integration;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class CacheTtlComplianceTest extends TestCase
{
    private MockClock $clock;
    private \Psr\SimpleCache\CacheInterface $cache;

    protected function setUp(): void
    {
        // 1. Initialize the official Symfony MockClock to the spec's baseline time
        // It natively satisfies Psr\Clock\ClockInterface
        $this->clock = new MockClock('2026-06-27 01:30:00', new \DateTimeZone('UTC'));

        // 2. Inject it into your PSR-16 Cache engine
        $this->cache = new YourConcreteCacheImplementation($this->clock);
    }

    public function testSpecificationTtlBoundaryWindow(): void
    {
        $key = 'spec_ttl_compliance_key';
        $value = 'cache_payload_data';
        $ttl = 300; // 5 minutes

        // State A: Time is 01:30:00. Item is stored.
        $this->assertTrue($this->cache->set($key, $value, $ttl));
        $this->assertTrue($this->cache->has($key));

        // State B: Move clock forward by 4 minutes and 59 seconds (01:34:59)
        // Using Symfony's built-in sleep() modifier which advances the mock clock safely
        $this->clock->sleep(299);

        $this->assertTrue($this->cache->has($key), 'Item expired prematurely before the 300-second TTL window closed.');
        $this->assertSame($value, $this->cache->get($key));

        // State C: Move clock forward by 1 more second to reach the exact boundary (01:35:00)
        $this->clock->sleep(1);

        $this->assertFalse($this->cache->has($key), 'Item is still visible at the exact expiration timestamp.');
        $this->assertNull($this->cache->get($key), 'Cache miss should return null once expiration timestamp is crossed.');
    }
}
```

*Implementing libraries* MAY expire an item before its requested *expiration time*, but MUST treat an item as expired once its *expiration time* is reached.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

3. [OPTIONAL] Test if the default expiration time is possible for been set.

Implementations MAY provide a mechanism for a user to specify a *default TTL* if one is not specified for a specific *cache item*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

3. [OPTIONAL] Test if the default expitation time is used when TTL is not explicitly specified or specified as null

If a *calling library* asks for an item to be saved but does not specify an *expiration time*, or specifies a *null expiration time* or *TTL*, an implementing library MAY use a configured *default duration*.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

4. [REQUIRED] Test if data never goes stale when the TTL is not set as well as the default expiration time

If no user-specified default is provided implementations MUST default to the maximum legal value allowed by the underlying implementation.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#13-cache)

If no *default duration* has been set, the *implementing library* MUST interpret that as a *request to cache the item forever*, or for as long as the underlying implementation supports.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

5. [REQUIRED] Test if negative or zero TTL results in data deletion

If a *negative or zero TTL* is provided, the item MUST be deleted from the cache if it exists, as it is expired already.

-- [PSR Documentation](https://www.php-fig.org/psr/psr-16/#12-definitions)

