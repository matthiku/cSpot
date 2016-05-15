var bibleBooks;


// make sure all AJAX calls are using the token stored in the META tag
// (see https://laravel.com/docs/5.2/routing#csrf-x-csrf-token)
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
});


// quick way to show the wait model
function showSpinner() {
    $('#show-spinner').modal({keyboard: false});
}
            
/**
 * List of future plan dates for highlighing in the calendar widget
 */
var SelectedDates = {};
SelectedDates[new Date().toLocaleDateString()] = 'Today';

// lyrics sequence data
var sequence;


$(document).ready(function() {


    /**
     * enabling certain UI features 
     */
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
  

    /**
     * On 'Home' page, get list of future plans and show calendar widget
     */
    if ( window.location.href == __app_url + '/home' ) {
        $.getJSON( __app_url + '/cspot/plans?filterby=future&api=api',
            function(result){
                $.each(result, function(i, field) {
                    hint = field.type.name+' led by '+field.leader.first_name; 
                    if ( field.teacher.first_name != "n/a" ) {
                        hint +=', teacher is ' + field.teacher.first_name; }
                    dt = new Date(field.date.split(' ')[0]).toLocaleDateString();
                    SelectedDates[dt] = hint;
                });
                // get the current browser window dimension (width)
                browserWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                numberOfMonths = 3;
                if (browserWidth<800) numberOfMonths = 2;
                if (browserWidth<600) numberOfMonths = 1;
                // Now style the jQ date picker
                $(function() {
                    /***
                     * Show Date Picker calendar widget
                     */
                    $( "#inpDate" ).datepicker({
                        numberOfMonths: numberOfMonths,
                        changeMonth   : true,
                        changeYear    : true,
                        maxDate       : "+4m",
                        dateFormat    : "yy-mm-dd",
                        beforeShowDay : 
                            function(date) {
                                var dot=date.toLocaleDateString();
                                var Highlight = SelectedDates[dot];
                                if (Highlight) {
                                    if (Highlight==='Today') {
                                        return [true, "", Highlight]; }
                                    return [true, "Highlighted", Highlight]; }
                                else {
                                    return [true, '', '']; }
                            }
                    });
                });
            }
        );
        
    }


    /**
     * Put focus on textarea when user opens the feedback modal dialog
     */
    $('#createMessage').on('shown.bs.modal', function () {
        $('#feedbackMessage').focus()
    })

    /**
     * Mark modified form fields with a new background
     * and show the submit/save buttons
     */
    $("input, textarea, input:radio, input:file").change(function(){
        // change background color of those fields
        $(this).css("background-color", "#D6D6FF");

        // show submit or sabe buttons
        $('.submit-button').show();
        blink('.submit-button');
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
            $('#show-spinner').show();
            var changed=false;
            should_seq_no = 0;
            movedItem = [];
            movedItem.id = ui.item.data('itemId');
            movedItem.seq_no = ui.item.attr('id').split('-')[2];
            // get all siblings of the just moved item
            siblings = $(ui.item).parent().children();
            // check each sibling's sequence
            for (var i = 1; i <= siblings.length; i++) {
                sib = siblings[-1+i];
                //console.log(i + ' attr:' + sib.id + ' id:' + sib.dataset.itemId + ' class:' + sib.classList);
                if (sib.classList.contains('trashed')) {
                    // ignore trashed items....
                    continue;
                }
                // is this the moved item?
                if ( sib.dataset.itemId == movedItem.id ) {
                    changed = sib;
                    //console.log(sib.id+' was moved. ');
                    break;
                } 
                else {
                    should_seq_no = 0.0 + sib.id.split('-')[2];
                    //console.log(sib.id + ' unmoved ');
                    if (changed) { 
                        break; 
                    }
                }
            }
            if (changed) {
                should_seq_no = 1 * should_seq_no;
                //console.log( 'Item '+changed.id+ ' (id # ' + changed.dataset.itemId +')  should now have seq no ' + (0.5 + should_seq_no) );
                window.location.href = __app_url + '/cspot/items/' + changed.dataset.itemId + '/seq_no/'+ (0.5 + should_seq_no);
                return;
            } else {
                // console.log('order unchanged');
            }
        },
    }).disableSelection();


    
    /**
     * Configuration for Items Presentation Views (present/chords/musicsheets)
     */
    if (window.location.href.indexOf('/items/')>10) {

        // handle keyboard events
        $(document).keydown(function( event ) {
            // key codes: 37=left arrow, 39=right, 38=up, 40=down, 34=PgDown, 33=pgUp, 
            //            36=home, 35=End, 32=space, 27=Esc, 66=e
            //
            console.log('pressed key code: '+event.keyCode);
            switch (event.keyCode) {
                case 37: advancePresentation('back'); break; // left arrow
                case 33: navigateTo('previous-item'); break; // left PgUp
                case 36: navigateTo('first-item');   break; // key 'home'
                case 39: advancePresentation();     break; // key right arrow
                case 32: advancePresentation();    break; // spacebar
                case 34: navigateTo('next-item'); break; // key 'PgDown'
                case 35: navigateTo('last-item'); break; // key 'end'
                case 27: navigateTo('back');     break; // key 'Esc'
                case 68: navigateTo('edit');    break; // key 'd'
                case 83: jumpTo('start-lyrics');break; // key 's'
                case 80: jumpTo('prechorus'); break; // key 'p'
                case 49: jumpTo('verse1'); break; // key '1'
                case 50: jumpTo('verse2'); break; // key '2'
                case 51: jumpTo('verse3'); break; // key '3'
                case 52: jumpTo('verse4'); break; // key '4'
                case 53: jumpTo('verse5'); break; // key '5'
                case 53: jumpTo('verse6'); break; // key '6'
                case 53: jumpTo('verse6'); break; // key '6'
                case 53: jumpTo('verse7'); break; // key '7'
                case 67: jumpTo('chorus1'); break; // key 'c'
                case 75: jumpTo('chorus2');  break; // key 'k'
                case 66: jumpTo('bridge');     break; // key 'b'
                case 69: jumpTo('ending');       break; // key 'e'
                case 76: $('.lyrics-parts').toggle(); break; // key 'l', show all lyrics
                case 109: $('#decr-font').click();   break; // key '-'
                case 107: $('#incr-font').click();   break; // key '+'
                default: break;
            }
        });
    }
    

    /**
     * prepare lyrics for presentation
     */
    if ( window.location.href.indexOf('/present')>10 ) {

        // make sure the main content covers all the display area
        $('#main-content').css('min-height', window.screen.height);

        // intercept mouse clicks into the presentation area
        $('#main-content').contextmenu( function() {
            return false;
        });

        // Allow mouse click (or finger touch) to move forward
        $('#main-content').click(function(){
            advancePresentation();
        });
        // allow rght-mouse-click to move one slide or item back
        $('#main-content').on('mouseup', function(event){
            if (event.which == 3) {
                event.preventDefault();
                advancePresentation('back');
            }
        });

        // re-format the lyrics
        reDisplayLyrics();

        // check if we have a predefined sequence from the DB
        sequence=($('#sequence').text()).split(',');

        // check if there are more lyric parts than 
        // indicated in the sequence due to blank lines discoverd in the lyrics
        if (sequence.length>2) 
            compareLyricPartsWithSequence();

        // auto-detect sequence if it is missing
        if (sequence.length<2) {
            createDefaultLyricSequence();
            sequence=($('#sequence').text()).split(',');
        }
    }

    /**
     * re-design the showing of lyrics interspersed with guitar chords
     */
    if ( $('#chords').text() != '' ) {
        // only do this for PRE tags, not on input fields etc...
        if ($('#chords')[0].nodeName == 'PRE') {
            reDisplayChords();
        }
        $('.edit-show-buttons').css('display', 'inline');
    }
    // remove dropup button and menu on info screens
    else if ( $('#bibletext').text()!='' || $('#comment').text()!='' ) {
        $('#jumplist').remove();
    }

    // if sheetmusic is displayed, show button to swap between sheetmusic and chords
    if ( window.location.href.indexOf('sheetmusic')>0 || window.location.href.indexOf('swap')>0 ) {
        $('#show-chords-or-music').css('display', 'inline');
    }

    // start showing bible parts if this is a bible reference
    if ($('.bible-text-present').length>0) {
        reFormatBibleText();
    }

});

