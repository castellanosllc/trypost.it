<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::create(['name' => 'English', 'code' => 'en', 'flag' => '/images/languages/en.png']);
        Language::create(['name' => 'Português', 'code' => 'pt', 'flag' => '/images/languages/pt.png']);
        Language::create(['name' => 'Español', 'code' => 'es', 'flag' => '/images/languages/es.png']);
    }
}
