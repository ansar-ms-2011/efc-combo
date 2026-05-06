<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class Base64Image implements Rule
{
    private $maxSize;
    private $allowedMimes;
    private $errorMessage;

    public function __construct($maxSize = 2048, $allowedMimes = ['jpg', 'jpeg', 'png', 'gif', 'webp'])
    {
        $this->maxSize = $maxSize; // in KB
        $this->allowedMimes = $allowedMimes;
    }

    public function passes($attribute, $value)
    {
        // Check if it's a base64 string
        if (!preg_match('/^data:image\/(\w+);base64,/', $value, $matches)) {
            $this->errorMessage = 'The :attribute must be a valid base64 image.';
            return false;
        }

        $imageType = $matches[1];

        // Check mime type
        if (!in_array($imageType, $this->allowedMimes)) {
            $this->errorMessage = 'The :attribute must be a file of type: ' . implode(', ', $this->allowedMimes);
            return false;
        }

        // Get base64 content
        $base64String = preg_replace('/^data:image\/\w+;base64,/', '', $value);
        $base64String = str_replace(' ', '+', $base64String);

        // Decode and check size
        $decodedData = base64_decode($base64String);
        $sizeInKB = strlen($decodedData) / 1024;

        if ($sizeInKB > $this->maxSize) {
            $this->errorMessage = "The :attribute may not be greater than {$this->maxSize} KB.";
            return false;
        }

        // Optional: Check if it's a valid image
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_buffer($finfo, $decodedData);
        finfo_close($finfo);

        if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            $this->errorMessage = 'The :attribute must be a valid image file.';
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage;
    }
}
