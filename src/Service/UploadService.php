<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadService {


    public function __construct(SluggerInterface $slugger) {
        $this->slugger = $slugger;
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
            return $e->getMessage();
        }
        return $newFilename;
    }

}