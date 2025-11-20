<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UpdateStatusController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->session()->has('organization_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:items,id',
            'status' => 'required|in:tersedia,reserved,habis',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $organizationId = $request->session()->get('organization_id');

        $items = Item::where('organization_id', $organizationId)
            ->whereIn('id', $request->item_ids)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diubah', 'count' => $items]);
    }
}


