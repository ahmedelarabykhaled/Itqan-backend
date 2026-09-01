<?php

namespace Tests\Feature\Console\Commands;

use App\Models\QuranRecitation;
use App\Models\QuranRecitationAyah;
use App\Support\QuranReciterCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PruneQuranRecitationsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_catalog_contains_the_approved_reciters(): void
    {
        $names = array_column(QuranReciterCatalog::all(), 'name_en');
        $slugs = QuranReciterCatalog::slugs();

        $this->assertCount(42, QuranReciterCatalog::all());
        $this->assertCount(42, array_unique($slugs));
        $this->assertSame($slugs, array_values(array_unique($slugs)));
        $this->assertSame([
            'Abdul Basit (Murattal)',
            'Abdul Muhsin Al-Qasim',
            'Abdul Rahman Al-Sudais',
            'Abdullah Basfar',
            'Abdullah Matroud',
            'Abu Bakr Al-Shatri',
            'Ahmed Al-Ajamy',
            'Ahmed Naina',
            'Akram Al-Alaqimy',
            'Al-Husary (Muallim)',
            'Al-Husary (Mujawwad)',
            'Al-Minshawi (Mujawwad)',
            'Al-Minshawi (Murattal)',
            'Ali Al-Huthaify',
            'Ali Hajjaj Al-Suesy',
            'Ali Jaber',
            'Ayman Sowaid',
            'Aziz Alili',
            'Fares Abbad',
            'Hani Al-Rifai',
            'Ibrahim Al-Akhdar',
            'Ibrahim Al-Dosari (Warsh)',
            'Khalid Al-Qahtani',
            'Khalifa Al-Tunaiji',
            'Maher Al-Muaiqly',
            'Mahmoud Ali Al-Banna',
            'Mahmoud Khalil Al-Husary',
            'Mishary Rashid Alafasy',
            'Mohammad Al-Tablawi',
            'Muhammad Abdul Kareem',
            'Muhammad Ayyub',
            'Muhammad Jibreel',
            'Nabil Al-Rifai',
            'Nasser Al-Qatami',
            'Saad Al-Ghamdi',
            'Sahl Yassin',
            'Salah Al-Budair',
            'Salah Bukhatir',
            'Saud Al-Shuraim',
            'Yasser Al-Dosari',
            'Yasser Salama',
            'Yassin Al-Jazaery (Warsh)',
        ], $names);
    }

    public function test_lists_kept_reciters(): void
    {
        $this->artisan('quran-recitations:prune', ['--list' => true])
            ->expectsOutputToContain('Keeping 42 reciter(s):')
            ->expectsOutputToContain('Abdul Basit (Murattal)')
            ->expectsOutputToContain('Yassin Al-Jazaery (Warsh)')
            ->assertSuccessful();
    }

    public function test_deletes_reciters_that_are_not_in_the_keep_list(): void
    {
        Storage::fake('public');

        $kept = $this->upsertKeptReciter();
        $extra = QuranRecitation::factory()->create([
            'slug' => 'prune_extra_'.uniqid(),
            'name_en' => 'Mustafa Ismail',
        ]);

        $keptPath = "quran-recitation-ayahs/{$kept->slug}/001001.mp3";
        $extraPath = "quran-recitation-ayahs/{$extra->slug}/001001.mp3";

        Storage::disk('public')->put($keptPath, 'keep-audio');
        Storage::disk('public')->put($extraPath, 'extra-audio');

        QuranRecitationAyah::factory()->create([
            'quran_recitation_id' => $extra->id,
            'mp3_file' => $extraPath,
        ]);

        $this->artisan('quran-recitations:prune', ['--force' => true])
            ->expectsOutputToContain("Deleted reciter: {$extra->slug}")
            ->assertSuccessful();

        $this->assertDatabaseHas('quran_recitations', ['id' => $kept->id]);
        $this->assertDatabaseMissing('quran_recitations', ['id' => $extra->id]);
        $this->assertDatabaseMissing('quran_recitation_ayahs', ['quran_recitation_id' => $extra->id]);
        Storage::disk('public')->assertExists($keptPath);
        Storage::disk('public')->assertMissing("quran-recitation-ayahs/{$extra->slug}");
    }

    public function test_dry_run_does_not_delete_reciters(): void
    {
        $extra = QuranRecitation::factory()->create([
            'slug' => 'prune_extra_'.uniqid(),
            'name_en' => 'Mustafa Ismail',
        ]);

        $this->artisan('quran-recitations:prune', ['--dry-run' => true])
            ->expectsOutputToContain('[DRY RUN] Would delete')
            ->expectsOutputToContain($extra->slug)
            ->expectsOutputToContain('No reciters were deleted.')
            ->assertSuccessful();

        $this->assertDatabaseHas('quran_recitations', ['id' => $extra->id]);
    }

    public function test_cancels_when_confirmation_is_declined(): void
    {
        Storage::fake('public');

        $extra = QuranRecitation::factory()->create([
            'slug' => 'prune_extra_'.uniqid(),
            'name_en' => 'Mustafa Ismail',
        ]);

        $this->artisan('quran-recitations:prune')
            ->expectsConfirmation($this->pruneConfirmationQuestion(), 'no')
            ->expectsOutputToContain('Cancelled.')
            ->assertSuccessful();

        $this->assertDatabaseHas('quran_recitations', ['id' => $extra->id]);
    }

    public function test_deletes_after_confirmation_is_accepted(): void
    {
        Storage::fake('public');

        $extra = QuranRecitation::factory()->create([
            'slug' => 'prune_extra_'.uniqid(),
            'name_en' => 'Mustafa Ismail',
        ]);

        $this->artisan('quran-recitations:prune')
            ->expectsConfirmation($this->pruneConfirmationQuestion(), 'yes')
            ->expectsOutputToContain("Deleted reciter: {$extra->slug}")
            ->assertSuccessful();

        $this->assertDatabaseMissing('quran_recitations', ['id' => $extra->id]);
    }

    public function test_reports_when_nothing_needs_pruning(): void
    {
        Storage::fake('public');

        $this->upsertKeptReciter();

        if (QuranRecitation::query()->whereNotIn('slug', QuranReciterCatalog::slugs())->exists()) {
            $this->markTestSkipped('Database already contains reciters outside the keep list.');
        }

        $this->artisan('quran-recitations:prune', ['--force' => true])
            ->expectsOutputToContain('Nothing to prune.')
            ->assertSuccessful();

        $this->assertTrue(
            QuranRecitation::query()->where('slug', QuranReciterCatalog::slugs()[0])->exists(),
        );
    }

    private function upsertKeptReciter(): QuranRecitation
    {
        $slug = QuranReciterCatalog::slugs()[0];

        return QuranRecitation::query()->where('slug', $slug)->first()
            ?? QuranRecitation::factory()->create([
                'slug' => $slug,
                'name_en' => 'Abdul Basit (Murattal)',
            ]);
    }

    private function pruneConfirmationQuestion(): string
    {
        $pendingCount = QuranRecitation::query()
            ->whereNotIn('slug', QuranReciterCatalog::slugs())
            ->count();

        return "This will permanently delete {$pendingCount} reciter(s) and their audio files. Continue?";
    }
}
