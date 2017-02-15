
/*\
|*|
|*|
|*#=========================================================================================== SPA UTILITIES
|*|
|*|
\*/

/* for eslint */
if (typeof($)===undefined) {
    var $, cSpot;
}
 


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
    $('#user-available-for-plan-id-'+plan_id).html( cSpot.const.waitspinner );

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
        $.get( cSpot.appURL+'/cspot/plans/'+plan_id+'/team/available/'+that.checked)
        .done(function() {
            $('#user-available-for-plan-id-'+plan_id).html( that.checked ? '&#10003;' : '&#10007;');
            if (teamPage) { location.reload(); }
        })
        .fail(function() {
            $('#user-available-for-plan-id-'+plan_id).text( "error" );
        })        
    }
}




/*\__________________________________________________________________________  ONSONG data handling
\*/


/*
    Prepare New OnSong Row 
    - launch modal for user to select the part code
    - prepare textarea for user to enter actual onsong data
    - save as a new row in the list of onSong parts
*/
function insertNewOnSongRow()
{
    // make sure no other element with this ID exists (from a previous adding)
    $('#adding-new-song-part').attr('id', '');

    // collapse the current song parts
    $('.cell-part-text').removeClass('show');    

    // clone the existing and hidden, empty row and show it
    $('#new-onsong-row').clone().attr('id', 'very-new-onsong-row').appendTo('#onsong-parts');
    $('#new-onsong-row').attr('id', 'adding-new-song-part');
    // restore the original row again
    $('#very-new-onsong-row').attr('id', 'new-onsong-row');

    $('#adding-new-song-part').fadeIn();
    // make sure no other row has this class
    $('#adding-new-song-part').siblings('div').removeClass('table-success');
    $('#adding-new-song-part').addClass('table-success');

    // pre-select next possible OnSong part
    $('#new-onsong-part-selection').val( findNextPossibleOnSongPart() );

    // hide all other action buttons
    $('.cell-part-action').hide();
    $('.for-existing-items').hide();
    $('.toggle-onsong-buttons').hide();
    // hide "add" link and show row with input hints
    $('.insertNewOnSongRow-link').hide();
    $('.show-onsong-format-hint').show();

    // show help info and save/cancel buttons
    $('#adding-new-song-part > .cell-part-text > .text-editor-hints').show();
    $('.text-editor-delete-button').hide(); // delete button not needed atm

    // make sure this part is not collapsed
    $('#adding-new-song-part > .cell-part-text').addClass('show');

    // make sure to hide the new row if user clicks outside the modal to hide it        
    $('#selectSongPartCodeModal').on('hidden.bs.modal', function () {
        removeNewOnSongRow($('#adding-new-song-part'));
    });
    // make sure all is in the visible viewport
    window.location.href = "#tbl-bottom";
    $('#selectSongPartCodeModal').on('shown.bs.modal', function () {
        // move the part-type selection dialog down to the bottom
        var mo = $('.modal-content.select-songpart-code')[0];
        $(mo).position({my:'left top', at:'left bottom', of:$('#adding-new-song-part > .cell-part-name')});
        // set the focus on the part-type selection 
        $('#new-onsong-part-selection').focus();
    })

    // call Modal for onsong part name selection
    $('#selectSongPartCodeModal').modal('show');
}

function editPartNameForSelection(code, what) 
{
    var sel = $('#new-onsong-part-selection');
    if (what=='remove') {
        for (var i = 0; i < sel[0].length; i++) {
            if ($(sel[0][i]).val() == code) {
                $(sel[0][i]).remove();
                return true;
            }
        }
    }

    if (what=='add')
        sel.append('<option data-code="'+code.code+'" value="'+code.id+'">'+code.name+'</option>');
}

/* insert the selected part code from the Modal into the new song aprt row
*/
function insertSelectedPartCode()
{
    // remove that event from the modal      
    $('#selectSongPartCodeModal').off('hidden.bs.modal');

    var newCodeId = $('#new-onsong-part-selection').val();
    var newCodeCode = $($('#new-onsong-part-selection :selected')[0]).data('code');
    var newCodeName = $($('#new-onsong-part-selection :selected')[0]).text();

    if (newCodeId) {
        // write html code to show Part name and part code
        var html = newCodeName + (newCodeCode!='m' ? ' <span class="text-white">('+newCodeCode+')</span>' : '');
        
        if (newCodeCode=='m')
            $('.hints-for-onsong-metadata').show();

        $('#adding-new-song-part > .cell-part-name').attr('href', '#collapse-'+newCodeCode);
        $('#adding-new-song-part > .cell-part-name').attr('aria-controls', 'collapse-'+newCodeCode);
        $('#adding-new-song-part > .cell-part-name > h6 > a > .song-part-name').html(html);
        $('#selectSongPartCodeModal').modal('hide');

        // add selected code id as data attribute to the row
        $($('#adding-new-song-part')[0]).data('part-id', newCodeId);

        $('#adding-new-song-part > .cell-part-text > .plaintext-editor').focus();

        return true;
    }
    // user didn't select a song part name
    $('#part-selection-error-msg').show();
    return false;
}

