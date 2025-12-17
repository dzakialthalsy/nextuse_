<?php

namespace App\Http\Controllers\Pemohon;

use Illuminate\Routing\Controller;
use App\Models\ItemRequest;
use Illuminate\Http\Request;

class MelihatPermohonanYangDiajukanController extends Controller
{
    /**
     * Use Case: Melihat permohonan yang diajukan (Pemohon)
     */
    public function __invoke(Request $request)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        $organizationId = (int) $request->session()->get('organization_id');

        $requests = ItemRequest::with(['item:id,judul,lokasi,jumlah,status,organization_id'])
            ->where('organization_id', $organizationId)
            ->latest()
            ->paginate(10);

        return view('permohonan.index', [
            'requests' => $requests,
        ]);
    }
}
