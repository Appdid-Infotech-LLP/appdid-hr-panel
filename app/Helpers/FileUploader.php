<?php

namespace App\Helpers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class FileUploader
{

    public static function uploadFile($file, string $path = "images", string $initials = "img"): string
    {
        if (isset($delete_path)) {
            Storage::disk('s3')->delete(substr($delete_path, 48));
        }
        // dd($file);
        $file_path = Storage::disk('s3')
            ->putFile('appdidcrm/' . $path ?? 'images/', $file, 'public', $initials . '-' . rand(1, 1000) . sha1(time()) . "." . $file
                ->getClientOriginalExtension());
        // dd($file_path);

        return Storage::disk('s3')->url($file_path);
    }
    // public static function uploadFile($file, string $path = "images", string $initials = "img"): string
    // {
    //     try {
    //         $destinationPath =  $path;
    //         $req_file = $initials . '-' . rand(1, 1000) . sha1(time()) . "." . $file->getClientOriginalExtension();
    //         $file = $file->move($destinationPath, $req_file);
    //         return  str_replace('\\', '/', $file);
    //     } catch (Exception  $e) {
    //         \Log::info('Exception in image upload : ' . $e->getMessage());
    //         throw new Exception("Failed to upload image. error : " . $e->getMessage());
    //     }
    // }

    //This function is for image size reduction it converts 5 mb image to almost 800 kb. and it does not has much impact in quality.
    public static function optimizeUploadFile($file, string $path = "images", string $initials = "img"): string
    {
        try {
            $originalPath = $path . '/' . $initials . '-' . rand(1, 1000) . sha1(time()) . "." . $file->getClientOriginalExtension();
            if (! file_exists($path)) {
                mkdir($path, 0777, true);
            }
            Image::make($file)->save($originalPath, 60);
            return $originalPath;
        } catch (Exception $e) {
            \Log::info('Exception in image upload : ' . $e->getMessage());
            throw new Exception("Failed to upload image. error : " . $e->getMessage());
        }
    }
}
