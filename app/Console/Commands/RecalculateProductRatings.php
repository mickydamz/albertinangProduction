<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class RecalculateProductRatings extends Command
{
    /**
     * Recompute every product's stored rating + rating_count purely from its
     * actual customer reviews. Products with no reviews are reset to unrated,
     * clearing any historical placeholder (e.g. a default 5-star) so the
     * storefront only ever shows ratings that real customers gave.
     */
    protected $signature = 'products:recalculate-ratings';

    protected $description = 'Recalculate product ratings and review counts from actual reviews';

    public function handle(): int
    {
        $updated = 0;

        Product::query()->chunkById(200, function ($products) use (&$updated) {
            foreach ($products as $product) {
                $reviews = $product->reviews()->whereNotNull('rating')->get();

                $count = $reviews->count();
                $avg   = $count > 0 ? round($reviews->avg('rating'), 1) : null;

                // Only write when something actually changes.
                if ((string) $product->rating !== (string) $avg
                    || (int) $product->rating_count !== $count) {
                    $product->rating       = $avg;
                    $product->rating_count = $count;
                    $product->save();
                    $updated++;
                }
            }
        });

        $this->info("Recalculated ratings. {$updated} product(s) updated.");

        return self::SUCCESS;
    }
}
