<?php

# (C) 2016 Matthias Kuhs, Ireland

use App\Models\Item;
use App\Models\Plan;
use App\Models\Song;
use App\Models\File;
use App\Models\User;
use App\Models\Team;

use App\Http\Controllers\Cspot\BibleController;

use Carbon\Carbon;

use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;

use Intervention\Image\ImageManager;



/**
 * Set a flash message in the session.
 *
 * @param  string $message
 * @return void
 */
function flash($message) 
{
    session()->flash('message', $message);
}


/**
 * Set a flash message in the session.
 *
 * @param  string $message
 * @return void
 */
function unFlash() 
{
    session()->flash('message', '');
}


/**
 * Set a flash ERROR message in the session.
 *
 * @param  string $message
 * @return void
 */
function flashError($message) 
{
    session()->flash('error', $message);
}




/**
 * Get list (collection) of Admin(s)
 */
function findAdmins( $field='all' )
{
    $users = User::get();
    $admins = [];
    foreach ($users as $user) {
        if ($user->isAdmin()) {
            if ($field=='all')
                array_push($admins, $user);
            else
                array_push($admins, $user[$field]);
        }
    }
    return $admins;
}



/**
 *  -------------
 *  F I L E S
 *  -------------
 */


/**
 * Correct sequence of files attached to an item
 */
function correctFileSequence($item_id)
{
    $item = Item::find($item_id);
    if ($item) {
        // get all files attached to this item
        $files = $item->files->sortBy('seq_no')->all();

        $seq = 0;
        // update the sequence nomber on each file
        foreach ($files as $file) {
            // get the actual file DB object and update it
            DB::table('files')
                ->where('id', $file->id)
                ->update(['seq_no' => $seq]);
            $seq += 1;
        }
    }
}


/**
 * Return sizes readable by humans
 */
function humanFileSize($bytes, $decimals = 2)
{
  $size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB'];
  $factor = floor((strlen($bytes) - 1) / 3);

  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .
      @$size[$factor];
}

/**
 * Is the mime type an image
 */
function is_image($mimeType)
{
    return starts_with($mimeType, 'image/');
}

/**
 * Save uploaded files to the designated folder
 * and create thumbnail and mini version as well
 */
function saveUploadedFile($request)
{
    $extension = $request->file('file')->getClientOriginalExtension();
    $token     = str_random(32).'.'.$extension; // new, random, physical file name
    $filename  = $request->file('file')->getClientOriginalName();
    $filesize  = $request->file('file')->getClientSize();
    $maxfilesize = $request->file('file')->getMaxFilesize();

    // check if request contains an id for the file category, otherwise assign 'unset' (0)
    $cat_id = $request->has('file_category_id') ? $request->get('file_category_id') : 0;

    // move the anonymous file to the central location using the random name
    $destinationPath = config('files.uploads.webpath');
    $request->file('file')->move($destinationPath, $token);

    // create a thumbnail copy of a file
    if (in_array(strtolower($extension), ['jpg','gif','png', 'jpeg'])) {
        createThumbs($destinationPath, $token);
    }

    // create and return the new FILE object
    $file = new File([
        'token'    => $token,
        'filename' => $filename,
        'filesize' => $filesize,
        'file_category_id' => $cat_id,
        'maxfilesize' => $maxfilesize,
    ]);
    return $file;  
}
/**
  * create a thumbnail copy of a file
  *
  * @param  string  $fPath  file path
  * @param  string  $fName  file name
  *
  * Save small copy of image as thumb_<file name> (max 300 width and max 200 height)
  * Save mini  copy of image as thumb_<file name> (max 150 width)
  *
  * thumbnail should be 300*200, with valid aspect ratio, bottom cropped if needed to retain 300x200!
  * mini version is just 50% of that
  */
