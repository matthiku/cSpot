/*\
|*|
|*|
|*|
|*#=========================================================================================== SLIDE PRESENTATION HELPERS
|*|
|*|
|*| prepares raw song lyrics/bible texts and turns them into single slides
|*|
\*/


/* for eslint */
if (typeof($)===undefined) {
    var $, cSpot;
}
 

/*
 * Prepare the presentation of lyrics, chords etc.
 *
 * called from loadFromLocalCache method in main.js
 */
function preparePresentation()
{
    // check if we have a VideoClip item or just lyrics
    if ($('#videoclip-url').length) {
        var videoclipUrl = $("#videoclip-url").text();
        ;;;console.log('Current item is a Video Clip');
    }

    // instead, have just lyrics or bible verses or images
    else { 
        if ($('#present-lyrics').length) {
            // re-format the lyrics
            reDisplayLyrics(); 
        }

        // start showing bible parts if this is a bible reference
        if ($('.bible-text-present').length) {
            reFormatBibleText(); 
        }

        // center and maximise images
        if ( $('.slide-background-image').length ) {
            prepareImages(); 
        }
    }


    /* configuration of the color picker
    */
    $(".colorPicker").spectrum({
        appendTo : '#colorPicker-container',
        showPaletteOnly: true,
        togglePaletteOnly: true,
        togglePaletteMoreText: 'more',
        togglePaletteLessText: 'less',
        color: 'yellow',
        palette: [
            ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
            ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
            ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
            ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
            ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
            ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
            ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
            ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
        ]
    });
    $(".BGcolorPicker").spectrum({
        appendTo : '#colorPicker-container',
        showPaletteOnly: true,
        togglePaletteOnly: true,
        togglePaletteMoreText: 'more',
        togglePaletteLessText: 'less',
        color: '#373a3c',
        palette: [
            ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
            ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
            ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
            ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
            ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
            ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
            ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
            ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
        ]
    });


    /**
     * Check some user-defined settings in the Local Storage of the browser
     */
    getLocalConfiguration()


    // check if we have a predefined sequence from the DB
    var sequence = ($('#sequence').text()).split(',');

    // check if there are more lyric parts than 
    // indicated in the sequence due to blank lines discoverd in the lyrics
    if (sequence.length>1) 
        compareLyricPartsWithSequence();

    // auto-detect sequence if it is missing
    if (sequence.length<2) {
        createDefaultLyricSequence();
        sequence = ($('#sequence').text()).split(',');
    }

    // make sure the sequence indicator isn't getting too big! 
    checkSequenceIndicatorLength();

    // make sure the main content covers all the display area, but that no scrollbar appears
    if ( $('#bottom-fixed-navbar').length ) {
        $('#main-content').css('max-height', window.innerHeight - $('#bottom-fixed-navbar').height());
        $('#main-content').css('min-height', window.innerHeight - $('#bottom-fixed-navbar').height());
    }



    /**
     * Save the new content into the local storage for offline presentations!
     */
    if (cSpot.presentation.useOfflineMode) {
        saveMainContentToLocalStorage();
    } 

    // start showing the time in the presentation
    show_time();
}



function show_time()
{
    var date = new Date();

    var h = date.getHours();
    if (h<10)
        h = "0"+h;
    var m = date.getMinutes();
    if (m<10)
        m = "0"+m;
    var result = h+":"+m;
    
    var id = 'time-of-day';
    document.getElementById(id).innerHTML = result;
    setTimeout('show_time("'+id+'");','60000');

    return true;
}


/*\
|* >------------------------------------------ PREPARE IMAGE SLIDES
\*/


/**
 * show multiple images as subsequent slides
 *  
 * This function is called in the document.ready method, when it finds this element:
 *      $('.slide-background-image')
 */
function prepareImages() 
{
    // make sure the images have the correct size, filling either width or height
    $('#main-content'           ).css('text-align', 'center');
    if ( $('#bottom-fixed-navbar').length )
        $('.slide-background-image' ).height( window.innerHeight - $('#bottom-fixed-navbar').height());
    $('.slide-background-image' ).css('max-width', window.innerWidth);
    $('.app-content'            ).css('padding', 0);
    
    // get list of all images and prepare them as individual slides
    var bgImages = $('.slide-background-image');

    $.each(bgImages, function(entry) {
        insertSeqNavInd( 1*entry+1, entry, 'slides' );
        // background image counter
        cSpot.presentation.BGimageCount += 1;
    });

    // activate (show) the first image
    var todo = $('#slides-progress-0').attr('onclick');
    eval(todo);
}



/*\
|* >------------------------------------------ PREPARE BIBLE TEXT SLIDES
\*/


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

    This function is called in the preparePresentation method, when it finds this element:
        $('.bible-text-present')
