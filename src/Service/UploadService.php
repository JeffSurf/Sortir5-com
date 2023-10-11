<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService {

    private LoggerInterface $logger;
    private SluggerInterface $slugger;

    public function __construct(private Filesystem $filesystem, SluggerInterface $slugger, LoggerInterface $logger) {
        $this->slugger = $slugger;
        $this->logger =$logger;
        $this->filesystem = $filesystem;
    }

    public function uploadFile($file, $directory): string {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
            $this->logger->error("Problème dans le téléchargement du fichier, " . $e->getMessage());
        }
        return $newFilename;
    }

    public function delete(?string $filename, string $directory): void
    {
        if(!$filename)
            return;

        if($this->filesystem->exists($directory . '/' . $filename))
            $this->filesystem->remove($directory . '/' . $filename);
    }

}