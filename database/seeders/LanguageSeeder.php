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
        Language::create(['name' => 'English', 'code' => 'en-US', 'flag' => '/images/languages/en-US.png']);
        Language::create(['name' => 'Portuguese (Brazil)', 'code' => 'pt-BR', 'flag' => '/images/languages/pt-BR.png']);
        Language::create(['name' => 'Spanish', 'code' => 'es', 'flag' => '/images/languages/es.png']);
    }
}
