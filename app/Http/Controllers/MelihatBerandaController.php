<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MelihatBerandaController extends Controller
{
    /**
     * Use Case: Melihat beranda (Donatur, Pemohon, Pengunjung)
     */
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        $category = $request->query('category');
        $condition = $request->query('condition');

        // Query items dari database
        $itemsQuery = Item::with('organization')
            ->where('is_draft', false)
            ->where('status', 'tersedia');

        // Search filter
        if ($query !== null && $query !== '') {
            $itemsQuery->where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('deskripsi', 'like', "%{$query}%")
                  ->orWhere('lokasi', 'like', "%{$query}%");
            });
        }

        // Category filter
        if ($category !== null && $category !== '' && strtolower(str_replace(' ', '', $category)) !== 'semua') {
            $categoryNormalized = $this->normalizeCategory($category);
            if ($categoryNormalized !== null) {
                $itemsQuery->where('kategori', $categoryNormalized);
            }
        }

        // Condition filter
        if ($condition !== null && $condition !== '' && strtolower($condition) !== 'all') {
            $itemsQuery->where('kondisi', strtolower($condition));
        }

        // Order by created_at desc (terbaru)
        $itemsQuery->orderBy('created_at', 'desc');

        $items = $itemsQuery->get();

        // Transform items ke format yang diharapkan view
        $products = $items->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->judul,
                'category' => $this->normalizeCategoryForView($item->kategori), // Untuk filter
                'category_display' => $item->kategori, // Untuk display
                'condition' => $item->kondisi,
                'location' => $item->lokasi,
                'user' => $item->organization ? $item->organization->organization_name : 'Unknown',
                'status' => ucfirst($item->status),
                'img_class' => 'h-56',
                'price' => 0, // Items tidak punya price field
                'foto_barang' => $item->foto_barang ?? [],
                'created_at' => $item->created_at,
            ];
        })->toArray();

        return view('beranda', [
            'products' => $products,
            'categories' => $this->getCategories(),
            'searchQuery' => $query,
            'selectedCategory' => $category,
            'selectedCondition' => $condition,
        ]);
    }

    /**
     * Normalize category from view format to database format
     */
    private function normalizeCategory(string $category): ?string
    {
        // Normalize input: remove spaces and convert to lowercase
        $normalized = strtolower(str_replace(' ', '', $category));
        
        $mapping = [
            'semua' => null, // Will be handled by query
            'elektronik' => 'Elektronik',
            'perabotan' => 'Perabotan',
            'pakaian' => 'Pakaian',
            'buku' => 'Buku & Alat Tulis',
            'buku&alattulis' => 'Buku & Alat Tulis',
            'olahraga' => 'Olahraga',
            'dapur' => 'Dapur',
            'mainan' => 'Mainan & Hobi',
            'mainan&hobi' => 'Mainan & Hobi',
            'lainnya' => 'Lainnya',
        ];

        return $mapping[$normalized] ?? $category;
    }

    /**
     * Normalize category from database format to view format
     */
    private function normalizeCategoryForView(string $category): string
    {
        $mapping = [
            'Elektronik' => 'elektronik',
            'Perabotan' => 'perabotan',
            'Pakaian' => 'pakaian',
            'Buku & Alat Tulis' => 'buku',
            'Olahraga' => 'olahraga',
            'Dapur' => 'dapur',
            'Mainan & Hobi' => 'mainan',
            'Lainnya' => 'lainnya',
        ];

        return $mapping[$category] ?? strtolower($category);
    }

    /**
     * Get categories list for view
     */
    private function getCategories(): array
    {
        return ['Semua', 'Elektronik', 'Perabotan', 'Pakaian', 'Buku & Alat Tulis', 'Mainan & Hobi', 'Olahraga', 'Dapur', 'Lainnya'];
    }
}