/* Check all existing song parts and determine which one could be the next....
*/
function findNextPossibleOnSongPart() {

    // get the exising onsong parts, then guess the next one
    if (cSpot.item.song.onsongs) {
        var max = 0, guess;
        cSpot.item.song.onsongs.forEach( function(elem) {
            var code = elem.song_part.code;
            if ( !isNaN(code))
                max = Math.max(max,code);
            if (code=='c') 
                guess = 'c';
        });
        // we already have a chorus
        if (guess=='c') max = Math.max(max,1);

        // we have verse 1 but no chorus yet
        if (max==1 && guess===undefined)
            guess='c';
        else 
            guess = 1*1+max;

        ;;;console.log('guessing next possible song part as '+guess);
        return cSpot.song_parts_by_code[guess].id;
    } 
    // or use the sequence for guessing the next one
    else if (cSpot.item.song.sequence) {
        return 1;
    }
    return 1;
}


function removeNewOnSongRow(row)
{
    if ($(row).prop('id')!= "adding-new-song-part"  &&  $('#adding-new-song-part').is(':visible'))
        return;

    // make sure we have no "outdated" min-height
    $(row).css('min-height', 0);

    $('.show-onsong-format-hint').hide();
    $('.hints-for-chords-over-lyrics-editor').hide();
    $('.hints-for-plaintext-editor').hide();
    $('.text-editor-hints').hide();
    $('.error-msg').hide();
    $('.insertNewOnSongRow-link').show();
    $('.for-existing-items').show();
    $('.toggle-onsong-buttons').show(); 
    
    var onsong_id = $(row).data('onsong-id') || 0; // (undefined for new elements)

    // remove row in case of a just added, empty row
    if (!onsong_id) {
        row.remove();
        return;
    }
    
    $(row).removeClass('table-warning');

    // reinstate row layout for existing rows
    // input area
    var onsongArea = $(row).children('.cell-part-text');
    $(onsongArea).children('.show-onsong-text').show();
    $(onsongArea).children('.write-onsong-text').show();
    $(onsongArea).children('textarea').hide();

    $('.show-onsong-text').addClass('link');

    // show correct action buttons
    $('.cell-part-action').hide();
    var cell =  $(onsongArea).children('.cell-part-action');
    $(cell).children('.for-existing-items').show(); 
}

function closeAdvOnSongEditor(row)
{
    row.removeClass('table-warning');

    var cell = row.children('.cell-part-text'); 
    cell.children('.editor-hints').hide(); 
    cell.children('.cell-part-action').children('.for-existing-items').show(); 
    cell.children('.show-onsong-text').show(); 
    // empty and hide the editor
    cell.children('.advanced-editor').html('').hide();

    removeNewOnSongRow(row);
}


/* show or hide the Editor selection buttons
*/
function toggleOnSongEditButtons(row) 
{
    // make sure we have no "outdated" min-height
    row.css('min-height', 0);

    // first make sure that we are not currently ADDing a new song part
    if (! $('.show-onsong-format-hint').is(':visible')) {

        // are we already EDITing another song part?
        if ($('.cell-part-action').is(':visible')) 
        {
            $('.cell-part-action').hide();
            $('#insertNewOnSongRow-link').show();
            $('.toggle-onsong-buttons').show();
        } 
        else 
        {
            // $('.cell-part-action').hide();
            $('.toggle-onsong-buttons').hide();
            $('.text-editor-delete-button').hide(); // make sure the delete button is hidden at first
            $('.hints-for-onsong-metadata').hide();

            // if the song part contains no chords, we can directly start the plaintext editor!
            // get handle on input elements etc
            var cell = row.children('.cell-part-text');
            var text = cell.children('.plaintext-editor').val();

            // write original song text into a data attribute
            //  in order to later be able see if anything was changed 
            row.data('orig-onsong-text', text);

            row.addClass('table-warning');
            $('.show-onsong-text').removeClass('link');

            // prevent opening of other editors
            $('.insertNewOnSongRow-link').hide();
            $('.show-onsong-format-hint').show();

            // check if this song part is still part of the SEQUENCE code list
            isThisPartInSequenceListThenHideDeleteBtn(row)

            // check if the original text and the converted text are the same (indicating that it contains no chords)
            if (text.trim() == convertOnSongToChordsOverLyrics(text).trim()) {
                // also show the delete button now
                $('.text-editor-delete-button').show();

                showPlaintextEditor(row);

                // if this is the part containing the metadata, we will show a different help section
                if (row.hasClass('onsong-meta-data')) {
                    // #tbl-row-[nnn] > td > div.text-editor-hints.small.hidden > p > span.hints-for-onsong-chords-part
                    cell.children('.text-editor-hints').children('.card').children('.card-block').children('.hints-for-onsong-metadata').show();
                }
                return;
            }

            // now we can show the buttons to select an EDITor
            $(cell).children('.cell-part-action').show();

            // make sure there is enough room for the buttons
            if (cell.height() < $(cell).children('.cell-part-action').height())
                cell.height( $(cell).children('.cell-part-action').height() );

            // position the buttons at the bottom of the cell
            $(cell).children('.cell-part-action').position({my: 'right bottom', at: 'right bottom', of: cell});
        }
    }
}       