function createThumbs($fPath, $fName) {
    // check if file has a valid extension for processing by Intervention/ImageManager
    $ext = pathinfo($fName, PATHINFO_EXTENSION);
    if (! in_array(strtolower($ext), ['jpg','gif','png','jpeg'])) {
        return;
    }
    // resize for thumbnail
    $img = Image::make($fPath.'/'.$fName)
        ->resize(250, null,         // max width
            function ($constraint) {
                $constraint->aspectRatio();
            })
        ->crop(250, 125, 0, 0 );
    $img->save($fPath.'/'.'thumb-'.$fName, 80);
    // resize for mini thumbnail
    $img = $img->resize(125, null, 
            function ($constraint) {
                $constraint->aspectRatio();
            });
    $img->save($fPath.'/'.'mini-'.$fName);

}
function deleteThumbs($fPath, $fName) {
    if (file_exists($fPath.'/'.'thumb-'.$fName)) {
        unlink($fPath.'/'.'thumb-'.$fName);
    }
    if (file_exists($fPath.'/'.'mini-'.$fName)) {
        unlink($fPath.'/'.'mini-'.$fName);
    }
}

/**
 * temporary job...
 */
function createThumbsForAll()
{
    chdir( 'public/'.config('files.uploads.webpath') );
    // create list of current files in images folder
    // (exclude thumb_... or mini_...)
    $files = glob('*.*');

    // loop through each file
    foreach ($files as $key => $imgfile) {
        // is it already minified?
        $prefix = explode('-', $imgfile);
        if ( $prefix[0]=='mini' || $prefix[0]=='thumb' ) {
            continue;
        }
        // check if thumb_... or mini_... already exists for this file
        if (file_exists('thumb-'.$imgfile) && file_exists('mini-'.$imgfile) ) {
            continue;
        }
        // create thumb and mini copy of this file
        Log::info("Creating thumbs for $imgfile:\n");
        createThumbs('.', $imgfile);
    }
    // make sure user 'www-data' has access rights to all files
    $files = glob('*.*');
    foreach ($files as $key => $value) {
        chmod($value, 0777);
    }
}



/**
 *  -------------
 *  S O N G S
 *  -------------
 */



/**
 * Search for songs
 *
 * @param  string $search
 * @return collection $songs
 */
function songSearch( $search )
{
    return Song::where('title', 'like', $search)->
             orWhere('title_2', 'like', $search)->
             orWhere('ccli_no', 'like', $search)->
             orWhere('book_ref','like', $search)->
             orWhere('author',  'like', $search)->
             orWhere('lyrics',  'like', $search)->
             take(10)->get();
}




/**
 *  -------------
 *  I T E M S
 *  -------------
 */




/**
 * Go to next or previous item in the list of items of a plan
 *      or swap between chords and sheetmusic
 */
function nextItem($plan_id, $item_id, $direction)
{
    $curItem = Item::find($item_id);
    $plan    = Plan::find($plan_id);
    // get all the items for this plan
    $items   = $plan->items()->orderBy('seq_no')->get();

    // get seq_no of desired next or previous item
    $new_seq_no = 0;    // to prevent unnassigned exception
    if ($direction == 'next') {
        if ($curItem->seq_no == count($items)) {
            $new_seq_no = 1.0;
        } else {
            $new_seq_no = $curItem->seq_no+1;
        }
    }
    elseif ($direction == 'previous') {
        if ($curItem->seq_no == 1.0) {
            $new_seq_no = count($items);
        } else {
            $new_seq_no = $curItem->seq_no-1;
        }
    } 
    elseif ($direction == 'swap') {
        $new_seq_no = $curItem->seq_no;
    }

    // find the new item id
    foreach ($items as $item) {
        if ($item->seq_no == $new_seq_no) {
            return $item->id;
        }
    }
    return $item->id;
}
function getItemTitle($item, $direction='next')
{
    $nextItemId = nextItem($item->plan_id, $item->id, $direction);
    $item = Item::find($nextItemId);
    if ($item->song_id && $item->song->title) {
        return $item->song->title;
    }
    return $item->comment;
}



