<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\FavoriteFolder;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $folders = collect([
            ['name' => 'Hotel Projects', 'icon' => 'hotel', 'sort_order' => 1],
            ['name' => 'Luxury Collection', 'icon' => 'diamond', 'sort_order' => 2],
            ['name' => 'Modern Fabrics', 'icon' => 'chair', 'sort_order' => 3],
            ['name' => 'Curtains', 'icon' => 'curtains', 'sort_order' => 4],
        ])->mapWithKeys(function (array $folder): array {
            $model = FavoriteFolder::updateOrCreate(['name' => $folder['name']], $folder);

            return [$folder['name'] => $model];
        });

        $favorites = [
            ['name' => 'Royal Jacquard V1', 'type' => 'collection', 'collection' => 'Luxury Collection', 'material' => '100% Silk Blend', 'colors' => ['#D8A91F', '#181A19', '#E4E4E4'], 'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBlgtBpUSnEsmJqZ2pyl-vYnIrxC0usg9xVZ2PtAdGIv68oXPupv9EuOKVNUakH1Vgc-zT1w3JQvLg9I99VrYQ8U5zkgJXiGOXLrOheJmm6mFtsTb5vLvC3R0HJYwwDUjJOaj9zEkAOJadhanmjZYlFSLjCJl-1K6q6Mp6ntqTXkAMYMpMXzwPIof0JTGcE_qbrhbzIeDN7eI4LQnmxx73czpcIBI610mCv05hDb75r5Nev0C-PMlg1MPxAZ2zI0FGWME1YK-IA5r0'],
            ['name' => 'Venetian Velvet', 'type' => 'texture', 'collection' => 'Hotel Projects', 'material' => 'Heavy Weight Polyester', 'colors' => ['#005D3B', '#351B1D', '#494B49'], 'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC0lK5wX6AZURtOW-I83-FxZz4OC9jZAhoeLVlLU5Oxu9k2pPrHkP95UPkqyW5bMmdOT2yEi1uTm7tJA4zWfi88ZqZ_AoFarP3HlYlqzIsnnJ3Redp6nXI4XEWOuZExpuACl3SAIlPa8VTOIAxlKHdasWg1-519fO3C47aPUbp4syFqx69Zu73_El9FAtfVAdf1br_TixdnwiZsMKYfBFChF8lTZ1fZsI9zDF-4iMpHNDmF0gWqFhIcepR9h1NXI02_CMl6JdzXkA0'],
            ['name' => 'Geometric Shear', 'type' => 'color', 'collection' => 'Curtains', 'material' => 'Linen Blend', 'colors' => ['#F3F1D9', '#C7A77A'], 'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC_YV-2hZDYoww6YKpTyGNL1nEFi719qy_D0FC-m0V2iAtrXC9-iEjksqOZr3R-1zvOUhoVP_dHPcNEoa4-EF_JRb27mwsVYT07uVEgYZObrgmJUNRgtvJcUv7lXORPBV-K8mtI60Y1Oq7REWAmz3lHGUOj5Fz9RUunUYJT3nUAJX561eCKd4tfWOTBK38_eY900FPqlZQBNOEVnYrOabJ-BN5B8hXpLOReEiGPrLS_M_Qk2vYHZrHPNGgA5E3mVXakOT2M4z5FBLg'],
            ['name' => 'Modern Bouclé', 'type' => 'texture', 'collection' => 'Modern Fabrics', 'material' => 'Wool Mix', 'colors' => ['#FFF6BC', '#92908F'], 'image_url' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBHmm6e29HUqzUE-KT8wfkCew_cs__nvOI5XYorjeyHvwhJLFFrPPgxwRO3H5DFJ-w2RBds3g9jKV7p-LRCRO0c-7Xu2O8dzsMb3zDbH8sCpx0428onuJB7pelrca6yM2K3jIaPWI06yxUBIqf4dzd3O-7RTnJsLohQKaGYpdaChmsCXL6PUmD11VIg_umfTw6soZ6oh9_iHtcNvpwkcDG_xqk0mDlaJY0DnZqXnzeyWlwmFMRQWfDC1oreY1QdoHq3f4vVc5TVNc0'],
        ];

        foreach ($favorites as $favorite) {
            Favorite::updateOrCreate(['name' => $favorite['name']], $favorite + ['favorite_folder_id' => $folders[$favorite['collection']]->id]);
        }
    }
}