/*
    Divide bible texts by their paragraphs and create a sequence list accordingly
*/
function reFormatBibleText() 
{
    // get the paragraphs with bible text
    var p = $('.bible-text-present p');
    // empty the pre-formatted text containter
    $('#bible-text-present-all').html('');
    // add the paragraphs' text each into the container
    $(p).each( function(entry) {
        text = $(this).text();
        clas = $(this).attr('class')
        console.log( 'class: ' + clas + ' ==> ' + $(this).html() );
        if (clas.substr(0,1)=='p' || clas.substr(0,1)=='q' || clas.substr(0,4)=='line') {
            $('#bible-text-present-all').append(
                '<pre class="bible-text-present">'+text+'</pre>'
                );
        }
        //insertSeqNavInd(entry+1,entry,'bible');
    });
    $('#bible-text-present-all').show();
}


/*
    check if there are more lyric parts than 
        indicated in the spre-defined equence due to blank lines discoverd in the lyrics
*/
function compareLyricPartsWithSequence()
{
    // get the predefined sequence
    sequenceDiv= $('#sequence').text();
    console.log('found predefined sequence: ' + sequenceDiv);
    sequence = ( $('#sequence').text() ).split(',');

    newSequence = '';
    nr = 0;
    // walk through the pre-defined sequence
    for (var i = 0; i < sequence.length; i++) {
        // what kind of lyric parts do we have (verse or chorus etc)
        type = identifyLyricsHeadings('['+sequence[i]+']');
        console.log('looking for part of type ' + type);
        // for each item in the sequence, find the corresponding lyric part(s)
        parts = $('[id^='+type+']');
        // for each part, add an indicator into the new sequence
        $(parts).each( function(entry){
            headerCode = $(this).data('header-code');
            newSequence += headerCode + ',' ;
            insertSeqNavInd(headerCode, nr);
            nr += 1;
        });
        $('#sequence').text(newSequence);

    }

}

