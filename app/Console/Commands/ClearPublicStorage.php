<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearPublicStorage extends Command {
    protected $signature = 'storage:clear';

    protected $description = 'Clear all files/folders inside storage/app/public except specified folders';

    protected $foldersToKeep = [
        'images',
        // add other folder names here
    ];

    public function handle() {
        $publicPath = storage_path('app/public');

        if (!File::exists($publicPath)) {
            $this->warn("Public storage folder does not exist: {$publicPath}");
            return;
        }

        // Delete files directly inside storage/app/public
        $files = File::files($publicPath);
        foreach ($files as $file) {
            if (File::delete($file)) {
                $this->info("Deleted file: {$file}");
            } else {
                $this->warn("Failed to delete file: {$file}");
            }
        }

        // Delete directories except those to keep
        $dirs = File::directories($publicPath);
        foreach ($dirs as $dir) {
            $folderName = basename($dir);

            if (in_array($folderName, $this->foldersToKeep)) {
                $this->info("Skipping folder: {$folderName}");
                continue;
            }

            if (File::deleteDirectory($dir)) {
                $this->info("Deleted folder: {$folderName}");
            } else {
                $this->warn("Failed to delete folder: {$folderName}");
            }
        }

        $this->info('Storage cleanup complete.');

        return;
    }
}
