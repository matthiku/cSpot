
/*\
|*|
|*|
|*#=========================================================================================== SPA UTILITIES
|*|
|*|
\*/


function setCurrentPageAsStartupPage(that)
{
    var actionURL = $(that).data('actionUrl');
    var pathname = window.location.pathname;

    // send the POST request
    $.post(actionURL, {'url' : pathname})
    .done( function(data) {
        // show confirmation
        console.log('set current page as startup-page for this user: '+pathname);
    }).fail( function(data) {
        console.log('Setting current page as home page failed!');
        console.log(data);
    });
}



/* Record a user's availability for a certain plan
 * (called when user clicks on the 'available' icon on plans.blade.php) */
function userAvailableForPlan(that, plan_id) {
    // make sure the tooltip is hidden now
    $(that).parent().parent().tooltip('hide')
    $('#user-available-for-plan-id-'+plan_id).text( "wait..." );

    var teamPage = false;
    // was this function called from within the Team page?
    if (that.checked == undefined) {
        showSpinner();
        teamPage = true;
        // inverse the current available status
        that.checked = ! $(that).data().available;
    }

    if ( $.isNumeric(plan_id) ) {
        console.log('User wants his availability changed to '+that.checked);
        // make AJAX call to 'plans/{plan_id}/team/{user_id}/available/'+that.checked
        $.get( __app_url+'/cspot/plans/'+plan_id+'/team/available/'+that.checked)
        .done(function() {
            $('#user-available-for-plan-id-'+plan_id).text( that.checked ? 'yes' : 'no');
            if (teamPage) { location.reload(); }
        })
        .fail(function() {
            $('#user-available-for-plan-id-'+plan_id).text( "error" );
        })        
    }
}



/*\__________________________________________________________________________  ITEM  Details Page
\*/



