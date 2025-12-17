<?php

namespace App\Http\Controllers\Pemohon;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MengunduhTemplateSuratController extends Controller
{
    /**
     * Provide the official surat kuasa template for download.
     * Use Case: Mengunduh template surat (Pemohon)
     */
    public function __invoke(): BinaryFileResponse
    {
        $templatePath = base_path('SuratPermohonanHibah.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'Template surat permohonan hibah belum tersedia.');
        }

        return response()->download($templatePath, 'SuratPermohonanHibah.docx');
    }
}
