<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageStorage
{
    public const PROFILE_IMAGE = 'uploads/profile_images';
    public const PRODUCT_IMAGE = 'uploads/product_images';
    public const STORE_IMAGE = 'uploads/store_images';
    public const BAZAAR_IMAGE = 'uploads/bazaar_images';
    
    /**
     * Disk to use for storing
     * 
     * @var string
     */
    protected $disk = 'public';

    /**
     * store the image file
     * 
     * @param UploadedFile $image
     * @return string
     */
    public function uploadImage(UploadedFile $image, string $folder)
    {
        $ext = strtolower($image->getClientOriginalExtension());
        $filename = now()->format('YmdHis') . '_' . Str::random(12) . '.' . $ext;
        $path = $image->storeAs($folder, $filename, $this->disk);
        return $path;
    }

    /**
     * Delete an image by its storage path.
     *
     * @param  string  $path
     * @return bool
     */
    public function deleteImage(string $path)
    {
        return Storage::disk($this->disk)->delete($path);
    }

    /**
     * Get a publicly accessible URL for a stored file.
     *
     * @param  string  $path
     * @return string
     */
    public static function getUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }
}
