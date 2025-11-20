<?php

namespace App\Http\Controllers;

use App\Models\ReportItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportItemController extends Controller
{
    /**
     * Tampilkan halaman form report item.
     */
    public function create(Request $request)
    {
        $itemId = $request->query('item_id', '#12345');
        $itemTitle = $request->query('title', 'Kamera Digital Canon EOS 700D - Kondisi Mulus');

        return view('report-item', [
            'itemId' => $itemId,
            'itemTitle' => $itemTitle,
        ]);
    }

    /**
     * Simpan report item baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'item_id' => ['required', 'string', 'max:255'],
                'item_title' => ['required', 'string', 'max:255'],
                'kategori' => ['required', 'in:konten-tidak-pantas,barang-palsu,spam,salah-kategori,menyesatkan,lainnya'],
                'deskripsi' => ['required', 'string', 'min:20'],
                'bukti' => ['nullable', 'array'],
                'bukti.*' => ['file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            ],
            [],
            [
                'item_id' => 'ID postingan',
                'item_title' => 'judul postingan',
                'kategori' => 'kategori pelanggaran',
                'deskripsi' => 'deskripsi',
                'bukti' => 'bukti',
            ]
        );

        // Get reporter ID from session (if logged in as organization)
        $reporterId = $request->session()->get('organization_id');

        // Handle file uploads
        $buktiPaths = [];
        if ($request->hasFile('bukti')) {
            $files = $request->file('bukti');
            
            // Ensure storage directory exists
            $storagePath = storage_path('app/public/report-evidence');
            if (!is_dir($storagePath)) {
                Storage::disk('public')->makeDirectory('report-evidence');
            }

            foreach ($files as $file) {
                if ($file->isValid()) {
                    $path = $file->store('report-evidence', 'public');
                    if ($path) {
                        $buktiPaths[] = $path;
                    }
                }
            }
        }

        // Check for duplicate report (same reporter, same item, same category, pending status)
        $duplicateReport = ReportItem::where('reporter_id', $reporterId)
            ->where('item_id', $validated['item_id'])
            ->where('kategori', $validated['kategori'])
            ->where('status', 'pending')
            ->first();

        if ($duplicateReport) {
            return back()
                ->withInput()
                ->with('duplicate_warning', true)
                ->with('duplicate_report_id', $duplicateReport->id);
        }

        // Check if item exists (in real app, check items table)
        // For now, we'll assume item exists

        ReportItem::create([
            'reporter_id' => $reporterId,
            'item_id' => $validated['item_id'],
            'item_title' => $validated['item_title'],
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'bukti_paths' => !empty($buktiPaths) ? $buktiPaths : null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('beranda')
            ->with('status', 'Report postingan berhasil dikirim. Tim kami akan meninjau laporan Anda segera.');
    }
}
