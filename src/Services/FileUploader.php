<?php

namespace App\Services;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader {

    public function __construct(
        #[Autowire('%app.upload.activities%')] private string $activitiesDirectory,
        #[Autowire('%app.upload.profile%')] private string $profileDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function uploadImage(UploadedFile $file, $context): string {
        if ($context === "activity") {
            return $this->upload($file, $this->activitiesDirectory);
        }
        if ($context === "user") {
            return $this->upload($file, $this->profileDirectory);
        }

        throw new \InvalidArgumentException("Unknown upload context: $context");
    }

    private function upload(UploadedFile $file, string $directory): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();


        try {
            $file->move($directory, $newFilename);
        } catch (FileException $e) {
            throw new \Exception($e->getMessage());
        }

        return $newFilename;
    }
}
