<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Removes what DemoDataSeeder added:
 *     php artisan db:seed --class=RemoveDemoDataSeeder
 */
class RemoveDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $removed = DemoDataSeeder::remove();

        $this->command?->info("Removed {$removed} sample fixed deposits.");
    }
}
