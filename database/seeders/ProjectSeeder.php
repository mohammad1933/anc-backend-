<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Hotel Lobby - Ritz Carlton', 'client' => 'Marriott International Group', 'status' => 'active', 'is_favorite' => true,
                'cover_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQyQePJUCD5WF_gZUUmrx1WDtBxvlHc-7WKzW5X0ucCF63AttpOgMuzenoFNL8Ny8_bnBOCshadqmpe7sNul8fkvn5nbcyVQ8DTikZDWAJzOD1GCoBx6j1HPcCxKdUa8NVTsJSyZUthk9A96fzStxNAeFynFepqHi6I4_FqwO5KfinrpMP6iZVBPSsjr8rn1PVqoL85H9EeRIuoFn0CabtRbE08fnI5v-9LPss1zCJQVBgE0qYsotT4hOggfxolWGY-JQKHcYUVVE',
                'fabrics' => $this->fabrics(12), 'saved_colors' => ['#18372D', '#B78A1C', '#E8E0D3'],
            ],
            [
                'name' => 'Villa Living Room - Emirates Hills', 'client' => 'Private Residence', 'status' => 'in_review', 'is_favorite' => false,
                'description' => 'A curation of high-pile velvets and artisanal weaves for the primary residence master suite and lounge area.',
                'cover_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAqsM9qRhlmELQlsdFtT2WWud7umjhlEYKaYWjYpCn92xtnxrKOeH9PWQnxSOqAHZgxS9S1nwB8uTffR8BvtYNAnSSZ35jSTfqWlKoqkG1VClArQaj7pD_qN0wk2we8mvwplv_9f-1xeZaeyLxxrRvHDaY3XnvPle5XqjRtaq_9ZitzdwbEdrlsUitUn9_KNxgDb9vX7rf7MzpGg2xiz2JjU1zIzU412bU1mfG25Q6YJwxdF0-LoASdg-S9aPuRO_uKA_SD8paRe5g',
                'fabrics' => [
                    ['name' => 'Venetian Gold Velvet', 'collection' => 'Milan Collection', 'color' => '#F9C34E', 'availability' => 'In Stock', 'code' => 'LUX-VG-2024', 'price' => 145, 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC2hiK4rJIhMPn-96uxsnOQHBDB-EvyrgfUeJyV6tbdDiVA15IdfbxfBAfwyz9DYzQRXkQOXaBvkgUwDhUoBH2ODR_E_AsG8b9xMNfIk4Ss2jL2ON8l3balcJI1Hi-XpmIywgCRWmuhIPFs0u5X2UL5byvXM67LVfNPJt2OPmXswh4NUrKf9fvlXHi-roJox0R1kvmUI5AzHV5OkZK4-j9rxqSkaO7_hzQDEi1Sfmfyl6iQGUdARr3PqOdyB7RwRJaobG7a-HVqL_8'],
                    ['name' => 'Nordic Weave Cream', 'collection' => 'Scandi Series', 'color' => '#DDD7CB', 'availability' => '8 Weeks Lead', 'code' => 'LUX-NW-881', 'price' => 110, 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuALDoMUaUZkjspHD86q2VyuhKApWGFo4N3XVHOvibfq_6wYywDlHje9l6cW-0CsD_utNYcxGkvTpWbWe7hqLfRoY-OW9tbU4dFYj5H53F76Jkc1pKpTwQVrZCm1Bk4ALgI7pGa2nZ9hxmDJtSHiL4jyfav44YCc8b-OC0cbeEKDEQsEEMNO3F64O5-icqeToyyhEJ5hywySAaU9tMDeBzp8Fv-TaS33n7ENuGjCgfdcHT6Ps09l2_O7SFBtSLvP0kAJhLSjIDBAmms'],
                    ['name' => 'Sage Silk Damask', 'collection' => 'Heritage Line', 'color' => '#C7C4B9', 'availability' => 'Low Stock', 'code' => 'LUX-SD-442', 'price' => 210, 'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuC1FE3I0ZjpEema4YxbYBz65q3xDDGgZwStYcw43Zj5QywrY1RaM_cswy_ah3SWjhlQovkUYGLhs6vQclrHwyxhuCaPWhS0DurbHBZrQU_AXZ_r2cSAyMiNNCoowejMmyB-Samqw-xEq1cnkn5ooFvFa0TalBl14Rrq_Jf9BxduMe9lK4N7grUge0sa0fJddi1o0WYrJ4AikZdu_DEJD6NbDf1_A_06n8yM4GT6OR9KbguQ9u9L-Cpvv44J691DPqZ1xsN_WZQdZFg'],
                ],
                'saved_colors' => ['#F9C34E', '#191B1A', '#E1E1E1', '#D5D5D5', '#8B6600'],
                'palette' => [
                    ['name' => 'Luxe Gold', 'hex' => '#F9C34E'], ['name' => 'Charcoal', 'hex' => '#191B1A'],
                    ['name' => 'Mist Grey', 'hex' => '#E1E1E1'], ['name' => 'Stone', 'hex' => '#D5D5D5'], ['name' => 'Bronze', 'hex' => '#8B6600'],
                ],
                'inspiration_images' => [
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuByaNWJXbSBMukIU6GoK1Pk-n5v5laatdwgWV5R0s3LDmAJkYICxrPTbDLlWJOyW77T0U7MaeJEMjTvTuuqZDbXyq8faMVO9oQj3-Bg9X6EVd9S0XthGsHyRUvZKyA4sqcmPwrXbBiDJV01XZJj120bRd-bmJ7vFuk8DpELWRBn1LsknRRxaNIXdDZ1rNbusd6IuPwfs5qV36h89MElyWjvAVqPdfaUXy_lg_IXsqANwRUovReKUwgCoF1devmFKo9BJVGXLigGhAw',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuCbUphiBYBec7k_Wiq4uFZVMdzpCde--UuifW3l8rz2koOU190FoSQUfl-GI42vtP5sZdncni7Ac6dZPe5uv83o57hcDYZQ12VIzv1Cw1cpF1Ati8H9ySHjnsQ8rmeelFVKl1Or7h7UiqeiBqYixbtLoKGm7zm-7c2xlAsW3tiTSTX60sJFaQnCqiYtsOXpSrP6jCySR57eudmcN4b5BcL07q4OX7GJfERgPd-Kgf1XLahZC-lmhcrpEEMzlDRyfdB72RdcFhh7jrM',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuDBOmeclqJ5KzqnHBFZUFP_QYs6jnKcqjy4edQC-_rIiIjfI4-cXhwtbkXy5mAZ1I9eUnP2l-mygCEzPCT59A_Et_vzaA9fXI0vZmLJN5o57t-j2W0GoKAgWzSCepkzwv_SWcp0LdeQt13FM65_19drQRQ41JDgX-8CdRUiBM3VBTGuVzmgZJsHdHoVkhEDIsmPLsijE5Iy4DeldWtD-Nq7VHNM8plnSxS6jl1Ve_zNf5mMvOlzvf8SQth0n9EVhJQ2gCkY8qCD44I',
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuAzDl_Rlk9xmBHlxscu3TtUX1OZs5M4Jdwc0E9Y1HvNZkF0mWD6hZ4JDG3Usjzv8aEnPw7EUYAqrxBY0Quembu0dPk9ISo2JqCm3MvEh_sxm1Extn7cmauyqaYbBx_zvxgVub2uFynMR40xoQd9l91KzVH0KKIupvU8TP_ruQBNcYz2bY7QiOUSECInzU1Y9mhJ4mx7yllJ8l8Fh1FHhcAuImShDiBaksrnb56bgzXhyzwiDwk5JC2z0r-RuXbxKjr-t9dvGnEOQ0c',
                ],
                'members' => [
                    ['name' => 'Elena Jenson', 'role' => 'Principal Designer', 'initials' => 'EJ'],
                    ['name' => 'Marcus Webb', 'role' => 'Project Manager', 'initials' => 'MW'],
                    ['name' => 'Sarah Chen', 'role' => 'Procurement', 'initials' => 'SC'],
                ],
                'timeline' => [
                    ['title' => 'Fabric Finalization', 'date' => '2026-10-24', 'completed' => true],
                    ['title' => 'Sample Approval', 'date' => '2026-11-12', 'completed' => false],
                    ['title' => 'Initial Order Placed', 'date' => '2026-12-05', 'completed' => false],
                ],
                'recent_activity' => [
                    ['text' => 'Elena added Venetian Gold Velvet to project.', 'time' => '2 hours ago'],
                    ['text' => 'Marcus updated lead time for Scandi Series.', 'time' => '5 hours ago'],
                    ['text' => 'Sarah uploaded 4 new inspiration photos.', 'time' => 'Yesterday'],
                ],
            ],
            [
                'name' => 'Bistro Signature - Downtown', 'client' => 'Signature Hospitality Group', 'status' => 'active', 'is_favorite' => false,
                'cover_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDuDyMGGmsTmhc1obMwjUnFhhRVmoWDJy4VhIq7W2SaCo3urABkXzG6aw9B-wauBi2g2wRCGPqapPzlGWw7aeLJyCs6yosN5CMcL1qaYQC59mdLH1Ld0O_v8RX2mKGKwMKCnNhP0bysHsr7NbgqjiZ8ttKTJnM9hy_sbhto2qiHGd8Mf_K8PqNaydfpL9YUB9ePmfvbN9dpYlAff7mgS1nD1PhZFw8UhyYTZhvFecTPi5bohrVRmBZG_St_sXXLSWkcTamSrJ8jOiU',
                'fabrics' => $this->fabrics(24), 'saved_colors' => ['#3A2018', '#7A322C', '#C9A86A'],
            ],
            [
                'name' => 'Penthouse Office', 'client' => 'TechVision Global', 'status' => 'active', 'is_favorite' => true,
                'cover_image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCqX2czVoyypQm-IYqfnVOkNwu95qupwBazdov4UWZjs1OYC9xknYF4Atw3O_Qw9Yx8cb9kjqhjMWd-zTXWgnWdUKLvtuB3q4rUma_3r6nOyKnGG2Z1GMpvNOvRtK-dj7lDiatrcRJItfWDZGUfE69589JA-gIkHmIPAL_O4UajuvMNwIHR0WP7QMYUFiNuLuXprqgCqrOEGD2z3q-zE-qq2M1X_cBo1JLeBMCwq3Sb9S4VRUeJdOKycBKi4AnnNPTo3jOuvy4wfPg',
                'fabrics' => $this->fabrics(18), 'saved_colors' => ['#26323A', '#88939A', '#B99352'],
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['name' => $project['name']], $project + $this->boardContent());
        }

        if (Project::count() < 12) {
            Project::factory()->count(12 - Project::count())->create();
        }
    }

    /** @return array<int, array{name: string, collection: string, color: string}> */
    private function fabrics(int $count): array
    {
        return collect(range(1, $count))->map(fn (int $number) => [
            'name' => ['Royal Velvet', 'Belgian Linen', 'Performance Bouclé', 'Silk Jacquard'][($number - 1) % 4].' '.str_pad((string) $number, 2, '0', STR_PAD_LEFT),
            'collection' => 'Luxe Architectural', 'color' => ['#7A5900', '#D2C5B0', '#454747'][$number % 3],
        ])->all();
    }

    /** @return array<string, mixed> */
    private function boardContent(): array
    {
        return [
            'notes' => ['Confirm final fabric quantities before specification sign-off.', 'Client prefers warm natural finishes and subtle texture.'],
            'inspiration_images' => ['https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=900&q=85'],
            'members' => [
                ['name' => 'Amelia Stone', 'role' => 'Lead Designer', 'initials' => 'AS'],
                ['name' => 'Karim Nasser', 'role' => 'Architect', 'initials' => 'KN'],
                ['name' => 'Lina Haddad', 'role' => 'Procurement', 'initials' => 'LH'],
            ],
            'timeline' => [
                ['title' => 'Concept approved', 'date' => now()->subDays(4)->toDateString(), 'completed' => true],
                ['title' => 'Samples delivered', 'date' => now()->addDays(3)->toDateString(), 'completed' => false],
                ['title' => 'Final specification', 'date' => now()->addDays(12)->toDateString(), 'completed' => false],
            ],
            'recent_activity' => [
                ['text' => 'Amelia added 3 fabrics', 'time' => '2 hours ago'],
                ['text' => 'Karim commented on the color palette', 'time' => 'Yesterday'],
            ],
        ];
    }
}