*/
function reFormatBibleText() 
{
    // get bible reference text from item comment
    var refList = $('#item-comment').text().split(';');
    var refNo = 0;
    // pattern to detect bible references
    var pattern = /(\d*)\s*([a-z]+)\s*(\d+)(?::(\d+))?(\s*-\s*(\d+)(?:\s*([a-z]+)\s*(\d+))?(?::(\d+))?)?/i;

    // remove unneeded parts of NIV code
    $('.sectionhead').remove();
    $('.nivfootnote').remove();

    // get all the paragraphs (<p> elements) with bible text
    var p = $('.bible-text-present p, .bible-text-present div');

    // empty the pre-formatted bible text containter and make it visible
    $('#bible-text-present-all').html('');
    // (the container initially was hidden by the backend. That way we avoid flickering!)
    $('#bible-text-present-all').show(); 
    
    // helper vars
    var verseList = [], verse_from=0, verse_to=199, verse, verno=1;

    // Now analyze each paragraph, reformat the bible text and add it back into the container
    $(p).each( function() {
        var text = $(this).text();
        var clas = $(this).attr('class')
        // ;;;console.log( 'CLASS: ' + clas + ' CONTENT: ' + $(this).html() );

        // write the bible ref as title
        if (clas=='bible-text-present-ref') {
            $.each(refList, function(index, value) {
                if (text.trim()=='') {return;}
                value = value.trim();
                if (value=='') {return;}

                // ignore if this is not a bible reference string
                if ( value.search(pattern) < 0 ) {
                    refNo += 1;
                    return;
                }
                // get access to each part of the bible ref: book, chapter, verse_from, verse-to and version
                var ref = splitBref(text);
                var rfc = splitBref(value);
                // is the bible text in the html source the same as in the reference?
                if (ref.book+ref.chapter == rfc.book+rfc.chapter ) {
                    // check if there was a vers unprinted from the previous Ref
                    if (verse !== undefined && verse.length>2  &&  verseList.indexOf(verno)>=0 ) { 
                        appendBibleText('p',verse,verno); verse = ''; }
                    // print the new Ref
                    if (refNo == index) {
                        appendBibleText('h1',value,'bible-text-ref-header');
                        refNo += 1;
                    }
                    verse_from = rfc.verse_from;
                    // if verse_to is ommitted, we use verse_from
                    if (rfc.verse_to !== undefined)
                        verse_to   = rfc.verse_to;
                    else 
                        verse_to   = rfc.verse_from;
                    verseList = verseList.concat( updateVerseList(verse_from, verse_to) );
                }
                // request whole chapter and write it into local cache for later reference
                localCacheBibleText( rfc );
            });
        }

        // Identify and disect NIV texts
        var cl4=clas.substr(0,4);
        var cl1=clas.substr(0,1);
        var elem;
        if (cl4=='line' || cl4=='pcon' || cl4=='reg' || cl4=='open' || cl4=='chap' || cl4=='emb' || cl4=='red' ) {
            // get all elements in one array
            elem = $(this).contents();
            // analyze each element and separate verse numbers and bible text
            $(elem).each( function() {
                var eltext = $(this).text();
                // if (eltext=='13') {debugger;}
                var thisCls = $(this).attr('class');
                if (thisCls=='reftext') {
                    if (verse && verno != eltext) {
                        // only append text that is within the reference
                        if ( verseList.indexOf(1*verno) >= 0 ) {
                            appendBibleText('p', verse, verno); 
                            verse = ''; 
                        }
                        // format subsequent verses in this chapter, but 
                        //      do not show them - unless requested by the presenter!
                        else if ( 1*verno > 1*verse_to ) {
                            appendBibleText('p', verse, verno, false); 
                            verse = ''; 
                        }
                        verno = eltext;
                    }
                    verse = '('+eltext+') '; } // add verse indicator at the front
                else if ( this.nodeName == '#text' || thisCls=='name' || thisCls=='red' ) {  // only add real text nodes
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
                var eltext = $(this).text();
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
        if ( verse !== undefined && verse.length>2 ) { verse += '<br>'; }


    });
    // write remaining verse if not empty or beyond scope
    if ( verse !== undefined  &&  verse.length > 2  &&  (1*verno <= 1*verse_to || !$.isNumeric(verno)) ) {
        appendBibleText( 'p', verse, verno ) 
    }
    // also write remaining verse (if any), but hide it (only for NIV texts)
    else if ( verse !== undefined  &&  verse.length > 2  &&  $.isNumeric(verno) ) {
        appendBibleText( 'p', verse, verno, false ) 
    }


    // all is set and we can show the first verse
    advancePresentation();

}
/* create array of numbers taken from the arguments 'from' and 'to' */
function updateVerseList(fr,to)
{
    var arr = [];
    if (1*fr <= 1*to) {
        for (var nr=1*fr; nr<=to; nr++) {
            arr.push(nr);
        }
    }
    return arr;
}
/*
    Split a bible reference into an array of book, chapter, verse_from, verse_to
*/
function splitBref(text)
{
    if (!text) {return;}

    var arr = {};
    var ref = text.split(' ');
    var nr = 0
    // check if book name starts with a number
    if ($.isNumeric(ref[0])) { 
        arr.book = ref[nr++] +' '+ ref[nr++]; }
    else if (text.substr(1,1)=='_') {
        arr.book = ref[nr++].replace('_',' '); }
    else { 
        arr.book = ref[nr++]; }
    // detect chapter and verse
    var chve = ref[nr++].split(':');
    arr.chapter = chve[0];
    // is there a verse reference?
    if (chve.length>1) {
        // detect verse_from and verse_to
        var vrs = chve[1].split('-');
        arr.verse_from = vrs[0];
        // analyze verse_to
        if (vrs.length>1) {
            // there could be another reference being attached...
            var vto = vrs[1].split(/[,;]/);
            arr.verse_to = vto[0];
        }
        if (arr.verse_to===undefined)
            arr.verse_to=arr.verse_from;
    } 
    // no verse references detected, use generic values
    else {
        arr.verse_from = 0;
        arr.verse_to = 199;
    }

    // name of the bible VERSION (without brackets!)
    arr.version = ref[nr]
    if (arr.version)
        arr.version = arr.version.replace(/(\(|\))/g,'');

    // problem with differing naming conventions for Psalm in NIV vs others
    if (arr.book=='Psalms') 
        arr.book='Psalm';
    return arr;
}
/* save a chapter to the local storage for later reference
    if it's not already cached...
*/
function localCacheBibleText( bRef ) 
{
    // body...
    var refName = bRef.version+'.'+bRef.book+'.'+bRef.chapter;
    var x = localStorage.getItem( refName );
    if (x===null) {
        ;;;console.log('Not found locally - getting '+refName+' from the server');
        // get chapter via AJAX
        $.get( cSpot.appURL+'/bible/text/'+bRef.version+'/'+bRef.book+'/'+bRef.chapter )
            .done( function(data) {
                ;;;console.log('storing ' + refName + ' to LocalStorage');
                if (data.verses!==undefined)
                    localStorage.setItem( refName, JSON.stringify(data) );
                if (data.response!==undefined)
                    localStorage.setItem( refName, JSON.stringify(data.response.search.result.passages) );
            });
    } else {
        ;;;console.log('Full chapter of '+refName+' found in local storage');
    }
}
/* get next bible verse from local storage (or server) and present it now
*/
function recallNextBibleverse()
{
    // first, get the last verse of the original bible ref
    var hdr = $('#bible-text-ref-header');
    var bRef = $(hdr).text();
    if (bRef) 
        bRef = splitBref(bRef);
    else
        return; // no bible reference found!

    // get handle on last shown verse
    var lastVerse = $('#'+bRef.verse_to);
    // is it an existing element?? "lastVerse" ??

    // now check if we already have the last verse in a chapter
    if ( $(lastVerse).css('display')=='none'  ||  
         $(lastVerse).css('display')===undefined ) {
        // make sure we have the current chapter in local storage
        localCacheBibleText( bRef );

        advancePresentation(); // go to the next, original verse
        return; // user has not yet advanced to the last verse of the original reference
    }

    // now clone the last shown verse, so that we can use it for the next verse
    var nextVerse = lastVerse.clone();

    // get the next verse from local storage / compose the key
    // but we can't advance yet unless we have the text in local storage!
    var stor = getLocalStoreBref(bRef);
    if (stor) stor = JSON.parse(stor);
    else return;


    // start putting the next chapter also into local storage
    var cRef = JSON.parse( JSON.stringify(bRef) );  // clone the object
    cRef.chapter = 1.0*cRef.chapter + 1;
    if (cRef.chapter <= Object.keys(cSpot.bibleBooks[bRef.book]).length) {
        localCacheBibleText( cRef );
    }

    // check if the next verse still is in the same chapter
    if (bRef.verse_to < cSpot.bibleBooks[bRef.book][bRef.chapter]) {
        bRef.verse_to = 1.0 * bRef.verse_to + 1;
    }
    // or in the next chapter of the same book
    else if (bRef.chapter <= Object.keys(cSpot.bibleBooks[bRef.book]).length) {
        bRef.verse_to = 1;
        bRef.verse_from = 1;
        bRef.chapter = 1.0 * bRef.chapter + 1;
        // get the new chapter now
        stor = getLocalStoreBref(bRef);
        if (stor) stor = JSON.parse(stor);
        else return;
        // we also need to remove from the DOM all verses from the previous chapter, 
        // as their IDs are the actual verse number and would be duplicates!
        $('.bible-text-present-parts').remove();
    }
    // we do not cross book boundaries
    else return;


    // check version
    var newChapter = false;
    var nivText;
    if (bRef.version=='NIV') {
        nivText = stor[0].text;
        if (nivText===undefined) 
            return;
        $(lastVerse).hide();
        var nextID = $(lastVerse).attr('id');
        if (nextID <= bRef.verse_to) {
            $('#'+(1.0*nextID+1)).show();
        } else {
            // go to next chapter
            ;;;console.log('go to next chapter...');
            newChapter = true;
        }
    }
    else {

        // find the next verse in the array of verses
        var a = stor.verses.find(findVerse, bRef.verse_to);
        if (a===undefined) 
            return;

        // hide the previous verses
        $('.bible-text-present-parts').hide();

        // reformat the verse and insert it into the presentation
        if (a.text) {
            $(nextVerse).html(a.text);
            $(hdr).after(nextVerse);
        }
        else return;

        // modify the new verse paragraph accordingly
        $(nextVerse).children('h3').remove();        // remove a possible paragraph header
        $(nextVerse).attr('id', bRef.verse_to);                // add the usual id 

    }
    // new bible text ref header
    bRef.header = bRef.book+' '+bRef.chapter+':'+bRef.verse_from+'-'+bRef.verse_to+' '+bRef.version.toUpperCase();

    // correct the bible text reference header:
    $('#bible-text-ref-header').text(bRef.header);
    // we should also remove the Progress Indicators:
    $('#lyrics-sequence-nav').remove();

    // write new chapter into the DIV
    if (newChapter) {
        $('#bible-text-present-all').html('');
        var p = document.createElement('p');
        $(p).addClass('item-comment');
        $(p).attr('id','item-comment');
        $(p).text(bRef.header);
        $('#bible-text-present-all').append(p);
        p = document.createElement('p');
        $(p).addClass('bible-text-present-ref');
        $(p).text(bRef.header);
        $('#bible-text-present-all').append(p);
        var div = document.createElement('div');
        $(div).addClass('bible-text-present');
        $('#bible-text-present-all').append(div);
        $('#bible-text-present-all').append(nivText);
        // run the pre-formatter again
        reFormatBibleText();
        // make the first verse visible
        $('#1').show();
    }

}

function getLocalStoreBref(bRef)
{
    var stor = localStorage.getItem( bRef.version+'.'+bRef.book+'.'+bRef.chapter );
    if (stor)
        return stor;
    // as it's not yet in local storage, get it from the server
    localCacheBibleText( bRef );    
}

function findVerse(element, index, array)
{
    return element.verse == this;
}

/*
    Append the reformatted bible text to the presentation and add a reference
    into the Sequence Indicator list in the Navbar (bottom right)
*/
function appendBibleText(type, text, verno, show)
{
    // do not add the same verse twise...
    if ( $('#bible-progress-'+verno ).length )
        return;

    var style = '';
    var parts = '" ';
    var id    = ' id="'+verno+'">';  // name of the SLIDE
    if (show===undefined) show = true; // by default, show this verse in the presentation

    // actual bible text is inserted as <p> element, and hidden at first
    if (type=='p') {
        if (show) insertSeqNavInd( verno, verno, 'bible' ); 
        style=' style="display: none"';
        parts='-parts" ';
    }
    // if the text is a bible reference, will be treated as H1 element
    if (type=='h1') {
        // if there are multiple bible references in one item, we only 
        // want to have one H1 element, so we attach the next bible ref to the existing H1
        var hText = $('#bible-text-ref-header').text();
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
    var rfc = splitBref(exisText); // existing header
    var bRef = splitBref(newText); // next header

    if (rfc.version==bRef.version) {
        var et = exisText.split(' ');
        var exisRef = et[0]+' '+et[1];
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




/*\
|* >------------------------------------------ PREPARE SONG SLIDES
\*/


/*
    check if there are more lyric parts than 
        indicated in the spre-defined equence due to blank lines discoverd in the lyrics
*/
function compareLyricPartsWithSequence()
{
    // get the predefined sequence
    var sequenceDiv= $('#sequence').text();
    //console.log('found predefined sequence: ' + sequenceDiv);
    var sequence = ( $('#sequence').text() ).split(',');

    var newSequence = '';
    var nr = 0, type, parts, headerCode;
    // walk through the pre-defined sequence
    for (var i in sequence) {
        // what kind of lyric parts do we have (verse or chorus etc)
        type = identifyLyricsHeadings('['+sequence[i]+']');
        console.log('looking for part of type ' + type);
        if (!type) return true; // precaution
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
    var sequence;

    // go through the list of lyric parts (as generated in function "reDisplayLyrics()")
    $(lyrList).each(function(entry) 
    {
        var id = $(this).attr('id');  // get name of that lyric part
        var pname = id.substr(0,5);
        if ( pname == 'verse' ) {
            var verseNum = id.substr(5);
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
            chorusSeq.push( 'c1'+id.substr(7) );  
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
    Show lyrics in presentation mode

    mainly: divide lyrics into blocks (verses, chorus etc) to be able to show them individually

    NOTE: headers must be in a single line and text enclosed om square brackets! E.g.: "[Verse 1]"

    This function is called in the document.ready method, when it finds this element:
        $('#present-lyrics')
*/
function reDisplayLyrics()
{
    // get the lyrics text and split it into lines
    var lyrics = $('#present-lyrics').text().split('\n');

    // now remove the lyrics in the existing <pre> tag in the DOM
    $('#present-lyrics').text('');

    var newLyr = '';
    var lines  = 0;         // counter for number of lines per each song part 
    var headerCode = 's'    // identifies the code within the sequence data
    // default song part if there are no headings
    var newDiv = '<div id="start-lyrics" class="lyrics-parts" ';
    var divNam = 'start-lyrics';
    var curPart= '';
    var region2= false;
    var apdxNam= 97; // char cod 97 = 'a' - indicates sub-parts of verses or chorusses etc
    var lyricsLine;

    // analyse each line and put it back into single pre tags
    for (var i = 0; i <= lyrics.length - 1; i++) {

        lyricsLine = lyrics[i].trim();  // get pure text

        // ignore last line if its empty
        if (i==lyrics.length-1 && lyricsLine=='')
            break;

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
            // find verse indicator (can be first word in the lyrics line, like: "[1] first line of lyrics")
            // or it could be like [chorus 2]
            var hdr = identifyLyricsHeadings( lyricsLine.split('] ')[0] ); 
            if (hdr.length>0) { 
                // verse indicator was found!
                curPart = hdr; 
                apdxNam= 97; // = 'a': reset appendix indicator (for forced lyric parts)
                // use 2nd part of initial lyricsline as actualy lyrics
                lyricsLine = lyricsLine.split('] ')[1]; // this will be 'undefined' if line was just the indicator!
            }
        }

        // if line starts with a dot, it will be visible here but not for chords presentation
        if (lyrics[i].substr(0,1)=='.')
            lyricsLine = lyricsLine.substr(1, lyricsLine.length-1);

        // check if we have a header or the actual lyrics
        if (hdr.length>0) {
            // insert identifiable blocks
            insertNewLyricsSlide(newDiv, newLyr, divNam, lines);
            divNam = hdr;
            newLyr = '';
            lines  = 0;
            region2= false;
            newDiv = '</div><div id="'+hdr+'" class="lyrics-parts" ';
        }

        // actual lyrics - insert as P element
        if (lyricsLine != undefined) {
            lines += 1;
            // insert horizontal line when requested
            if (lyricsLine=='[region 2]') {
                newLyr += '<hr class="hr-big">';
                region2 = true;
            } else {
                var cls = 'text-present ';
                var stl = '';
                if (region2) cls = 'text-present text-present-region2';
                // check if line contains style codes:
                var styles = getStylesFromLyricsLine(lyricsLine);
                if (styles.length>0) {
                    stl = 'style="'+styles+'"';
                    cls = '';
                    lyricsLine = lyricsLine.split('>')[1];
                }
                newLyr += '<p class="'+cls+' mb-0" '+stl+'>'+lyricsLine+'</p>';
            }
        }
    }
    // insert the last lyrics part
    insertNewLyricsSlide(newDiv, newLyr, divNam, lines);
}

// extract style codes from a single lyrics line (must be at the start of line!)
function getStylesFromLyricsLine(line)
{
    var codes = line.split('>');
    if ( line.substr(0,1)=='<' && codes.length>1 ) {
        var styles = codes[0].split('<')[1];
        console.log('found styles: '+styles);
        return styles;
    }
    return '';
}

// insert new SLIDE into the presentation
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
    ;;;console.log( 'Inserted new SLIDE (lyrics part) called ' + divNam );
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



/* grab all existing OnSong elements and rewrite 
   them with seperate lines of chords and lyrics */
function reFormatOnsongLyrics()
{
    var onsongs = $('.show-onsong-text');
    $(onsongs).each( function(item) {
        rewriteOnsong(onsongs[item]);
    });
}


/*\
|* >------------------------------------------ PREPARE  CHORDS  DISPLAY
\*/


/*
    called directly from Present_navbar blade file
    used by chords.blade.php and sheetmusic.blade.php

    this MUST run after reDisplayChords!
*/
function prepareChordsPresentation(what)
{
    // make type of presentation globally available
    if (cSpot.presentation)
        cSpot.presentation.type = what;
    else 
        cSpot.presentationType = what

    // check if user has changed the default font size for the presentation
    applyLocallyDefinedTextFormatting();

    // make sure the main content covers all the display area
    //$('#main-content').css('min-height', window.screen.height);

    // intercept mouse clicks into the presentation area
    $('body').contextmenu( function() {
        return false;
    });

    // check if rendered items for this plan are 
    // cached no the server - then load it into local storage
    loadCachedPresentation(cSpot.presentation.plan_id); 

    // save the final rendition of this page to LocalStorage
    saveMainContentToLocalStorage(what);
}



// Use Regex patterns to identify chords versus lyrics versus headings
// and to show them in different colors
// (called from document.ready.js)
function reDisplayChords()
{
    // selector of the chords element is different on the Item Detail page or in a presentation
    var selectorName = '#chords';
    // get the chords text and split it into lines
    var chords = $(selectorName).text().split('\n');

    if (chords.length==1) return;

    // empty the exisint pre tag
    $(selectorName).text('');
    // analyse each line and put it back into single pre tags
    for (var i = 0; i <= chords.length - 1; i++) {
        if (chords[i].length==0  || chords[i].substr(0,1)=='.') continue;
        // if a line looks like chords, make it red
        if ( identifyChords(chords[i]) ) {
            $(selectorName).append('<pre class="red mb-0">'+chords[i]+'</pre>');
        }
        else {
            var hdr = identifyHeadings(chords[i]).split('$');
            var anchor = '';
            if (hdr.length>1 && hdr[1].length>0)
                anchor = '<a name="'+hdr[1]+'"></a>';
            $(selectorName).append(anchor+'<pre class="mb-0 '+hdr[0]+'">'+chords[i]+'</pre>');
        }
    }
}
function identifyHeadings(str)
{
    // identify headers by the first word in a line, case-insensitive

    var patt = /^(coda|end)/i;
    if ( patt.test(str) ) 
        return ' pl-3 bg-info$';

    patt = /^(Verse)/i;
    if ( patt.test(str) ) {
        var nm=''; var n=str.split(' '); 
        if (n.length>1) {
            nm=n[1].substr(0,1); 
            $('#jumplist').show();
            $('#jump-verse'+nm).show();
        }
        return ' pl-3 bg-success$verse'+nm; 
    }
    patt = /^(Chorus)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-chorus').show();
        return ' pl-3 bg-info$chorus';
    }
    patt = /^(Pre-Chorus)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-chorus').show();
        return ' pl-3 bg-info$chorus';
    }
    patt = /^(bridge)/i;
    if ( patt.test(str) ) {
        $('#jumplist').show();
        $('#jump-bridge').show();
        return ' pl-3 bg-info$bridge';
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
    
    patt = /\b[CDEFGAB](?:#{1,2}|b{1,2})?(?:maj7?|min7?|sus2?|sus4?|m?)\b/g;
    if ( patt.test(str) ) return true;
    
    patt = /\b[CDEFGB]\b/g;
    if ( patt.test(str) ) return true;

    return false;
}





/*\
|* >------------------------------------------ HELPERS  TO  PREPARE  THE  PRESENTATION  UI
\*/


function countLines(where) {
    var divHeight = document.getElementById(where).offsetHeight
    var elem = document.getElementById(where);
    var lineHeight = parseInt(elem.style.fontSize);
    var lines = divHeight / lineHeight;
    return parseInt(lines);
}


// the Sequence indicators at the bottom right could 
// get too long, so we need to hide some parts
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
        seq = $(what+'-progress-indicator');
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



// Insert the Sequence Navigation indicators into the navbar 
function insertSeqNavInd(what, nr, where)
{
    // do not add the same number again...
    if ( $('#bible-progress-'+nr ).length )
        return;

    // set default action
    where = where || 'lyrics';

    console.log('inserting sequence NavBar indicator for '+ what + ' as '+where+' part # ' + nr);

    var data = '<span id="'+where+'-progress-' + nr + '" class="'+where+'-progress-indicator" ' +
           'data-show-status="unshown" onclick="'+where+'Show(' + "'" + what + "'" + ');">';
    data += formatSeqInd(what)+'&nbsp;</span>';

    $('#lyrics-sequence-nav').append( data );
}
// special formatting for sequence indicators of lyric parts
function formatSeqInd(code){
    code  = code.toString();
    var char1 = code.substr(0,1);
    var char2 = code.substr(1,1);
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






/*\
|* >------------------------------------------ HELPERS  FOR  NAVIGATION IN THE  ACTUAL PRESENTATION
\*/


// On the lyrics screen, advance to the next item or sub-item (song parts)
function advancePresentation(direction)
{
    // set default value....
    direction = direction || 'forward';

    // make sure the list of indicators doesn't get too long
    checkSequenceIndicatorLength();

    var seq, found, todo, i, thisID;

    if ($('#present-lyrics').length > 0) {

        // make sure the main lyrics div is visible
        $('#present-lyrics').show(); 

        // do we have a specific sequence provided?
        seq = $('.lyrics-progress-indicator');

        // no sequence indicators found! Hopefully the default lyrics block was created...
        if (seq.length < 1) {
            // first check if we have been here before, then we can advance to the next item
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
                    ;;;console.log('found ' + $(this).attr('id'));
                    $(this).data().showStatus = 'done';
                    $('.lyrics-progress-indicator').removeClass('bg-danger');
                    $(this).addClass('bg-danger');
                    todo = $(this).attr('onclick');
                    eval( todo );
                    return false;
                }
                if (found) {return false;}
            });
            // all items were shown, so we can move to the next item
            if (! found) {
                $('#present-lyrics').fadeOut();
                navigateTo('next-item');
                return;
            }
        }
        // no, we try to move backwards in the sequence of song parts
        else {
            for (i = seq.length - 1; i >= 0; i--) {
                if ($(seq[i]).hasClass('bg-danger')) {
                    ;;;console.log('currently active part is # '+i+' with text: '+$(seq[i]).text() );
                    // we have reached the first part, going further back means previous plan item!
                    if (i==0) { 
                        navigateTo('previous-item'); 
                        return; }
                    $(seq[i]).data().showStatus = 'unshown';
                    $('.lyrics-progress-indicator').removeClass('bg-danger');
                    $(seq[i-1]).addClass('bg-danger');
                    todo = $(seq[i-1]).attr('onclick');
                    eval( todo );
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
        seq = $('.bible-progress-indicator');
        // check if there is a bible text
        if (seq.length===0) {
            // missing bible text, so we stay on this slide
            return;
        }

        // loop through all sequence items and find the next that wasn't shown yet
        found = false;
        if (direction=='forward') {
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    thisID = $(this).attr('id')
                    $(this).data().showStatus = 'done';
                    showNextSlide(thisID,'.bible');
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
            for (i = seq.length - 1; i >= 0; i--) {
                if ( $(seq[i]).data().showStatus == 'done') {
                    thisID = $(seq[i]).attr('id')
                    if (i<1) {break;} // we can't move any further back....
                    found=true;
                    $(seq[i]).data().showStatus = 'unshown';  // make this part 'unshown'
                    thisID = $(seq[i-1]).attr('id')
                    showNextSlide(thisID,'.bible');
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
        seq = $('.slides-progress-indicator');
        // loop through all sequence items and find the next that wasn't shown yet
        found = false;
        if (direction=='forward') {
            $(seq).each(function(entry){
                if ( $(this).data().showStatus  == 'unshown' ) {
                    found = true;
                    $(this).data().showStatus = 'done';
                    showNextSlide( $(this).attr('id'), '.slides' );
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
            for (i = seq.length - 1; i >= 0; i--) {
                if ( $(seq[i]).data().showStatus == 'done') {
                    if (i<1) {break;} // we can't move any further back....
                    found=true;
                    $(seq[i]).data().showStatus = 'unshown';  // make this part 'unshown'
                    showNextSlide( $(seq[i-1]).attr('id'), '.slides','back' );
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


// Using keyboard shortcuts differently on the lyrics presentation or chords pages
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

// show the indicated bible verse and indicate it on the Progress Indicator Bar
function showNextSlide(thisID,what,direction)
{
    // modify the background color of the progress indicators
    $(what+'-progress-indicator').removeClass('bg-danger');
    $('#'+thisID).addClass('bg-danger');

    // get the click-event of the progress-indicator button
    var todo = $('#'+thisID).attr('onclick');

    // for sending positons, we have special case for bible verse slides
    var snp = thisID.split('-');
    if (direction=='back' && snp.length>2 && snp[0]=='bible') {
        thisID = snp[0]+'-'+snp[1]+'-'+(1+1*snp[2]); // we must add 1 to the id number
    }
    sendShowPosition(thisID);

    // execute the click-event of the button
    eval( todo );
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
    var goWhereButton = document.getElementById('go-'+where);
    // link doesn't exist:
    if (goWhereButton==null) return;

    // fade background and show spinner, but not in presentation mode!
    if ( document.baseURI.search('/present')<10 )
        showSpinner();

    // in presentation Mode, do we want a blank slide between items?
    if ( cSpot.presentation.configBlankSlides  &&  cSpot.presentation.screenBlank ) {
        cSpot.presentation.screenBlank = false;
        // check if there is an empty slide/item (an item without lyrics, bibletext or images)
        var reg = /^[\s]+$/; // regex for a string containing only white space.
        var main  = $('#main-content').text();
        // check if there are images 
        var images = $('.slide-background-image');
        // if the slide contains anything but spaces, we were still presenting something
        // and we now show an empty (blank) slide
        if (! reg.test(main) || images) {
            $('#main-content').html('<div>.</div>');
            ;;;console.log('inserting empty slide...');
            // hide current slide title to indicate blank slide
            $('#item-navbar-label').hide();
            $('#show-blank-screen').addClass('ui-state-active')
            return;
        }
        console.log('slide was already empty, proceeding to next item...');
        // otherwise, if the slide/item was empty anyway, we proceed to the next item
    }

    // if configured, show blank screen after next item
    cSpot.presentation.screenBlank = true;



    /*\
       > For OFFLINE MODE: check if the next (or previous) item is cached in LocalStorage 
    \*/
    if ( where!='edit' && where!='back' && cSpot.presentation.useOfflineMode) {

        // get the current item identification values
        var cur_seq_no  = 1.0*cSpot.presentation.seq_no;        // dynamic value (will be changed below or on reload)
        var max_seq_no  = 1.0*cSpot.presentation.max_seq_no;    // static value
        var cur_plan_id = 'offline-'+cSpot.presentation.plan_id;       // static value
        var next_seq_no, prev_seq_no;

        // calculate the identifiers for the next or previous item in local storage
        if (cur_seq_no >= max_seq_no) {
            next_seq_no = cur_plan_id + '-' + 1;
        } else {
            next_seq_no = cur_plan_id + '-' + (1*cur_seq_no+1);
        }
        if (cur_seq_no >= 1) {
            prev_seq_no = cur_plan_id + '-' + (1*cur_seq_no-1);
        } else {
            prev_seq_no = cur_plan_id + '-' + max_seq_no;
        }

        // Rewrite the page content with the cached data from Local Storage (LS)
        
        // test if direction is forward and if next item exists in LS
        if ( where == 'next-item'     && isInLocalStore(next_seq_no) ) {

            // write cached data into the DOM
            writeCachedDataIntoDOM( next_seq_no );

            // re-apply locally defined text formatting
            applyLocallyDefinedTextFormatting();

            // maintain the correct item sequence number for the just inserted slides
            if (cSpot.presentation.seq_no == max_seq_no) {
                cSpot.presentation.seq_no = 1;
            } else {
                cSpot.presentation.seq_no = 1.0*cSpot.presentation.seq_no + 1;
            }

            // modify the next-item button to reflect the new item in the url
            modifyHRefOfJumpbutton(next_seq_no, prev_seq_no)

            // show new location in DropUp menu
            $('.dropdown-item.nowrap').removeClass('bg-info')
            $('#menu-item-seq-no-'+cSpot.presentation.seq_no+'\\.0').addClass('bg-info');

            // show title again (if it was hidden because of blank slide)
            $('#item-navbar-label').show();

            return;
        }

        // test if direction is backward and if previous item exists in LS
        if ( where == 'previous-item' && isInLocalStore(prev_seq_no) ) {

            // write cached data into the DOM
            writeCachedDataIntoDOM(prev_seq_no);

            // re-apply locally defined text formatting
            applyLocallyDefinedTextFormatting();

            // maintain the correct item sequence number for the just inserted slides
            cSpot.presentation.seq_no -= 1;
            if (cSpot.presentation.seq_no <= 1) {
                cSpot.presentation.seq_no = 1*max_seq_no+1;
            }

            // modify the previous-item button to reflect the new item in the url
            modifyHRefOfJumpbutton(next_seq_no, prev_seq_no)

            // show title again (if it was hidden because of blank slide)
            $('#item-navbar-label').show();

            return;
        }

    }


    // make content disappear slowly...
    $('#main-content').fadeOut();
    $('#bottom-fixed-navbar>ul').fadeOut();

    // inform server of current position if we are presenter
    sendShowPosition(where);

    if (goWhereButton.href && goWhereButton.href!='#' ) {
        // try to go to the location defined in href
        window.location.href = goWhereButton.href;
        return;
    }    
    // otherwise, try to simulate a click on this element
    goWhereButton.click();
}

// change the item-id to seq-no in the href element of the 'next' and 'previous' buttons 
function modifyHRefOfJumpbutton(next_seq_no, prev_seq_no)
{
    // provide seq no for both buttons
    var btn_seq_no = {'next-item': next_seq_no, 'previous-item': prev_seq_no};

    // modify both buttons
    for (var where in btn_seq_no) {

        // get handle on the button element
        var goWhereButton = document.getElementById('go-'+where);

        // if link doesn't exist, ignore the rest
        if (goWhereButton==null) continue;

        // find the old item id
        var hrefValue = goWhereButton.href.split('/');
        var indexItemId = hrefValue.indexOf('items')+1;
        if (indexItemId>0) {
            var buttonsItemId = hrefValue[indexItemId];
            var newHref = goWhereButton.href.replace(buttonsItemId, btn_seq_no[where]);
            goWhereButton.setAttribute('href', newHref);
        }
    }

    // hide spinner, if present
    $('#show-spinner').modal('hide')    
}


function showBlankScreen()
{
    $('#main-content').toggle();
    sendShowPosition('blank');
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
            $(indic[i]).data('showStatus', 'done');
        } 
        else if ( found>=0 ) {
            if ($(indic[i]).data())
                $(indic[i]).data('showStatus', 'unshown');
            $(parts[i]).hide();
        }
        else 
        {
            $(parts[i]).hide();
            $(indic[i]).removeClass('bg-danger');
            $(indic[i]).data('showStatus', 'done');
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
                var indic = ( $(this).attr('onclick') ).split("'");
                if (indic.length>1) {indic=indic[1]} else {return false;}
                var gesucht = decompPartCode(indic); // 'gesucht' is the song part for the current sequence indicator
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

    if (cSpot.presentation.BGimageCount > 0)
        showNextBGimage();
    
    // inform server accordingly
    sendShowPosition(what);
    
    // first, fade out the currently shown text, then fade in the new text
    $('.lyrics-parts').fadeOut().promise().done( function() { $('#'+what).fadeIn() } );

    // elevate the currently used button
    if ($('#btn-show-'+what).length)
        $('.lyrics-show-btns').removeClass('btn-danger');   // make sure all other buttons are back to normal
    $('#btn-show-'+what).removeClass('btn-outline-info');   // aremove ouline for this button
    $('#btn-show-'+what).addClass('btn-danger');            // add warning class for this button
}
function decompPartCode(what) {
    var apdx = '';
    var fc = what.substr(0,1);
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

function showNextBGimage()
{
    // compute next bg image number
    var nr = ++cSpot.presentation.currentBGimage;
    if (nr >= cSpot.presentation.BGimageCount) {
        cSpot.presentation.currentBGimage = 0;
        nr = 0;
    }

    // activate (show) the first image
    ;;;console.log('showing next BG image: '+nr);
    var todo = $('#slides-progress-'+nr).attr('onclick');
    eval(todo);

}


/*\
|* >-------------------------------------------------------------------------------------- CONFIGURATION  AND  CUSTOMIZATION
\*/



// --------------------------------------------------------------------------------------- GET

function getLocalConfiguration() 
{
    // check if we want to be Main Presenter
    // if the value in LocalStorage was set to 'true', then we activate the checkbox:
    if ( getLocalStorageItem('config-MainPresenter', 'false') == 'true' ) {
        // Check if there already is a presenter
        if ( cSpot.presentation.mainPresenter ) {
            // someone else is already ....
            localStorage.setItem('config-MainPresenter', 'false');
        } 
        else {
            // make sure the Server knows we want to be presenter (if we are allowed to...)
            setMainPresenter();
            // activate the checkbox in the UI
            changeCheckboxIcon( '#setMainPresenterItem', true );
            // if we are Main Presenter, we can't sync to another ....
            localStorage.setItem('config-SyncPresentation', 'false');

            // now broadcast our current position!
            sendShowPosition('start');  // will include plan_id and item_id 
        }
    }


    // check if we want to syncronise our own presentation with the Main Presenter
    // if the value in LocalStorage was set to 'true', then we activate the checkbox:
    if ( getLocalStorageItem('config-SyncPresentation', 'false') == 'true' ) {

        // show in pop-up menu
        changeCheckboxIcon('#syncPresentationIndicator', true);

        // if we sync our presentation, we can't be Main Presenter
        localStorage.setItem('config-MainPresenter', 'false');

        // save in global namespace
        cSpot.presentation.sync = true;
    } 
    else 
        cSpot.presentation.sync = false; 



    // show a blank slide between items (default: no)
    cSpot.presentation.configBlankSlides = getLocalStorageItem('config-BlankSlides', 'false') == 'true';

    // if the value in LocalStorage was set to 'true', then we activate the checkbox:
    changeCheckboxIcon('#config-BlankSlidesItem', cSpot.presentation.configBlankSlides);



    // use the offline mode (Local Storage) - Default is: Yes
    cSpot.presentation.useOfflineMode = getLocalStorageItem('config-OfflineMode', 'true') == 'true';
    
    // if the value in LocalStorage was set to 'true', then we activate the checkbox:
    changeCheckboxIcon('#config-OfflineModeItem', cSpot.presentation.useOfflineMode);


    // OnSong "chords-over-lyrics" import: user-defined tab-size (default is 4)
    var howManyVersesPerSlide = localStorage.getItem('config-ShowVersCount');
    // if the value in LocalStorage was set to 'true', then we activate the checkbox:
    if (howManyVersesPerSlide>0 && howManyVersesPerSlide<6) {
        $('#config-ShowVersCount').val( howManyVersesPerSlide );
    }

    applyLocallyDefinedTextFormatting();

}

/* 
    Read and apply locally defined text format settings from Local Storage

    The format of the keys for those values is:

        format_<selectorname>_<styleattribute>

    Because of that, we can simply loop through all keys and apply them easily!
*/
function applyLocallyDefinedTextFormatting(reset) 
{

    $.each(localStorage, function(key, value) {
        var k = key.split('_');
        var what = k[0];
        var selector = k[1];
        var attribute = k[2];

        // only use keys named 'format_...' and only do this for existing elements
        if ( what=='format'  &&  $(selector).length ) {

            if (reset=='reset') {
                localStorage.removeItem(key);
                return true; // same as 'continue'
            }

            // selector 'body' is only for lyrics presentation
            if (selector == 'body'  &&  location.pathname.indexOf('/present')<0)
                return true;

            // don't use in Song Details or Item Details pages
            if (location.pathname.indexOf('/edit')>5)
                return true;


            ;;;console.log('formatting "'+ selector + '" with style "' + attribute + '" as "' + value + '"');

            if ( attribute=='font-size'  &&  $.isNumeric(value) ) 
                $( selector ).css( k[2], parseInt(value) );
            else
                $( selector ).css( k[2], value );
            
        }
    });

    // having applied all locally defined formatting, we can now show the lyrics...
    $('.text-present').show();
}    

/*  Reset all locally defined formatting values
*/
function resetLocalFormatting()
{
    applyLocallyDefinedTextFormatting('reset');
}


// --------------------------------------------------------------------------------------- SET

// called from the links in the configuration popup
function changeBlankSlidesConfig() {
    cSpot.presentation.configBlankSlides = ! cSpot.presentation.configBlankSlides;
    var sett = cSpot.presentation.configBlankSlides;
    ;;;console.log('User changed setting for "Show empty slides between items" to ' + sett );
    changeCheckboxIcon('#config-BlankSlidesItem', sett);
    localStorage.setItem('config-BlankSlides', sett);
}

function changeOfflineModeConfig() {
    cSpot.presentation.useOfflineMode = ! cSpot.presentation.useOfflineMode;
    var sett = cSpot.presentation.useOfflineMode;
    ;;;console.log('User changed setting for "Use cached items from Local Storage" to ' + sett );
    changeCheckboxIcon('#config-OfflineModeItem', sett);
    localStorage.setItem('config-OfflineMode', sett);

    // if caching was now enabled, get copy of server-stored cache!
    if (sett) {
        loadCachedPresentation(cSpot.presentation.plan_id);
    }
}

function changeConfigShowVersCount() {
    var sett = $('#config-ShowVersCount').val();
    console.log('User changed setting for "Show how many bible verses per slide" to ' + sett );
    localStorage.setItem('config-ShowVersCount', sett);
}

function changeTextAlign(selectorList, how) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    selectorList.forEach( function(selector) {
        var element = $(selector);
        if (element.length>0) {
            $(element).css('text-align', how);
            localStorage.setItem('format_'+selector+'_text-align', how);
            ;;;console.log('LocalStorage for "'+selector+'" was set to "'+localStorage.getItem('format_'+selector+'_text-align')+'"');
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
        var element = $(selector);
        // only proceed if the element actually exists in the current document
        if (element.length>0) {
            var fontSize = parseFloat($(element).css('font-size')) * factor;
            if (fontSize<8 || fontSize>150) return;
            $(element).css('font-size', fontSize);
            localStorage.setItem('format_'+selector+'_font-size', fontSize);
            ;;;console.log('LocalStorage for "'+selector+'" was set to "'+localStorage.getItem('format_'+selector+'_font-size')+'"');
        }
    });
}


/**
 * Change the text or BG color of a given element
 * stores the value in LocalStorage for later reference
 *
 * @param  selectorList string or array of valid CSS selectors
 * @param  how string for color definition
 * return void
 */
function changeColor(selectorList, how, what) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    selectorList.forEach( function(selector) {
        var element = $(selector);
        var sel_name;
        // only proceed if the element actually exists in the current document
        if (element.length>0) {
            if (what=='BG') {
                if (selector=='#main-content')
                    $(selector).removeClass('bg-inverse');
                $(element).css('background-color', how);
                 sel_name = selector+'_'+'background-color';
            }
            else {
                $(element).css('color', how);
                sel_name = selector+'_color';
            }
            localStorage.setItem('format_'+sel_name, how);
            ;;;console.log('LocalStorage for "'+selector+'" was set to "'+localStorage.getItem('format_'+sel_name)+'"');
        }
    });
}




/*\
|* >------------------------------------------------------------------ CACHING ELEMENTS IN LOCAL STORAGE AND ON THE SERVER
\*/



/* write cached data into the DOM -         called from         navigateTo()
*/
function writeCachedDataIntoDOM(identifier) {

    ;;;console.log('Next item comes cached from local storage! Identifier: '+identifier);

    var type = cSpot.presentation.type || cSpot.presentationType;

    if ( type == 'chords'  || type == 'sheetmusic' ) {
        $('#main-content'       ).html(localStorage.getItem(identifier + '-mainContent-'+ type));
        $('#present-navbar'     ).html(localStorage.getItem(identifier + '-present-navbar' ));
        return;
    }

    $('#main-content'           ).html(localStorage.getItem(identifier + '-mainContent'));
    $('#lyrics-parts-indicators').html(localStorage.getItem(identifier + '-seqIndicator'));
    $('#lyrics-sequence-nav'    ).html(localStorage.getItem(identifier + '-sequenceNav'));
    $('#item-navbar-label'      ).html(localStorage.getItem(identifier + '-itemNavBar' ));
    $('#item-navbar-next-label' ).html(localStorage.getItem(identifier + '-itemNavBarNext'));
    // $('#next-item-button'       ).html(localStorage.getItem(identifier + '-itemNBNextItemBtn'));

    // in case it was added to identify a blank slide:
    $('#show-blank-screen').removeClass('ui-state-active')

    // take the opportunity to get the latest plan data and see if the plan was updated meanwhile
    var plan_id = cSpot.presentation.plan_id;

    $.post( cSpot.routes.apiGetPlan, { 'plan_id' : plan_id } )
        .done( 
            function(data) {
                ;;;console.log(data);
                cSpot.presentation.plan = data;
                // if local cache is out of date, delete the cached items
                isCachedPlanStillUptodate(plan_id);
            }
        );
}

function isCachedPlanStillUptodate(plan_id) {
    var cachedPlanDate = localStorage.getItem('offline-'+plan_id+'-0-planUpdatedAt');
    if ( cachedPlanDate  &&  cachedPlanDate.trim() != cSpot.presentation.plan.updated_at ) {
        ;;;console.log('Local Plan cache is outdated!');
        clearLocalCache()
        return false;
    }
    return true;
}


// save main content html to local storage
function saveMainContentToLocalStorage(what) {    

    // only if activated ....
    if ( !cSpot.presentation.useOfflineMode ) {
        return;
    }

    if (what) {
        what = '-' + what;
    } else {
        what = '';
    }
    var plan_id = cSpot.presentation.plan_id
    var itemIdentifier = 'offline-' + plan_id + '-' + (cSpot.presentation.seq_no * 1.0);

    // we should have only one plan in LocalStorage!
    checkLocalStorageForPresentation(plan_id);

    // check if items in plan cache have expired since plan has been updated meanwhile
    if ( isCachedPlanStillUptodate(plan_id) ) {
        ;;;console.log('Presentation Cache for plan id '+plan_id+' is still valid or has just been created.');
    } 
    else {
        // save the new datetime of the last update to this plan
        saveLocallyAndRemote(plan_id, 'offline-'+plan_id+'-0-planUpdatedAt', cSpot.presentation.plan.updated_at);
    }

    // save the Main Content into localStorage
    saveLocallyAndRemote(plan_id, itemIdentifier +'-mainContent'+what,  $('#main-content').html());
    // lyrics parts indicator element
    saveLocallyAndRemote(plan_id, itemIdentifier +'-seqIndicator',      $('#lyrics-parts-indicators').html());
    // sequence navigator element
    saveLocallyAndRemote(plan_id, itemIdentifier +'-sequenceNav',       $('#lyrics-sequence-nav').html());
    // item label 
    saveLocallyAndRemote(plan_id, itemIdentifier +'-itemNavBar',        $('#item-navbar-label').html());
    saveLocallyAndRemote(plan_id, itemIdentifier +'-itemNavBarNext',    $('#item-navbar-next-label').html());
    // saveLocallyAndRemote(plan_id, itemIdentifier +'-itemNBNextItemBtn', $('#next-item-button').html());
    // whole NavBar for chords and sheetmusic
    saveLocallyAndRemote(plan_id, itemIdentifier +'-present-navbar',    $('#present-navbar').html());

    ;;;console.log('saving this item with seq.no '+cSpot.presentation.seq_no+' from plan with id '+plan_id+' to Local Storage.');
}

// Make sure LocalStorage contains only one plan cached for the presentation for offline use
function checkLocalStorageForPresentation(plan_id) 
{
    // only if activated ....
    if ( cSpot.presentation && !cSpot.presentation.useOfflineMode ) {
        return;
    }

    // do nothing if parameter is missing
    if (!plan_id) return;
    
    // loop through each localStorage item
    for (var key in localStorage) {

        // look for key names with a specific structure: 
        //      "offline-<ppp>-<s>-<text....>"
        // (where: ppp=planId, s=seq.no and text is element name)
        var ident = key.split('-');

        if (ident.length>3 && ident[0]=='offline') {
            // check if planId != given plan_id
            if ( ident[1].length>0  &&  !isNaN(ident[1])  &&  ident[1]!=plan_id ) {
                //console.log('removing item from other plan from localStorage: '+key);
                localStorage.removeItem(key);
            }
        }
    }
}

function isInLocalStore(identifier)
{
    // do nothing if parameter is missing
    if (!identifier) return false;

    var type = cSpot.presentation.type || cSpot.presentationType;

    var ik = identifier.split('-');

    // check if items in plan cache have expired since plan has been updated meanwhile
    if ( ! isCachedPlanStillUptodate(ik[1]) ) {
        return false;
    }
    
    // loop through each localStorage item
    for (var key in localStorage) {

        // look for key names with a specific structure: 
        //      "offline-<ppp>-<s>-<text....>"
        // (where: ppp=planId, s=seq.no and text is element name)
        var sk = key.split('-');

        if (sk[0]!='offline') continue;

        if ( ik[0]==sk[0]  &&  ik[1]==sk[1]  &&  ik[2]==sk[2] ) 
        {
            // we found a valid cache for this type of presentation
            if ( (type == 'lyrics' && sk[3]=='seqIndicator') || sk[4]==type )
                return true;
        }
    }
    return false;
}

// Save data to local Storage and also cache it on the server for others to use
function saveLocallyAndRemote(plan_id, key, value)
{
    if (!plan_id || !key || !value ) {
        return;
    }

    // compare with value already existing in cache 
    var existingValue = localStorage.getItem(key);
    // do nothing if identical !
    if ( existingValue  &&  existingValue.localeCompare(value) == 0 ) {
        ;;;console.log(value.length + ' already in cache: ' + key)
        return;
    }

    // remove excess blanks from the string, but not with chords
    if (key.search('chords') < 0)
        value = value.replace(/ {2,}/g,' ')

    // save locally
    localStorage.setItem(key, value.trim());

    // save on server
    $.post(
        cSpot.appURL+'/cspot/plan/'+plan_id+'/cache',
        {
            'item_id'   : cSpot.presentation.item_id,
            'key'       : key,
            'value'     : value.trim() + ' ',       // have at least one blank for server-side validation to work
        }, 
        console.log('Cached data saved on server: ' + key)
    );
}

// load server-cached pre-rendered items into LocalStorage
// (this is called from the presentation view file)
function loadCachedPresentation(plan_id)
{
    // validate plan id ?
    if (!plan_id || isNaN(parseInt(plan_id)))
        return;

    // is local caching enabled?
    if ( getLocalStorageItem('config-OfflineMode', 'true') == 'false' ) 
        return

    // get data via AJAX call
    $.get(cSpot.appURL+'/cspot/plan/'+plan_id+'/cache', function(data, status) {

        if ( status == 'success') {
            // save to LocalStorage
            var planCache = JSON.parse(data.data);

            // make sure we have only one plan locally in cache!
            checkLocalStorageForPresentation(plan_id);

            // write plan updated_at date into cache
            saveLocallyAndRemote(plan_id, 'offline-'+plan_id+'-0-planUpdatedAt', cSpot.presentation.plan.updated_at);
            
            ;;;console.log('Saving server-cached pre-rendered items to LocalStorage');

            // get each key/value pair and save it to LocalStorage
            for (var item in planCache) {

                // indicate presence of cache in the dropup menu
                var seq_no = planCache[item].key.split('-')[2];
                $('#in-cache-seq-no-'+seq_no+'\\.0').show();

                // but first check if an identical item doesn't already exists locally 
                var existingValue = localStorage.getItem(planCache[item].key);
                // do nothing if identical !
                if ( existingValue  &&  existingValue.localeCompare(planCache[item].value) == 0 ) {
                    // ;;;console.log('already in cache: ' + planCache[item].key)
                    continue;
                }
                // now save new item to local store
                localStorage.setItem( planCache[item].key, planCache[item].value );
            }            
        }
    });
}

// clear local cache
function clearLocalCache()
{
    // we 'hack' this function and pretend to ave a plan with this fantasy id
    checkLocalStorageForPresentation(999999999);
}

// clear server-side cached presentation data
function clearServerCache(plan_id)
{
    // validate plan id
    if (!plan_id || isNaN(parseInt(plan_id)))
        return;

    // show wait spinner
    $('#showCachedItems').html('<i class="fa fa-spin fa-spinner"></i> one moment, please ...');

    // send the delte request
    $.post(cSpot.appURL+'/cspot/plan/'+plan_id+'/cache/delete', function(data, status) {

        if ( status == 'success') {
            // show result in UI
            $('#showCachedItems').text(data.data);
        }

    });
}



/*\
|* >------------------------------------------------------------------ SYNC PRESENTATION
\*/


// User becomes Main presenter (if no other is yet)
function changeMainPresenter() {

    var sett = ! (cSpot.presentation.mainPresenter.id == cSpot.user.id);
    ;;;console.log('User tries to change setting for "Become Main Presenter" to ' + sett );

    if (sett==false) {
        // User is no longer the Main Presenter, so make sure he can sync 
        $('#config-SyncPresentation').parent().parent().parent().show();
        
        // inform the server accordingly
        setMainPresenter('false');

        localStorage.setItem('config-MainPresenter', sett);

    } 
    else {    
        // inform the server accordingly
        setMainPresenter();
    }
}

// User wants to sync with the main presentation
function changeSyncPresentation() {
    cSpot.presentation.sync = ! cSpot.presentation.sync;
    var sett = cSpot.presentation.sync;
    console.log('User tries to change setting for "Sync Presentation" to ' + sett );

    changeCheckboxIcon('#config-MainPresenter', sett);

    // save this to local storage for later reference
    localStorage.setItem('config-SyncPresentation', sett);

    // now do the first sync
    syncPresentation(cSpot.presentation.syncData);
}

// User wants to become Main Presenter
function setMainPresenter(trueOrFalse) {
    var sett = trueOrFalse || 'true';
    // we first uncheck this and see what the server says...
    changeCheckboxIcon( '#config-MainPresenter', false );
    // keep the user updated...
    $('.showPresenterName').html('<i class="fa fa-spin fa-spinner"></i>');

    // now check with the Server - is there already a Main Presenter?
    $.ajax({
        url: cSpot.presentation.mainPresenterSetURL,
        type: 'PUT',
        data: {switch: sett},
        success: function(data, status) {

            // show the dropup menu again so that the user can see the changed setting
            $('#presentConfigDropUpMenu').dropdown('toggle')
            
            // user was accepted     or  was already the active Main Presenter
            if (data.status == '201' || (data.status == '202' && data.data.id == cSpot.user.id) ) {
                // Hide the Sync checkbox as the Main Presenter can't sync with another presenter...
                $('#config-SyncPresentation').parent().parent().parent().hide();
                // tick the Main Presenter checkbox
                changeCheckboxIcon( '#setMainPresenterItem', true);
                ;;;console.log('User was accepted as "Main Presenter"' );
                localStorage.setItem('config-MainPresenter', 'true');
                // show presenter name 
                $('.showPresenterName').text(' ('+data.data.name+')')
            }
            else {
                if (data.status == '205') {
                    changeCheckboxIcon( '#setMainPresenterItem', false);
                    ;;;console.log(data.status + ' User removed as Main Presenter');
                    $('.showPresenterName').text(' ('+data.data.name+')')
                }
                else {
                    changeCheckboxIcon( '#setMainPresenterItem', false);
                    console.log(data.status + ' User was NOT accepted as "Main Presenter"' + data.data );
                    $('.showPresenterName').text(' ('+data.data.name+')')
                }
                localStorage.setItem('config-MainPresenter', 'false');
                changeCheckboxIcon( '#config-MainPresenter', false );
            }
            // in any case, set the local value of the Main Presenter
            cSpot.presentation.mainPresenter = data.data;
        },
        error: function(data) {
            console.log(data);
            changeCheckboxIcon( '#config-MainPresenter', false );
        },
    });
}

// new Sync request received
function syncPresentation(syncData) {

    // initially, we might not have any syncData....
    if (syncData==undefined || syncData.plan_id==0 || syncData.item_id==0 || syncData.slide=='none' ) return;

    ;;;console.log('tyring to sync show for: ' + JSON.stringify(syncData));

    // do nothing if we are already at the right location...
    if (cSpot.presentation.plan_id == syncData.plan_id 
     && cSpot.presentation.item_id == syncData.item_id 
     && cSpot.presentation.slide   == syncData.slide
     && syncData.slide != 'blank'   ) // (blank is a toggler!)
    { 
            ;;;console.log('already in sync!');
            return;
    }

    // analyze current url
    var myurl = parseURLstring();
    var pathParts = myurl.pathname.split('/');
    var showType  = pathParts[pathParts.length-1];

    // are we still on the same plan and item?
    if ( cSpot.presentation.plan_id != syncData.plan_id  ||  cSpot.presentation.item_id != syncData.item_id ) {
        ;;;console.log('we have to load a new page:'+myurl.pathname);
        if (showType == 'present' || showType == 'chords' || showType == 'sheetmusic')
            window.location.href = cSpot.appURL + '/cspot/items/' + syncData.item_id + '/' + showType;
        return;
    }

    ;;;console.log('Showtype is '+showType+'. We have to jump to a new slide: ' + syncData.slide);

    var slideNameParts = syncData.slide.split('-');

    // go to the new slide
    if (showType == 'present')
        if (slideNameParts.length>2) {
            if (slideNameParts[0]=='bible') {
                console.log('jumping to bible verse ' + slideNameParts[2]);
                bibleShow(slideNameParts[2]);
            } 
            if (slideNameParts[0]=='slides') {
                console.log('jumping to image slide ' + slideNameParts[2]);
                slidesShow(slideNameParts[2]);
            } 
        } 
        else if (syncData.slide=='blank') {
            showBlankScreen();
        }
        else {
            lyricsShow(syncData.slide);
        }
    else 
        navigateTo(syncData.slide);
}







/*\
|*|
|*|
|*+------------------------------------------ END of    presentation.js   ------------------------------------
|*|
|*|
\*/

