<?php

namespace App\Http\Controllers\Donatur;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingRequestController extends Controller
{
    /**
     * Display all incoming requests for the donor's items.
     */
    public function index(Request $request)
    {
        if (! $request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        if ($request->session()->get('is_donor') !== true) {
            return redirect()->route('beranda');
        }

        $organizationId = (int) $request->session()->get('organization_id');

        $statusOptions = [
            'semua' => 'Semua Status',
            'pending' => 'Menunggu Review',
            'review' => 'Sedang Ditinjau',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];

        $sortOptions = [
            'terbaru' => 'Terbaru',
            'terlama' => 'Terlama',
            'jumlah-terbanyak' => 'Jumlah Terbanyak',
            'jumlah-tersedikit' => 'Jumlah Paling Sedikit',
        ];

        $filters = [
            'search' => trim((string) $request->get('search')),
            'status' => $request->get('status', 'semua'),
            'sort' => $request->get('sort', 'terbaru'),
            'item_id' => $request->get('item_id'),
        ];

        $requestsQuery = ItemRequest::query()
            ->with([
                'item' => fn ($query) => $query->select('id', 'judul', 'lokasi', 'jumlah', 'status', 'organization_id', 'foto_barang'),
                'organization:id,organization_name,contact_person,phone,email',
                'organization.profile:id,organization_id,contact_phone,contact_email,location',
            ])
            ->whereHas('item', fn ($query) => $query->where('organization_id', $organizationId));

        if ($filters['search'] !== '') {
            $search = $filters['search'];
            $requestsQuery->where(function ($query) use ($search) {
                $query->whereHas('item', fn ($itemQuery) => $itemQuery->where('judul', 'like', "%{$search}%"))
                    ->orWhereHas('organization', fn ($orgQuery) => $orgQuery->where('organization_name', 'like', "%{$search}%"));
            });
        }

        if ($filters['status'] !== 'semua' && isset($statusOptions[$filters['status']])) {
            $requestsQuery->where('status', $filters['status']);
        }

        if (! empty($filters['item_id'])) {
            $requestsQuery->where('item_id', (int) $filters['item_id']);
        }

        $sortMap = [
            'terbaru' => ['created_at', 'desc'],
            'terlama' => ['created_at', 'asc'],
            'jumlah-terbanyak' => ['requested_quantity', 'desc'],
            'jumlah-tersedikit' => ['requested_quantity', 'asc'],
        ];
        $sortRule = $sortMap[$filters['sort']] ?? $sortMap['terbaru'];

        $requests = $requestsQuery
            ->orderBy($sortRule[0], $sortRule[1])
            ->paginate(10)
            ->withQueryString();

        $statusCounts = ItemRequest::select('status', DB::raw('count(*) as total'))
            ->whereHas('item', fn ($query) => $query->where('organization_id', $organizationId))
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $statusCounts->sum(),
            'pending' => (int) ($statusCounts['pending'] ?? 0),
            'review' => (int) ($statusCounts['review'] ?? 0),
            'approved' => (int) ($statusCounts['approved'] ?? 0),
            'rejected' => (int) ($statusCounts['rejected'] ?? 0),
        ];

        $itemsForFilter = Item::query()
            ->where('organization_id', $organizationId)
            ->orderBy('judul')
            ->get(['id', 'judul']);

        return view('donatur.permohonan.index', [
            'requests' => $requests,
            'stats' => $stats,
            'filters' => $filters,
            'statusOptions' => $statusOptions,
            'sortOptions' => $sortOptions,
            'itemsForFilter' => $itemsForFilter,
        ]);
    }
}