/**
 * Insert a new item into the list of items of a plan
 *
 * Make sure the new sequence number fits sequentially into 
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function insertItem( $request )
{
    // get plan id from the hidden input field in the form
    $plan_id = $request->plan_id;
    // get new seq no for this item
    $new_seq_no = $request->seq_no;
    // get the Plan model and find the plan
    $plan  = Plan::find($plan_id);
    // get all the items for this plan
    $items = $plan->items()->orderBy('seq_no')->get();

    Log::info('INSERTITEM-newSeqNo old:'.$new_seq_no);

    // numbering the items, starting with 1.0
    $counter = 1.0;
    if ($new_seq_no <= $counter) {
        $new_seq_no = 1;
        $counter = 2;
    }
    foreach ($items as $item) {
        if ($new_seq_no <= $item->seq_no && $new_seq_no > $counter-1 ) {
            $new_seq_no = $counter;
            $counter += 1;
        }
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id); # get the actual DB record
            $item->seq_no = $counter;     # update the current selection
            $i->seq_no = $counter;        # update the seq_no
            $i->save();                 # save the record
        }
        $counter += 1.0;
    }
    // change new seq_no if it's bigger than the current counter
    if ($new_seq_no >= $counter) {
        $new_seq_no  = $counter;
    }
    // create a new Item using the input data from the request
    $newItem = new Item( $request->except(['seq_no', 'moreItems', '_token']) );
    $newItem->seq_no = $new_seq_no;
    // check if a song id was provided in the request
    if (isset($request->song_id)) {
        $newItem->song_id = $request->song_id;
    }
    // saving the new Item via the relationship to the Plan
    $item = $plan->items()->save( $newItem );
    $plan->new_seq_no = $new_seq_no;


    // handle file uplaods
    if ($request->hasFile('file')) {
        if ($request->file('file')->isValid()) {
            // use the helper function
            $file = saveUploadedFile($request);
            // add the file as a relationship to the song
            $item->files()->save($file);
        }
        else {
            flash('Uploaded file could not be validated!');
        }
    } 

    Log::info('INSERTITEM-newSeqNo new:'.$new_seq_no);

    if( isset($newItem->song_id) ) {
        $msg = $newItem->song->title;
    } else {
        $msg = $newItem->comment;
    }
    flash('New Item added: ' . $msg);
    return $plan;
}




/**
 * Delete an item from the list of items of a plan
 *
 * Make sure the new sequence number fits sequentially into 
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function moveItem($id, $direction)
{
    // this item should be moved
    $moveItem = Item::find($id);
    if (!$moveItem) { return false ;}
    // 'move' the item by changing the seq no
    $cur_seq_no = $moveItem->seq_no;
    if ($direction == 'earlier') {$cur_seq_no -= 1.1;}
    if ($direction == 'later'  ) {$cur_seq_no += 1.1;}
    $moveItem->update(['seq_no' => $cur_seq_no]);

     // get all items of the related plan
    $plan  = Plan::find( $moveItem->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // start the numbering of all items with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id);   # get the actual DB record
            $item->seq_no = $counter;     # update the current selection
            $i->seq_no = $counter;        # update the seq_no
            $i->save();                   # save the record
        }
        $counter += 1.0;        
    }
    return true;
}




/**
 * Delete an item from the list of items of a plan
 *
 * (the model migth allow soft deletes!)
 *
 * Make sure the new sequence number fits sequentially into 
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function deleteItem($id)
{
    // this item should be deleted
    $moveItem = Item::find($id);
    if (!$moveItem) { return false ;}

     // get all items of the related plan
    $plan  = Plan::find( $moveItem->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // numbering them countering with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->id == $id) {
            $moveItem->delete();            
        } else {
            if ($item->seq_no <> $counter) {
                $i = Item::find($item->id); # get the actual DB record
                # if found, update the current selection
                if ($i) { 
                    $item->seq_no = $counter; 
                    $i->seq_no = $counter;      # update the seq_no
                    $i->save();                 # save the record
                }  
            }
            $counter += 1.0;        
        }
    }
    return true;
}



/**
 * RESTORE an item 
 *
 * (the model migth allow soft deletes!)
 *
 * Make sure the new sequence number fits sequentially into 
 *    the list of sequence numbers of the existing items for a plan
 *    and that all current sequence numbers are in 1.0 steps
 *
 * @param object $items
 * @param number $new_seq_no
 */
function restoreItem($id)
{
    // this item should be restored
    $item    = Item::onlyTrashed()->find($id);
    if (!$item) { return false ;}

    $item->restore();

     // get all items of the related plan
    $plan  = Plan::find( $item->plan_id );
    $items = $plan->items()->orderBy('seq_no')->get();

    // numbering them countering with 1.0
    $counter = 1.0;
    foreach ($items as $item) {
        if ($item->seq_no <> $counter) {
            $i = Item::find($item->id); # get the actual DB record
            $item->seq_no = $counter;   # update the current selection
            $i->seq_no = $counter;      # update the seq_no
            $i->save();                 # save the record
        }
        $counter += 1.0;        
    }
    return true;
}



