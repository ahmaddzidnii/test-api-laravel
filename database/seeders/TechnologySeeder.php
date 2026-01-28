<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TechnologySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('data/technologies.json');

        $items = json_decode(File::get($path), true);

        foreach ($items as $item) {
            DB::table('technologies')->updateOrInsert(
                ['name' => $item['nama']],
                [
                    'logo_url'  => $item['sumber'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
