<?php

namespace App\Jobs;

use DB;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BatchJobs extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {


        /*
            create thumbnails for all existing image files
            that havn't got one yet
        */
        #createThumbsForAll(); // helper function in helpers.php




        /*
            get missing file sizes for song and item attachments
        */
        // get list of all files
        $files = DB::table('files')->get();

        # change into the directory containing the uploaded files 
        chdir( 'public/'.config('files.uploads.webpath') );

        foreach ($files as $key => $file) {
            # check if file size is already defined
            if ( $file->filesize > 0 ) {
                echo $file->filesize . '- Already done for: '.$file->filename."\n";
                continue;
            }

            # get actual file size
            if (file_exists($file->token)) {
                $filesize = filesize($file->token);
            }
            else {
                echo "Error! File '$file->token' not found!\n";
                $filesize = 0;
            }

            echo 'Size: ' . humanFilesize($filesize) . ", Name: ". $file->filename . "\n";

            # write filesize back to DB table
            DB::table('files')
                ->where('id', $file->id)
                ->update(['filesize' => $filesize]);
        }



    }


}
