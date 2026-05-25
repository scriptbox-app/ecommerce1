<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Str;
use Botble\Slug\Facades\SlugHelper;

class ProductTagSeeder extends BaseSeeder
{
    public function run(): void
    {
        $tags = [
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Mobile',
            ],
            [
                'name' => 'Iphone',
            ],
            [
                'name' => 'Printer',
            ],
            [
                'name' => 'Office',
            ],
            [
                'name' => 'IT',
            ],
        ];

        ProductTag::query()->truncate();
        Slug::query()->where('reference_type', ProductTag::class)->delete();

        foreach ($tags as $key => $item) {
            $tag = ProductTag::query()->create($item);

            Slug::query()->create([
                'reference_type' => ProductTag::class,
                'reference_id' => $tag->id,
                'key' => Str::slug($tag->name),
                'prefix' => SlugHelper::getPrefix(ProductTag::class),
            ]);
        }
    }
}