/* 
    Create Default Lyric Sequence -
        if there is no pre-defined sequence in the songs DB table, 
        we can attempt to create our own based on the hints (headers) in the lyrics
*/
function createDefaultLyricSequence() 
{
    // get all lyric parts created so far
    var lyrList = $('.lyrics-parts');

    // if a bridge is included or no lyric parts exists: FAIL!
    if ( $('[id^=bridge]').length>0  ||  lyrList.length==0) 
        return;

    console.log('Trying to auto-detect song structure');

    // check if there is a chorus
    var chorus = false;     
    if ( $('[id^=chorus1]').length==1 )
        chorus = true;

    var nr = 0;  // indicates current lyric part number
    var insChorus=1;  // indicates verse number afer which we have to insert a chorus

    // go through the list of lyric parts (as generated in function "reDisplayLyrics()")
    $(lyrList).each(function(entry) 
    {
        id = $(this).attr('id');  // get name of that lyric part

        if ( id.substr(0,5) == 'verse' ) {
            verseNum = id.substr(5);
            verseNumInt = 0+id.substr(5,1);
            if (chorus && verseNumInt-1 == insChorus) {
                sequence += 'c,';
                insertSeqNavInd('c', nr);
                nr += 1;
                insChorus = verseNumInt;
            }
            sequence += verseNum + ',';
            insertSeqNavInd(verseNum, nr);
            nr += 1; // increase parts number
        }
    });
    // insert remaining chorus, if needed
    if (chorus) {
        sequence += 'c,';
        insertSeqNavInd('c', nr);
        nr += 1;
        insChorus = verseNum;
    }

    // do we also have an ending?
    if ($('[id^=ending]').length>0) {
        sequence += 'e';
        insertSeqNavInd('e', nr);
    }

    // now write the new sequence into the proper element
    $('#sequence').text(sequence);
}
/* Insert the Sequence Navigation indicators into the navbar */
function insertSeqNavInd(what, nr, where)
{
    // set default action
    where = where || 'lyrics';

    //console.log('inserting sequence indicator for '+ what + ' as '+where+' part # ' + nr);

    data = '<span id="'+where+'-progress-' + nr + '" class="'+where+'-progress-indicator"' +
           'data-show-status="unshown" onclick="'+where+'Show(' + "'" + what + "'" + ');">';
    data += formatSeqInd(what)+'&nbsp;</span>';

    $('#lyrics-sequence-nav').append( data );
}
function formatSeqInd(code){
    code  = code.toString();
    char1 = code.substr(0,1);
    char2 = code.substr(1,1);
    if ($.isNumeric(char1)) {
        if ( code.length==1  ||  $.isNumeric(char2) ) 
            return code;
        return '<span class="text-muted">'+char1+'<sup>'+char2+'</sup></span>';
    }
    char1 = char1.toUpperCase();
    if (code.length==1) 
        return char1;
    if (char1 != 'C')
        return char1+'<sup>'+char2+'</sup>';
    if (char2==1) char2 = 'h';
    if (code.length==2)
        return char1+char2;
    return '<span class="text-muted">'+char1+char2+'<sup>'+code.substr(2)+'</sup></span>';
}

