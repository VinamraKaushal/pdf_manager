<?php

namespace App\Http\Controllers;

use App\Services\GuestCreditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use setasign\Fpdi\TcpdfFpdi;
use Illuminate\Support\Str;

class PdfLockUnlockController extends Controller {
    protected $creditService;

    public function __construct(GuestCreditService $creditService) {
        $this->creditService = $creditService;
    }

    public function showLockForm() {
        return view("lock_unlock_pdf.lock_pdf");
    }

    public function showUnlockForm() {
        return view("lock_unlock_pdf.unlock_pdf");
    }

    public function lockPdf(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
            'pdf_password' => 'required|string|min:4',
        ]);

        $pdfFiles = $request->file('pdfs');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'pdfs' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        $password = $request->input('pdf_password');
        $converted = [];
        $folder = 'locked_pdfs';

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();

            $pdfPath = $pdfFile->storeAs("converted_pdfs/originals", "$filename.pdf", 'public');
            $fullPdfPath = storage_path("app/public/$pdfPath");

            $pdf = new TcpdfFpdi();

            $pageCount = $pdf->setSourceFile($fullPdfPath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }

            $pdf->SetProtection(['copy', 'print'], $password, $password);

            $lockedDir = storage_path("app/public/converted_pdfs/{$folder}");
            if (!is_dir($lockedDir)) {
                mkdir($lockedDir, 0755, true);
            }

            $outputPath = $lockedDir . "/locked_$filename.pdf";

            $pdf->Output($outputPath, 'F');

            $converted[] = [
                'original_name' => $pdfFile->getClientOriginalName(),
                'file_name' => "locked_$filename.pdf",
            ];
        }

        session()->flash('locked_pdfs', $converted);
        return back()->with('success', 'PDF(s) locked with password successfully.');
    }

    // In Progress

    public function unlockPdf(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
            'pdf_password' => 'required|string|min:4',
        ]);

        $pdfFiles = $request->file('pdfs');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'pdfs' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        $password = $request->input('pdf_password');
        $converted = [];
        $folder = 'unlocked_pdfs';

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();

            // Store uploaded encrypted PDF temporarily
            $originalPath = $pdfFile->storeAs("converted_pdfs/$folder", "$filename.pdf", 'public');
            $fullPath = storage_path("app/public/$originalPath");

            try {
                $pdf = new TcpdfFpdi();

                // Must set password before loading the file
                $pdf->setPdfPassword($password);

                // Load encrypted PDF
                $pageCount = $pdf->setSourceFile($fullPath);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($tplId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);
                }

                // Output unlocked PDF to storage
                $outputPath = storage_path("app/public/converted_pdfs/{$folder}/unlocked_$filename.pdf");
                $pdf->Output($outputPath, 'F');

                $converted[] = [
                    'original_name' => $pdfFile->getClientOriginalName(),
                    'file_name' => "unlocked_$filename.pdf",
                ];
            } catch (\Exception $e) {
                return back()->withErrors([
                    'pdfs' => "Failed to unlock PDF: " . $e->getMessage(),
                ]);
            }
        }

        session()->flash('unlocked_pdfs', $converted);
        return back()->with('success', 'PDF(s) unlocked successfully.');
    }
}
