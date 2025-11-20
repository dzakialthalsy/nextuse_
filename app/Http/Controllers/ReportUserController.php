<?php

namespace App\Http\Controllers;

use App\Models\ReportUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportUserController extends Controller
{
    /**
     * Tampilkan halaman form report user.
     */
    public function create(Request $request)
    {
        $targetUser = $request->query('user', '@janedoe');
        $targetUserName = $request->query('name', 'Jane Doe');

        return view('report-user', [
            'targetUser' => $targetUser,
            'targetUserName' => $targetUserName,
        ]);
    }

    /**
     * Simpan report user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'target_user' => ['required', 'string', 'max:255'],
                'target_user_name' => ['nullable', 'string', 'max:255'],
                'kategori' => ['required', 'in:penipuan,spam,pelecehan,akun-palsu,lainnya'],
                'deskripsi' => ['required', 'string', 'min:20'],
                'bukti' => ['nullable', 'array'],
                'bukti.*' => ['file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            ],
            [],
            [
                'target_user' => 'pengguna yang dilaporkan',
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

        // Check for duplicate report (same reporter, same target, same category, pending status)
        $duplicateReport = ReportUser::where('reporter_id', $reporterId)
            ->where('target_user', $validated['target_user'])
            ->where('kategori', $validated['kategori'])
            ->where('status', 'pending')
            ->first();

        if ($duplicateReport) {
            return back()
                ->withInput()
                ->with('duplicate_warning', true)
                ->with('duplicate_report_id', $duplicateReport->id);
        }

        ReportUser::create([
            'reporter_id' => $reporterId,
            'target_user' => $validated['target_user'],
            'target_user_name' => $validated['target_user_name'] ?? null,
            'kategori' => $validated['kategori'],
            'deskripsi' => $validated['deskripsi'],
            'bukti_paths' => !empty($buktiPaths) ? $buktiPaths : null,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('beranda')
            ->with('status', 'Report pengguna berhasil dikirim. Tim kami akan meninjau laporan Anda segera.');
    }
}
