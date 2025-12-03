<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemRequestController extends Controller
{
    public function index(Request $request)
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
    /**
     * Display the application form for a given item.
     */
    public function create(Request $request, Item $item)
    {
        $authResult = $this->authorizeRequestor($request, $item);
        if ($authResult instanceof RedirectResponse) {
            return $authResult;
        }

        $organizationId = $authResult;

        $latestRequest = ItemRequest::where('item_id', $item->id)
            ->where('organization_id', $organizationId)
            ->latest()
            ->first();

        return view('permohonan.create', [
            'item' => $item->load('organization.profile'),
            'latestRequest' => $latestRequest,
        ]);
    }

    /**
     * Store a new application for an item.
     */
    public function store(Request $request, Item $item)
    {
        $authResult = $this->authorizeRequestor($request, $item);
        if ($authResult instanceof RedirectResponse) {
            return $authResult;
        }

        $organizationId = $authResult;
        $maxJumlah = max(1, (int) ($item->jumlah ?? 1));

        $validated = $request->validate(
            [
                'requested_quantity' => ['required', 'integer', 'min:1', 'max:' . $maxJumlah],
                'surat_kuasa' => [
                    'required',
                    File::types(['pdf', 'doc', 'docx'])->max(10240), // 10MB
                ],
            ],
            [],
            [
                'requested_quantity' => 'jumlah permohonan',
                'surat_kuasa' => 'surat permohonan hibah',
            ]
        );

        $hasPending = ItemRequest::where('item_id', $item->id)
            ->where('organization_id', $organizationId)
            ->whereIn('status', ['pending', 'review'])
            ->exists();

        if ($hasPending) {
            return redirect()
                ->route('items.show', $item->id)
                ->withErrors(['permohonan' => 'Anda sudah memiliki permohonan yang masih diproses untuk barang ini.']);
        }

        $suratPath = $request->file('surat_kuasa')->store('permohonan/surat-permohonan-hibah', 'public');

        ItemRequest::create([
            'item_id' => $item->id,
            'organization_id' => $organizationId,
            'requested_quantity' => $validated['requested_quantity'],
            'surat_kuasa_path' => $suratPath,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('items.show', $item->id)
            ->with('success', 'Permohonan barang berhasil dikirim. Kami akan menghubungi Anda melalui kontak profil setelah diproses.');
    }

    /**
     * Provide the official surat kuasa template for download.
     */
    public function downloadTemplate(): BinaryFileResponse
    {
        $templatePath = base_path('SuratPermohonanHibah.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template surat permohonan hibah belum tersedia.');
        }

        return response()->download($templatePath, 'SuratPermohonanHibah.docx');
    }

    /**
     * Ensure the requester is allowed to submit an application.
     */
    protected function authorizeRequestor(Request $request, Item $item)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        if ($item->is_draft) {
            return redirect()
                ->route('beranda')
                ->withErrors(['permohonan' => 'Barang belum dipublikasikan.']);
        }

        if ($request->session()->get('is_receiver') !== true) {
            return redirect()
                ->route('items.show', $item->id)
                ->withErrors(['permohonan' => 'Fitur ini hanya tersedia bagi organisasi penerima terverifikasi.']);
        }

        $organizationId = (int) $request->session()->get('organization_id');

        if ($item->organization_id === $organizationId) {
            return redirect()
                ->route('items.show', $item->id)
                ->withErrors(['permohonan' => 'Anda tidak dapat mengajukan permohonan untuk barang yang Anda bagikan sendiri.']);
        }

        if (!in_array($item->status, ['tersedia', null], true)) {
            return redirect()
                ->route('items.show', $item->id)
                ->withErrors(['permohonan' => 'Barang tidak tersedia untuk diajukan.']);
        }

        return $organizationId;
    }
}