/*
    On the lyrics screen, advance to the next item or sub-item (song parts)
*/
function advancePresentation(direction)
{
    // set default value....
    direction = direction || 'forward';

    if ($('#present-lyrics').length > 0) {

        // make sure the main lyrics div is visible
        $('#present-lyrics').show(); 

        // do we have a specific sequence provided?
        var seq = $('.lyrics-progress-indicator');

        // no sequence indicators found! Hopefully the default lyrics block was created...
        if (seq.length < 1) {
            // first check if have been here before, then we can advance to the next item
            if ( $('#start-lyrics').data('was-shown')=='true' ) {
                navigateTo('next-item');
                return;
            }
            $('#start-lyrics').show();
            $('#start-lyrics').data('was-shown', 'true');
            $('#lyrics-title').fadeOut('fast');
            return;
        }
        else if (direction=='forward') {
            // loop through all sequence items and find the next that wasn't shown yet
            found = false;
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    console.log('found ' + $(this).attr('id'));
                    $(this).data().showStatus = 'done';
                    $('.lyrics-progress-indicator').removeClass('bg-danger');
                    $(this).addClass('bg-danger');
                    $(this).click();
                    return false;
                }
            });
            // all items were shown, so we can move to the next item
            if (! found) {
                $('#present-lyrics').fadeOut();
                navigateTo('next-item');
                return;
            }
        }
        // no we try to move backwards in the sequence of song parts
        else {
            for (var i = seq.length - 1; i >= 0; i--) {
                if ($(seq[i]).hasClass('bg-danger')) {
                    console.log('currently active part is # '+i+' with text: '+$(seq[i]).text() );
                    // we have reached the first part, going further back means previous plan item!
                    if (i==0) { 
                        navigateTo('previous-item'); 
                        return; }
                    $(seq[i]).data().showStatus = 'unshown';
                    $('.lyrics-progress-indicator').removeClass('bg-danger');
                    $(seq[i-1]).addClass('bg-danger');
                    $(seq[i-1]).click();
                    return;
                } 
            }
            // all song parts have been shown, so we must be at 
            //     the first and now have to go to the previous plan item
            navigateTo('previous-item');
            return;
        }

    }

    // we are showing a bible text
    else if ($('.bible-text-present').length>0) {
        var seq = $('.bible-progress-indicator');
        // loop through all sequence items and find the next that wasn't shown yet
        found = false;
        $(seq).each(function(entry){
            if ( $(this).data().showStatus  == 'unshown' ) {
                found = true;
                console.log('found ' + $(this).attr('id'));
                $(this).data().showStatus = 'done';
                $('.bible-progress-indicator').removeClass('bg-danger');
                $(this).addClass('bg-danger');
                $(this).click();
                return false; // escape the each loop...
            }
        });
        if (! found) {
            navigateTo('next-item');
            return;
        }
    }
    // we're not showing a song, so we simply move to the next plan item
    else if (direction=='forward') 
        { navigateTo('next-item'); }
    else {
        navigateTo('previous-item');
    }
}


/*
    Using keyboard shortcuts differently on the lyrics presentation or chords pages
*/
function jumpTo(where)
{
    // the lyrics presentation page uses buttons to show parts and hide the rest
    if ($('#present-lyrics').length > 0) {
        $('#present-lyrics').show(); 
        $('#btn-show-'+where).click();
    }
    // the chords page uses anchors to jump to...
    else 
        window.location.href = '#'+where;
}


