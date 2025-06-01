<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PDFOpsController;
use App\Http\Controllers\ConvertToPDFController;
use App\Http\Controllers\PdfLockUnlockController;
use App\Http\Controllers\ConvertFromPDFController;

Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/tools', 'tools')->name('tools');
    Route::get('/about', 'about')->name('about');
    Route::get('/help-center', 'helpCenter')->name('help_center');
});

Route::controller(ConvertToPDFController::class)->group(function () {
    Route::get('/convert/word-to-pdf', 'wordToPdf')->name('convert.word.form');
    Route::post('/convert/word-to-pdf', 'convertWordToPdf')->name('convert.word.to.pdf');

    Route::get('/convert/excel-to-pdf', 'excelToPdf')->name('convert.excel.form');
    Route::post('/convert/excel-to-pdf', 'convertExcelToPdf')->name('convert.excel.to.pdf');

    Route::get('/convert/ppt-to-pdf', 'pptToPdf')->name('convert.ppt.form');
    Route::post('/convert/ppt-to-pdf', 'convertPptToPdf')->name('convert.ppt.to.pdf');

    Route::get('/convert/image-to-pdf', 'imageToPdf')->name('convert.image.form');
    Route::post('/convert/image-to-pdf', 'convertImageToPdf')->name('convert.image.to.pdf');
});

Route::controller(ConvertFromPDFController::class)->group(function () {
    Route::get('/convert/pdf-to-png', 'pdfToPng')->name('convert.pdf.png.form');
    Route::post('/convert/pdf-to-png', 'convertPdfToPng')->name('convert.pdf.to.png');

    Route::get('/convert/pdf-to-jpg', 'pdfToJpg')->name('convert.pdf.jpg.form');
    Route::post('/convert/pdf-to-jpg', 'convertPdfToJpg')->name('convert.pdf.to.jpg');

    Route::get('/convert/pdf-to-word', 'pdfToWord')->name('convert.pdf.word.form');
    Route::post('/convert/pdf-to-word', 'convertPdfToWord')->name('convert.pdf.to.word');

    Route::get('/convert/pdf-to-excel', 'pdfToExcel')->name('convert.pdf.excel.form');
    Route::post('/convert/pdf-to-excel', 'convertPdfToExcel')->name('convert.pdf.to.excel');
});

Route::controller(PdfLockUnlockController::class)->group(function () {
    Route::get('/security/lock', 'showLockForm')->name('lock_pdf');
    Route::post('/security/lock', 'lockPdf')->name('pdf.lock');

    Route::get('/security/unlock', 'showUnlockForm')->name('unlock_pdf');
    Route::post('/security/unlock', 'unlockPdf')->name('pdf.unlock');
});

Route::controller(PDFOpsController::class)->group(function () {
    Route::get('/pdf/merge', 'mergeForm')->name('pdf.merge.form');
    Route::post('/pdf/merge', 'merge')->name('pdf.merge');

    Route::get('/pdf/split', 'splitForm')->name('pdf.split.form');
    Route::post('/pdf/split', 'split')->name('pdf.split');
});