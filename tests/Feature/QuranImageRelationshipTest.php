<?php

namespace Tests\Feature;

use App\Models\QuranImage;
use App\Models\QuranImagePart;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuranImageRelationshipTest extends TestCase
{
    use DatabaseTransactions;

    public function test_quran_image_has_many_parts(): void
    {
        $quranImage = QuranImage::factory()->create([
            'width' => 1200,
        ]);

        $parts = QuranImagePart::factory()
            ->count(2)
            ->for($quranImage)
            ->create();

        $quranImage->refresh()->load('parts');

        $this->assertCount(2, $quranImage->parts);
        $this->assertTrue($quranImage->parts->contains($parts->first()));
        $this->assertTrue($quranImage->parts->contains($parts->last()));
    }

    public function test_quran_image_part_belongs_to_quran_image(): void
    {
        $quranImage = QuranImage::factory()->create([
            'width' => 800,
        ]);

        $part = QuranImagePart::factory()
            ->for($quranImage)
            ->create([
                'source_url' => 'https://example.com/part.png',
            ]);

        $this->assertTrue($part->quranImage->is($quranImage));
        $this->assertSame(800, $part->quranImage->width);
    }

    public function test_deleting_quran_image_cascades_to_parts(): void
    {
        $quranImage = QuranImage::factory()->create();

        $partIds = QuranImagePart::factory()
            ->count(3)
            ->for($quranImage)
            ->create()
            ->pluck('id');

        $quranImage->delete();

        foreach ($partIds as $partId) {
            $this->assertDatabaseMissing('quran_image_parts', [
                'id' => $partId,
            ]);
        }
    }
}