/*
    Show lyrics in presentation mode
    mainly: divide lyrics into blocks (verses, chorus etc) to be able to show them individually
*/
function reDisplayLyrics()
{
    // get the lyrics text and split it into lines
    var lyrics = $('#present-lyrics').text().split('\n');
    // empty the existing pre tag
    $('#present-lyrics').text('');
    var newLyr = '';
    var lines  = 0;         // counter for number of lines per each song part 
    var headerCode = 's'    // identifies the code within the sequence data
    // default song part if there are no headings
    var newDiv = '<div id="start-lyrics" class="lyrics-parts" ';
    var divNam = 'start-lyrics';
    var curPart= '';
    var apdxNam= 97; // char cod 97 = 'a' - indicates sub-parts of verses or chorusses etc

    // analyse each line and put it back into single pre tags
    for (var i = 0; i <= lyrics.length - 1; i++) {

        lyricsLine = lyrics[i].trim();  // get pure text

        // treat empty lines as start for a new slide!
        if (lyrics[i].length==0) {
            if (i==0) continue; // but not a leading empty line....
            hdr = curPart + String.fromCharCode(apdxNam++);
        }
        else { 
            hdr = identifyLyricsHeadings( lyricsLine ); 
            if (hdr.length>0) { 
                curPart = hdr; 
                var apdxNam= 97; // reset appendix indicator (for forced lyric parts)
            }
        }

        // check if we have a header or the actual lyrics
        if (hdr.length>0) {
            // insert identifiable blocks
            insertNewLyricsSlide(newDiv, newLyr, divNam, lines);
            divNam = hdr;
            newLyr = '';
            lines  = 0;
            newDiv = '</div><div id="'+hdr+'" class="lyrics-parts" ';
        }
        // actual lyrics - insert as PRE element
        else {
            lines += 1;
            newLyr += '<pre class="text-present m-b-0">'+lyricsLine+'</pre>';
        }
    }
    // insert the last lyrics part
    insertNewLyricsSlide(newDiv, newLyr, divNam, lines);
}

// insert new SLIDE into the presentatinon
function insertNewLyricsSlide(newDiv, newLyr, divNam, lines)
{
    // only if the part is not empty..
    if (lines == 0) { return; }

    newDiv += ' data-header-code="'+headerCode(divNam)+'">';

    $('#present-lyrics').append( newDiv + newLyr + '</div>' );
    $('#'+divNam).hide();
    $('#btn-show-'+divNam).show();    
    console.log( 'Inserted lyrics part called ' + divNam );
}
function headerCode(divNam) {
    switch (divNam.substr(0,5)){
        case 'bridg': return 'b'+divNam.substr(6);
        case 'choru': return 'c'+divNam.substr(6);
        case 'prech': return 'p'+divNam.substr(9);
        case 'endin': return 'e'+divNam.substr(6);
        case 'verse': return divNam.substr(5);
        default: return '';
    }
}

function bibleShow(what)
{
    var p = $('.bible-text-present p');
    for (var i=0; i<p.length; i++) {
        if (Math.abs(what - i) > 4)
            $(p[i]).hide();
    }
    $(p[what-1]).show();
}

// called from the lyrics buttons made visible in reDisplayLyrics function
function lyricsShow(what)
{
    // from the 'what' determine the proper ID value of the desired song part
    if (what.length<4) {
        apdx = '';
        fc = what.substr(0,1);
        if ( $.isNumeric(fc) || fc != 'c' ) {
            apdx = what.substr(1);   
            what = identifyLyricsHeadings('['+fc+']')+apdx;
        } else {
            apdx = what.substr(2);
            what = identifyLyricsHeadings('['+what.substr(0,2)+']')+apdx;
        }
    }
    // do nothing if the object doesn't exist...
    if ( $('#'+what).length == 0 )  { return }

    console.log('showing song part called '+what);
    
    $('.lyrics-parts').fadeOut().promise().done( function() { $('#'+what).fadeIn() } );
    // elevate the currently used button
    $('.lyrics-show-btns').removeClass('btn-danger');       // make sure all other buttons are back to normal
    $('#btn-show-'+what).removeClass('btn-info-outline');   // aremove ouline for this button
    $('#btn-show-'+what).addClass('btn-danger');            // add warning class for this button
}
function identifyLyricsHeadings(str)
{
    switch (str.toLowerCase()) {
        case '[1]': return 'verse1';
        case '[2]': return 'verse2';
        case '[3]': return 'verse3';
        case '[4]': return 'verse4';
        case '[5]': return 'verse5';
        case '[6]': return 'verse6';
        case '[7]': return 'verse7';
        case '[8]': return 'verse8';
        case '[9]': return 'verse9';
        case '[prechorus]': return 'prechorus';
        case '[p]': return 'prechorus';
        case '[chorus 2]': return 'chorus2';
        case '[t]': return 'chorus2';
        case '[chorus]': return 'chorus1';
        case '[chorus1]': return 'chorus1';
        case '[c]': return 'chorus1';
        case '[c1]': return 'chorus1';
        case '[bridge]': return 'bridge';
        case '[b]': return 'bridge';
        case '[ending]': return 'ending';
        case '[e]': return 'ending';
        default: return '';
    }
}