/* allow Admins/Authors/Plan owners to delete an attached file (image)    
*/
function deleteFile(id)
{
    // Prompt for confirmation as this is irrevocable:
    if ( ! confirm('Are you sure to finally remove this file?') ) 
        return;

    // show wait spinner
    $('#file-'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    // get token from form field
    $.ajax({
        url:    '/cspot/files/'+id+'/delete', 
        method: 'DELETE',
    })
    .done(function(data) {
        $('#file-'+id).html(data.data);
    })
    .fail(function(data) {
        if (data.responseJSON) {
            alert("image deletion failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("image deletion failed! "+JSON.stringify(data));
        }
    });
}

/* unlink FILE from its item
*/
function unlinkFile(item_id, file_id)
{
    // show wait spinner
    $('#file-'+file_id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.ajax({
        url:    '/cspot/items/'+item_id+'/unlink/'+file_id+'', 
        method: 'PUT',
    }).done(function(data) {
        $('#file-'+file_id).html(data.data);
    }).fail(function(data) {
        if (data.responseJSON) {
            alert("image unlinking failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("image unlinking failed! "+JSON.stringify(data));
        }
    });
}


/* unlink SONG from its item
*/
function unlinkSong(that, item_id, song_id, plan_url)
{
    // disable button and show wait spinner
    $(that).addClass('disabled');
    $(that).children('small').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    // send unlink request
    $.ajax({
        url:    '/cspot/items/'+item_id+'/unlinkSong/'+song_id+'', 
        method: 'PUT',
    })
    .done(function(data) {
        console.log(data);
        $(that).children('small').text(data.data);
        // go back to plan 
        //window.location.href = plan_url;
    })
    .fail(function(data) {
        if (data.responseJSON) {
            alert("song unlinking failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("song unlinking failed! "+JSON.stringify(data));
        }
    });
}


/* toggle field 'show_comment'
*/
function toggleShowComment(that, id, actionUrl) 
{
    // replace current note with spinner while doing AJAX
    $('#'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.post( 
        actionUrl, 
        { 
            'id'    : id, 
            'value' : $(that).prop('checked'),
        })
    .done( 
        function(data) {
            // show result
            if (data == 'true')
                $('#'+id).html( 'Notes are presented as Title in the presentation' );
            else
                $('#'+id).html( 'Show notes as Title in the presentation' );
        }
    );
}


/* toggle field 'show_comment'
*/
function toggleHideTitle(that, id, actionUrl) 
{
    var oldText = $('#'+id).text();

    // replace current note with spinner while doing AJAX
    $('#'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.post( 
        actionUrl, 
        { 
            'id'    : id, 
            'value' : $(that).prop('checked'),
        })
    .done( 
        function(data) {
            // show result
            if (data == '1' || data == '0')
                $('#'+id).text(oldText);
            else
                $('#'+id).text( JSON.stringify(data) );
        })
    .fail(
        function(data) {
            // show result
            $('#'+id).text( JSON.stringify(data) );
        }
    );
}


/* toggle field 'key' in order to use item as Announcements Slide
*/
function toggleShowAnnouncement(that, id, actionUrl) 
{
    // replace current note with spinner while doing AJAX
    $('#'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.post( 
        actionUrl, 
        { 
            'id'    : id, 
            'value' : $(that).prop('checked') ? 'announcements' : 'none',
        })
    .done( 
        function(data) {
            // show result
            if (data == 'announcements')
                $('#'+id).html( 'This item will show the Announcements Slide in the presentation' );
            else
                $('#'+id).html( 'Use this item to show the announcements in the presentation?' );
        }
    );
}


/* empty (clear) a public or private note of an item
*/
function deleteItemNote(which, id, actionUrl) 
{
    // replace current note with spinner while doing AJAX
    $('#'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.post( 
        actionUrl, 
        { 
            'id'    : id, 
            'value' : '_' 
        })
    .done( 
        function(data) {
            // remove old text from note element
            $('#'+id).html('');
            // remove the link that triggered this function
            $('#'+which+'-notes-erase-link').hide();
        }
    );
}


/* FLEO - Plan leader can mark an item as "for leader's eyes only"
*/
function changeForLeadersEyesOnly(that) {

    // get data from parent html element (tr)
    var data = $(that).parent().data();
    if (! data) return; // should not happen!

    // construct values for the AJAX call
    var actionURL = data.itemUpdateAction;
    var value     = $(that).data('value') ? '0' : '1'; // reverse the current value
    var id        = 'forLeadersEyesOnly-item-id-' + data.itemId;

    $(that).children('i').removeClass('fa-eye');
    $(that).children('i').removeClass('fa-eye-slash');
    $(that).children('i').addClass('fa-spin fa-spinner');

    // AJAX update
    $.post( actionURL, 
        {
            value : value,
            id    : id,
        })
        .done( function(data) 
        {
            $(that).children('i').removeClass('fa-spin fa-spinner');
            // show correct icon according to new setting
            $(that).children('i').addClass( data==1 ? 'fa-eye-slash': 'fa-eye');
            // reflect new setting also in the data attribute
            $(that).attr('data-value', data); $(that).data('value', data);
            // on the Item Detail page, also show the right text for the new setting
            $(that).children('small').toggle();
        })
        .fail( function(data) 
        {
            $(that).children('i').removeClass('fa-eye');
            $(that).children('i').removeClass('fa-eye-slash');
            $(that).children('i').addClass('fa-exclamation-triangle');
            console.log('update of forLeadresEyesOnly failed!!');
        });
}




/*\____________________________________________________________________________  PLAN  Details Page
\*/



/* Reset the song search form
*/
function resetSearchForSongs() 
{
    ;;;console.log('resetting modal popup');

    $('.modal-content').css('background-color', '#fff');    
    $('.modal-select-song').hide();
    $('.modal-select-file').hide();
    $('.modal-select-clips').hide();
    $('.modal-select-comment').hide();
    $('.modal-select-scripture').hide();
    $('.modal-pre-selection').show();
    $('#searching').hide();    
    $('#search-result').html('');
    $('#searchForSongsSubmit').hide();
    $('.show-location-selection').hide();
    $('.show-file-add-button').hide();
    $('.image-selection-slideshow').hide();
    $('.show-next-image-arrows').attr('disabled', '');
    $('#link-to-more-images').html('');
    $('#file_category_id').val('');
    $('#MPselect').val(0);
    $('#search-string').val('');
    $('#clips').val('');
    $('#clipsHint').html('');
    $('#haystack').focus();
    $('#searchSongModalLabel').text('Select what to insert');
    $('#search-action-label').text('Full-text search incl. lyrics:');
    $('#txtHint').html('');
    $('#haystack').val('');
    $('#show-images-for-selection').html('');
    $('#comment').val('');
}

                
/*\
|*|----------------------------------------------------------------------
|*|    Insert NEW or update EXISTING ITEMS on the Plan Overview page
|*|----------------------------------------------------------------------
|*|
|*| (this is called from document.ready.js!)
|*|
|*| The corresponding modal is included in plan.blade.php as a BLADE include, referring 'cspot.snippets.add_item_modal'
|*|
|*| The method below is called when the modal popup is activated (shown) by clicking on the respective buttons or links.
|*| It populates the modal popup with the data provided by the launching button ....
|*|
|*| Initially, a menu with 3 buttons is shown for the selection of 'Song', 'Scripture' or 'Comment/Note'.
|*| Each will un-hide a different list of input and selection elements.
|*|
|*| This same modal is also being used to update an existing song item (ie. to change the song)
|*|
|*| The new data is processed via the 'searchForSongs' js helper function above
\*/
function insertNewOrUpdateExistingItems( event )
{
    ;;;console.log('preparing modal popup for inserting or adding items. ' + JSON.stringify(event.relatedTarget));

    // first make sure the form is back in its initial state
    resetSearchForSongs();

    // get item-specific data from the triggering element
    var button = $(event.relatedTarget);        // Button that triggered the modal
    var item = {};
    item.action   = button.data('item-action');
    item.plan_id  = button.data('plan-id');      // Extract info from data-* attributes
    item.item_id  = button.data('item-id');
    item.song_id  = button.data('song-id');
    item.seq_no   = button.data('seq-no' );
    item.actionUrl= button.data('action-url');
    item.buttonID = button.attr('id');
    cSpot.item = item;

    ;;;console.log( 'cSpot.item = ' + JSON.stringify(item) );

    // prepare title text for popup dialog
    var ar_seq = item.seq_no.split('-');
    var titleText = 'before item No '+item.seq_no;

    // was modal opened from existing item?
    if (item.action=="update-song" || location.pathname.search('chords') > 0) {
        // directly activate the song selection
        showModalSelectionItems('song');
        $('#searchSongForm'      ).attr('data-action', item.actionUrl);
        $('#searchSongModalLabel').text('Select song');

        titleText = 'for item No '+item.seq_no;
        if ( ar_seq[0] == 'after')
            titleText = 'after item No '+ar_seq[1];
    }

    else if (item.action=="update-scripture") {
        // directly activate the scripture selection
        showModalSelectionItems('scripture');
        // use current comment text as initial value
        var curCom = button.parent().children().first().text().trim();
        $('#comment').val( curCom=='Click to edit' ? '' : curCom );
        // URL needed to update the comment as derived from the calling element
        $('#searchSongForm'      ).attr('data-action', item.actionUrl);
        $('#searchSongModalLabel').text('Select a scripture');

        titleText = 'for item No ' + item.seq_no;
    } 

    else if (item.action=="add-file") {
        // make sure the form is partially hidden initially
        $('.show-file-add-button').hide()

        // directly activate the file selection
        showModalSelectionItems('file');

        //$('#comment').val('new image added');

        titleText = 'for item No ' + item.seq_no;
    } 

    else if (item.action=="insert-item") {

        titleText = 'after item No ' + ar_seq[1];
    } 

    // set title text for popup dialog
    $('#modal-show-item-id').text( titleText+':' );


    // Update the modal's content
    $('#plan_id'      ).val(item.plan_id);
    $('#beforeItem_id').val(item.item_id);
    $('#seq-no'       ).val(item.seq_no);

    $('#haystack').focus(); // make sure the search string input field has focus


    // prevent the Song Search Form from being submitted when 
    //      the ENTER key is used; instead, perform the actual search
    $("#searchSongForm").submit(function(event){

        // if a NEW item with a file was selectd, submit the form
        if (cSpot.item.action=='insert-file-item') {
            return true;
        }

        // if a NEW file for an existing item was selectd, DON'T submit the form
        if (cSpot.item.action=='add-file') {

            uploadNewFile();

            return false; // form should NOT be submitted
        }

        if (! $('#searchForSongsButton').is(':visible') ||  $('#song_id').val()=='')
            return false;

    });


    // intervene cancel button - reset form and close the popup
    $("#searchSongForm").on('cancel', function(event){
        event.preventDefault();
        resetSearchForSongs();
        $('#searchSongModal').modal('hide');
    });

}


/* User has selected WHAT he wants to insert, 
   now we present the appropriate input elements */
function showModalSelectionItems(what)
{
    ;;;console.log('showing Selection Items for: '+what);

    cSpot.item.type = what;

    // hide all pre-selection parts of the modal
    $('.modal-pre-selection').hide();               

    // show all parts for selecting a song or entering a comment
    $('.modal-select-'+what).show();                

    // different background color during song selection
    if (what=='song')
        $('.modal-content').css('background-color', '#c2c2d6'); 

    $('#searchSongModalLabel').text('Insert '+ (what=='file' ? 'image' : what) );

    $('.modal-input-'+what).focus();

    // show submit button for comments, scripture or file upload
    if (what=='comment' || what=='scripture' || what=='file') 
        $('#searchForSongsSubmit').show();

    // make sure the FILE form is partially hidden initially
    if (what=='file') {
        // the user wants to insert a NEW item with a file
        if ( cSpot.item.action==undefined || cSpot.item.action=='insert-item' ) {
            ;;;console.log('user wants to insert a new item with a file(image) attached');
            cSpot.item.action = 'insert-file-item';
        }

        $('.show-file-add-button').hide();
        $('#comment').val(' ');
    }

    if (what=='clips') {
        $('#searchForSongsButton').show();
    }
}


/* Called from the Modal popup on the PLAN details page, 
   this function searches for songs, presents a list and/or 
   song history information; uses AJAX to do the full-text search */
function searchForSongs()
{
    ;;;console.log('Searching for or selecting songs?' );
    ;;;console.log('cSpot.item: '+JSON.stringify(cSpot.item) );

    var plan_id = cSpot.item.plan_id;
    var action  = cSpot.item.action;
    var seq_no  = cSpot.item.seq_no;
    var type    = cSpot.item.type;

    // user chose to add a file to an existing item
    if (action=='add-file') {
        // do not SUBMIT the form
        return false;
    }
    // user added a new item with a file attached
    if (action=='insert-file-item') {
        // SUBMIT the form
        return true;
    }
    if (action != "update-scripture" && (type  =='scripture'|| type  =='comment') ) {
        if ($('#comment').val().length > 1) {
            $('#searchSongModal').modal('hide');
            showSpinner();
            document.getElementById('searchSongForm').submit();
        }
        // SUBMIT the form
        return true;
    }

    // are we still searching or has the user already selected a song?
    var modus = 'selecting';
    if ( $('#searchForSongsButton').is(':visible')  ) {
        var search       = $('#search-string').val();
        var mp_song_id   = $('#MPselect').val();
        if (!mp_song_id || mp_song_id==0)    // perhaps it was a clip that was selected
            mp_song_id = $('#ClipSelect').val();
        var haystack_id  = $('input[name=haystack]:checked', '#searchSongForm').val();
        if (search=='' && mp_song_id==0  && haystack_id==undefined) {
            return;         // search string was empty...
        }
        if (mp_song_id>0) {
            search = '(song id: '+mp_song_id+')';    // MP song selection is preferred
            ;;;console.log('user is searching for this: '+mp_song_id);
        }
        if (haystack_id) {
            search = '(song id: '+mp_song_id+')'; 
            mp_song_id = haystack_id;
            ;;;console.log('user selected this song id: '+mp_song_id);
        }
        modus = 'searching';
        $('.search-form-item').hide();  // hide search input fields and labels
        $('#searching').show();         // show spinner
    }
    // alternate the form action buttons

    if (modus=='searching') {
        $('#searchForSongsButton').toggle();
        $('#searchForSongsSubmit').toggle();
        // get the action URL
        var actionURL = $('#plan_id').data('search-url');

        // update via AJAX 
        $.post( actionURL, { search: search, song_id: mp_song_id })
            .done(function(data) {
                if (typeof(data)!='object')
                    data.data = "[]"; // simulate empty result
                var result = JSON.parse(data.data);
                if (result.length==0 || !result ) {
                    $('#search-action-label').text('Nothing found for "'+search+'", please try again:');
                    $('#searchForSongsButton').toggle();
                    $('#searchForSongsSubmit').toggle();
                    $('#searching').hide();  
                    $('.search-form-item').show();  
                    $('#haystack').focus();
                    return;
                }
                $('#search-action-label').html('<span class="bg-info">Select the desired Song:</span>');
                $('#search-action-label').append('<span class="pull-xs-right small text-muted">Preview:</span>');
                $('#searching').hide();
                $('#searchForSongsSubmit').hide();

                // call function to fill the search result element
                createAndShowSearchResult('#search-result', result);

            })
            .fail(function(data) {
                $('#searching').hide();
                console.log(data);
                $('#search-result').text("Search failed! Please notify admin! " + JSON.stringify(data));
            });

        return;
    }

    // which song was selected?
    var song_id = $('input[name=searchRadios]:checked', '#searchSongForm').val();

    // check if user entered a comment
    var comment = $('#comment' ).val();

    // was this called via 'showUpdateSongForm' function?
    if (action == "update-song") {
        if (song_id!=undefined) {
            // attach lyrics to song_id input field, so that when user selects this song, we can attach it as title to the table cell
            // (we get this from the selection in the search results to whose parent element the lyrics were attached)
            $('#song_id').attr(  'title',  $('input[name=searchRadios]:checked', '#searchSongForm').parent().attr('title')  );
            updateSong(song_id);
        }
        return;
    }
    // was this called via 'AddScriptureRef' button?
    if (action == "update-scripture") {
        addScriptureRef();
        return;
    }

    // did user select a song? It should always be a string, even '0'....
    if ( (! song_id  || song_id == '0') && ! comment )
        // nothing selected and comment is empty
        return false; // no

    // reset search form back to normal
    resetSearchForSongs();

    showSpinner()
    $('#searchForSongsButton').toggle();
    $('#searchForSongsSubmit').toggle();
    
    // write it into the form
    $('#song_id').val(song_id);
    ;;;console.log('Writing the selected song_id as value of the hidden input element: '+song_id)

    // restore the original search form
    $('#searchSongModal').modal('hide');    // close the modal
    $('#search-result').html('');           // remove the search results
    $('#search-string').val('');            // reset the search string
    $('#searching').hide();                 // hide the spinner

    // if this is called from the Presentation view, 
    // we will make the insertion via an AJAX call
    if (  location.pathname.indexOf('/present') > 0  ||  location.pathname.search('chords') > 0  ) {
        
        // this function inserts the new item via AJAX
        insertNewItemIntoPlan( plan_id, seq_no, song_id, comment );

        // we need return false to the form so that it doesn't submit!
        return false;
    }

    // Is this intended to be a new item at the end of the list of items?
    // Then we can't use the 'insert-before-item-so-and-so' concept in the Item Controller
    // and we need to change the beforeItem_ID accordingly ...
    if (seq_no.substr(0,5) == 'after') {
        $('#seq_no').val(seq_no);
        $('#beforeItem_id').val(cSpot.item.item_id);
    }


    // for some reason, the form doesn't submit if only a comment was given...
    if (comment) {
        // submit the form - causes a POST http request to STORE a new item
        document.getElementById('searchSongForm').submit();
    }
}

/*  loop through each item in the search result and present it to the user for selection
*/
function createAndShowSearchResult(elem, result)
{
    ;;;console.log('building the search-result list now');

    // make sure the place is empty at first
    var html = '';  
    $(elem).html(html);

    // loop through each item
    for (var i = 0; i < result.length; i++) {

        if (result[i].id==0)                // ignore song with id # 0
            continue;

        var count = result[i].plans.length; // number of plans that already used this song

        var lastPlanDate = false;           //date of last time this song was used ("2016-05-08 00:00:00")

        if (result[i].plans.length) {
            lastPlanDate = result[i].plans[0].date; 
        }

        // create a new DOM element 

        // song Count indicator
        var bold = document.createElement('b');
        if (count>25)
            bold.className="text-danger";
        $(bold).append( count );

        // innermost SPAN containing the song info
        var spn2 = document.createElement('span');
        spn2.className = "label label-default";
        var lastUse = lastPlanDate ? moment(lastPlanDate, 'YYYY-MM-DD HH:mm:ss').fromNow() : 'never used!!';
        spn2.innerHTML = '<b>Last used: '+lastUse+'</b> Total: ';
        spn2.appendChild(bold);
        $(spn2).append(' times');

        // spn containing the youtube link
        var spnYT = document.createElement('span');
        if ( result[i].youtube_id.length>0 ) {
            spnYT.className = "pull-xs-right";
            spnYT.title     = "preview song";
            var anchor = document.createElement('a');
            anchor.href = '#';
            $(anchor).attr('onclick', 'showYTvideoPreview("'+ result[i].youtube_id +'", this)');
            anchor.innerHTML = '<i class="fa fa-youtube-play red"></i>';
            spnYT.appendChild(anchor);
        }

        // spn containing the CCLI SongSelect link
        var spnSS = document.createElement('span');
        if ( result[i].ccli_no>10000 ) {
            spnSS.className = "pull-xs-right m-r-1";
            spnSS.title     = "review song on SongSelect";
            var anchor = document.createElement('a');
            anchor.href = cSpot.env.songSelectUrl + result[i].ccli_no;
            anchor.target = 'new';
            anchor.innerHTML = '<i class="fa fa-music"></i>';
            spnSS.appendChild(anchor);
        }

        // the span that pulls it left...
        var spnleft = document.createElement('span');
        spnleft.className = "pull-xs-left";
        spnleft.appendChild(spn2);

        // 2. the <small> element containing Title 2 and the song info
        var sml = document.createElement('small');
        sml.className = 'hidden-sm-down';
        $(sml).append(result[i].title_2); 
        $(sml).append('<br>');      // make sure it starts on a new line
        sml.appendChild(spnleft);

        // 3. the Label element
        var lbl = document.createElement('label');
        lbl.className="c-input c-radio";
        lbl.title = result[i].lyrics.replace(/"/g,"&quot;");
        $(lbl).append('<input type="radio" name="searchRadios" value="'+result[i].id+'">');
        $(lbl).append('<span class="c-indicator"> </span>');
        $(lbl).append( (result[i].book_ref ? '('+result[i].book_ref+') ' : ' ' ) + result[i].title + ' ');
        lbl.appendChild(sml);

        // 1. The overall DIV
        var div = document.createElement('div');
        div.className="c-inputs-stacked search-result-items"+ (i%2!=0 ? ' even' : '');
        $(div).attr('onclick', "$('#searchForSongsSubmit').click()");
        div.appendChild(spnYT);
        div.appendChild(spnSS);
        div.appendChild(lbl);

        $(elem).append(div);
    }
}

/*  show a preview of the selected song in the search area
*/
function showYTvideoPreview(ytid, that)
{
    // hide the search result for now
    $('.search-result-items').toggle();
    $(that).parent().parent().toggle();

    // show the YT preview DIV and insert the player code
    $('#show-video-clip').append('<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ytid+'" frameborder="0" allowfullscreen></iframe>');
    $('#show-video-clip').show();
}

/* execute the update via AJAX and show the new data on the page
*/
function updateSong(song_id)
{
    $('#searchSongModal').modal('hide');
    ;;;console.log('closing song form, got song id '+song_id);
    var item_id   = cSpot.item.item_id;
    var seq_no    = cSpot.item.seq_no;
    var myCell    = $('#tr-item-'+seq_no.replace('before-','').replace('after-','').replace('.','-'));
    myCell.children('.show-songbook-ref').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
    myCell.children('.show-song-title').text('');
    myCell.children('.show-youtube-links').html('');

    // update item via AJAX
    var actionURL = $('#searchSongForm').attr('data-action');
    $.post( actionURL, { song_id: song_id })

        .done(function(data) {
            // get global song list in order to show the newly added song in the UI
            var haystackMP = cSpot.songList;
            // on success, show new song data
            for (var i=0; i<haystackMP.length; i++) {
                if (haystackMP[i].id == song_id) {
                    myCell.children('.show-songbook-ref').text(haystackMP[i].book_ref);
                    myCell.children('.show-song-title').text(haystackMP[i].title);
                    myCell.children('.show-song-title').attr('data-original-title',$('#song_id').attr('title'));
                    var href = myCell.children().children('.edit-song-link').attr('href');
                    if (href) {
                        href = href.replace(myCell.data('oldSongId'),song_id);
                        myCell.children().children('.edit-song-link').attr('href', href);
                    }
                    break;
                }
            }
        })
        
        .fail(function(data) {
            myCell.children('.show-song-title').text('Failed! Press F12 for more');
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
}

/*  hide File-Category selector; show local-vs-remote-files choice
*/
function showLocalVersusRemoteButtons(that)
{
    // hide the file-category selector
    $(that).parent().parent().hide();
    var cat = $(that).val();

    // skip the next step (choice between upload and cspot images)
    // as category 'newest' is only for c-spot images
    if (cat == 'newest') {

        // go directly to the images selection
        // with a handle on that button as it contains important information....
        showImagesSelection( document.getElementById('btn-select-cspot-images') );
        return;
    }

    // get text-equivalent of the numeric value
    var catText;
    for (var i = 1; i < that.length; i++) {
        if (that[i].value == cat)
            catText = that[i].text;
    }
    // show selected category
    $('#show-selected-category').text(catText);

    // show the two buttons
    $('#show-location-selection').show();
}

/*  Show images from server for selection
*/
function showImagesSelection(that, ajax_url)
{
    // path to the images
    var img_path = that.dataset.imagesPath;

    // url for the AJAX call
    if (ajax_url==undefined)
        var ajax_url = that.dataset.ajaxUrl + '/' + $('#file_category_id').val();

    var maxVisible = 3;
    if ( $('#searchSongModal').is(':visible') )
        maxVisible = 2;

    // show the section that will hold the images
    $('.image-selection-slideshow' ).show();
    $('.show-next-image-arrows'    ).hide();
    $('.show-next-image-arrows'    ).attr('disabled', '');
    $('#images-for-selection-label').text('');
    $('#link-to-more-images'       ).html('');
    $('#show-images-for-selection' ).html( cSpot.const.waitspinner + ' loading...' );

    $.getJSON( ajax_url )

        .done(function(data) {

            ;;;console.log(data.total+' images found');

            // fill label text
            var lbl = 'Browse through the images and select one:';
            if (data.total == 0) {
                lbl = 'No images of this category found. Restart and select another one.';
                $('.show-next-image-arrows').hide();
            }
            $('#images-for-selection-label').text(lbl);

            // show bottom label
            lbl = 'Showing images '+data.from+' - '+data.to;
            if (data.total > data.to)
                lbl += '. Total: '+data.total+'. <a href="#" data-images-path="'+img_path+'" onclick="showImagesSelection(this,'+"'"+data.next_page_url+"'"+')">Get more</a>';
            $('#link-to-more-images').html(lbl);

            // activate the 'show-next-images' button
            $('.show-next-image-arrows').show();
            $('.show-next-image-arrows').last().removeAttr('disabled');
            $('#show-images-for-selection' ).html(''); // remove the wait spinner

            // add each image as an <img> element into the DOM, but only make the first 2 visible
            var showit = true;
            for (var nr = 0; nr < data.to; nr++) {

                if (nr>maxVisible-1) { showit = false; }

                // insert the images into the DOM
                insertNextSelectionImage( data.data[nr], '#show-images-for-selection', img_path, showit );
            }
        })

        .fail(function(data) {
            console.log('get failed!');
            console.log(data);
        });
}

/* append a new image into this element
*/
function insertNextSelectionImage(data, parentElem, path, visible)
{
    if (data==undefined) return;

    // create a new anchor element
    var anchor = document.createElement('a');
    anchor.href = '#';
    $(anchor).attr('onclick', 'addItemWithFileOrAddFileToItem('+data.id+')');

    // create a new img element
    var image = document.createElement('img');
    image.src = path + '/thumb-'+data.token;
    image.classList.add('slideshow-images');

    // insert the image into the anchor
    $(anchor).append(image);

    // hide if requested
    if (! visible) {
       $(anchor).hide();
    }

    // now insert all into the DOM
    $(parentElem).append(anchor);
}

/*  hide the current and show the next (or previous) images in the images selection 
*/
function showNextImages(direction)
{
    // get list of all image elements
    var all = $('#show-images-for-selection').children('a');

    // how many can be visible at one time?
    var showTogether = 2;
    if (document.location.pathname.search('/edit') > 1)
        showTogether = 3;
    var visible = showTogether;

    // iterate through the list
    if (direction=='forw') {

        for (var i = 0; i < all.length; i++) {

            if ( $(all[i]).is(':visible') ) {
                $(all[i]).hide();
                visible--;
            } else {

                if (visible < showTogether) {
                    $(all[i]).show();
                    visible++;
                    // disable the forward button if we just showed the last image(s)
                    if (i+1 == all.length)
                        $('.show-next-image-arrows').last().attr('disabled', '');
                    else
                        $('.show-next-image-arrows').first().removeAttr('disabled');
                }
            }
        }
    } 
    else {
        for (var i = all.length - 1; i >= 0; i--) {

            if ( $(all[i]).is(':visible') ) {
                $(all[i]).hide();
                visible--;
            } else {

                if (visible < showTogether) {
                    $(all[i]).show();
                    visible++;
                    // disable the forward button if we just showed the last image(s)
                    if (i == 0)
                        $('.show-next-image-arrows').first().attr('disabled', '');
                    else
                        $('.show-next-image-arrows').last().removeAttr('disabled');
                }
            }
        }
    }
}

/*  Add NEW item to plan with a file -  OR:  add a file to an existing plan item
*/
function addItemWithFileOrAddFileToItem(file_id)
{
    $('#show-images-for-selection' ).html( cSpot.const.waitspinner + ' one moment...' );
    $('.show-next-image-arrows'    ).hide();

    // we still need plan_id, seq_no, end perhaps item_id
    var plan_id = cSpot.item.plan_id;
    var item_id = cSpot.item.item_id;
    var seq_no  = cSpot.item.seq_no;

    // check if we are editing an item
    if (cSpot.item.action==undefined && cSpot.item.id != undefined) {
        item_id = cSpot.item.id;
        cSpot.item.action = 'add-file';
    }

    ;;;console.log('Uploading new file via AJAX - type: '+cSpot.item.action);


    // 1. File needs to be added to an existing item

    if ( cSpot.item.action == 'add-file' ) {
        $.post(
            cSpot.routes.apiAddFiles, 
            { 'item_id' : item_id, 'file_id' : file_id }
        )
        .done( function(data) {

            ;;;console.log("PHP Output:");
            ;;;console.log( data );
            successfullyAddedFileToItem(data.data);
        })
        .fail(function( data ) {

            console.log("AJAX Error Output:");
            console.log( data );            
            $('#search-result').html('Error! '+data);

        });
        return false;
    }


    // 2. Add new item with selected file attached

    // set the value in the form
    $('#file_id').val(file_id);
    $('#beforeItem_id').val(item_id);

    $('#searchSongModal').modal('hide');
    showSpinner();

    //submit the form
    $('#searchForSongsSubmit').click();
}


/*  upload nwe file via AJAX and show little icon when successful
*/
function uploadNewFile()
{
    ;;;console.log('Uploading new file via AJAX - Url: '+cSpot.item.actionUrl);

    $('#search-result').html(cSpot.const.waitspinner + ' uploading....');

    // make sure the song_id (even empty) is not transmitted via the form element
    $('#song_id').val(cSpot.item.song_id); // TODO: we have to insert it again later!

    var fd = new FormData(document.getElementById("searchSongForm"));

    $.ajax({
        url: cSpot.item.actionUrl,
        type: "POST",
        data: fd,
        processData: false,  // tell jQuery not to process the data
        contentType: false   // tell jQuery not to set contentType
    })
    .done(function( data ) {
        ;;;console.log("PHP Output:");
        ;;;console.log( data );
        successfullyAddedFileToItem(data);
    })
    .fail(function( data ) {

        console.log("AJAX Error Output:");
        console.log( data );        
        $('#search-result').html('Error! '+data);
    });
}

/*  show icon for the uploaded file
*/
function successfullyAddedFileToItem(data)
{
    resetSearchForSongs();
    $('#searchSongModal').modal('hide');

    $('#'+cSpot.item.buttonID).parent().prepend('<i class="fa fa-file-picture-o" title="'+data.filename+'"></i>');

    // was file added on the item detail page?
    if (  $('#col-2-file-add').length) {

        // reload the page until proper SPA code is written
        window.location.reload;

        // hide the elements
        $('#col-2-file-add').hide();
        $('#add-another-image-link').hide();
    }
}


/* Insert new item into a plan via AJAX
*/
function insertNewItemIntoPlan( plan_id, seq_no, song_id, comment )
{
    ;;;console.log('Inserting new item into plan via AJAX - PlanID: '+plan_id+', SeqNo: '+seq_no );

    // determine new sequence number
    sno = seq_no.split('-');
    if (sno[0]=='after') {
        seq_no = 1 * sno[1] + 0.1;
    }

    $.post( cSpot.routes.apiItems, 
    {
        'plan_id' : plan_id,
        'seq_no'  : seq_no,
        'song_id' : song_id,
        'comment' : comment,
    })
    .done(function(data){

        ;;;console.log('New item inserted! Seq_No: ' + seq_no);
        ;;;console.log(data);

        // only when we are in Presentation Mode
        if (sno.length > 1) {
            var nextButton = document.getElementById('go-next-item');
            // advance to next item (which now is the just inserted item!)
            nextButton.click();

            // this won't work when we already show the last item, as the button is disabled
            // so wee need another strategy - get the URL from the button and replace the 
            // current item_id with the item_id that we jsut received!

            // but first, clean up the item id....
            if (cSpot.item.item_id.substr(0,6)=='after-')
                cSpot.item.item_id = cSpot.item.item_id.split('-')[1]

            if (nextButton.getAttribute('disabled')=='disabled') {
                var newPath = document.getElementById('go-next-item')
                    .pathname.replace(cSpot.item.item_id,data.data.newest_item_id);
                ;;;console.log('now navigating to the new item: '+newPath);
                document.location.href = newPath;
            }
        }

    })
    .fail(function(data) {
        // show error somewhere...
        console.log(data);
    });
}



/* remove a single item
*/
function removeItem(that)
{
    myTR = that.parentElement.parentElement.parentElement.parentElement; // get handle on whole TABLE ROW
    myTD = that.parentElement.parentElement.parentElement;              // get handle on table CELL
    $(myTR).addClass('text-muted');                                    // 'mute' table row
    $(myTD).children().hide();                                        // hide action buttons
    $(myTD).append('<i class="fa fa-spinner fa-spin fa-fw"></i>');   // show spinner while updating

    var actionURL = $(that).data().actionUrl;                       // delete item via AJAX
    $.post( actionURL )
        .done(function(data) {
            $('.fa-spinner').hide();
            $(myTR).children('td').each(function(){
                $(this).addClass('trashed');
            })
            // on success, fade-out table row and show action buttons for hidden items
            $(myTR).slideUp(550, function() {
                $(myTR).addClass('trashed');
                $(myTD).children('.trashedButtons').show();
            });
            // update number of trashed items
            var trashedItemsCount = parseInt($('#trashedItemsCount').text());
            $('#trashedItemsCount').text(1+trashedItemsCount);
            $('#trashedItems').show();
        })
        .fail(function(data) {
            $(myTD).text('Failed! Press F12 for more');
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
}


function addScriptureRef()
{
    // get handle to table row containing the original comment
    var seq_no = cSpot.item.seq_no;
    var TRid = 'tr-item-'+seq_no.replace('.','-');

    // get new comment value
    var newText = $('#comment').val();

    var that = $('#'+TRid).children(".comment-cell");                 // show spinner while updating
    $(that).children(".comment-textcontent").html( cSpot.const.waitspinner );

    $.post( cSpot.routes.apiItemUpdate, { 
            value : newText,
            id    : $(that).children(".comment-textcontent").attr('id'),
        })
        .done(function(data) {
            resetCommentText(TRid, newText);
        })
        .fail(function(data) {
            $(that).children(".comment-textcontent").text('Failed! Press F12 for more');
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });

    // close modal
    $('#searchSongModal').modal('hide');
}

/* show comment text again
*/
function resetCommentText(id, newText) 
{
    that = $('#'+id).children(".comment-cell");
    $(that).children(".comment-textcontent").text(newText);
    if (! newText)      // only show 'edit' icon when comment is empty
        $(that).children(".fa-pencil").css('display', 'inline');
}


/*  delete comment text
*/
function eraseThisComment(that, item_id)
{
    console.log('trying to erase comment for item id '+item_id);

    $('#comment-item-id-'+item_id).html(cSpot.const.waitspinner);

    // get handle to table row containing the original comment
    var TRid  = $(that).parent().parent().attr('id');

    // compose id/value pair
    var id    = 'comment-item-id-'+item_id;
    var value = '_'; // underscore denotes an empty value in the ItemController

    $.post( cSpot.routes.apiItemUpdate, { 
            id    : id,
            value : value,
        })
        .done(function(data) {
            resetCommentText(TRid, data);
        })
        .fail(function(data) {
            $(that).children(".comment-textcontent").text('Failed! Press F12 for more');
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
}


/* Called from the Modal popup on the FILES LIST page, 
   this function will save the updated file information via AJAX */
function updateFileInformation()
{
    // get the old data
    var fileID   = $('#file-id').val();
    var dispElem = $('#file-'+fileID);
    var oldData  = $(dispElem).data('content');

    // get the new data
    var newFn = $('#filename').val()
    var newFC = $('#file_category_id').val()

    // compare
    if (oldData.file_category_id == newFC 
             && oldData.filename == newFn) return;

    // get the action URL
    var actionURL = $('#file-id').data('action-url')+fileID;

    // update via AJAX 
    $.post( actionURL, { id: fileID, filename: newFn, file_category_id: newFC })
        .done(function(data) {
            dispElem.find('.fileshow-filename').text(newFn);
            dispElem.find('.fileshow-category').text($('#file_category_id option:selected').text());
        })
        .fail(function(data) {
            dispElem.find('.fileshow-filename').text("Update failed! Please notify admin! " + JSON.stringify(data));
        });
    
    // close the modal and update the data on the screen
    $('#fileEditModal').modal('hide');
}



/*  Report song to CCLI  and set field 'reported_at'
*/
function reportSongUsageToCCLI(that, item_id, reported_at)
{
    var currHtml = that.innerHTML;

    // show spinner while updating
    $(that).html(cSpot.const.waitspinner);

    if (reported_at==null) {

        // create midnight today
        var dt = new Date();
        dt.setHours(0,0,0,0);

        // update reported_at to today with time = 00:00
        $.post(
            cSpot.routes.apiItemUpdate, 
            {
                'id'    : 'reported_at-item-id-'+item_id,
                'value' : dt.toDateString(),
            }
        )
        .done( function(data) {
            console.log(data);
            $(that).html(currHtml);         // restore old button content 

            $(that).removeClass('btn-outline-danger');
            $(that).addClass('btn-outline-warning');

            // change last parameter in 'onclick' parameter of 'that' to 'data'(current date)
            $(that).attr('onclick', 'reportSongUsageToCCLI(this, '+item_id+', "'+data+'")');
            $(that).attr('href', '#');      // no more linking to CCLI...
            $(that).removeAttr('target');

            $(that).attr('title', 'Please confirm here when Song Usage Report to CCLI has been completed!');
            $(that).tooltip('dispose');     // since title has changed, remove old tooltip
            $(that).tooltip();              // and generate a new one
            $(that).tooltip('show');
        })
        .fail( function(data) {
            console.log('POST update failed! ');
            console.log(data);
        });
    }
    else {
        var dt = new Date();
        // update reported_at to today with time = 00:00
        $.post(
            cSpot.routes.apiItemUpdate, 
            {
                'id'    : 'reported_at-item-id-'+item_id,
                'value' : dt.toJSON(),
            }
        )
        .done( function(data) {
            console.log(data);
            
            $(that).html('<i class="fa fa-copyright"></i><i class="fa fa-check"></i>');
            $(that).addClass('narrow');
            $(that).removeClass('btn-outline-warning');
            $(that).removeClass('m-r-1');
            $(that).addClass('btn-outline-success');

            $(that).attr('title', 'Song Usage has already been reported to CCLI.');
            $(that).tooltip('dispose');     // since title has changed, remove old tooltip
            $(that).tooltip();              // and generate a new one
            $(that).tooltip('show');
        })
        .fail( function(data) {
            console.log('POST update failed! ');
            console.log(data);
        });
    }
}


/*\
|*#===========================================================================================    END   OF SPA UTILITIES
\*/




