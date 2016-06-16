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

// show blank lines between presentation items?
var showBlankBetweenItems;
var screenBlank = true;
var howManyVersesPerSlide;



/**
 * show multiple images as subsequent slides
 */
function prepareImages() 
{
    // make sure the images have the correct size, filling either width or height
    $('#main-content').css('text-align', 'center');
    $('.slide-background-image').height( window.innerHeight - $('.navbar-fixed-bottom').height());
    $('.slide-background-image').css('max-width', window.innerWidth);
    $('.app-content').css('padding', 0);
    var bgImages = $('.slide-background-image');
    $.each(bgImages, function(entry) {
        insertSeqNavInd(1*entry+1,entry,'slides');
    });
    // activate the first image
    todo = $('#slides-progress-0').attr('onclick');
    eval(todo);
}



/*
    Re-Formatting of Bible Texts

    Bible texts are delivered from the backend in the format in which either 
        bibleApi.org or biblehub.com delivers them.
    Both formats contain HTML code. This code must be removed and replaced
        in order to display all bible versions in a similar, controllable fashion.
    Both formats have in common that they deliver the text in <p> elements albeit
        with differing class names. They will be used to distringuish the formats.
    The <p> elements also contain child elements for verse numbers and footnotes etc
        which have to be removed (with only the verse numbers being retained)
*/
function reFormatBibleText() 
{
    // get bible reference text from item comment
    var refList = $('#item-comment').text().split(';');
    var refNo = 0;

    // get all the paragraphs (<p> elements) with bible text
    var p = $('.bible-text-present p');

    // empty the pre-formatted bible text containter and make it visible
    $('#bible-text-present-all').html('');
    $('#bible-text-present-all').show(); 
    // (the container initially was hidden by the backend. That way we avoid flickering!)
    
    // helper vars
    var verse_from=0, verse_to=199, verse, verno=1;

    // Now analyze each paragraph, reformat the bible text and add it back into the container
    $(p).each( function(entry) {
        text = $(this).text();
        clas = $(this).attr('class')
        console.log( 'CLASS: ' + clas + ' CONTENT: ' + $(this).html() );

        // write the bible ref as title
        if (clas=='bible-text-present-ref') {
            $.each(refList, function(index, value) {
                if (text.trim()=='') {return;}
                value = value.trim();
                if (value=='') {return;}
                // get access to each part of the bible ref: book, chapter, verse_form, verse-to and version
                var ref = splitBref(text);
                var rfc = splitBref(value);
                // is the bible text in the html source the same as in the reference?
                if (ref.book+ref.chapter == rfc.book+rfc.chapter ) {
                    // check if there was a vers unprinted from the previous Ref
                    if (verse != undefined && verse.length>2) { 
                        appendBibleText('p',verse,verno); verse = ''; }
                    // print the new Ref
                    if (refNo == index) {
                        appendBibleText('h1',value,'bible-text-ref-header');
                        refNo += 1;
                    }
                    verse_from = rfc.verse_from;
                    verse_to   = rfc.verse_to;
                }
            });
        }

        // Identify and disect NIV texts
        var cl4=clas.substr(0,4);
        var cl1=clas.substr(0,1);
        if (cl4=='line' || cl4=='pcon' || cl4=='reg' ) {
            // get all elements in one array
            elem = $(this).contents();
            // analyze each element and separate verse numbers and bible text
            $(elem).each( function() {
                eltext = $(this).text();
                // if (eltext=='13') {debugger;}
                if ($(this).attr('class')=='reftext') {
                    if (verse && verno != eltext) {
                        // only append text that is within the reference
                        if ( 1*verno >= 1*verse_from && 1*verno <= 1*verse_to ) {
                            appendBibleText('p',verse,verno); verse = ''; }
                        verno = eltext;
                    }
                    verse = '('+eltext+') '; } // add verse indicator at the front
                else if ( this.nodeName == '#text' || $(this).attr('class')=='name' ) {  // only add real text nodes
                    if (eltext.substr(0,1)=='\n') { eltext=eltext.substr(1); }
                    verse += eltext;
                }
            });
        }

        // find other translations and re-format the text
        else if ( cl1=='p' || cl1=='q' || cl1=='m' ) {
            // get all elements in one array
            elem = $(this).contents();
            // analyze each elements and separate verse numbers and bible text
            $(elem).each( function() {
                eltext = $(this).text();
                if ($(this).attr('class')=='v') {
                    if (verse && verno != eltext) {
                        appendBibleText('p',verse,verno); }
                    verno = eltext; 
                    verse = '('+eltext+') ';
                }
                else {
                    verse += eltext; }
            });
        }

        // if the verse is incomplete, it is because we need a mew line
        if ( verse != undefined && verse.length>2 ) { verse += '<br>'; }


    });
    // write remaining verse if not empty or beyond scope
    if ( verse != undefined  &&  verse.length > 2  &&  (1*verno <= 1*verse_to || !$.isNumeric(verno)) ) {
        appendBibleText('p',verse,verno) }

    // all is set and we can show the first verse
    advancePresentation();

}
/*
    Split a bible reference into an array of book, chapter, verse_from, verse_to
*/
function splitBref(text)
{
    if (!text) {return;}

    arr = new Array;
    ref = text.split(' ');
    nr = 0
    // check if book name starts with a number
    if ($.isNumeric(ref[0])) { 
        arr.book = ref[nr++] +' '+ ref[nr++]; }
    else if (text.substr(1,1)=='_') {
        arr.book = ref[nr++].replace('_',' '); }
    else { 
        arr.book = ref[nr++]; }
    // detect chapter and verse
    chve = ref[nr++].split(':');
    arr.chapter = chve[0];
    // is there a verse reference?
    if (chve.length>1) {
        // detect verse_from and verse_to
        vrs = chve[1].split('-');
        arr.verse_from = vrs[0];
        // analyze verse_to
        if (vrs.length>1) {
            // there could be another reference being attached...
            vto = vrs[1].split(/[,;]/);
            arr.verse_to = vto[0];
        }
    } 
    // no verse references detected, use generic values
    else {
        arr.verse_from = 0;
        arr.verse_to = 199;
    }

    // name of the bible version
    arr.version = ref[nr];

    // problem with differing naming conventions for Psalm in NIV vs others
    if (arr.book=='Psalms') { arr.book='Psalm' };
    return arr;
}
/*
    Append the reformatted bible text to the presentation and add a reference
    into the Sequence Indicator list in the Navbar (bottom right)
*/
function appendBibleText(type, text, verno)
{
    style = '';
    parts = '" ';
    id    = ' id="'+verno+'">';

    // actual bible text is inserted as <p> element, and hidden at first
    if (type=='p') {
        insertSeqNavInd( verno, verno, 'bible' ); 
        style=' style="display: none"';
        parts='-parts" ';
    }
    // if the text is a bible reference, will be treated as H1 element
    if (type=='h1') {
        // if there are multiple bible references in one item, we only 
        // want to have on H1 element, so we attach the next bible ref to the existing H1
        hText = $('#bible-text-ref-header').text();
        if (hText != '') {
            formatBibleRefHeader(hText, text);
            return;
        }
    }
    // append the constructed element now to the existing element
    $('#bible-text-present-all').append(
        '<'+type+style+' class="bible-text-present'+parts+id+text+'</'+type+'>'
        );   
}
/* 
    If an item contains more than one bible reference, we must format
    the header in an appropriate way to show the various references appropriately
*/
function formatBibleRefHeader( exisText, newText) {
    // split the references into a bRef array
    rfc = splitBref(exisText); // existing header
    bRef = splitBref(newText); // next header

    if (rfc.version==bRef.version) {
        et = exisText.split(' ');
        exisRef = et[0]+' '+et[1];
        // are we still in the same book with the new text?
        if (rfc.book==bRef.book) {
            // same chapter
            if (rfc.chapter==bRef.chapter) {
                $('#bible-text-ref-header').text(exisRef+','+bRef.verse_from+'-'+bRef.verse_to+' '+bRef.version);
            }
            // different chapter
            else {
                $('#bible-text-ref-header').text(exisRef+';'+bRef.chapter+':'+bRef.verse_from+'-'+bRef.verse_to+' '+bRef.version);
            }
            return;
        }
        // different book
        else {
            $('#bible-text-ref-header').text(exisRef+';'+bRef.book+' '+bRef.chapter+':'+bRef.verse_from+'-'+bRef.verse_to+' '+bRef.version);
        }
    }
    $('#bible-text-ref-header').append('; ' + newText);   
}


