<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;



class CustomizeController extends Controller
{

    protected function updateDotEnv($key, $newValue, $delim='')
    {
        $path = base_path('.env');
        // get old value from current env
        $oldValue = env($key);
        // special case for true/false values
        // (we need to first 'stringify' it!)
        if ($newValue=='true' || $newValue=='false' ) {
            $oldValue = $oldValue ? 'true' : 'false';
        }
        // rewrite file content with changed data
        if (file_exists($path)) {
            // replace current value with new value 
            file_put_contents(
                $path, str_replace(
                    $key.'='.$delim.$oldValue.$delim, 
                    $key.'='.$delim.$newValue.$delim, 
                    file_get_contents($path)
                )
            );
        }
    }

    // show current configuration
    public function index()
    {
        return view('admin/customize');
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
        if ($request->has('church_ccli')) {
            $this->updateDotEnv('CHURCH_CCLI', $request->church_ccli);
        }
        if ($request->has('enable_sync')) {
            $this->updateDotEnv('PRESENTATION_ENABLE_SYNC', 'true');
        } else {
            $this->updateDotEnv('PRESENTATION_ENABLE_SYNC', 'false');
        }

        if ($request->hasFile('favicon_file')) {
            if ($request->file('favicon_file')->isValid()) {
                // move the new logo file to the public folder with new name
                $request->file('favicon_file')->move( public_path().'/images/custom', 'favicon.ico' );
                // update .env to show we are using custom logos
                $this->updateDotEnv('USE_CUSTOM_LOGOS','yes');

                flash('New file '.$request->file('favicon_file')->getClientOriginalName()
                    .' saved as favicon.ico'
                    .' size was '.$request->file('favicon_file')->getClientSize() );
            }
        }

        if ($request->hasFile('logo_file')) {
            if ($request->file('logo_file')->isValid()) {
                // move the new logo file to the public folder, subfolder 'custom', using the predefined name
                $request->file('logo_file')->move( public_path().'/images/custom', env('CHURCH_LOGO_FILENAME') );
                // update .env to show we are using custom logos
                $this->updateDotEnv('USE_CUSTOM_LOGOS','yes');

                flash('New file '.$request->file('logo_file')->getClientOriginalName()
                    .' saved as '.env('CHURCH_LOGO_FILENAME')
                    .' size was '.$request->file('logo_file')->getClientSize() );
            }
        }

        return redirect('home');
    }

}


