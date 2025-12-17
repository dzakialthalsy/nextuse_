<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class MendaftarkanAkunOrganisasiController extends Controller
{
    /**
     * Tampilkan halaman registrasi organisasi.
     * Use Case: Mendaftarkan akun organisasi (Pengunjung)
     */
    public function index(Request $request)
    {
        if ($request->session()->has('organization_id')) {
            return redirect()->route('beranda');
        }
        return view('registrasi');
    }

    /**
     * Proses pendaftaran organisasi baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(
            [
                'organizationName' => ['required', 'string', 'max:255'],
                'organizationType' => ['required', 'in:yayasan,kampus,sekolah,pemerintah,komunitas,perusahaan-sosial,lainnya'],
                'organizationId' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'email:rfc,dns', 'max:255', 'unique:organizations,email'],
                'phone' => ['required', 'string', 'max:30'],
                'contactPerson' => ['required', 'string', 'max:255'],
                'role' => ['required', 'in:donor,receiver'],
                'password' => ['required', 'string', 'min:8'],
                'confirmPassword' => ['required', 'same:password'],
                'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                'agreement' => ['accepted'],
            ],
            [],
            [
                'organizationName' => 'nama organisasi',
                'organizationType' => 'jenis organisasi',
                'organizationId' => 'nomor induk organisasi',
                'email' => 'email organisasi',
                'phone' => 'nomor telepon',
                'contactPerson' => 'penanggung jawab',
                'role' => 'peran',
                'password' => 'password',
                'confirmPassword' => 'konfirmasi password',
                'document' => 'dokumen organisasi',
                'agreement' => 'persetujuan syarat dan ketentuan',
            ]
        );
        $role = $validated['role'];
        $isDonor = $role === 'donor';
        $isReceiver = $role === 'receiver';

        $documentPath = null;

        try {
            // Karena document required, pastikan file ada dan valid
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                
                // Pastikan file valid dan tidak ada error saat upload
                if (!$file->isValid()) {
                    $errorMessage = 'File dokumen tidak valid atau rusak.';
                    $errorCode = $file->getError();
                    
                    // Berikan pesan error yang lebih spesifik berdasarkan error code
                    if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                        $errorMessage = 'Ukuran file terlalu besar. Maksimal 5MB.';
                    } elseif ($errorCode === UPLOAD_ERR_PARTIAL) {
                        $errorMessage = 'File hanya terunggah sebagian. Silakan coba lagi.';
                    } elseif ($errorCode === UPLOAD_ERR_NO_FILE) {
                        $errorMessage = 'Tidak ada file yang diunggah.';
                    } elseif ($errorCode === UPLOAD_ERR_NO_TMP_DIR) {
                        $errorMessage = 'Folder temporary tidak ditemukan. Hubungi administrator.';
                    } elseif ($errorCode === UPLOAD_ERR_CANT_WRITE) {
                        $errorMessage = 'Gagal menulis file ke disk. Hubungi administrator.';
                    } elseif ($errorCode === UPLOAD_ERR_EXTENSION) {
                        $errorMessage = 'Upload dihentikan oleh ekstensi PHP.';
                    }
                    
                    return back()
                        ->withErrors(['document' => $errorMessage])
                        ->withInput();
                }
                
                // Pastikan file tidak kosong
                $fileSize = $file->getSize();
                if ($fileSize === false || $fileSize <= 0) {
                    return back()
                        ->withErrors(['document' => 'File dokumen tidak boleh kosong.'])
                        ->withInput();
                }
                
                // Pastikan file memiliki nama yang valid
                $originalName = $file->getClientOriginalName();
                if (empty($originalName)) {
                    return back()
                        ->withErrors(['document' => 'File dokumen harus memiliki nama yang valid.'])
                        ->withInput();
                }
                
                // Pastikan file memiliki path temporary yang valid
                $realPath = $file->getRealPath();
                if (empty($realPath) || !file_exists($realPath)) {
                    return back()
                        ->withErrors(['document' => 'File dokumen tidak dapat diakses. Silakan coba lagi.'])
                        ->withInput();
                }
                
                // Pastikan direktori storage ada
                $storagePath = storage_path('C:/laragon/www/NextUse/storage/app/public/organization-documents');
                if (!is_dir($storagePath)) {
                    Storage::disk('public')->makeDirectory('organization-documents');
                }
                
                // Simpan file
                $documentPath = $file->store('organization-documents', 'public');
                
                // Pastikan file berhasil disimpan
                if (empty($documentPath) || !Storage::disk('public')->exists($documentPath)) {
                    return back()
                        ->withErrors(['document' => 'Gagal menyimpan file dokumen. Silakan coba lagi.'])
                        ->withInput();
                }
            } else {
                // Jika document required tapi tidak ada file, kembalikan error
                return back()
                    ->withErrors(['document' => 'File dokumen wajib diunggah.'])
                    ->withInput();
            }

            Organization::create([
                'organization_name' => $validated['organizationName'],
                'organization_type' => $validated['organizationType'],
                'organization_id' => $validated['organizationId'] ?? null,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'contact_person' => $validated['contactPerson'],
                'password' => $validated['password'],
                'document_path' => $documentPath,
                // Untuk MVP, akun langsung aktif dan siap login.
                'is_active' => true,
                'is_donor' => $isDonor,
                'is_receiver' => $isReceiver,
            ]);
        } catch (Throwable $th) {
            if ($documentPath) {
                Storage::disk('public')->delete($documentPath);
            }

            throw $th;
        }

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil, silakan masuk menggunakan kredensial organisasi Anda.');
    }
}