/* 
    List filtering: Reload page with alternate filtering
*/
function toogleAllorFuturePlans()
{
    showSpinner();
    // get current url and query string
    var currUrl = window.location.href.split('?');
    var newUrl  = currUrl[0];
    if (currUrl.length > 1) 
    {
        var queryStr = currUrl[1].split('&');
        if (queryStr.length > 1) {
            newUrl += '?';
            for (var i = queryStr.length - 1; i >= 0; i--) {
                parms = queryStr[i].split('=');
                if (parms[0]=='show') {
                    parms[1]=='all'  ?  parms[1]='future'  :  parms[1]='all';
                    queryStr[i] = 'show='+parms[1];
                }                
                newUrl += queryStr[i];
                if (i > 0) newUrl += '&';
            }
        }
    } 
    window.location.href = newUrl;
}

/* 
    List sorting: Reload page with the 'orderBy' segment and the given field name
*/
function reloadListOrderBy(field)
{
    showSpinner();
    // get current url and query string
    var currUrl = window.location.href.split('?');
    var newUrl  = currUrl[0] + '?';
    if (currUrl.length > 1) 
    {
        var queryStr = currUrl[1].split('&');
        var orderbyFound = false;
        if (queryStr.length > 1) {
            for (var i = queryStr.length - 1; i >= 0; i--) {
                parms = queryStr[i].split('=');
                if (parms[0]=='orderby') {
                    queryStr[i] = 'orderby='+field;
                    orderbyFound = true;
                }
                if (parms[0]=='order') {
                    parms[1]=='desc'  ?  parms[1]='asc'  :  parms[1]='desc';
                    queryStr[i] = 'order='+parms[1];
                }                
                newUrl += queryStr[i];
                if (i > 0) newUrl += '&';
            }
        } 
        else {
            // retain the existing query string
            newUrl += queryStr[0];
        }
    } 
    // check if existing query string already contained a orderby param
    if (currUrl.length > 1 && ! orderbyFound) newUrl += '&';
    if (currUrl.length < 2 || ! orderbyFound) {
        newUrl += 'orderby='+field;
        newUrl += '&order=asc';
    }

    window.location.href = newUrl;
}


/*
    Show input field in header to filter data in this column or apply the filter if already set
*/
function showFilterField(field)
{
    // clear existing filter and reload page without
    if ($('#filter-'+field+'-clear').is(':visible')) 
    {
        var currUrl  = window.location.href.split('?');
        if (currUrl.length > 1) {
            // fade background and show spinner
            showSpinner();
            // remove filter elements from URL query string
            var queryStr = currUrl[1].split('&');
            var newUrl = currUrl[0];
            if (queryStr.length > 2) {
                newUrl += '?';
                for (var i = queryStr.length - 1; i >= 0; i--) {
                    if (queryStr[i].substr(0,6) != 'filter' ) {
                        newUrl += queryStr[i];
                        if (i > 0) newUrl += '&';
                    }
                }
            }
            window.location.href = newUrl;
            return;
        }
    }
         
    // define html code for search input field
    var newHtml = '<input id="filter-fffff-input" style="line-height: normal;" type="text" placeholder="search fffff">'
    newHtml    += '<i id="filter-fffff-submit" class="fa fa-check-square"> </i>';
    // did user click on the visible search icon?
    if ($('#filter-'+field+'-show').is(':visible')) 
    {
        // add new html code, replacing all placeholders with current field name
        $('#'+field+'-search').append(newHtml.replace(/fffff/g, field));
        $('#filter-'+field+'-input').delay(800).focus();
        $('#filter-'+field+'-show').hide();
    } 
    else 
    {
        if ( $('#filter-'+field+'-input').val().length > 0 ) {
            // fade background and show spinner
            showSpinner();

            var search =  $('#filter-'+field+'-input').val();
            var currUrl  = window.location.href.replace('#','');
            if (currUrl.indexOf('?')>1) {
                var newUrl = currUrl + '&filterby='+field+'&filtervalue='+search;
            } else {
                var newUrl = currUrl + '?filterby='+field+'&filtervalue='+search;
            }
            window.location.href = newUrl;
            return;
        }
        $('#filter-'+field+'-input').remove();
        $('#filter-'+field+'-submit').remove();
        $('#filter-'+field+'-show').show();
    }
}


