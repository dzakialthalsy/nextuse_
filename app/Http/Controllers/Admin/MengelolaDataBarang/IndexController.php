<?php

namespace App\Http\Controllers\Admin\MengelolaDataBarang;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Organization;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * Tampilkan dashboard pengelolaan data pengguna & barang.
     */
    public function __invoke(Request $request)
    {
        $searchUser = $request->query('search_user');
        $searchItem = $request->query('search_item');

        $usersQuery = Organization::query();
        if ($searchUser) {
            $usersQuery->where(function ($query) use ($searchUser) {
                $query->where('organization_name', 'like', "%{$searchUser}%")
                    ->orWhere('email', 'like', "%{$searchUser}%")
                    ->orWhere('organization_type', 'like', "%{$searchUser}%");
            });
        }

        $itemsQuery = Item::with('organization')->where('is_draft', false);
        if ($searchItem) {
            $itemsQuery->where(function ($query) use ($searchItem) {
                $query->where('judul', 'like', "%{$searchItem}%")
                    ->orWhere('kategori', 'like', "%{$searchItem}%")
                    ->orWhere('lokasi', 'like', "%{$searchItem}%");
            });
        }

        $users = $usersQuery->orderByDesc('created_at')->paginate(8, ['*'], 'users_page')->withQueryString();
        $items = $itemsQuery->orderByDesc('created_at')->paginate(8, ['*'], 'items_page')->withQueryString();

        return view('admin.mengelola-data-barang', [
            'users' => $users,
            'items' => $items,
            'stats' => [
                'total_users' => Organization::count(),
                'total_items' => Item::where('is_draft', false)->count(),
                'need_review' => Item::where('status', 'habis')->count(),
            ],
        ]);
    }
}

