var bibleBooks;


$(document).ready(function() {

    $(function () {
        // activate the tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // activate popvers
        $('[data-toggle="popover"]').popover();
        $('.popover-dismiss').popover({
            trigger: 'focus'
        });

        // enable Tabs
        $('#tabs').tabs();
    });
  
    /***
     * Get array with all bible books with all chapters and number of verses in each chapter
     */
    $.get(__app_url+'/bible/books/all/verses', function(data, status) {

        if ( status == 'success') {
            bibleBooks = data;
        }
    });



    /**
     * items on Plan page can be moved into new positions
     */
    $("#tbody-items").sortable({
        items   : "> tr",
        appendTo: "parent",
        cursor  : 'move',
        helper  : "clone",
        handle  : '.drag-item',
        distance: '5',
        forceHelperSize: true,
        stop    : function (event, ui) {
            var changed=false;
            shouldBe = 0;
            movedItemId = $(ui.item).attr('id').split('-')[2];
            // get all siblings of the just moved item
            siblings = $(ui.item).parent().children();
            // check each sibling's sequence
            for (var i = 1; i <= siblings.length; i++) {
                // check for incorrect ordered items
                // (  $(siblings[i]).attr('id') == 'tr-item-'+(1+i)+'.0' ) 
                if (  $(siblings[-1+i]).attr('id') == 'tr-item-'+movedItemId  && movedItemId != i ) {
                    changed = $(siblings[-1+i]);
                    if (movedItemId > i) {
                        shouldBe = -0.5+i;
                    } else {
                        shouldBe = 0.5+i
                    }
                    //console.log($(siblings[i]).attr('id')+' should have '+shouldBe);
                } //else {
                    //console.log((1+i)+' OK! '+$(siblings[i]).attr('id'));
                //}
            }
            if (changed) {
                // console.log( 'Item '+changed.attr('id')+ ' (id # ' + changed.data('item-id') +')  should now have seq no ' + (shouldBe) );
                window.location.href = __app_url + '/cspot/items/' + changed.data('item-id') + '/seq_no/'+ (shouldBe);
            } else {
                console.log('order unchanged');
            }
        },
    }).disableSelection();


    
    // handle keyboard events
    $(document).keydown(function( event ) {
        // key codes: 37=left arrow, 39=right, 38=up, 40=down, 34=PgDown, 33=pgUp, 
        //            36=home, 35=End, 32=space, 27=Esc, 66=e
        //event.preventDefault();
        console.log('pressed key code: '+event.keyCode);
        switch (event.keyCode) {
            case 37: navigateTo('previous-item'); break;
            case 36: navigateTo('first-item');   break;
            case 39: navigateTo('next-item');   break;
            case 32: navigateTo('next-item');  break;
            case 35: navigateTo('last-item'); break;
            case 27: navigateTo('back');     break;
            case 66: navigateTo('back');    break;
            case 69: navigateTo('edit');   break;
            default: break;
        }
    });    
    // handle swiping on smartphones
    $('#app-layout').on("swipeleft",function(){
        navigateTo('next-item');
    });
    $('#app-layout').on("swiperight",function(){
        navigateTo('previous-item'); 
    });
    

    // re-design the showing of lyrics interspersed with guitar chords
    if ( $('#chords').text() != '' ) {
        // only do this for PRE tags, not on input fields etc...
        if ($('#chords')[0].nodeName == 'PRE') {
            reDisplayChords();
        }
    }

});


