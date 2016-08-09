
/*\
|*|
|*|
|*#=========================================================================================== SPA UTILITIES
|*|
|*|
\*/




/*
    allow Admins/Authors/Plan owners to delete an attached file (image)    
*/
function deleteFile(id)
{
    // TODO: Prompt for confirmation as this is irrevocable:
    if (! confirm('Are you sure to finally remove this file?')) {return;}
    // get token from form field
    $.ajax({
        url:    '/cspot/files/'+id+'/delete', 
        method: 'DELETE',
    }).done(function(data) {
        $('#file-'+id).html(data.data);
    }).fail(function(data) {
        if (data.responseJSON) {
            alert("image deletion failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("image deletion failed! "+JSON.stringify(data));
        }
    });
}

/*  
    unlink FILE from its item
*/
function unlinkFile(item_id, file_id)
{
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


/*  
    unlink SONG from its item
*/
function unlinkSong(item_id, song_id, plan_url)
{
    $.ajax({
        url:    '/cspot/items/'+item_id+'/unlinkSong/'+song_id+'', 
        method: 'PUT',
    }).done(function(data) {
        console.log(data);
        // go back to plan 
        window.location.href = plan_url;
    }).fail(function(data) {
        if (data.responseJSON) {
            alert("song unlinking failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("song unlinking failed! "+JSON.stringify(data));
        }
    });
}



/**
 * Record a user's availability for a certain plan
 * (called when user clicks on the 'available' icon on plans.blade.php)
 */
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



/* 
    Called from the Modal popup on the FILES LIST page, 
    this function will save the updated file information via AJAX
*/
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


/*
    User has selected WHAT he wants to insert, 
    now we present the appropriate input elements
*/
function showModalSelectionItems(what)
{
    $('.modal-pre-selection').hide();               // hide all pre-selection parts of the modal
    $('.modal-select-'+what).show();                // show all parts for selecting a song or entering a comment
    $('#searchSongModalLabel').text('Insert '+what);
    $('.modal-input-'+what).focus();
    // show submit button for comments or scripture
    if (what!='song') {
        $('#searchForSongsSubmit').show();
    }
}
/* 
    Reset the song search form
*/
function resetSearchForSongs() 
{
    $('.modal-select-song').hide();
    $('.modal-select-comment').hide();
    $('.modal-select-scripture').hide();
    $('.modal-pre-selection').show();
    $('#modal-show-item-id').text('');
    $('#searching').hide();    
    $('#search-result').html('');
    $('#searchForSongsSubmit').hide();
    $('#MPselect').val(0);
    $('#search-string').val('');
    $('#search-string').focus();
    $('#search-action-label').text('Full-text search incl. lyrics:');
    $('#txtHint').html('');
    $('#haystack').val('');
}

/* 
    Called from the Modal popup on the PLAN OVERVIEW page, 
    this function searches for songs, presents a list and/or 
    song history information; uses AJAX to do the full-text search
*/
function searchForSongs(that)
{    
    // are we still searching or has the user already selected a song?
    var modus = 'selecting';
    if ( $('#searchForSongsButton').is(':visible') ) {
        var search       = $('#search-string').val();
        var mp_song_id   = $('#MPselect').val();
        var haystack_id  = $('input[name=haystack]:checked', '#searchSongForm').val();
        if (search=='' && mp_song_id==0  && haystack_id==undefined) {
            return;         // search string was empty...
        }
        if (mp_song_id>0) {
            search = '(song id: '+mp_song_id+')';    // MP song selection is preferred
        }
        if (haystack_id) {
            search = '(song id: '+mp_song_id+')'; 
            mp_song_id = haystack_id;
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
                if (result.length==0) {
                    $('#search-action-label').text('Nothing found for "'+search+'", please try again:');
                    $('#searchForSongsButton').toggle();
                    $('#searchForSongsSubmit').toggle();
                    $('#searching').hide();  
                    $('.search-form-item').show();  
                    $('#search-string').focus();
                    return;
                }
                $('#search-action-label').text('Click the desired Song:');
                $('#searching').hide();
                $('#searchForSongsSubmit').hide();

                var html = '';  
                // create the HTML to present the search result to the user for selection
                for (var i = 0; i < result.length; i++) {
                    if (result[i].id==0)
                        continue;                       // ignore song with id # 0
                    var count = result[i].plans.length; // number of plans that already used this song
                    var lastPlanDate = false;           //date of last time this song was used ("2016-05-08 00:00:00")
                    if (result[i].plans.length) {
                        lastPlanDate = result[i].plans[0].date; 
                    }
                    html += '<div onclick="$(\'#searchForSongsSubmit\').click()" class="c-inputs-stacked'+ (i%2==0 ? ' even' : '') +'">';
                    html +=     '<label class="c-input c-radio" title="';
                    html +=         result[i].lyrics.replace(/"/g,"&quot;") + '"><input type="radio" name="searchRadios" value="';
                    html +=         result[i].id +'"><span class="c-indicator"></span>';
                    html +=         (result[i].book_ref ? '('+result[i].book_ref+') ' : ' ')  + result[i].title + ' ';
                    html +=         '<small>'+result[i].title_2+'<br><span class="pull-xs-right">';
                    html +=             '<b>Last used:</b> <span class="label label-default">'
                    html +=                 ( lastPlanDate ? moment(lastPlanDate, 'YYYY-MM-DD HH:mm:ss').fromNow() : 'never used!!');
                    html +=             '</span> Total: <b'+ (count>25 ? ' class="red">' : '>') + count + '</b> times</span></small>';
                    html +=     '</label></div>' ;
                }
                $('#search-result').html(html);
            })
            .fail(function(data) {
                $('#searching').hide();
                console.log(data);
                $('#search-result').text("Search failed! Please notify admin! " + JSON.stringify(data));
            });
    } 
    else {
        // which song was selected?
        var song_id = $('input[name=searchRadios]:checked', '#searchSongForm').val();

        var plan_id = $('#plan_id').val();
        var seq_no  = $('#seq-no' ).val();

        // check if user entered a comment
        var comment = $('#comment' ).val();

        // was this called via 'showUpdateSongForm' function?
        if (plan_id=="update-song") {
            if (song_id!=undefined) {
                // attach lyrics to song_id input field, so that when user selects this song, we can attach it as title to the table cell
                // (we get this from the selection in the search results to whose parent element the lyrics were attached)
                $('#song_id').attr(  'title',  $('input[name=searchRadios]:checked', '#searchSongForm').parent().attr('title')  );
                updateSong(song_id);
            }
            return;
        }
        // was this called via 'AddScriptureRef' button?
        if (plan_id=="update-scripture") {
            addScriptureRef(that);
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
        console.log('Writing the selected song_id as value of the hidden input element: '+song_id)

        // restore the original search form
        $('#searchSongModal').modal('hide');    // close the modal
        $('#search-result').html('');           // remove the search results
        $('#search-string').val('');            // reset the search string
        $('#searching').hide();                 // hide the spinner

        // for some reason, the form doesn't submit if only a comment was given...
        if (comment) {
            document.getElementById('searchSongForm').submit();
        }
    }

}


/*
    execute the update via AJAX and show the new data on the page
*/
function updateSong(song_id)
{
    $('#searchSongModal').modal('hide');
    console.log('closing song form, got song id '+song_id);
    var item_id   = $('#beforeItem_id').val();
    var seq_no    = $('#seq-no').val();
    var myCell    = $('#tr-item-'+seq_no.replace('.','-'));
    myCell.children('.show-songbook-ref').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
    myCell.children('.show-song-title').text('');
    myCell.children('.show-youtube-links').html('');

    // update item via AJAX
    var actionURL = $('#searchSongForm').attr('data-action');
    $.post( actionURL, { song_id: song_id })
        .done(function(data) {
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



/*
    remove a single item
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


function addScriptureRef(that)
{
    // get handle to table row containing the original comment
    var seq_no = $('#seq-no').val();
    var TRid = 'tr-item-'+seq_no.replace('.','-');

    // get new comment value
    var newText = $('#comment').val();

    // send update via AJAX
    var actionURL = __app_url + '/cspot/api/items/update';

    that = $('#'+TRid).children(".comment-cell");                 // show spinner while updating
    $(that).children(".comment-textcontent").html('<i class="fa fa-spinner fa-spin"></i>');

    $.post( actionURL, { 
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
/*
    show comment text again
*/
function resetCommentText(id, newText) {
    that = $('#'+id).children(".comment-cell");
    $(that).children(".comment-textcontent").text(newText);
    if (! newText)      // only show 'edit' icon when comment is empty
        $(that).children(".fa-pencil").css('display', 'inline');
}

