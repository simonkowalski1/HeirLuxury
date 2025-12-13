<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Str;

class BackfillProductSlugs extends Command
{
    protected $signature = 'products:backfill-slugs';

    protected $description = 'Generate slugs for all products that are missing slugs';

    public function handle(): int
    {
        $this->info("Starting slug backfill...");

        Product::chunk(200, function ($products) {
            foreach ($products as $p) {
                $slug = Str::slug($p->name . '-' . $p->id);

                $p->slug = $slug;
                $p->save();

                $this->line("Updated: {$p->name} → {$slug}");
            }
        });

        $this->info("Slug backfill complete!");
        return self::SUCCESS;
    }
}
