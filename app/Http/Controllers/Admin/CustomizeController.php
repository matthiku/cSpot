<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Log;


class CustomizeController extends Controller
{

    protected function updateDotEnv($key, $newValue, $delim='')
    {

        Log::info('Request to update key '.$key.' to new value: '.$newValue);

        $path = base_path('.env');
        // get old value from current env
        $oldValue = env($key);

        // was there any change?
        if ($oldValue === $newValue) {
            return;
        }

        // special case for true/false values
        // (we need to first 'stringify' it!)
        if ($newValue=='true' || $newValue=='false' ) {
            $oldValue = $oldValue ? 'true' : 'false';
        }

        Log::info('Customization: Update for key '.$key.': Old value: '.$oldValue.', New value: '.$newValue);

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
        if ($request->has('church_youtube_playlist_id')) {
            $this->updateDotEnv('CHURCH_YOUTUBE_PLAYLIST_ID', $request->church_youtube_playlist_id);
        }

        if ($request->has('songselect_url')) {
            $this->updateDotEnv('SONGSELECT_URL', $request->songselect_url);
        }
        if ($request->has('songselect_search')) {
            $this->updateDotEnv('SONGSELECT_SEARCH', $request->songselect_search);
        }
        if ($request->has('ccli_report_url')) {
            $this->updateDotEnv('CCLI_REPORT_URL', $request->ccli_report_url);
        }
        if ($request->has('hymnal_net_play')) {
            $this->updateDotEnv('HYMNAL.NET_PLAY', $request->hymnal_net_play);
        }
        if ($request->has('hymnal_net_search')) {
            $this->updateDotEnv('HYMNAL.NET_SEARCH', $request->hymnal_net_search);
        }
        if ($request->has('youtube_play')) {
            $this->updateDotEnv('YOUTUBE_PLAY', $request->youtube_play);
        }
        if ($request->has('youtube_search')) {
            $this->updateDotEnv('YOUTUBE_SEARCH', $request->youtube_search);
        }

        if ($request->has('youtube_playlist_url')) {
            $this->updateDotEnv('YOUTUBE_PLAYLIST_URL', $request->youtube_playlist_url);
        }

        if ($request->has('bible_versions')) {
            $this->updateDotEnv('BIBLE_VERSIONS', $request->bible_versions);
        }


        if ($request->has('enable_sync')) {
            $this->updateDotEnv('PRESENTATION_ENABLE_SYNC', $request->get('enable_sync'));
        } 
        if ($request->has('enable_debug')) {
            $this->updateDotEnv('APP_DEBUG', $request->get('enable_debug'));
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