/**
 * Function to open plan selected via date picker
 * better name: "openPlanByDate"
 */
function submitDate(date) 
{
    window.location.href = __app_url + '/cspot/plans/by_date/' + date.value;
}



/*
    allow Admins to delete an attached file (image)    
*/
function deleteFile(id)
{
    // TODO: Prompt for confirmation as this is irrevocable:
    if (! confirm('Are you sure to finally remove this file?')) {return;}
    // get token from form field
    $.ajax({
        url:    '/cspot/files/'+id+'/delete', 
        method: 'DELETE',
    }).done(function() {
        $('#file-'+id).remove();
    }).fail(function() {
        alert("image deletion failed!");
    });
}



/*
    On the Songs Detail page, 
    show the previously hidden song search input field
    and set the focus on it
*/
function showSongSearchInput(that, selector)
{
    $(that).hide();
    $(selector).show();
    $("input[name='search']").focus();
}



function changeTextAlign(selectorList, how) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    selectorList.forEach( function(selector) {
        element = $(selector);
        if (element.length>0) {
            $(element).css('text-align', how);
            localStorage.setItem(selector+'_text-align', how);
            console.log('LocalStorage for '+selector+' was set to '+localStorage.getItem(selector+'_text-align'));
        }
    });
}

/**
 * Increase or decrease font size of a given element
 *
 * stores the value in LocalStorage for later reference
 *
 * param  selectorList string or array of valid CSS selectors
 * return void
 */
function changeFontSize(selectorList, how) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    var factor = 1.1;
    if (how=='decrease')
        factor = 0.9;
    selectorList.forEach( function(selector) {
        element = $(selector);
        if (element.length>0) {
            fontSize = parseInt($(element).css('font-size')) * factor;
            if (fontSize<12 || fontSize>150) return;
            $(element).css('font-size', fontSize);
            localStorage.setItem(selector+'_font-size', fontSize);
            console.log('LocalStorage for '+selector+' was set to '+localStorage.getItem(selector+'_font-size'));
        }
    });
}

function getLocalStorValue(name) {
    value = localStorage.getItem(name);
    // console.log('LocalStorage for '+name+' was at '+value);
    return value;
}



/**
 * Ask user to allow fullscreen mode for presentations
 */
function requestFullScreen(element) {
    // Supports most browsers and their versions.
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullscreen;

    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}


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
            hdr = identifyHeadings(chords[i]).split('$');
            anchor = '';
            if (hdr.length>1 && hdr[1].length>0)
                anchor = '<a name="'+hdr[1]+'"></a>';
            $('#chords').append(anchor+'<pre class="m-b-0 '+hdr[0]+'">'+chords[i]+'</pre>');
        }
    }
}
function identifyHeadings(str)
{
    // identify headers by the first word in a line, case-insensitive

    patt = /^(coda|end)/i;
    if ( patt.test(str) ) 
        return ' p-l-3 bg-info$';

    patt = /^(Verse)/i;
    if ( patt.test(str) ) {
        nm=''; n=str.split(' '); 
        if (n.length>1) {
            nm=n[1].substr(0,1); 
            $('#jumplist').show();
            $('#jump-verse'+nm).show();
        }
        return ' p-l-3 bg-success$verse'+nm; }
    patt = /^(Chorus)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-chorus').show();
        return ' p-l-3 bg-info$chorus';
    }
    patt = /^(bridge)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-bridge').show();
        return ' p-l-3 bg-info$bridge';
    }

    patt = /^(Capo|Key|\()/;
    if ( patt.test(str) ) 
        return ' big text-primary$';

    patt = /^(Intro|Other|\()/;
    if ( patt.test(str) ) 
        return ' text-primary$';

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
    console.log('Trying to navigate to '+where);

    // prevent this if user is in an input field or similar area
    if (document.activeElement.tagName != "BODY") return;

    // get the element that contains the proper link
    a = document.getElementById('go-'+where);
    // link doesn't exist:
    if (a==null) return;

    // fade background and show spinner, but not in presentation mode!
    if ( document.baseURI.search('/present')<10 )
        showSpinner();

    if (a.onclick==null) {
        // try to go to the location defined in href
        window.location.href = a.href;
        return;
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

