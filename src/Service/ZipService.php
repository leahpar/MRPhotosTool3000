<?php


namespace App\Service;


use App\Entity\Shooting;
use ZipArchive;

class ZipService
{
    /**
     * ZipService constructor.
     */
    public function __construct(private readonly string $publicDirectory, private readonly string $shootingsDirectory)
    {
    }

    public function zip(Shooting $shooting, string $filename): string
    {
        $zipfile = $this->publicDirectory . '/media/zip/' . $filename;
        $dir = $this->shootingsDirectory . '/' . $shooting->getSlug();

        // Création répertoire zips si nécessaire
        if (!is_dir($this->publicDirectory . '/media/zip/')) {
            mkdir($this->publicDirectory . '/media/zip/');
        }

        // Création zip si pas déjà présent
        if (!file_exists($zipfile)) {
            $this->zip_dir($dir, $zipfile);
        }

        return $zipfile;
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

        $zip = new ZipArchive();
        $zip->open($zipfile, ZipArchive::CREATE);

        //$files = glob($dir . "/*.jp*g");
        $files = glob($dir . "/*.*");

        $zipdir = basename($zipfile, '.zip');

        foreach ($files as $file) {
            $filename = basename((string) $file);
            $zip->addFile($file, $zipdir."/".$filename);
        }

        $zip->close();
    }
}
