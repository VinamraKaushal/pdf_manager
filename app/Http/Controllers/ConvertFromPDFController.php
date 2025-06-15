<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use App\Services\GuestCreditService;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf;

class ConvertFromPDFController extends Controller {
    protected $creditService;

    public function __construct(GuestCreditService $creditService) {
        $this->creditService = $creditService;
    }
    public function pdfToPng() {
        return view("convert_from_pdf.pdf_to_png");
    }

    public function convertPdfToPng(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
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

        $converted = [];

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();
            $folder = 'pdf_to_png';

            $pdfPath = $pdfFile->storeAs("converted_pdfs/{$folder}", "$filename.pdf", 'public');
            $fullPdfPath = storage_path("app/public/$pdfPath");

            $this->ensureDirectoryExists(storage_path("app/public/converted_pdfs/{$folder}"));

            $pdf = new Pdf($fullPdfPath);
            $pdf->setOutputFormat('png');

            $imageFileName = "$filename.png";
            $outputImagePath = "converted_pdfs/{$folder}/$imageFileName";
            $pdf->saveImage(storage_path("app/public/$outputImagePath"));

            $converted[] = [
                'original_name' => $pdfFile->getClientOriginalName(),
                'file_name' => $imageFileName,
            ];
        }

        session()->flash('converted_pdf_to_png', $converted);
        return back()->with('success', 'PDFs converted to PNG successfully.');
    }

    public function pdfToJpg() {
        return view("convert_from_pdf.pdf_to_jpg");
    }

    public function convertPdfToJpg(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
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

        $converted = [];

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();
            $folder = 'pdf_to_jpg';

            $pdfPath = $pdfFile->storeAs("converted_pdfs/{$folder}", "$filename.pdf", 'public');
            $fullPdfPath = storage_path("app/public/$pdfPath");

            $this->ensureDirectoryExists(storage_path("app/public/converted_pdfs/{$folder}"));

            $pdf = new Pdf($fullPdfPath);
            $pdf->setOutputFormat('jpg');

            $imageFileName = "$filename.jpg";
            $outputImagePath = "converted_pdfs/{$folder}/$imageFileName";
            $pdf->saveImage(storage_path("app/public/$outputImagePath"));

            $converted[] = [
                'original_name' => $pdfFile->getClientOriginalName(),
                'file_name' => $imageFileName,
            ];
        }

        session()->flash('converted_pdf_to_jpg', $converted);
        return back()->with('success', 'PDFs converted to JPG successfully.');
    }

    public function pdfToWord() {
        return view("convert_from_pdf.pdf_to_word");
    }

    public function convertPdfToWord(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
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

        $converted = [];
        $parser = new Parser();

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();
            $folder = 'pdf_to_word';

            $pdfPath = $pdfFile->storeAs("converted_pdfs/{$folder}", "$filename.pdf", 'public');
            $fullPdfPath = storage_path("app/public/$pdfPath");

            $this->ensureDirectoryExists(storage_path("app/public/converted_pdfs/{$folder}"));

            $pdf = $parser->parseFile($fullPdfPath);
            $text = $pdf->getText();

            $phpWord = new PhpWord();
            $section = $phpWord->addSection();

            $lines = preg_split('/\r\n|\r|\n/', trim($text));
            foreach ($lines as $line) {
                $section->addText($line);
            }

            $wordFileName = "$filename.docx";
            $wordPath = "converted_pdfs/{$folder}/$wordFileName";
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save(storage_path("app/public/$wordPath"));

            $converted[] = [
                'original_name' => $pdfFile->getClientOriginalName(),
                'file_name' => $wordFileName,
            ];
        }

        session()->flash('converted_pdf_to_word', $converted);
        return back()->with('success', 'PDFs converted to Word with extracted text.');
    }

    public function pdfToExcel() {
        return view("convert_from_pdf.pdf_to_excel");
    }

    public function convertPdfToExcel(Request $request) {
        $request->validate([
            'pdfs' => 'required|array',
            'pdfs.*' => 'file|mimes:pdf|max:10000',
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

        $converted = [];
        $parser = new Parser();

        foreach ($request->file('pdfs') as $pdfFile) {
            $filename = Str::uuid()->toString();
            $folder = 'pdf_to_excel';

            $pdfPath = $pdfFile->storeAs("converted_pdfs/{$folder}", "$filename.pdf", 'public');
            $fullPdfPath = storage_path("app/public/$pdfPath");

            $this->ensureDirectoryExists(storage_path("app/public/converted_pdfs/{$folder}"));

            $pdf = $parser->parseFile($fullPdfPath);
            $text = $pdf->getText();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', $text);

            $excelFileName = "$filename.xlsx";
            $excelPath = "converted_pdfs/{$folder}/$excelFileName";
            $writer = new Xlsx($spreadsheet);
            $writer->save(storage_path("app/public/$excelPath"));

            $converted[] = [
                'original_name' => $pdfFile->getClientOriginalName(),
                'file_name' => $excelFileName,
            ];
        }

        session()->flash('converted_pdf_to_excel', $converted);
        return back()->with('success', 'PDFs converted to Excel with extracted text.');
    }

    private function ensureDirectoryExists(string $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
