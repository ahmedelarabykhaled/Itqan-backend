<?php

namespace App\Support;

class QuranReciterCatalog
{
    /**
     * Reciters to keep, using the highest-quality complete everyayah.com source.
     *
     * @var list<array{slug: string, name_en: string, bitrate: string}>
     */
    public const RECITERS = [
        ['slug' => 'Abdul_Basit_Murattal_192kbps', 'name_en' => 'Abdul Basit (Murattal)', 'bitrate' => '192kbps'],
        ['slug' => 'Muhsin_Al_Qasim_192kbps', 'name_en' => 'Abdul Muhsin Al-Qasim', 'bitrate' => '192kbps'],
        ['slug' => 'Abdurrahmaan_As-Sudais_192kbps', 'name_en' => 'Abdul Rahman Al-Sudais', 'bitrate' => '192kbps'],
        ['slug' => 'Abdullah_Basfar_192kbps', 'name_en' => 'Abdullah Basfar', 'bitrate' => '192kbps'],
        ['slug' => 'Abdullah_Matroud_128kbps', 'name_en' => 'Abdullah Matroud', 'bitrate' => '128kbps'],
        ['slug' => 'Abu_Bakr_Ash-Shaatree_128kbps', 'name_en' => 'Abu Bakr Al-Shatri', 'bitrate' => '128kbps'],
        ['slug' => 'ahmed_ibn_ali_al_ajamy_128kbps', 'name_en' => 'Ahmed Al-Ajamy', 'bitrate' => '128kbps'],
        ['slug' => 'Ahmed_Neana_128kbps', 'name_en' => 'Ahmed Naina', 'bitrate' => '128kbps'],
        ['slug' => 'Akram_AlAlaqimy_128kbps', 'name_en' => 'Akram Al-Alaqimy', 'bitrate' => '128kbps'],
        ['slug' => 'Husary_Muallim_128kbps', 'name_en' => 'Al-Husary (Muallim)', 'bitrate' => '128kbps'],
        ['slug' => 'Husary_128kbps_Mujawwad', 'name_en' => 'Al-Husary (Mujawwad)', 'bitrate' => '128kbps'],
        ['slug' => 'Minshawy_Mujawwad_192kbps', 'name_en' => 'Al-Minshawi (Mujawwad)', 'bitrate' => '192kbps'],
        ['slug' => 'Minshawy_Murattal_128kbps', 'name_en' => 'Al-Minshawi (Murattal)', 'bitrate' => '128kbps'],
        ['slug' => 'Hudhaify_128kbps', 'name_en' => 'Ali Al-Huthaify', 'bitrate' => '128kbps'],
        ['slug' => 'Ali_Hajjaj_AlSuesy_128kbps', 'name_en' => 'Ali Hajjaj Al-Suesy', 'bitrate' => '128kbps'],
        ['slug' => 'Ali_Jaber_64kbps', 'name_en' => 'Ali Jaber', 'bitrate' => '64kbps'],
        ['slug' => 'Ayman_Sowaid_64kbps', 'name_en' => 'Ayman Sowaid', 'bitrate' => '64kbps'],
        ['slug' => 'aziz_alili_128kbps', 'name_en' => 'Aziz Alili', 'bitrate' => '128kbps'],
        ['slug' => 'Fares_Abbad_64kbps', 'name_en' => 'Fares Abbad', 'bitrate' => '64kbps'],
        ['slug' => 'Hani_Rifai_192kbps', 'name_en' => 'Hani Al-Rifai', 'bitrate' => '192kbps'],
        ['slug' => 'Ibrahim_Akhdar_32kbps', 'name_en' => 'Ibrahim Al-Akhdar', 'bitrate' => '32kbps'],
        ['slug' => 'warsh/warsh_ibrahim_aldosary_128kbps', 'name_en' => 'Ibrahim Al-Dosari (Warsh)', 'bitrate' => '128kbps'],
        ['slug' => 'Khaalid_Abdullaah_al-Qahtaanee_192kbps', 'name_en' => 'Khalid Al-Qahtani', 'bitrate' => '192kbps'],
        ['slug' => 'khalefa_al_tunaiji_64kbps', 'name_en' => 'Khalifa Al-Tunaiji', 'bitrate' => '64kbps'],
        ['slug' => 'MaherAlMuaiqly128kbps', 'name_en' => 'Maher Al-Muaiqly', 'bitrate' => '128kbps'],
        ['slug' => 'mahmoud_ali_al_banna_32kbps', 'name_en' => 'Mahmoud Ali Al-Banna', 'bitrate' => '32kbps'],
        ['slug' => 'Husary_128kbps', 'name_en' => 'Mahmoud Khalil Al-Husary', 'bitrate' => '128kbps'],
        ['slug' => 'Alafasy_128kbps', 'name_en' => 'Mishary Rashid Alafasy', 'bitrate' => '128kbps'],
        ['slug' => 'Mohammad_al_Tablaway_128kbps', 'name_en' => 'Mohammad Al-Tablawi', 'bitrate' => '128kbps'],
        ['slug' => 'Muhammad_AbdulKareem_128kbps', 'name_en' => 'Muhammad Abdul Kareem', 'bitrate' => '128kbps'],
        ['slug' => 'Muhammad_Ayyoub_128kbps', 'name_en' => 'Muhammad Ayyub', 'bitrate' => '128kbps'],
        ['slug' => 'Muhammad_Jibreel_128kbps', 'name_en' => 'Muhammad Jibreel', 'bitrate' => '128kbps'],
        ['slug' => 'Nabil_Rifa3i_48kbps', 'name_en' => 'Nabil Al-Rifai', 'bitrate' => '48kbps'],
        ['slug' => 'Nasser_Alqatami_128kbps', 'name_en' => 'Nasser Al-Qatami', 'bitrate' => '128kbps'],
        ['slug' => 'Ghamadi_40kbps', 'name_en' => 'Saad Al-Ghamdi', 'bitrate' => '40kbps'],
        ['slug' => 'Sahl_Yassin_128kbps', 'name_en' => 'Sahl Yassin', 'bitrate' => '128kbps'],
        ['slug' => 'Salah_Al_Budair_128kbps', 'name_en' => 'Salah Al-Budair', 'bitrate' => '128kbps'],
        ['slug' => 'Salaah_AbdulRahman_Bukhatir_128kbps', 'name_en' => 'Salah Bukhatir', 'bitrate' => '128kbps'],
        ['slug' => 'Saood_ash-Shuraym_128kbps', 'name_en' => 'Saud Al-Shuraim', 'bitrate' => '128kbps'],
        ['slug' => 'Yasser_Ad-Dussary_128kbps', 'name_en' => 'Yasser Al-Dosari', 'bitrate' => '128kbps'],
        ['slug' => 'Yaser_Salamah_128kbps', 'name_en' => 'Yasser Salama', 'bitrate' => '128kbps'],
        ['slug' => 'warsh/warsh_yassin_al_jazaery_64kbps', 'name_en' => 'Yassin Al-Jazaery (Warsh)', 'bitrate' => '64kbps'],
    ];

    /**
     * @return list<array{slug: string, name_en: string, bitrate: string}>
     */
    public static function all(): array
    {
        return self::RECITERS;
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_column(self::RECITERS, 'slug');
    }

    public static function contains(string $slug): bool
    {
        return in_array($slug, self::slugs(), true);
    }
}
