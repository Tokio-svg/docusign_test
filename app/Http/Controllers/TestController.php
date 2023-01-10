<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    // -----------------------------------------------
    // ログファイルダウンロード
    // -----------------------------------------------
    public function getLogs() {
        $log_disk = Storage::disk('logs');
        $localFiles = $log_disk->allFiles();

        $download_file_name = 'logs.zip';
        $file_path = storage_path('app/' . $download_file_name);
        $zip = new ZipArchive();
        $isOpen = $zip->open($file_path, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($isOpen === true) {

            foreach ($localFiles as $file) {
                if (!$file) {
                    Log::warning('fail get log');
                    continue;
                }
                $log_file = new UploadedFile(storage_path("logs/" . $file), $file);
                $file_ext = $log_file->getClientOriginalExtension();
                if ($file_ext === 'log') {
                    $file_name = $log_file->getClientOriginalName();
                    // $log_disk->delete($file);
                    // $zip->addFromString($file_name, $log_file);
                    $zip->addFile($log_file, $file_name);
                }
            }

            $zip->close();
            ob_end_clean();
        }
        $headers = [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $download_file_name . '"'
        ];
        return response()
            ->download($file_path, $download_file_name, $headers)
            ->deleteFileAfterSend(true);
    }
}
