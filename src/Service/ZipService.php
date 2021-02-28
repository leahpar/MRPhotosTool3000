<?php


namespace App\Service;


use App\Entity\Shooting;
use ZipArchive;

class ZipService
{
    private string $publicDirectory;
    private string $shootingsDirectory;


    /**
     * ZipService constructor.
     * @param string $public_directory
     * @param string $shootings_directory
     */
    public function __construct(string $public_directory, string $shootings_directory)
    {
        $this->publicDirectory = $public_directory;
        $this->shootingsDirectory = $shootings_directory;
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
            $filename = basename($file);
            $zip->addFile($file, $zipdir."/".$filename);
        }

        $zip->close();
    }
}