/**
 * check if string contains bible references, 
 * returns the bible texts as an array
 *
 * @param string $refString
 * @return array bible text from bibles.org
 */
function getBibleTexts($refString) 
{
    // regex pattern to match bible references (http://stackoverflow.com/questions/22254746/bible-verse-regex)
    $pattern = '/(\d*)\s*([a-z]+)\s*(\d+)(?::(\d+))?(\s*-\s*(\d+)(?:\s*([a-z]+)\s*(\d+))?(?::(\d+))?)?/i';
    $bibleTexts = [];
    $bb = new BibleController;
    $books = json_decode( $bb->books()->getContent() );

    if ( preg_match($pattern, $refString) ) {

        // several refs are seperated by semicolon
        $refs = explode(';', $refString);

        foreach ($refs as $ref) {
            $parts   = explode('(', $ref );
            if (count($parts)<2) continue;
            $version = explode(')', $parts[1] );
            $bref    = preg_split( '/[\s,:-]+/', $parts[0] );
            $num = 0;
            // check if the first word really is the name of a bible book 
            foreach ($bref as $key => $br) {
                if ( ( $br=='1' || $br=='2' || $br=='3' ) && in_array($br.' '.$bref[$key+1], $books) ) {
                    break;
                }
                if ( ! in_array( ucfirst($br), $books)) {
                    $num += 1;
                } else { break; }
            } 
            // No correct book name found?
            if (sizeof($bref)-$num < 3 ) continue;

            // book name can be preceeded by a 1,2 or 3
            if ($bref[$num]==1 || $bref[$num]==2 || $bref[$num]==3) {
                $book    = $bref[$num] . ' ' . $bref[1+$num];
                $num++;
            } else {
                $book    = $bref[$num];
            } 
            $chapter = $bref[$num+1];
            $verseFr = $bref[$num+2];   
            $verseTo = '';         
            if ( isset($bref[$num+3]) ) $verseTo = $bref[$num+3];
            if ( $verseTo == '' || ! is_numeric($verseTo) ) {
                $verseTo = $verseFr;
            } 
            // execute the text search
            $text = $bb->getBibleText($version[0], $book, $chapter, $verseFr, $verseTo);

            // was the search successful? then it should contain at least one passage array
            try {
                $result = json_decode( $text->getContent())->response->search->result->passages ;
                if ( count($result) > 0 ) {
                    $text = $result[0];
                    $text->text = str_ireplace( 'h3', 'strong', $text->text );
                    $text->text = str_ireplace( 'h2', 'i', $text->text );
                    $bibleTexts[] = $text;
                }
            }
            catch(Exception $e) { echo 'failed to convert '.$text->__toString(); }
        }
    }
    return $bibleTexts;
}




/**
 *  -------------
 *  P L A N S
 *  -------------
 */




/**
 * getUsersRolesAndInstruments
 *
 * Create an array of all users, their roles and instruments 
 *
 * @return arary
 */
function getUsersRolesAndInstruments() 
{

    // produce array with users and all their roles
    $rou = User::with('roles', 'instruments')->get();
    $userRoles = [];
    foreach ($rou as $user) {
        $roles = [];
        $instruments = [];
        foreach ($user->roles as $value) {
            if ($value->id > 3) { // no administrative roles needed
                array_push($roles, ['role_id'=>$value->id, 'name'=>$value->name] ); }
        }
        // provide data about user's music instruments
        foreach ($user->instruments as $value) {
            array_push($instruments, ['instrument_id'=>$value->id, 'name'=>$value->name] ); 
        }
        $userRoles[$user->id] = ['name'=>$user->name, 'roles'=>$roles, 'instruments'=>$instruments];       
    }
    return $userRoles;
}


/**
 * get list of future plans where the current user is listed as member
 */
function listOfPlansForUser()
{
    $list = [];
    $team = Team::where('user_id', Auth::user()->id)->get();
    foreach ($team as $key => $value) {
        if ($team[$key]->available) {
            $list[$team[$key]->plan_id] = True;
        }
    }
    return $list;
}



function addDefaultRolesToPlan($plan)
{        
    // add leader/teacher roles to the team for this plan
    $team = $plan->teams()->create([
        'user_id' => $plan->leader_id, 
        'role_id' => env('LEADER_ID', 4)  // default is 4 if not set in .env
    ]);
    if ($plan->teacher_id) {
        $plan->teams()->create([
            'user_id' => $plan->teacher_id, 
            'role_id' => env('TEACHER_ID', 5)   // default is 5 if not set in .env
        ]);
    }
}