// Compares the list of codes in the SEQUENCE field with the code of the current row.
// If the code is listed in the sequence, the DELETE button will not be shown
function isThisPartInSequenceListThenHideDeleteBtn(row)
{
    var code = row.children('.cell-part-name').data('part-code');
    var seq = getPartsSequenceListFromDragZone();
    if (seq.indexOf(code) >= 0)
        $('.text-editor-delete-button').hide();
    else
        $('.text-editor-delete-button').show();
}

/* show the Advanced OnSong editor
*/
function showAdvOnSongEditor(row) 
{
    // keep the height of the row while editing
    var height = row.css('height');
    row.css('min-height', height);

    // hide the editor selection buttons
    row.children('.cell-part-text').children('.cell-part-action').children('.for-existing-items').hide(); 

    // get the existing OnSong data
    var textDiv = row.children('.cell-part-text').children('textarea');
    var onSongData = textDiv.val();

    // divide text into lines and add them as individual divs
    var newHtml = '';
    var lines = onSongData.split('\n');
    lines.forEach( function(elem) {
        newHtml += '<div class="onsong-edit-lines pl-2">'+splitOnSongLines(elem)+"</div>\n";
    });

    // show the editor, hide the other stuff
    row.children('.cell-part-text').children('.show-onsong-text').hide(); 
    row.children('.cell-part-text').children('.editor-hints').show(); 
    row.children('.cell-part-text').children('.advanced-editor').html(newHtml).show(); 


    // make the chords draggable
    $('.onsong-edit-lines').sortable({
        axis: "x",
        opacity: 0.5,
        cursorAt: { left: 5 },
        placeholder: "ui-state-highlight",
        start: function(event, ui) {
            ui.placeholder.html('_'.repeat(ui.helper.outerWidth()/10));
        },
        containment: "#"+row.children('.cell-part-text').children('.advanced-editor').attr('id'),
    });
}


/* show OnSong Plaintext Editor
*/
function showPlaintextEditor(row)
{
    // keep the height of the row while editing
    var height = row.css('height');
    row.css('min-height', height);

    // show correct action buttons
    $('.for-existing-items').hide(); 

    // get handle on input elements etc
    var cell = row.children('.cell-part-text');
    // hide display-only text, show writeable input area
    cell.children('.cell-part-action').hide();
    cell.children('.show-onsong-text').hide();
    cell.children('.write-onsong-text').hide();
    cell.children('.plaintext-editor').show();

    cell.children('.text-editor-hints').show();

    // textarea height according to the number of lines in the OnSong text - but at least 3
    cell.children('.plaintext-editor').attr(
        'rows', 
        Math.max(cell.children('.plaintext-editor').val().split('\n').length, 3)
    );
    cell.children('.plaintext-editor').focus();
}


/*  Show "Chords-over-Lyrics" Editor

    Converts the OnSong data into chords-over-lyrics format.

    This editor allows for editing the plain lyrics, without the interspersed chords.
    However, the user has to make sure that the chords over the lyrics are still in the right place.
    When being saved, the data willl be converted back into the OnSong format.
*/
function showChOLyEditor(row)
{
    // keep the height of the row while editing
    var height = row.css('height');
    row.css('min-height', height);

    // show correct action buttons
    $('.for-existing-items').hide(); 

    // get handle on input elements etc
    var cell = row.children('.cell-part-text');
    // hide display-only text, show writeable input area
    cell.children('.cell-part-action').hide();
    cell.children('.show-onsong-text').hide();
    cell.children('.write-onsong-text').hide();
    cell.children('.chords-over-lyrics-editor').show();

    cell.children('.text-editor-hints').show();

    // get original OnSong data and convert it to chords-over-lyrics format
    var text = cell.children('.plaintext-editor').val();
    text = convertOnSongToChordsOverLyrics(text);
    cell.children('.chords-over-lyrics-editor').val(text);

    // textarea height according to the number of lines in the OnSong text - but at least 3
    cell.children('.chords-over-lyrics-editor').attr(
        'rows', 
        Math.max(cell.children('.chords-over-lyrics-editor').val().split('\n').length, 3)
    );
    cell.children('.chords-over-lyrics-editor').focus();
}


