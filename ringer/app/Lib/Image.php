<?php

namespace App\Lib;

use \Cloudinary;
use \Cloudinary\Uploader;

class Image
{
    public static $default = 'https://res.cloudinary.com/markowebdev-com/image/upload/v1585397725/ringer/default.png';

    public static function upload($image)
    {
        Cloudinary::config([
          'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
          'api_key' => env('CLOUDINARY_API_KEY'),
          'api_secret' => env('CLOUDINARY_API_SECRET'),
          'secure' => true
        ]);

        $response =  Uploader::upload($image, ['folder' => 'ringer']);

        return $response['secure_url'];
    }

    public static function delete($url)
    {
        Cloudinary::config([
          'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
          'api_key' => env('CLOUDINARY_API_KEY'),
          'api_secret' => env('CLOUDINARY_API_SECRET'),
          'secure' => true
        ]);

        $position = strrpos($url, '/') + 1;
        $imageName = substr($url, $position);
        $publicID = preg_replace('/\.(png|jpg|jpeg|gif)$/', '', $imageName);

        return Uploader::destroy("ringer/$publicID");
    }
}
