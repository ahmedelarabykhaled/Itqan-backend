<?php

namespace App\Support;

class QuranAyahCatalog
{
    /**
     * @var list<int>
     */
    public const AYAH_COUNTS = [
        7, 286, 200, 176, 120, 165, 206, 75, 129, 109,
        123, 111, 43, 52, 99, 128, 111, 110, 98, 135,
        112, 78, 118, 64, 77, 227, 93, 88, 69, 60,
        34, 30, 73, 54, 45, 83, 182, 88, 75, 85,
        54, 53, 89, 59, 37, 35, 38, 29, 18, 45,
        60, 49, 62, 55, 78, 96, 29, 22, 24, 13,
        14, 11, 11, 18, 12, 12, 30, 52, 52, 44,
        28, 28, 20, 56, 40, 31, 50, 40, 46, 42,
        29, 19, 36, 25, 22, 17, 19, 26, 30, 20,
        15, 21, 11, 8, 8, 19, 5, 8, 8, 11,
        11, 8, 3, 9, 5, 4, 7, 3, 6, 3,
        5, 4, 5, 6,
    ];

    /**
     * @return list<array{surah: int, ayah: int}>
     */
    public static function pairs(?int $surahFilter = null, ?int $limitAyahs = null): array
    {
        $pairs = [];
        $surahs = $surahFilter !== null ? [$surahFilter] : range(1, count(self::AYAH_COUNTS));

        foreach ($surahs as $surah) {
            if ($surah < 1 || $surah > count(self::AYAH_COUNTS)) {
                continue;
            }

            $pairs[] = ['surah' => $surah, 'ayah' => 0];

            $totalAyahs = self::AYAH_COUNTS[$surah - 1];

            for ($ayah = 1; $ayah <= $totalAyahs; $ayah++) {
                $pairs[] = ['surah' => $surah, 'ayah' => $ayah];
            }
        }

        if ($limitAyahs !== null && $limitAyahs > 0) {
            return array_slice($pairs, 0, $limitAyahs);
        }

        return $pairs;
    }

    public static function fileLabel(int $surah, int $ayah): string
    {
        return sprintf('%03d%03d', $surah, $ayah);
    }
}