function deleteOnSongText(row)
{
    if ( confirm('This cannot be undone. Are you sure?') )
        saveNewOnSongText(row, 'delete');
}


function saveNewOnSongText(row, del)
{
    // make sure we have no "outdated" min-height
    row.css('min-height', 0);

    // hide unneeded parts and reset bg color
    $('.show-onsong-format-hint').hide();
    $('.error-msg').hide();
    $('.new-onsong-field').css('background-color', 'inherit');

    // determine the area to show the waitspinner etc.
    var cell;
    if (del == 'delete')
        cell = $(row).children('.cell-part-text'); // this is the actual data AND the hints and buttons
    else
        cell = $(row).children('.cell-part-text').children('.text-editor-hints'); // only hints and buttons

    // different cell for Adv.Editor:
    if (!$(cell[0]).is(':visible'))
        cell = $(row).children('.cell-part-text').children('.editor-hints');

    // verify input data from a newly added row
    var part_id = $($('#adding-new-song-part')[0]).data('part-id');

    var onsong_id = row.data('onsong-id') || false; // (undefined for new elements)
    if (onsong_id && !part_id)
        part_id = $(row).data('part-id') || false;   // for existing elements

    // is the text from the plaintext or the Chords-over-Lyrics editor?
    var textarea = row.children('.cell-part-text').children('textarea');
    var text = '';
    if ( textarea.length>1  &&  $(textarea[1]).val() )
        // use the text from the chords-over-lyrics editor if present
        text = $(textarea[1]).val() || $(textarea[0]).val();
    else 
        text = $(textarea).val();

    // no chords text provided
    if (!text) {
        $(textarea).focus();
        row.children('.cell-part-text').children('.error-msg').show();
        $(textarea).css('background-color', 'red');
        return;
    }

    // check if it is 'chords over lyrics' format!
    if ( text.indexOf('[') < 0  &&  text.indexOf(']') < 0  &&  text.indexOf("\n") > -1 )
        text = joinLyricsAndChordsToOnSong(text);


    // check if anything was changed
    if (del != 'delete'  &&  text == row.data('orig-onsong-text')) {
        $(textarea).focus();
        row.children('.cell-part-text').children('.error-msg').text('Nothing was changed.');
        row.children('.cell-part-text').children('.error-msg').show();
        return;
    }


    // all good, we can proceed. Hide the action buttons
    var oldCellHtml = cell.html(); 
    cell.html('<div class="bg-warning text-white fully-width text-xs-center"> '+cSpot.const.waitspinner
        + (del == 'delete' ? ' deleting ...' : ' saving ...') + '</div>'); 

    // is this a delete request?
    if (del == 'delete') {
        text = '_'; // the controller will interpret '_' as a request to delete this item
        var my = row.children('.cell-part-name').text().trim().split('(');
        if (my.length>1) {
            var cd = my[1].split(')')[0];
            var nm = my[0].trim();
        }
        var code = { 
            'id'   : part_id, 
            'code' : cd, 
            'name' : nm,
        };
    }

    // save data via AJAX
    var table = $('#onsong-parts');
    var song_id = $(table).data('song-id');
    var save_onsong_url = $(table).data('update-onsong-url');

    $.post(save_onsong_url, {
            'onsong_id' : onsong_id,
            'song_id' : song_id,
            'part_id' : part_id,
            'text'    : text,
        })

        .done( function(data) {

            // remove waitspinner
            cell.html(oldCellHtml); 

            // no proper data returned if user is not authorised for this call
            if (data.data === undefined) {
                removeNewOnSongRow(row); // remove the editor hints and buttons
                row.children('.cell-part-text').children('.write-onsong-text').html('You are not authorised for this request.').show();
                row.children('.cell-part-text').children('textarea').hide();
                row.children('.cell-part-text').children('.cell-part-action').hide();
                return false;
            }

            // insert success data into the new table row or the existing row (for updates)
            row.children('.cell-part-text').children('.write-onsong-text').html(data.data.text).show();
            // show it as chords over lyrics
            rewriteOnsong(row.children('.cell-part-text').children('.show-onsong-text'));
            // also write it into the textarea for further edits in this session
            if ( textarea.length>1) {
                $(textarea[0]).val(data.data.text);
                $(textarea[1]).val('');
            } else
                $(textarea).val(data.data.text);

            // for new rows
            if (!onsong_id) {
                // add this to the local representation of the song
                cSpot.item.song.onsongs.push(data.data);

                // write new onsong id into the data and other attributes of the new row and sub-cells
                row.data('onsong-id', data.data.id);
                row.data('part-id', data.data.song_part_id);
                row.children('.cell-part-text').children('advanced-editor').attr('id', 'advanced-editor-'+data.data.id);
                row.children('.cell-part-text').attr('id', 'collapse-'+data.data.song_part.code);
                row.children('.cell-part-name').data('partCode', data.data.song_part.code);

                // add the new part codes to the list of draggable codes
                $('#song-parts-drag-zone').append('<span class="p-1 rounded edit-chords partcodes-draggable bg-warning text-white mr-1" id="partcodes-draggable-' +
                    data.data.song_part.code+'">' + data.data.song_part.code+"</span>\n");
                makePartCodesDraggable();
                $('#submit-sequence-button').show(); // show save seq. button
                $('#song-parts-sequence').show();   // make sure the sequence area is visible
                $('.no-onsong-sequence-help-text').hide(); // hide the help text for adding new parts

                // make sure this part name isn't used a 2nd time for this song
                editPartNameForSelection(data.data.song_part_id, 'remove');

                removeNewOnSongRow(row); // remove the editor hints and buttons

                window.location.href = '#tbl-bottom';
            }
            // for existing rows
            else  {
                // was it a delete request?
                if (data.data == '_') {
                    // remove this part from the local object also
                    removeFromLocalOnSongParts( row.data('onsongId') );
                    // we can put this partname back into the selection
                    editPartNameForSelection(code, 'add');

                    // remove from draggable list and show save button
                    $('#partcodes-draggable-'+cd).remove();
                    $('#submit-sequence-button').show();

                    // drop from out of the global cSPOT variable
                    //TODO TODO

                    row.remove();
                } 
                else {
                    row.children('.cell-part-text').children('textarea').hide();
                    $('.cell-part-action').hide();
                    // if we used the Adv. ONSong editor:
                    closeAdvOnSongEditor(row);
                }
                removeNewOnSongRow(row);
            }
            // enable ADD button
            $('.insertNewOnSongRow-link').show();
            row.removeClass('table-warning');
            row.addClass('table-success');
        })
        .fail(function(data) {
            // show error
            console.log(data);
            cell.html(oldCellHtml);
    });
}