/**
 * Trigger certain actions when leader or teacher of a plan was changed
 *
 * a) send notification to each invovled
 * b) change the team accordingly
 *
 * @param Request $request all data from the HTTP request (the new data)
 * @param EloquentModel $plan  (the old data)
 */
function checkIfLeaderOrTeacherWasChanged($request, $plan)
{
    // check if LEADER was changed
    if ( $plan->leader_id != $request->leader_id ) {

        // affected users must be notified of this change accordingly 
        $new_leader = User::find($request->leader_id);
        sendInternalMessage(
            'Leader changed for '.Carbon::parse($plan->date)->format('l, jS \\of F Y'), 
            Auth::user()->name . ' changed the leader for this '.
                $plan->type->name.' from '.$plan->leader->name.' to '.$new_leader->name, 
            $new_leader->id);

        // find the corresponding team record for the leader
        $leader = Team::where([
            ['plan_id', $plan->id], 
            ['role_id', env('LEADER_ID', 4)]  // default is 4 if not set in .env
        ]);
        if ($leader->count()) {               // update the team record
            $leader->update(['user_id' => $request->leader_id ]); }
        else {                                // create a new team member...
            $plan->leader_id = $request->leader_id;
            addDefaultRolesToPlan($plan);
        }
    }

    // check if TEACHER was changed
    if ( $plan->teacher_id != $request->teacher_id ) {

        // affected users must be notified of this change accordingly 
        $new_teacher = User::find($request->teacher_id);
        sendInternalMessage(
            'Teacher changed for '.Carbon::parse($plan->date)->format('l, jS \\of F Y'), 
            Auth::user()->name . ' changed the teacher for this '.
                $plan->type->name.' from '.$plan->teacher->name.' to '.$new_teacher->name, 
            $new_teacher->id);

        // find the corresponding team record for the teacher
        $teacher = Team::where([
            ['plan_id', $plan->id], 
            ['role_id', env('TEACHER_ID', 5)]  // default is 4 if not set in .env
        ]);
        if ($teacher->count()) {               // update the team record
            $teacher->update(['user_id' => $request->teacher_id ]); }
        else {                                // create a new team member...
            $plan->teacher_id = $request->teacher_id;
            addDefaultRolesToPlan($plan);
        }
    }
}




/**
 *  -----------------
 *  M E S S A G E S
 *  -----------------
 */




/**
 * send message via internal messenger
 */
function sendInternalMessage($subject, $message, $recipient_id, $email=true)
{

    $thread = Thread::create(
        [
            'subject' => $subject,
        ]
    );

    // Message
    $message = Message::create(
        [
            'thread_id' => $thread->id,
            'user_id'   => Auth::user()->id,
            'body'      => $message,
        ]
    );

    // Sender
    Participant::create(
        [
            'thread_id' => $thread->id,
            'user_id'   => Auth::user()->id,
            'last_read' => new Carbon,
        ]
    );

    // Add Recipients
    $thread->addParticipants([$recipient_id]);

    if ($email)
        sendEmailNotification($message);

    return $thread->id;

}
/**
 * Delete the internal message if the associated task was completed
 */
function deleteConfirmRequestThread($id)
{
    $thread = Thread::find($id);
    if ($thread) {
        $thread->delete();
    }
}

/**
 * Send Email notification of new internal messages
 *
 * @param Message $message 
 */
function sendEmailNotification(Message $message)
{
    $subject = 'c-SPOT internal message notification';
    $thread = Thread::find($message->thread_id);
    $thread_subject = $thread->subject;
    $message_body = $message->body;

    foreach ($thread->participants as $key => $recipient) {
        $user = $recipient->user;
        # check if user actually wants to be notified
        if ($user->notify_by_email) {
            Mail::send('cspot.emails.notification', 
                ['user'=>$user, 'subject'=>$subject, 'messi'=>$message],
                function ($msg) use ($user, $subject) {
                    $msg->from(findAdmins()[0]->email, 'c-SPOT Admin');
                    $msg->to($user->email, $user->getFullName());
                    $msg->subject($subject);
                }
            );
        }
    }    
}