function countLines(where) {
    var divHeight = document.getElementById(where).offsetHeight
    var elem = document.getElementById(where);
    var lineHeight = parseInt(elem.style.fontSize);
    var lines = divHeight / lineHeight;
    return parseInt(lines);
}


/*
    the Sequence indicators at the bottom right could 
    get too long, so we need to hide some parts
*/
function checkSequenceIndicatorLength()
{
    // max items shown before or after the current item
    var limit = 4;
    if (window.innerWidth < 800) {
        limit = 3; }
    if (window.innerWidth > 1250) {
        limit = 5; }

    var what = '.lyrics';

    // get the list of sequence indicators
    var seq = $(what+'-progress-indicator');
    if (seq.length > 0  &&  seq.length < 9) {return;}
    // no lyrics found so we might have bible texts
    if (seq.length == 0) {
        what = '.bible';
        var seq = $(what+'-progress-indicator');
        if (seq.length < 9) {return;}
    }

    // lets find the currently active sequence and then hide much earlier and much later parts
    var active_id = getProgressIDnumber(what+'-progress-indicator.bg-danger');

    // html elements to be inserted where more indicators are hidden
    var moreIndFW = '<span class="more-indicator"><i class="fa fa-angle-double-right"></i></span>';
    var moreIndBW = '<span class="more-indicator"><i class="fa fa-angle-double-left"></i> </span>';
    // first remove all old 'more' indicators
    $('.more-indicator').remove();

    // walk through the list of indicators and hide those 
    // that are too far away from the currently active one 
    $(seq).each(function(entry){
        // get this element's ID number
        var thisID = 1*getProgressIDnumber(this);
        if ( thisID+limit-2 < active_id  ||  thisID-limit > active_id ) {
            $(this).hide();
        } else { 
            $(this).show(); 
            if (thisID+limit-2 == active_id) {
                $(this).prepend(moreIndBW);}
            if (thisID-limit == active_id) {
                $(this).append(moreIndFW); }
        }
    });
}
// find the sequence number in the element ID attribute
function getProgressIDnumber(fromWhat)
{
    var current = $(fromWhat);
    if (current.length==0) {return 0;}
    var curr_id = $(fromWhat).attr('id').split('-');
    if (curr_id.length<3) {return;}
    return parseInt( curr_id[2] );
}

