<?php


namespace App\Service;


use App\Entity\Shooting;
use ZipArchive;

class ZipService
{

    public function zip(Shooting $shooting)
    {

    }


    /**
     * Zip images in a directori
     * @param string $dir       input directory
     * @param string $zipfile   output zip
     */
    private function zip_dir(string $dir, string $zipfile)
    {
        if (!is_dir($dir)) {
            return;
        }

        if (!file_exists($zipfile)) {
            $zip = new ZipArchive();
            $zip->open($zipfile, ZipArchive::CREATE);

            $files = glob($dir . "/*.jp*g");

            $zipdir = basename($zipfile, '.zip');

            foreach ($files as $file) {
                $filename = basename($file);
                $zip->addFile($file, $zipdir."/".$filename);
            }

            $zip->close();
        }
    }
}