/*
    Use Regex patterns to identify chords versus lyrics versus headings
    and to show them in different colors
*/
function reDisplayChords()
{
    // get the chords text and split it into lines
    chords = $('#chords').text().split('\n');
    // empty the exisint pre tag
    $('#chords').text('');
    // analyse each line and put it back into single pre tags
    for (var i = 0; i <= chords.length - 1; i++) {
        if (chords[i].length==0) continue;
        // if a line looks like chords, make it red
        if ( identifyChords(chords[i]) ) {
            $('#chords').append('<pre class="red m-b-0">'+chords[i]+'</pre>');
        }
        else {
            hdr = identifyHeadings(chords[i]);
            $('#chords').append('<pre class="m-b-0 '+hdr+'">'+chords[i]+'</pre>');
        }
    }
}
function identifyHeadings(str)
{
    // identify headers by the first word in a line, case-insensitive

    var patt = /^(Chorus|bridge|coda|end)/i;
    if ( patt.test(str) ) 
        return ' p-l-3 bg-info';

    var patt = /^(Verse)/i;
    if ( patt.test(str) ) 
        return ' p-l-3 bg-success';

    var patt = /^(Capo|Key|Intro|Other|\()/;
    if ( patt.test(str) ) 
        return ' text-primary';

    return '';
}
function identifyChords(str)
{
    
    var patt = /[klopqrtvwxyz1345689]/g;
    if ( patt.test(str) ) return false;
    
    var patt = /\b[CDEFGAB](?:#{1,2}|b{1,2})?(?:maj7?|min7?|sus2?|m?)\b/g;
    if ( patt.test(str) ) return true;
    
    var patt = /\b[CDEFGB]\b/g;
    if ( patt.test(str) ) return true;

    return false;
}


/** 
 * Navigate to next item
 *
 * @string direction - part of the ID of an anchor on the calling page that executes the navigation
 */
function navigateTo(where) 
{
    // prevent this if user is in an input field or similar area
    if (document.activeElement.tagName != "BODY") return;

    // get the element that contains the proper link
    a = document.getElementById('go-'+where);
    // link doesn't exist:
    if (a==null) return;

    // fade background and show spinner
    $('#show-spinner').modal({keyboard: false})

    if (a.onclick==null) {
        // try to go to the location defined in href
        window.location.href = a.href;        
    }    
    // try to simulate a click on this element
    a.click();
}




/***
 * Build a Bible Reference string
 */
function showNextSelect(fromOrTo, what) {
    book    = $('#from-book').val();
    chapter = $('#from-chapter').val();

    // make sure all fields are visible now
    $('.select-reference').show();

    // remove old options from select box
    emptyRefSelect(fromOrTo, what);
    var x = document.getElementById(fromOrTo+'-'+what);

    // API call to get the books/chapter/verses data
    if (typeof(bibleBooks)=='object') {
        // make the element visible
        $('#'+fromOrTo+'-'+what).show();

        // minimum value for the 'to' verse is the 'from' verse
        minNumber = 1
        if (fromOrTo=='to' && what=='verse') {
            minNumber = $('#from-verse').val();
        }

        // are wee looking at chapters of a book or verses of a chapter?
        if (what=='chapter') {
            maxNumber = Object.keys(bibleBooks[book]).length;
        } else {
            maxNumber = bibleBooks[book][chapter];
        }

        // populate the select input with the relevant numbers
        for (var i = minNumber; i <= maxNumber; i++) {
            var option = document.createElement("option");
            option.text = i;
            option.value = i;
            x.add(option);
        }
        // if book has only one chapter, populate the verses right now
        if (what=='chapter') {
            showNextSelect(fromOrTo, 'verse');
        }
        if (fromOrTo=='from' && what=='verse') {
            showNextSelect('to', 'verse');
            $('.select-version').show();                
        }
    };
}

function populateComment() {
    // ignore if nothing was selected
    if ($('#from-book').val()==null || $('#from-book').val()==' ') { 
        return; }

    // check existing comment
    oldComment = $('#comment').val();
    if (oldComment.length>0) {
        oldComment += '; ';
    }

    // set default and minimum value identical with 'from' value
    $('#comment').val( oldComment
        + $('#from-book').val()+' '
        + $('#from-chapter').val()+':'
        + $('#from-verse').val() 
        +($('#to-verse').val() != $('#from-verse').val() ? '-'+$('#to-verse').val() : '') + ' ('
        + $('#version').val() + ')'
        );

    $('#waiting').show();
    // now get the bible text via API and display it on the page
    showScriptureText($('#version').val(), $('#from-book').val(), $('#from-chapter').val(), $('#from-verse').val(), $('#to-verse').val())

    $('#from-book').val('');
    emptyRefSelect('from', 'chapter');
    emptyRefSelect('from', 'verse');
    emptyRefSelect('to', 'verse');
    $('#version').val('');
    $('.select-reference').hide();
    $('.select-version').hide();
    $('#col-2-song-search').hide();
    $('#comment-label').text('Bible Reading');
    blink('.save-buttons');
}

function emptyRefSelect(fromOrTo, what) {
    // get the <select> element 
    var x = document.getElementById(fromOrTo+'-'+what);
    $(x).hide();
    // clear the element of all current options
    for (i=x.length; i>=0; i--) {
        x.remove(i);
    }
}

function showScriptureText(version,book,chapter,fromVerse,toVerse) 
{

    book = book.replace(' ', '+');

    $.get(__app_url+'/bible/passage/'+version+'/'+book+'/'+chapter+'/'+fromVerse+'/'+toVerse , 
        function(data, status) 
        {
            if ( status == 'success') 
            {
                $('#waiting').hide();
                passage = data.response.search.result.passages;
                if (passage.length>0) 
                {
                    text = (passage[0].text).replace(/h3/g, 'strong');
                    text = text.replace(/h2/g, 'i');
                    $('#bible-passages').append( 
                        '<h5>' + passage[0].display +' ('+passage[0].version_abbreviation + ')</h5>' +
                        '<div>'+ text + '</div>' +
                        '<div class="small">' + passage[0].copyright + '</div><hr>'                        
                    );                         
                } 
                else 
                {
                    $('#show-passages').html('(passage not found)');
                }
            }
            else 
            {
                $('#waiting').append(' Not found! ' + data);
            }
        }
    );

}





var timeoutID;
function delayedCloseFlashingModals(selector) {
    timeoutID = window.setTimeout( closeMyModal, 2000, selector);
}
function closeMyModal(selector) {
    $(selector).modal('hide');
    // set focus again on main input field
    $('.main-input').focus();    
}



function toggleTrashed() {
    $('.trashed').toggle();
    if ($('#toggleBtn').text() == 'Show') {
        $('#toggleBtn').text('Hide');
    } else {
        $('#toggleBtn').text('Show');
    }
}


function blink(selector){
    $(selector).show();
    $(selector).animate({opacity:0}, 150, "linear", function(){
        $(this).delay(50);
        $(this).animate({opacity:1}, 150, function(){
            blink(this);
        });
        $(this).delay(500);
    });
}

