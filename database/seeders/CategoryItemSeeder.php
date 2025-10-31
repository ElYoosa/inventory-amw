<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;

class CategoryItemSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori utama
        $categories = [
            ['name' => 'Perlengkapan Jamaah', 'description' => null],
            ['name' => 'Perlengkapan Operasional', 'description' => null],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // Daftar 30 item perlengkapan jamaah
        $items = [
            'Suitcase Hardcase','Cabin Hardcase','Suitcase','Cabin Bag','Passport Bag',
            'Shoe Bag','Duaa Book','Batik (Female)','Batik (Male)','Hijab',
            'Ihram Belt','Ihram','Prayer Kit (Female)','Ihram Kids','Hijab Kids',
            'Prayer Kit (Female) Kids','Money Wallet Big','Money Wallet Small',
            'Passport Cover','Stiker Zam-Zam','Neck Pillow','ID Card Holder',
            'ID Card Strap','Souvenir Bag','Syall','Stiker Kurma','Plastik Snack',
            'Map Anamta','Tumbler','Hat'
        ];

        $cat = Category::where('name', 'Perlengkapan Jamaah')->first();

        foreach ($items as $name) {
            Item::firstOrCreate(
                ['name' => $name, 'category_id' => $cat->id],
                ['unit' => 'pcs', 'stock' => 0, 'min_stock' => 10]
            );
        }
    }
}
