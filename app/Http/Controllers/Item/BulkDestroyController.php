<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BulkDestroyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->session()->has('organization_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $organizationId = $request->session()->get('organization_id');
        $items = Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->get();

        foreach ($items as $item) {
            if ($item->foto_barang) {
                foreach ($item->foto_barang as $foto) {
                    if (Storage::disk('public')->exists($foto)) {
                        Storage::disk('public')->delete($foto);
                    }
                }
            }
        }

        Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => count($items) . ' barang berhasil dihapus',
            'count' => count($items),
        ]);
    }
}


