<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
Use Illuminate\Support\Facades\URL;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saveImage($image, $path = 'public')
    {
        if($image)
        {
            return null;
        }

        $filename =  time().'.png';
        // Save image
        \storage::disk($path)->put($filename, base64_decode($image));

        // Return path
        return URL::to('/').'/storage/'.$path.'/'.$filename;
    }
}