function removeFromLocalOnSongParts(which)
{
    cSpot.item.song.onsongs.forEach( function(elem, idx, arr) {
        if (elem.id == which)
            cSpot.item.song.onsongs.splice(idx,1);
    });
}


/* get the changed data from the OnSong editor back into the textarea
*/
function submitEditedOnSong(row)
{
    // get the old OnSong data
    var textDiv = row.children('.cell-part-text').children('textarea.plaintext-editor');

    // the edited OnSong data
    var newData = '';
    var lines = row.children('.cell-part-text').children('.advanced-editor').children('div');
    // we must take care of newLine !
    $.each(lines, function(elem) {
        newData += $(lines[elem]).text()+"\n";
    });

    // remove the last newLine character!
    newData = newData.substring(0, newData.length-1);

    if (newData == textDiv.val()) {
        closeAdvOnSongEditor(row);
        return;
    }

    // write the edited data into the display area and the textarea input
    textDiv.val(newData);

    // then submit the new data to the host
    saveNewOnSongText(row);
}

/* Split OnSong lines (with interspersed chords in square brackets) 
    into spans of chords and lyrics 
*/
function splitOnSongLines(line) {
    var spans = '';

    // split the text into chars
    var parts = line.split('[');

    for (var i = 0; i < parts.length; i++) {
        var spl = parts[i].split(']');
        // does this part contain chords AND lyrics?
        if (spl.length>1) {
            spans += createNewSpan(spl[0], true);
            spans += splitLyricsToSpans(spl[1]);
        } else {
            spans += splitLyricsToSpans(spl[0]);
        }
    }

    return spans;
}

function splitLyricsToSpans(line) {
    var spans = '';
    var chars = line.split('');
    for (var i = 0; i < chars.length; i++) {
        spans += createNewSpan(chars[i]);
    }
    return spans;
}

