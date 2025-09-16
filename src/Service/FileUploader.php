<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
final class FileUploader
{
    public function __construct(private readonly string $targetDirectory) {}

    public function upload(UploadedFile $uploadedFile): string{
        $filename = uniqid() . '.' . $uploadedFile->guessExtension();
        try{
            $uploadedFile->move($this->targetDirectory, $filename);

        }catch (FileException $e){
            throw new FileException("Error uploading file");
        }
        return $filename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function delete(? string $filename, string $rep): void{
        if(null != $filename){
            if(file_exists($rep.'/'.$filename)){
                unlink($rep.'/'.$filename);
            }
        }
    }
}