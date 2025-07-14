<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageContent;

class PageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $page = Page::firstOrCreate(
            ['slug' => 'terms_and_conditions'],
            ['publish' => 'published']
        );

        PageContent::firstOrCreate(
            ['page_id' => $page->id],
            ['name' => 'Terms and Conditions','page_content' => 'Terms and Conditions']
        );

        $page = Page::firstOrCreate(
            ['slug' => 'privacy_policy'],
            ['publish' => 'published']
        );

        PageContent::firstOrCreate(
            ['page_id' => $page->id],
            ['name' => 'Privacy Policy','page_content' => 'Privacy Policy']
        );
    }
}