/*
    check if there are more lyric parts than 
        indicated in the spre-defined equence due to blank lines discoverd in the lyrics
*/
function compareLyricPartsWithSequence()
{
    // get the predefined sequence
    sequenceDiv= $('#sequence').text();
    //console.log('found predefined sequence: ' + sequenceDiv);
    sequence = ( $('#sequence').text() ).split(',');

    newSequence = '';
    nr = 0;
    // walk through the pre-defined sequence
    for (var i in sequence) {
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

    var chorus = false;   // we still need to find out if a chorus exists
    var nr = 0;          // indicates current lyric part number
    var verseNumInt = 0 // 
    var insChorus = 1; // indicates verse number afer which we have to insert a chorus
    var chorusSeq=[]; // contains CSV list of chorus parts

    // go through the list of lyric parts (as generated in function "reDisplayLyrics()")
    $(lyrList).each(function(entry) 
    {
        id = $(this).attr('id');  // get name of that lyric part
        var pname = id.substr(0,5);
        if ( pname == 'verse' ) {
            verseNum = id.substr(5);
            verseNumInt = 1*id.substr(5,1);
            if (chorus && verseNumInt > insChorus) {
                for (var i in chorusSeq) {
                    sequence += chorusSeq[i] + ',';
                    insertSeqNavInd(chorusSeq[i], nr++);
                }
                insChorus = verseNumInt;
            }
            sequence += verseNum + ',';
            insertSeqNavInd(verseNum, nr++);
        }
        // some lyrics don't conaint any headers so we show the first part under 'start-lyrics'
        if (pname == 'start') {
            sequence += 's,';
            insertSeqNavInd('s', nr++);
        }
        // collect all chorus parts until we insert them before the next verse or at the end
        if (pname == 'choru') {
            chorus = true;
            chPart = 'c1'+id.substr(7);
            chorusSeq.push( chPart );  
        }
    });
    // insert remaining chorus, if needed
    if (chorus && verseNumInt >= 1) {
        for (var i in chorusSeq) {
            sequence += chorusSeq[i] + ',';
            insertSeqNavInd(chorusSeq[i], nr++);
        }
    }

    // do we also have an ending?
    if ($('[id^=ending]').length>0) {
        sequence += 'e';
        insertSeqNavInd('e', nr);
    }

    // now write the new sequence into the proper element
    $('#sequence').text(sequence);
}
/* 
    Insert the Sequence Navigation indicators into the navbar 
*/
function insertSeqNavInd(what, nr, where)
{
    // set default action
    where = where || 'lyrics';

    console.log('inserting sequence NavBar indicator for '+ what + ' as '+where+' part # ' + nr);

    data = '<span id="'+where+'-progress-' + nr + '" class="'+where+'-progress-indicator" ' +
           'data-show-status="unshown" onclick="'+where+'Show(' + "'" + what + "'" + ');">';
    data += formatSeqInd(what)+'&nbsp;</span>';

    $('#lyrics-sequence-nav').append( data );
}
/*
    special formatting for sequence indicators of lyric parts
*/
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

    // make sure the list of indicators doesn't get too long
    checkSequenceIndicatorLength();

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

        if (direction=='forward') {
            // loop through all sequence items and find the next that wasn't shown yet
            found = false;
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    console.log('found ' + $(this).attr('id'));
                    $(this).data().showStatus = 'done';
                    $('.lyrics-progress-indicator').removeClass('bg-danger');
                    $(this).addClass('bg-danger');
                    todo = $(this).attr('onclick');
                    eval( todo );
                    // $(this).click();
                    return false;
                }
                if (found) {return false;}
            });
            // all items were shown, so we can move to the next item
            if (! found) {
                //$('#present-lyrics').fadeOut();
                navigateTo('next-item');
                return;
            }
        }
        // no, we try to move backwards in the sequence of song parts
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
                    todo = $(seq[i-1]).attr('onclick');
                    eval( todo );
                    //$(seq[i-1]).click();
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
        if (direction=='forward') {
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    console.log('found ' + $(this).attr('id'));
                    $(this).data().showStatus = 'done';
                    $('.bible-progress-indicator').removeClass('bg-danger');
                    $(this).addClass('bg-danger');
                    todo = $(this).attr('onclick');
                    eval( todo );
                    return false; // escape the each loop...
                }
            });
            if (! found) {
                //$('.bible-text-present').fadeOut();
                navigateTo('next-item');
                return;
            }
        } 
        else {
            found=false;
            for (var i = seq.length - 1; i >= 0; i--) {
                if ( $(seq[i]).data().showStatus == 'done') {
                    console.log('found ' + $(seq[i]).attr('id'));
                    if (i<1) {break;} // we can't move any further back....
                    found=true;
                    $(seq[i]).data().showStatus = 'unshown';  // make this part 'unshown'
                    $('.bible-progress-indicator').removeClass('bg-danger');
                    $(seq[i-1]).addClass('bg-danger');
                    todo = $(seq[i-1]).attr('onclick');
                    eval( todo );
                    break; // escape the for loop...
                }
            }
            if (! found) {
                //$('.bible-text-present').fadeOut();
                navigateTo('previous-item');
                return;
            }
        }
    }

    // we are showing images
    else if ($('.slide-background-image').length>0) {
        var seq = $('.slides-progress-indicator');
        // loop through all sequence items and find the next that wasn't shown yet
        found = false;
        if (direction=='forward') {
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    console.log('found ' + $(this).attr('id'));
                    $(this).data().showStatus = 'done';
                    $('.slides-progress-indicator').removeClass('bg-danger');
                    $(this).addClass('bg-danger');
                    todo = $(this).attr('onclick');
                    eval( todo );
                    return false; // escape the each loop...
                }
            });
            if (! found) {
                navigateTo('next-item');
                return;
            }
        } 
        else {
            found=false;
            for (var i = seq.length - 1; i >= 0; i--) {
                if ( $(seq[i]).data().showStatus == 'done') {
                    console.log('found ' + $(seq[i]).attr('id'));
                    if (i<1) {break;} // we can't move any further back....
                    found=true;
                    $(seq[i]).data().showStatus = 'unshown';  // make this part 'unshown'
                    $('.slides-progress-indicator').removeClass('bg-danger');
                    $(seq[i-1]).addClass('bg-danger');
                    todo = $(seq[i-1]).attr('onclick');
                    eval( todo );
                    break; // escape the for loop...
                }
            }
            if (! found) {
                navigateTo('previous-item');
                return;
            }
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

    NOTE: headers must be in a single line and text enclosed om square brackets! E.g.: "[Verse 1]"
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
            // we have no headings in this lyris, so we invent one....
            if (curPart == '') { 
                hdr = curPart = 'verse1';
                insertNewLyricsSlide(newDiv, newLyr, divNam, lines);
                divNam = hdr;
                newLyr = '';
                lines  = 0;
                newDiv = '</div><div id="'+hdr+'" class="lyrics-parts" ';
            } else {
                // an empty line within a song part is treated as a sub-header
                // ==> 'verse1' will become 'verse1a'
                hdr = curPart + String.fromCharCode(apdxNam++);
            }
        }
        // or we already have a pre-defined header line for this song part
        else { 
            // find verse indicator (can be first word in the lyrics line, like: "[1] {first line of lyrics}")
            hdr = identifyLyricsHeadings( lyricsLine.split(' ')[0] ); 
            if (hdr.length>0) { 
                // verse indicator was found!
                curPart = hdr; 
                var apdxNam= 97; // = 'a': reset appendix indicator (for forced lyric parts)
                // use 2nd part of initial lyricsline as actualy lyrics
                lyricsLine = lyricsLine.split('] ')[1]; // this will be 'undefined' if line was just the indicator!
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
        // actual lyrics - insert as P element
        if (lyricsLine != undefined) {
            lines += 1;
            newLyr += '<p class="text-present m-b-0">'+lyricsLine+'</p>';
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

    // insert the lyrics back into the HTML doc
    $('#present-lyrics').append( newDiv + newLyr + '</div>' );
    // make sure this part is still hidden
    $('#'+divNam).hide();
    // make the hidden select button for this part visible
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



/** 
 * Navigate to next slide or item
 *
 * @string direction - part of the ID of an anchor on the calling page that executes the navigation
 */
function navigateTo(where) 
{
    console.log('Navigating to '+where);

    // prevent this if user is in an input field or similar area
    if (document.activeElement.tagName != "BODY") return;

    // get the element that contains the proper link
    a = document.getElementById('go-'+where);
    // link doesn't exist:
    if (a==null) return;

    // fade background and show spinner, but not in presentation mode!
    if ( document.baseURI.search('/present')<10 )
        showSpinner();

    // in presentation Mode, do we want a blank slide between items?
    if (showBlankBetweenItems && screenBlank ) {
        screenBlank = false;
        // check if there is an empty slide/item (an item without lyrics, bibletext or images)
        var reg = /^[\s]+$/; // regex for a string containing only white space.
        var main  = $('#main-content').text();
        // if the slide contains anything but spaces, we were still presenting something
        // and we now show an empty (blank) slide
        if (! reg.test(main)) {
            $('#main-content').html('<div>.</div>');
            console.log('inserting empty slide...');
            return;
        }
        console.log('slide was already empty, proceeding to next item...');
        // otherwise, if the slide/item was empty anyway, we proceed to the next item
    }

    // make content disappear slowly...
    $('#main-content').fadeOut();
    $('#bottom-fixed-navbar>ul').fadeOut();

    if (a.onclick==null) {
        // try to go to the location defined in href
        window.location.href = a.href;
        return;
    }    
    // try to simulate a click on this element
    a.click();
}




function slidesShow(what)
{
    var parts = $('.slide-background-image');
    var indic = $('.slides-progress-indicator');
    var found = false;
    // loop through all bible verses until number 'what' is found...
    for (var i=0; i<parts.length; i++) 
    {
        if ($(parts[i]).data().slidesId == what)             
        {
            found = true;
            $(parts[i]).show();
            $(indic[i]).addClass('bg-danger');
            $(indic[i]).data().showStatus = 'done';
        } 
        else if ( found ) {
            $(indic[i]).data().showStatus = 'unshown';
            $(indic[i]).removeClass('bg-danger');
            $(parts[i]).hide();
        }
        else 
        {
            $(parts[i]).hide();
            $(indic[i]).removeClass('bg-danger');
            $(indic[i]).data().showStatus = 'done';
        }
    }
}

function bibleShow(what)
{
    var parts = $('.bible-text-present-parts');
    var indic = $('.bible-progress-indicator');
    var found = -1;
    // loop through all bible verses until number 'what' is found...
    for (var i=0; i<parts.length; i++) 
    {
        if ($(parts[i]).attr('id') == what)             
        {
            found = i;
            $(parts[i]).show();
            $(indic[i]).addClass('bg-danger');
            $(indic[i]).data().showStatus = 'done';
        } 
        else if ( found>=0 ) {
            $(indic[i]).data().showStatus = 'unshown';
            $(parts[i]).hide();
        }
        else 
        {
            $(parts[i]).hide();
            $(indic[i]).removeClass('bg-danger');
            $(indic[i]).data().showStatus = 'done';
        }
    }
}

// called from the lyrics buttons made visible in reDisplayLyrics function
function lyricsShow(what)
{
    // from the short version of a 'what', determine the proper ID value of the desired song part
    if (what.length<4) {
        what = decompPartCode(what);
    } else {
        // As the user choose a song part directly, we need to correct the automatic advancement!

        // first get the list of all progress indicators
        var seq = $('.lyrics-progress-indicator');
        var gefunden = false;
        // check each to see where we want to be
        $(seq).each(function(entry){
            // always remove the previous seq indicator
            $(this).removeClass('bg-danger');

            // as long as we haven't found the item clicked...
            if (! gefunden) {
                // try to recompile the action for this button into the name of the song part
                // e.g. if the action is onclick="showLyrics('1')" then the song part is 'verse1' etc
                indic = ( $(this).attr('onclick') ).split("'");
                if (indic.length>1) {indic=indic[1]} else {return false;}
                gesucht = decompPartCode(indic); // 'gesucht' is the song part for the current sequence indicator
                if (gesucht==indic) {return false;}
            }
            // now we can see if the song part the parent function whats to show is the same
            // as the current part (gesucht) in the sequence indicator list
            if (what == gesucht && ! gefunden) {
                // now we need to mark all following song parts as 'unshown'
                gefunden = true;
                $(this).addClass('bg-danger');
                $(this).data().showStatus = 'done';
            }
            // mark the rest as unshown
            else if ( gefunden ) {
                $(this).data().showStatus = 'unshown';
            } else {
                $(this).data().showStatus = 'done';
            }
        });
    }
    // do nothing if the object doesn't exist...
    if ( $('#'+what).length == 0 )  { return }

    console.log('showing song part called '+what);
    
    // first, fade out the currently shown text, then fade in the new text
    $('.lyrics-parts').fadeOut().promise().done( function() { $('#'+what).fadeIn() } );

    // elevate the currently used button
    $('.lyrics-show-btns').removeClass('btn-danger');       // make sure all other buttons are back to normal
    $('#btn-show-'+what).removeClass('btn-info-outline');   // aremove ouline for this button
    $('#btn-show-'+what).addClass('btn-danger');            // add warning class for this button
}
function decompPartCode(what) {
    apdx = '';
    fc = what.substr(0,1);
    if ( $.isNumeric(fc) || fc != 'c' ) {
        apdx = what.substr(1);   
        what = identifyLyricsHeadings('['+fc+']')+apdx;
    } else {
        apdx = what.substr(2);
        what = identifyLyricsHeadings('['+what.substr(0,2)+']')+apdx;
    }
    return what;
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
        case '[s]': return 'start-lyrics';
        case '[chorus 2]': return 'chorus2';
        case '[t]': return 'chorus2';
        case '[chorus]': return 'chorus1';
        case '[chorus1]': return 'chorus1';
        case '[c]': return 'chorus1';
        case '[ch]': return 'chorus1';
        case '[c1]': return 'chorus1';
        case '[c2]': return 'chorus2';
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


/**
    Open modal popup to show linked YT video
*/
function showYTvideoInModal(ytid, title)
{
    //https://www.youtube.com/"+ ytid.substr(0,2)=="PL" ? 'playlist?list=' : 'watch?v=' + ytid }}";
    $('#snippet-modal-title').text(title);
    $('#snippet-modal-content')
        .html('<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ytid+'" frameborder="0" allowfullscreen></iframe>');
    $('.help-modal').modal();
}


/**
    When user presses enter in the Songs List view, check 
    which filter field is open and trigger its function
 */
function findOpenFilterField() 
{
    // check which search fields open
    var searchFields = $("[id^=filter-]");
    $.each(searchFields, function(entry) {
        if ( $(searchFields[entry]).is(':visible') ){
            var id = $(searchFields[entry]).attr('id').split('-');
            if (id[2] == 'input') {  // only look at input elements!
                var action = $('#'+id[1]+'-search').attr('onclick');
                eval(action);
                return;
            }
        }
    });
}

/*
    Show input field in header to filter data in this column or apply the filter if already set
*/
function showFilterField(field)
{
    // Is this field already visible?
    if ($('#filter-'+field+'-clear').is(':visible')) 
    {
        // check if there is a query string in the URL
        var currUrl  = window.location.href.split('?');
        // then clear existing filter and reload page without a filter
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

    // check if there are other search fields open
    var searchFields = $("[id^=filter-]");
    $.each(searchFields, function(entry) {
        if ( $(searchFields[entry]).is(':visible') ){
            var fld = $(searchFields[entry]).attr('id').split('-')[1];
            if (fld != field) {
                $('#filter-'+fld+'-input').remove();
                $('#filter-'+fld+'-submit').remove();
                $('#filter-'+fld+'-show').show();
            }
        }
    });
         
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
        // Did user enter search data?
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

/**
 * called from the configuratino button on the navbar
 */
function configBlankSlides() {
    var sett = ! $('#configBlankSlides').prop( "checked" );
    console.log('User changed setting for "Show empty slides between items" to ' + sett );
    localStorage.setItem('configBlankSlides', sett);
}
function changeConfigShowVersCount() {
    var sett = $('#configShowVersCount').val();
    console.log('User changed setting for "Show how many bible verses per slide" to ' + sett );
    localStorage.setItem('configShowVersCount', sett);
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
            fontSize = parseFloat($(element).css('font-size')) * factor;
            if (fontSize<8 || fontSize>150) return;
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
        return ' p-l-3 bg-success$verse'+nm; 
    }
    patt = /^(Chorus)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-chorus').show();
        return ' p-l-3 bg-info$chorus';
    }
    patt = /^(Pre-Chorus)/i;
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
    
    var patt = /[klopqrtvwxyz1368]/g;
    if ( patt.test(str) ) return false;
    
    var patt = /\b[CDEFGAB](?:#{1,2}|b{1,2})?(?:maj7?|min7?|sus2?|sus4?|m?)\b/g;
    if ( patt.test(str) ) return true;
    
    var patt = /\b[CDEFGB]\b/g;
    if ( patt.test(str) ) return true;

    return false;
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




/**
 * On the Team page, show the role select element once the user was selcted
 * 
 * param 'who' refers to the element from which this method was called
 */
function showRoleSelect(who, role_id)
{    
    // default value for role_id
    role_id = role_id || undefined;

    // make the role selection elements (radio buttons) visible
    $('#select-team-role').fadeIn();

    // now show the comment input and submit button
    $('#comment-input').fadeIn();
    $('#submit-button').fadeIn();

    // grab the div around the radio buttons 
    var roleSelectBox = $('#select-role-box');
    // create a radio item
    var radio1 = '<label class="c-input c-radio role-selector-items"><input id="';
    var radio2 = '" name="role_id" type="radio"><span class="c-indicator"></span>';
    var radio3 = '</label>';
    
    // make sure we have a proper JSON object with all users and all their roles
    // ('userRolesArray' was created in a javascript snippet in the team.blade.php file)
    if (typeof(userRolesArray)=='object') {
        var user = userRolesArray[who.value];
        var roles = user.roles;
        // first empty the select box
        $('#select-role-box').html('');
        // add each role as a radio button and label
        for (var i in roles) {
            var radio = radio1 + 'role_id-'+roles[i].role_id+'" ';
            if (roles[i].role_id == role_id) {
                radio += 'checked ';
            }
            radio += 'value="' + roles[i].role_id;
            radio += radio2 + roles[i].name + radio3;
            roleSelectBox.append(radio);
        }
        var instruments = user.instruments;
        if (instruments.length > 0) { 
            $('#show-instruments').html('(plays: '); }
        else {
            $('#show-instruments').html(); }
        for (var i in instruments) {
            var text = instruments[i].name;
            if (i < instruments.length-1) {
                text += ', '; } 
            else {
                text += ')'; }
            $('#show-instruments').append(text);
        }
    }
    if (role_id==undefined) {
        // select the first item, so that the user MUST make a choice
        $('.role-selector-items').first().click();
    }
}

/**
 * Record a user's availability for a certain plan
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



var timeoutID;
function delayedCloseFlashingModals(selector) {
    timeoutID = window.setTimeout( closeMyModal, 3000, selector);
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


$(document).ready(function() {


    /*
        formatting of pagination buttons
    */
    if ($('.pagination').length>0) {
        $(function() {
            // add missing classes and links into the auto-geneerated pagination buttons
            $('.pagination').children().each(function() { $(this).addClass('page-item'); });
            $('.page-item>a').each(function() { $(this).addClass('page-link'); });
            var pgActive = $('.active.page-item').html();
            $('.active.page-item').html('<a class="page-link" href="#">'+pgActive+'</a>');
            $('.disabled.page-item').each(function() {
                var innerHtml = $(this).html();
                $(this).html('<a class="page-link" href="#">'+innerHtml+'</a>');
            });
        });
    }


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
                                var ui_class = '';
                                var highlight = SelectedDates[dot];
                                if (highlight) {
                                    if (highlight==='Today') {
                                        return [true, '', highlight]; }
                                    return [true, "ui-highlighted", highlight]; }
                                else {
                                    if (date.getDay()==0)
                                        return [true, 'ui-datepicker-sunday', '']; 
                                    return [true, '', '']; 
                                }
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
    $("#file").on('mouseover', function() {
        // do this only once ...
        if ($('.submit-button').is(':visible')) return;
        $('.submit-button').show();
        blink('.submit-button');
    });
    $("input, textarea, input:radio, input:file").click(function() {
        // change background color of those fields
        $(this).css("background-color", "#D6D6FF");

        // do this only once ...
        if ($('.submit-button').is(':visible')) return;

        // show submit or sabe buttons
        $('.submit-button').show();
        blink('.submit-button');
    });



    /***
     * Get array with all bible books with all chapters and number of verses in each chapter
     */
    if (window.location.href.indexOf('/cspot/')>10) {
        $.get(__app_url+'/bible/books/all/verses', function(data, status) {

            if ( status == 'success') {
                bibleBooks = data;
            }
        });
    }



    /**
     * items on Plan page can be moved into new positions
     */
    if ($("#tbody-items").length) {
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
    }

    
    /**
     * On the Songs List page, allow some key codes
     */
    if (window.location.href.indexOf('cspot/songs')>10) {

        $(document).keydown(function( event ) {
            console.log('pressed key code: '+event.keyCode);
            switch (event.keyCode) {
                case 13: findOpenFilterField(); break; // Enter key
                default: break;
            }            
        });

    }
    
    /*
        On presentation views, allow mouse-click to advance to next or prev. item
    */
    if ($('#main-content').length) {
        // intercept mouse clicks into the presentation area
        $('#main-content').contextmenu( function() {
            return false;
        });

        // allow rght-mouse-click to move one slide or item back
        $('#main-content').on('mouseup', function(event){
            event.preventDefault();
            if (event.which == 1) {
                advancePresentation(); }
            if (event.which == 3) {
                advancePresentation('back'); }
        });        
    }

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
     * prepare lyrics or bible texts or image slides for presentation
     */
    if ( window.location.href.indexOf('/present')>10 ) {

        // start showing bible parts if this is a bible reference
        if ($('.bible-text-present').length) {
            reFormatBibleText(); }

        // re-format the lyrics
        if ($('#present-lyrics').length) {
            reDisplayLyrics(); }

        // center and maximise images
        if ( $('.slide-background-image').length ) {
            prepareImages();
        }

        /**
         * Check some user-defined settings in the Local Storage of the browser
         */

        // check if user wants a blank slide between items
        showBlankBetweenItems = getLocalStorValue('configBlankSlides');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (showBlankBetweenItems=='true') {
            $('#configBlankSlides').prop( "checked", true );
        }

        // how many bible verses per slide?
        howManyVersesPerSlide = getLocalStorValue('configShowVersCount');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (howManyVersesPerSlide>0 && howManyVersesPerSlide<6) {
            $('#configShowVersCount').val( howManyVersesPerSlide );
        }

        // check if user has changed the default font size and text alignment for the presentation
        textAlign = getLocalStorValue('.text-present_text-align');
        $('.text-present').css('text-align', textAlign);
        $('.bible-text-present').css('text-align', textAlign);
        $('.bible-text-present>p').css('text-align', textAlign);
        $('.bible-text-present>h1').css('text-align', textAlign);

        fontSize = getLocalStorValue('.text-present_font-size');
        if ($.isNumeric(fontSize)) {
            $('.text-present').css('font-size', parseInt(fontSize));
        }
        $('.text-present').show();

        fontSize = getLocalStorValue('.bible-text-present_font-size');
        if ($.isNumeric(fontSize)) {
           $('.bible-text-present').css('font-size', parseInt(fontSize));
           $('.bible-text-present>p').css('font-size', parseInt(fontSize));
           $('.bible-text-present>h1').css('font-size', parseInt(fontSize));
        }

        // check if we have a predefined sequence from the DB
        sequence=($('#sequence').text()).split(',');

        // check if there are more lyric parts than 
        // indicated in the sequence due to blank lines discoverd in the lyrics
        if (sequence.length>1) 
            compareLyricPartsWithSequence();

        // auto-detect sequence if it is missing
        if (sequence.length<2) {
            createDefaultLyricSequence();
            sequence=($('#sequence').text()).split(',');
        }

        // make sure the sequence indicator isn't getting too big! 
        checkSequenceIndicatorLength();

        // make sure the main content covers all the display area, but that no scrollbar appears
        $('#main-content').css('max-height', window.innerHeight - $('.navbar-fixed-bottom').height());
        $('#main-content').css('min-height', window.innerHeight - $('.navbar-fixed-bottom').height() - 10);

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

});
