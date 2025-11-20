<?php

namespace App\Http\Controllers;

use App\Models\ModerationHistory;
use App\Models\ReportItem;
use App\Models\ReportUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminReviewController extends Controller
{
    /**
     * Tampilkan halaman review untuk report item atau report user.
     */
    public function show(Request $request, string $type, int $id)
    {
        if ($type === 'item') {
            $report = ReportItem::with(['reporter', 'reviewer', 'moderationHistory.moderator'])
                ->findOrFail($id);
        } elseif ($type === 'user') {
            $report = ReportUser::with(['reporter', 'reviewer', 'moderationHistory.moderator'])
                ->findOrFail($id);
        } else {
            abort(404);
        }

        // Format data untuk view
        $reportData = [
            'id' => $report->id,
            'reportId' => $type === 'item' ? 'RPT-ITEM-' . str_pad($report->id, 4, '0', STR_PAD_LEFT) : 'RPT-USER-' . str_pad($report->id, 4, '0', STR_PAD_LEFT),
            'date' => $report->created_at->toISOString(),
            'status' => $this->mapStatus($report->status),
            'reporter' => [
                'nama' => $report->reporter ? $report->reporter->organization_name : 'Anonim',
                'id' => $report->reporter_id ?? null,
            ],
            'category' => $this->mapCategory($report->kategori),
            'description' => $report->deskripsi,
            'evidence' => $this->formatEvidence($report->bukti_paths ?? []),
            'moderationHistory' => $report->moderationHistory->map(function ($history) {
                return [
                    'id' => $history->id,
                    'moderator' => [
                        'nama' => $history->moderator ? $history->moderator->organization_name : 'Admin',
                        'avatar' => null,
                    ],
                    'action' => $history->action,
                    'reason' => $history->reason,
                    'timestamp' => $history->created_at->toISOString(),
                    'status' => $history->status,
                ];
            })->toArray(),
        ];

        // Add item-specific or user-specific data
        if ($type === 'item') {
            $reportData['item'] = [
                'id' => $report->item_id,
                'title' => $report->item_title,
                'owner' => [
                    'nama' => 'Pemilik Barang', // In real app, get from items table
                    'id' => null,
                ],
                'description' => 'Deskripsi barang...', // In real app, get from items table
                'photos' => [], // In real app, get from items table
                'status' => 'active', // In real app, get from items table
                'postedAt' => $report->created_at->toISOString(),
            ];
        } else {
            $reportData['targetUser'] = [
                'nama' => $report->target_user_name ?? $report->target_user,
                'id' => $report->target_user,
            ];
        }

        return view('admin.admin-review', [
            'report' => $reportData,
            'reportType' => $type,
            'reportModel' => $report,
        ]);
    }

    public function redirectToFirst(Request $request)
    {
        $firstItem = ReportItem::where('status', 'pending')->orderByDesc('created_at')->first();
        if ($firstItem) {
            return redirect()->route('admin.review.show', ['type' => 'item', 'id' => $firstItem->id]);
        }

        $firstUser = ReportUser::where('status', 'pending')->orderByDesc('created_at')->first();
        if ($firstUser) {
            return redirect()->route('admin.review.show', ['type' => 'user', 'id' => $firstUser->id]);
        }

        return redirect()->route('admin.mengelola-data.index')->with('status', 'Tidak ada laporan untuk ditinjau.');
    }

    /**
     * Simpan keputusan admin untuk report.
     */
    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $validated = $request->validate(
            [
                'decision' => ['required', 'in:reject,accept'],
                'reject_reason' => ['required_if:decision,reject', 'string', 'min:10'],
                'action' => ['required_if:decision,accept', 'in:delete,suspend,warn,no-action'],
                'action_note' => ['nullable', 'string', 'max:1000'],
            ],
            [],
            [
                'decision' => 'keputusan',
                'reject_reason' => 'alasan penolakan',
                'action' => 'tindakan',
                'action_note' => 'catatan tambahan',
            ]
        );

        if ($type === 'item') {
            $report = ReportItem::findOrFail($id);
        } elseif ($type === 'user') {
            $report = ReportUser::findOrFail($id);
        } else {
            abort(404);
        }

        $adminId = $request->session()->get('organization_id');
        if (!$adminId) {
            throw ValidationException::withMessages([
                'decision' => 'Anda harus login sebagai admin untuk melakukan moderasi.',
            ]);
        }

        // Update report
        $report->decision = $validated['decision'];
        $report->reject_reason = $validated['reject_reason'] ?? null;
        $report->action = $validated['action'] ?? null;
        $report->action_note = $validated['action_note'] ?? null;
        $report->status = $validated['decision'] === 'reject' ? 'ditolak' : 'diterima';
        $report->reviewed_at = now();
        $report->reviewed_by = $adminId;
        $report->save();

        // Create moderation history entry
        $actionText = $this->getActionText($validated['decision'], $validated['action'] ?? null);
        ModerationHistory::create([
            'reportable_type' => get_class($report),
            'reportable_id' => $report->id,
            'moderator_id' => $adminId,
            'action' => $actionText,
            'reason' => $validated['reject_reason'] ?? $validated['action_note'] ?? null,
            'status' => $validated['decision'] === 'accept' ? 'approved' : 'rejected',
        ]);

        // TODO: Implement actual actions (delete item, suspend user, etc.)
        if ($validated['decision'] === 'accept' && $validated['action']) {
            $this->performAction($type, $report, $validated['action']);
        }

        return redirect()
            ->back()
            ->with('status', 'Keputusan moderasi berhasil disimpan.');
    }

    /**
     * Map status to frontend format.
     */
    private function mapStatus(string $status): string
    {
        return match ($status) {
            'diterima' => 'resolved',
            'ditolak' => 'rejected',
            'pending' => 'pending',
            default => 'pending',
        };
    }

    /**
     * Map category to display name.
     */
    private function mapCategory(string $kategori): string
    {
        return match ($kategori) {
            'konten-tidak-pantas' => 'Konten Tidak Pantas',
            'barang-palsu' => 'Barang Palsu',
            'spam' => 'Spam',
            'salah-kategori' => 'Salah Kategori',
            'menyesatkan' => 'Menyesatkan',
            'penipuan' => 'Penipuan',
            'pelecehan' => 'Pelecehan',
            'akun-palsu' => 'Akun Palsu',
            default => 'Lainnya',
        };
    }

    /**
     * Format evidence paths to evidence array.
     */
    private function formatEvidence(array $paths): array
    {
        return collect($paths)->map(function ($path, $index) {
            $isImage = in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif']);
            return [
                'id' => 'ev' . ($index + 1),
                'type' => $isImage ? 'image' : 'pdf',
                'url' => asset('storage/' . $path),
                'filename' => basename($path),
                'uploadedAt' => now()->toISOString(),
            ];
        })->toArray();
    }

    /**
     * Get action text for moderation history.
     */
    private function getActionText(string $decision, ?string $action): string
    {
        if ($decision === 'reject') {
            return 'Laporan ditolak';
        }

        return match ($action) {
            'delete' => 'Postingan dihapus',
            'suspend' => 'Postingan ditangguhkan',
            'warn' => 'Peringatan diberikan',
            'no-action' => 'Tidak ada tindakan lanjut',
            default => 'Laporan diterima',
        };
    }

    /**
     * Perform the actual action (delete, suspend, etc.).
     */
    private function performAction(string $type, $report, string $action): void
    {
        // TODO: Implement actual actions
        // For example:
        // - If delete: mark item/user as deleted
        // - If suspend: mark item/user as suspended
        // - If warn: send warning email/notification
        // - If no-action: do nothing
    }
}
