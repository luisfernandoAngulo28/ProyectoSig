<?php

use Illuminate\Database\Seeder;

class TruncateSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        \App\OrganizationPhone::truncate();
        \App\OrganizationUser::truncate();
        \App\Organization::truncate();
        \App\ContactForm::truncate();
        \App\InformationTranslation::truncate();
        \App\Information::truncate();
        \App\ContentTranslation::truncate();
        \App\Content::truncate();
        \App\TitleTranslation::truncate();
        \App\Title::truncate();
        \App\SocialNetwork::truncate();

    }
}