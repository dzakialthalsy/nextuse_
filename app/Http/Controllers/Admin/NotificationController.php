<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportItem;
use App\Models\ReportUser;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = $request->session()->get('organization_id');
        $isAdmin = (bool) $request->session()->get('is_admin');

        if (! $organizationId || ! $isAdmin) {
            return response()->json([]);
        }

        $items = ReportItem::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (ReportItem $r) {
                return [
                    'title' => 'Laporan Postingan',
                    'preview' => ($r->item_title ? $r->item_title.' • ' : '').ucfirst(str_replace('-', ' ', $r->kategori)),
                    'time' => optional($r->created_at)->diffForHumans(),
                    'url' => route('admin.review.show', ['type' => 'item', 'id' => $r->id]),
                    'avatar' => 'I',
                ];
            });

        $users = ReportUser::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function (ReportUser $r) {
                return [
                    'title' => 'Laporan Pengguna',
                    'preview' => ($r->target_user_name ? $r->target_user_name.' • ' : '').ucfirst(str_replace('-', ' ', $r->kategori)),
                    'time' => optional($r->created_at)->diffForHumans(),
                    'url' => route('admin.review.show', ['type' => 'user', 'id' => $r->id]),
                    'avatar' => 'U',
                ];
            });

        return response()->json($items->merge($users)->take(5)->values());
    }
}