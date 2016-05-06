<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class ConfigController extends Controller
{

    protected function updateDotEnv($key, $value, $delim='')
    {
        $path = base_path('.env');
        // rewrite file content with changed data
        if (file_exists($path)) {
            // replace current value with new value 
            file_put_contents(
                $path, str_replace(
                    $key.'='.$delim.env($key).$delim, 
                    $key.'='.$delim.$value.$delim, 
                    file_get_contents($path)
                )
            );
        }
    }

    // show current configuration
    public function index()
    {
        return view('admin/config');
    }


    // show current configuration
    public function update(Request $request)
    {
        // change env settings
        if ($request->has('church_name')) {
            $this->updateDotEnv('CHURCH_NAME', $request->church_name, '"');
        }
        if ($request->has('church_url')) {
            $this->updateDotEnv('CHURCH_URL', $request->church_url, '"');
        }

        if ($request->hasFile('file')) {
            if ($request->file('file')->isValid()) {
                // move the new logo file to the public folder with new name
                $request->file('file')->move( public_path('images'), env('CHURCH_LOGO_FILENAME') );
            }
        }

        return redirect('home');
    }

}