function createNewSpan(ch, isChord) {
    var span = '<span';
    if (isChord) {
        span += ' class="px-0 edit-chords bg-warning btn btn-sm">';
        span += '<span class="invisible">[</span>'+ch;
        span += '<span class="invisible">]</span></span>';
    } 
    else 
        span += '>'+ch+'</span>';
    return span;
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
    $('#file-figure-'+id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    // get token from form field
    $.ajax({
        url:    '/cspot/files/'+id+'/delete', 
        method: 'DELETE',
    })
    .done(function(data) {
        $('#file-'+id).html(data.data);
        // different id on Song Details page
        $('#file-figure-'+id).html(data.data);
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

/* unlink FILE (bg image) from Plan Item
*/
function unlinkFile(item_id, file_id)
{
    // show wait spinner
    $('#file-'+file_id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
    $('#file-figure-'+file_id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.ajax({
        url:    cSpot.routes.apiItemsFileUnlink,
        data:   {'item_id': item_id, 'file_id': file_id}, 
        method: 'PUT',
    }).done(function(data) {
        $('#file-'+file_id).html(data.data);
        $('#file-figure-'+file_id).html(data.data);
    }).fail(function(data) {
        if (data.responseJSON) {
            alert("image unlinking failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("image unlinking failed! "+JSON.stringify(data));
        }
    });
}


/* unlink FILE from Song item
*/
function unlinkSongFile(song_id, file_id)
{
    // show wait spinner
    $('#file-figure-'+file_id).html('<i class="fa fa-spinner fa-spin fa-fw"></i>');

    $.ajax({
        url:    cSpot.routes.apiSongsFileUnlink,
        data:   {'song_id': song_id, 'file_id': file_id}, 
        method: 'PUT',
    }).done(function(data) {
        $('#file-figure-'+file_id).html(data.data);
    }).fail(function(data) {
        if (data.responseJSON) {
            alert("image unlinking failed! Error: "+data.responseJSON.data+'.  Code:'+data.responseJSON.status);
        }
        else {
            alert("image unlinking failed! "+JSON.stringify(data));
        }
    });
}


/* unlink SONG from Plan Item
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
    // desired value is the reverse of the current value
    var value     = $(that).data('value')=='1' ? '0' : '1'; 
    var id        = 'forLeadersEyesOnly-item-id-' + data.itemId;

    ;;;console.log('Changing visibility of this item. Desired value: '+value);

    $(that).children('i').removeClass('red');
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
            ;;;console.log('Result from change request. New value: '+data);
            $(that).children('i').removeClass('fa-spin fa-spinner');
            // show correct icon according to new setting
            $(that).children('i').addClass( data==0 
                ? 'fa-eye'
                : 'fa-eye-slash red');
            // reflect new setting also in the data attribute
            $(that).attr('data-value', data); 
            $(that).data('value', data);
            // reflect new setting also in the data attribute
            $(that).attr('title', data==0 
                ? "Item is visible for all users. Click to change!" 
                : "Item visible for leader's eyes only. Click to change!");
            $(that).tooltip(); // refresh the tooltip...
            // on the Item Detail page, also show the right text for the new setting
            $(that).children('small').toggle();
            if (data==0)
                $('.item-comment-public').show();
            else
                $('.item-comment-public').hide();
        })
        .fail( function(data) 
        {
            $(that).children('i').removeClass('fa-eye');
            $(that).children('i').removeClass('fa-eye-slash');
            $(that).children('i').addClass('fa-exclamation-triangle');
            console.log('update of forLeadresEyesOnly failed!!');
            console.log(JSON.stringify(data));
        });
}



/*\____________________________________________________________________________  PLAN  Details Page = Items List page
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
    $('#show-video-clip').children('div').html(''); // reset video preview
    
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
    // if (item.action=="update-song" || location.pathname.search('chords') > 0) {
    if ( item.action=="update-song" ) {
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
        var curCom = button.siblings().first().text().trim();
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
    if (what=='comment' || what=='scripture' || what=='file') {
        // show submit button
        $('#searchForSongsSubmit').show();
        // set focus appropriately
        if (what=='comment')
            $('#comment').focus();
        if (what=='scripture')
            $('#from-book').focus();
        if (what=='file')
            $('#file_category_id').focus();
    }

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
                    noSongFound(search, type);
                    return;
                }
                $('#search-action-label').html('<span class="bg-info mr-1">Select the desired Song:</span>');
                $('#search-action-label').append('<span class="pull-xs-right small text-muted">Preview:</span>');
                $('#searching').hide();
                $('#searchForSongsSubmit').hide();

                // call function to fill the search result element
                createAndShowSearchResult('#search-result', result);

            })
            .fail(function(data) {
                $('#searching').hide();
                if (data.status==404) {
                    noSongFound(search, type);
                    return;
                }
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

/*
*/
function noSongFound(search,type) 
{
    var msg = 'Nothing found for "'+search+'", please try again:';
    $('#search-result').text(msg);
    $('#searchForSongsButton').toggle();
    $('#searchForSongsSubmit').toggle();
    $('#searching').hide();
    if (type!='song') {
        $('#search-action-label').text(msg);
        $('.search-form-item').show();  
        $('#haystack').focus();
    }
        
    showModalSelectionItems(type);
    if (type=='song')
        $('#search-string').focus();
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
        var anchor;

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
        if ( lastUse.split(' ')[1]=='days' && lastUse.split(' ')[0]<12 )
            $(spn2).addClass('red') // make it all red if the song has been used just recently
        $(spn2).append(' times');

        // spn containing the youtube link
        var spnYT = document.createElement('span');
        if ( result[i].youtube_id.length>0 ) {
            spnYT.className = "pull-xs-right";
            spnYT.title     = "preview song";
            anchor = document.createElement('a');
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
            anchor = document.createElement('a');
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
        lbl.className="c-input link c-radio";
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
    // only show the 'clicked' song
    $(that).parent().parent().toggle();

    // show the YT preview DIV and insert the player code
    $('#show-video-clip').children('div').html('<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ytid+'" frameborder="0" allowfullscreen></iframe>');
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


/* reset the file selection facility
*/
function resetAddFilesElement() {
    $('#show-location-selection').hide();
    $('.image-selection-slideshow').hide();
    $('.show-file-add-button').hide();
    $('.modal-select-file').show();
}

/*  hide File-Category selector; show local-vs-remote-files choice
*/
function showLocalVersusRemoteButtons(that, modus)
{
    // hide the file-category selector
    if (modus != 'files_upload' && modus != 'default_items')
        $(that).parent().parent().hide();

    // get category id from selected value
    var cat = $(that).val();

    // get text-equivalent of the numeric value
    var catText;
    for (var i = 1; i < that.length; i++) {
        if (that[i].value == cat)
            catText = that[i].text;
    }
    // show selected category
    $('.show-selected-category').text(catText);

    // for the selection of images for default items
    if (modus == 'default_items') {
        $('.modal-select-file>p').hide();
        $('#btn-select-cspot-images').click();
        $('.image-selection-slideshow').show();
        return;
    }

    $('.show-selected-category').show();

    // skip the next step (choice between upload and cspot images)
    // as category 'newest' is only for c-spot images
    if (cat == 'newest') {

        // go directly to the images selection
        // with a handle on that button as it contains important information....
        showImagesSelection( document.getElementById('btn-select-cspot-images') );
        return;
    }

    // if only file uploads are requested:
    if (modus == 'files_upload') {
        $('.show-file-add-button').show();
        $('#file').on('change', function() {
            uploadSingleFile('#file', cat);
        })
        return;
    }

    // show the two buttons 'upload' or 'select existing'
    $('#show-location-selection').show();

    // scroll down
    location.href = "#upload-or-select";
}

function uploadSingleFile(selector, category)
{
    // create a 'virtual' form with the filename
    var formData = new FormData();
    formData.append('file', $(selector)[0].files[0]);
    formData.append('file_category_id', category);

    // show spinner while uploading
    $('.show-file-add-button').html(cSpot.const.waitspinner + ' uploading ....');

    $.ajax({
            url : cSpot.routes.apiUpload,
            type : 'POST',
            data : formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
        }).done( 
            function( data ) {
                console.log(data.data);
                $('.show-file-add-button').html(cSpot.const.waitspinner + ' Success! Reloading page ... ');
                // reload page and show latest upload
                location.href = location.pathname+"?newest=yes";
            
        }).fail( function( data ) {
            $('.show-file-add-button').text('Error, pres F12 to see more!');
            console.log("File Upload Error Output:");
            console.log( data );        
        })
}

/*  Show images from server for selection
*/
function showImagesSelection(that, ajax_url)
{
    // path to the images
    var img_path = that.dataset.imagesPath;

    // was file category set?
    var cat = $('#file_category_id').val() || $('input[name="file_category_id"]:checked').val();

    // url for the AJAX call
    if (ajax_url==undefined)
        var ajax_url = that.dataset.ajaxUrl + '/' + cat;

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
            // scroll down...
            location.href="#bottom";
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
    $(anchor).attr('onclick', 'addItemWithFileOrAddFileToItem('+data.id+', this)');

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
    // scroll down...
    location.href="#bottom";
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
function addItemWithFileOrAddFileToItem(file_id, that)
{
    // for default items, just set the hidden input field value
    if (cSpot.item=='default_items'){
        $('#file_id').val(file_id);         // write file id into input field
        $('.add-files-card' ).html(that);   // show selected image instead of the file selection
        return;
    }

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

    ;;;console.log('Uploading new file or attaching existing file to item via AJAX - type: '+cSpot.item.action);


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
        showSpinner();
        window.location.reload();

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
            resetCommentText(TRid, data.responseJSON.data);
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
            resetCommentText(TRid, data.responseJSON.data);
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
}


/*  delete Plan Note
*/
function erasePlanNote(plan_id)
{
    console.log('trying to erase note for plan id '+plan_id);

    $('#info-plan-id-'+plan_id).html(cSpot.const.waitspinner);

    // compose id/value pair
    var id    = 'info-plan-id-'+plan_id;
    var value = '_'; // underscore denotes an empty value in the ItemController

    $.post( cSpot.routes.apiPlanUpdate, { 
            id    : id,
            value : value,
        })
        .done(function(data) {
            $('#info-plan-id-'+plan_id).text('');
        })
        .fail(function(data) {
            $('#info-plan-id-'+plan_id).text(data.responseJSON.data);
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
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



/* Even 'normal' users can add a note to a plan
*/
function addNoteToPlan( event )
{
    if (event != undefined) {
        $('#showAddedPlanNote').text('');
        $('#textareaAddPlanNote').focus();
        return;
    }

    // get note from modal
    var note = $('#textareaAddPlanNote').val();
    
    // user should click close if he doesn't want to a dd a note...
    if (note=='') return;

    $('#showAddedPlanNote').html(cSpot.const.waitspinner);
    $('#addPlanNoteModal').modal('hide');        

    // 
    // send new note to controller
    $.post( cSpot.routes.apiAddNote, {
        note: note,
        id  : cSpot.plan.id,
    })
    .done( function(data) {
        // on success, add note to existing <p> in plan view
        $('#showAddedPlanNote').text(data);
        // close modal again
    })
    .fail( function(data) {
        console.log(data);
        console.log('Failed to add new note to plan!');
        $('#showAddedPlanNote').text('Failed to add new note to plan! Press F12 to see more and notify Admin!');
    });
}


/* change status of 'private' setting of plan
*/
function togglePlanPrivate( that, plan_id ) 
{
    console.log('trying to change "private" setting for plan id '+plan_id+' to '+that.checked);

    var origtext = $('.plan-private-field').text();
    $('.plan-private-field').html(cSpot.const.waitspinner);
    $('#plan-private-'+plan_id).html(cSpot.const.waitspinner);

    // compose id/value pair
    var id    = 'private-plan-id-'+plan_id;
    var value = that.checked;

    $.post( cSpot.routes.apiPlanUpdate, { 
            id    : id,
            value : value,
        })
        .done(function(data) {
            $('.plan-private-field').html(origtext);
            if (data==1) 
                $('#plan-private-'+plan_id).html('&#10003;');
            else
                $('#plan-private-'+plan_id).html('&#10007;');            
        })
        .fail(function(data) {
            $('.plan-private-field').html(data.responseJSON.data);
            console.log("Update failed! Please notify admin! " + JSON.stringify(data));
        });
}


/*  upload new file via AJAX and show little icon when successful
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


/*\____________________________________________________________________________  FILES  List  Page
\*/



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

    // ignore and close dialog if nothing was changed
    if (oldData.file_category_id == newFC 
             && oldData.filename == newFn) return;

    // show spinner
    if ( oldData.file_category_id != newFC )
        $('.fileshow-category-'+fileID).html(cSpot.const.waitspinner);
    if ( oldData.filename != newFn )
        $('.fileshow-filename-'+fileID).html(cSpot.const.waitspinner);

    // get the action URL
    var actionURL = $('#file-id').data('action-url')+fileID;

    // update via AJAX 
    $.post( actionURL, { id: fileID, filename: newFn, file_category_id: newFC })
        .done(function(data) {
            ;;;console.log('update successful:');
            ;;;console.log(data);
            var file = data.data;
            $('.fileshow-filename-'+fileID).text(file.filename);
            $('.fileshow-category-'+fileID).text($('#file_category_id option:selected').text());
            // also reset the data on the EDIT button
            $('#edit-button-'+fileID).attr('data-filename', file.filename);
            $('#edit-button-'+fileID).attr('data-cat', file.file_category_id);
        })
        .fail(function(data) {
            console.log('update failed!');
            console.log(data);
            $('.fileshow-filename-'+fileID).text("Update failed! Please notify admin! Press F12 for more details." + JSON.stringify(data));
        });
    
    // close the modal and update the data on the screen
    $('#fileEditModal').modal('hide');
}





/*\
|*|
|*#===========================================================================================    END   OF SPA UTILITIES
|*|
\*/




