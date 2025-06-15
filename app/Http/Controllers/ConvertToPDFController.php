<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory as ExcelIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\Settings as WordSettings;
use PhpOffice\PhpPresentation\IOFactory;
use App\Services\GuestCreditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Exception;

class ConvertToPDFController extends Controller {
    protected $creditService;

    public function __construct(GuestCreditService $creditService) {
        $this->creditService = $creditService;
    }
    public function wordToPdf() {
        return view("convert_to_pdf.doc_to_pdf");
    }

    public function excelToPdf() {
        return view("convert_to_pdf.excel_to_pdf");
    }

    public function pptToPdf() {
        return view("convert_to_pdf.ppt_to_pdf");
    }

    public function imageToPdf() {
        return view("convert_to_pdf.image_to_pdf");
    }

    public function convertWordToPdf(Request $request) {
        $request->validate([
            'documents' => 'required|array',
            'documents.*' => 'file|mimes:doc,docx,odt,txt|max:10240',
        ]);

        $pdfFiles = $request->file('documents');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'documents' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        return $this->processDocuments(
            $request->file('documents'),
            'converted_docs',
            function ($file, $ext, $path) {
                if (in_array($ext, ['doc', 'docx', 'odt'])) {
                    $phpWord = WordIOFactory::load($file->getRealPath(), 'Word2007');

                    WordSettings::setPdfRendererName(WordSettings::PDF_RENDERER_TCPDF);
                    WordSettings::setPdfRendererPath(base_path('vendor/tecnickcom/tcpdf'));

                    $pdfWriter = WordIOFactory::createWriter($phpWord, 'PDF');
                    $pdfWriter->save($path);
                } elseif ($ext === 'txt') {
                    $content = nl2br(e(file_get_contents($file->getRealPath())));
                    $html = "<html><body><pre style='font-family: monospace;'>{$content}</pre></body></html>";

                    $dompdf = new Dompdf();
                    $dompdf->loadHtml($html);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();

                    file_put_contents($path, $dompdf->output());
                }
            },
            'Word documents successfully converted to PDF!'
        );
    }

    public function convertExcelToPdf(Request $request) {
        $request->validate([
            'excels' => 'required|array',
            'excels.*' => 'file|mimes:xls,xlsx,csv|max:10240',
        ]);

        $pdfFiles = $request->file('excels');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'excels' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        return $this->processDocuments(
            $request->file('excels'),
            'converted_excels',
            function ($file, $ext, $path) {
                $spreadsheet = ExcelIOFactory::load($file->getRealPath());

                $htmlWriter = ExcelIOFactory::createWriter($spreadsheet, 'Html');

                ob_start();
                $htmlWriter->save('php://output');
                $html = ob_get_clean();

                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                file_put_contents($path, $dompdf->output());
            },
            'Excel files successfully converted to PDF!'
        );
    }

    public function convertPptToPdf(Request $request) {
        $request->validate([
            'presentations' => 'required|array',
            'presentations.*' => 'file|mimes:pptx|max:10240',
        ]);

        $pdfFiles = $request->file('presentations');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'presentations' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        return $this->processDocuments(
            $request->file('presentations'),
            'converted_ppts',
            function ($file, $ext, $path) {
                $presentation = IOFactory::load($file->getRealPath());

                $html = '<html><body style="font-family: Arial;">';

                foreach ($presentation->getAllSlides() as $index => $slide) {
                    $html .= "<div style='page-break-after: always;'>";
                    $html .= "<h2>Slide " . ($index + 1) . "</h2>";

                    foreach ($slide->getShapeCollection() as $shape) {
                        if (method_exists($shape, 'getText')) {
                            $text = $shape->getText();
                            $html .= "<p>" . nl2br(e($text)) . "</p>";
                        }
                    }

                    $html .= "</div>";
                }

                $html .= '</body></html>';

                $dompdf = new Dompdf();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                file_put_contents($path, $dompdf->output());
            },
            'PowerPoint files successfully converted to PDF!'
        );
    }

    public function convertImageToPdf(Request $request) {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|max:10240',
        ]);

        $pdfFiles = $request->file('images');
        $fileCount = count($pdfFiles);

        if (!Auth::check()) {
            if (!$this->creditService->hasEnoughCredits($fileCount)) {
                return back()->withErrors([
                    'images' => 'Insufficient credits. Please log in or try later.',
                ]);
            }

            $this->creditService->deductCredits($fileCount);
        }

        $html = "<html><body style='margin:0; padding:0;'>";

        foreach ($request->file('images') as $file) {
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            $mime = $file->getMimeType();

            $html .= "<div style='page-break-after: always; text-align: center;'>
                        <img src='data:{$mime};base64,{$imageData}' style='max-width:100%; max-height:100%;' />
                    </div>";
        }

        $html .= "</body></html>";

        $filename = 'images-' . time() . '-' . Str::random(5) . '.pdf';
        $folder = 'image';
        $path = storage_path("app/public/converted_pdfs/{$folder}/{$filename}");

        $this->ensureDirectoryExists(dirname($path));

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        file_put_contents($path, $dompdf->output());

        $converted = session()->get('converted_images', []);
        $converted[] = [
            'original_name' => 'Multiple Images',
            'file_name' => $filename,
            'folder' => $folder,
        ];
        session()->flash('converted_images', $converted);

        return back()->with('success', 'All images successfully combined and converted into a single PDF!');
    }

    private function processDocuments(array $files, string $sessionKey, callable $convertCallback, string $successMessage) {
        $converted = session()->get($sessionKey, []);

        $folderMap = [
            'converted_docs' => 'word',
            'converted_excels' => 'excel',
            'converted_ppts' => 'ppt',
        ];

        $subfolder = $folderMap[$sessionKey] ?? 'others';

        foreach ($files as $file) {
            try {
                $originalName = $file->getClientOriginalName();
                $filename = pathinfo($originalName, PATHINFO_FILENAME);
                $ext = strtolower($file->getClientOriginalExtension());

                $uniqueName = Str::slug($filename) . '-' . time() . '-' . Str::random(5) . '.pdf';

                $path = storage_path("app/public/converted_pdfs/{$subfolder}/{$uniqueName}");

                $this->ensureDirectoryExists(dirname($path));

                $convertCallback($file, $ext, $path);

                $converted[] = [
                    'original_name' => $originalName,
                    'file_name' => $uniqueName,
                    'folder' => $subfolder,
                ];
            } catch (Exception $e) {
                Log::error("Conversion failed for {$file->getClientOriginalName()}: " . $e->getMessage());
                continue;
            }
        }

        session()->flash($sessionKey, $converted);

        return back()->with('success', $successMessage);
    }

    private function ensureDirectoryExists(string $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
