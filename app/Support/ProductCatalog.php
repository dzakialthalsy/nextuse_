<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductCatalog
{
    /**
     * Dummy dataset that mimics items shown on the landing page.
     */
    public static function all(): array
    {
        return [
            [
                'name' => 'Kamera Digital Canon EOS 700D 1',
                'category' => 'elektronik',
                'condition' => 'bekas',
                'location' => 'Jakarta Selatan',
                'user' => 'Ahmad Rizki',
                'price' => 2_500_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
            [
                'name' => 'Kamera Digital Canon EOS 700D 2',
                'category' => 'elektronik',
                'condition' => 'baru',
                'location' => 'Bandung',
                'user' => 'Budi Santoso',
                'price' => 3_200_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
            [
                'name' => 'Meja Belajar Kayu Jati',
                'category' => 'perabotan',
                'condition' => 'bekas',
                'location' => 'Surabaya',
                'user' => 'Siti Nurhaliza',
                'price' => 800_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
            [
                'name' => 'Koleksi Novel Harry Potter Lengkap',
                'category' => 'buku',
                'condition' => 'baru',
                'location' => 'Yogyakarta',
                'user' => 'Dewi Lestari',
                'price' => 650_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
            [
                'name' => 'Sepatu Olahraga Running Nike',
                'category' => 'olahraga',
                'condition' => 'bekas',
                'location' => 'Jakarta Pusat',
                'user' => 'Joko Widodo',
                'price' => 450_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
            [
                'name' => 'Set Piring Keramik Dapur',
                'category' => 'dapur',
                'condition' => 'baru',
                'location' => 'Bekasi',
                'user' => 'Rina Gunawan',
                'price' => 150_000,
                'img_class' => 'h-56',
                'status' => 'Tersedia',
            ],
        ];
    }

    public static function categories(): array
    {
        return ['Semua', 'Elektronik', 'Perabotan', 'Pakaian', 'Buku', 'OlahRaga', 'Dapur'];
    }

    public static function filter(?string $query = null, ?string $category = null, ?string $condition = null): array
    {
        return collect(self::all())
            ->when($query !== null && $query !== '', function (Collection $collection) use ($query) {
                $needle = Str::lower($query);

                return $collection->filter(function (array $item) use ($needle) {
                    return Str::contains(Str::lower($item['name']), $needle)
                        || Str::contains(Str::lower($item['category']), $needle)
                        || Str::contains(Str::lower($item['location']), $needle);
                });
            })
            ->when($category !== null && $category !== '' && Str::lower($category) !== 'semua', function (Collection $collection) use ($category) {
                $selected = Str::lower($category);

                return $collection->filter(function (array $item) use ($selected) {
                    return Str::lower($item['category']) === $selected;
                });
            })
            ->when($condition !== null && $condition !== '' && $condition !== 'all', function (Collection $collection) use ($condition) {
                $selected = Str::lower($condition);

                return $collection->filter(function (array $item) use ($selected) {
                    return Str::lower($item['condition']) === $selected;
                });
            })
            ->values()
            ->all();
    }

    public static function uniqueValues(string $key): array
    {
        $values = array_column(self::all(), $key);

        sort($values);

        return array_values(array_unique($values));
    }
}

