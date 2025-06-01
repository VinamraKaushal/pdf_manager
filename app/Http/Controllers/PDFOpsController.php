<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use ZipArchive;

class PDFOpsController extends Controller {
    protected $baseStoragePath = 'converted_pdfs';

    public function mergeForm() {
        return view("pdf_ops.merge_pdf");
    }

    public function merge(Request $request) {
        $request->validate([
            'pdfs' => 'required|array|min:2',
            'pdfs.*' => 'required|file|mimes:pdf|max:10240',
        ]);

        $mergedFileName = 'merged_' . time() . '.pdf';
        $subfolder = 'merged';
        $dirPath = storage_path("app/public/{$this->baseStoragePath}/{$subfolder}");

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        $pdf = new Fpdi();

        foreach ($request->file('pdfs') as $uploadedFile) {
            $pageCount = $pdf->setSourceFile($uploadedFile->getPathname());

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }
        }

        $fullPath = "{$dirPath}/{$mergedFileName}";
        file_put_contents($fullPath, $pdf->Output('S'));

        session()->flash('merged_pdfs', [[
            'original_name' => 'Merged PDF',
            'file_name' => $mergedFileName,
            'path' => "{$this->baseStoragePath}/{$subfolder}/{$mergedFileName}",
        ]]);

        return redirect()->back()->with('success', __('PDF files merged successfully.'));
    }

    public function splitForm() {
        return view("pdf_ops.split_pdf");
    }

    public function split(Request $request) {
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240',
        ]);

        $uploadedFile = $request->file('pdf');
        $sourceFilePath = $uploadedFile->getPathname();
        $pageCount = (new Fpdi())->setSourceFile($sourceFilePath);

        $savedFiles = [];
        $subfolder = 'split';
        $dirPath = storage_path("app/public/{$this->baseStoragePath}/{$subfolder}");

        if (!File::exists($dirPath)) {
            File::makeDirectory($dirPath, 0755, true);
        }

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $pdfSingle = new Fpdi();
            $pdfSingle->setSourceFile($sourceFilePath);
            $tplIdx = $pdfSingle->importPage($pageNo);
            $size = $pdfSingle->getTemplateSize($tplIdx);

            $pdfSingle->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdfSingle->useTemplate($tplIdx);

            $fileName = 'split_' . time() . "_page_{$pageNo}.pdf";
            $fullPath = "{$dirPath}/{$fileName}";

            file_put_contents($fullPath, $pdfSingle->Output('S'));
            $savedFiles[] = $fullPath;
        }

        // Create ZIP file
        $zipName = 'split_pages_' . time() . '.zip';
        $zipPath = "{$dirPath}/{$zipName}";
        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
            foreach ($savedFiles as $file) {
                if (file_exists($file)) {
                    $zip->addFile($file, basename($file));
                }
            }
            $zip->close();
        }

        session()->flash('split_pdfs', [[
            'original_name' => $uploadedFile->getClientOriginalName(),
            'file_name' => $zipName,
            'path' => "{$this->baseStoragePath}/{$subfolder}/{$zipName}",
            'type' => 'zip',
        ]]);

        return redirect()->back()->with('success', __('PDF split into individual pages and zipped successfully.'));
    }

    public function download(Request $request, $type, $filename) {
        $sessionKey = "{$type}_pdfs";
        $pdfList = session($sessionKey, []);

        $pdfInfo = collect($pdfList)->firstWhere('file_name', $filename);

        if (!$pdfInfo || !file_exists(storage_path("app/public/{$pdfInfo['path']}"))) {
            abort(404);
        }

        // Remove from session
        $remaining = array_filter($pdfList, fn($f) => $f['file_name'] !== $filename);
        if (!empty($remaining)) {
            session()->flash($sessionKey, $remaining);
        }

        return response()->download(storage_path("app/public/{$pdfInfo['path']}"), $pdfInfo['file_name']);
    }
}
