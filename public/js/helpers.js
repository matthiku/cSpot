var bibleBooks;


// make sure all AJAX calls are using the token stored in the META tag
// (see https://laravel.com/docs/5.2/routing#csrf-x-csrf-token)
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
});

            
/**
 * List of future plan dates for highlighing in the calendar widget
 */
var SelectedDates = {};
SelectedDates[new Date().toLocaleDateString()] = 'Today';


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
     * handle keyboard events
     */
    $(document).keydown(function( event ) {
        // key codes: 37=left arrow, 39=right, 38=up, 40=down, 34=PgDown, 33=pgUp, 
        //            36=home, 35=End, 32=space, 27=Esc, 66=e
        //event.preventDefault();
        console.log('pressed key code: '+event.keyCode);
        switch (event.keyCode) {
            case 37: navigateTo('previous-item'); break; // left arrow
            case 36: navigateTo('first-item');   break; // key 'home'
            case 39: navigateTo('next-item');   break; // key right arrow
            case 32: navigateTo('next-item');  break; // spacebar
            case 35: navigateTo('last-item'); break; // key 'end'
            case 66: navigateTo('back');    break; // key 'b'
            case 69: navigateTo('edit');   break; // key 'e'
            case 49: window.location.href='#verse1'; break; // key '1'
            case 50: window.location.href='#verse2'; break; // key '2'
            case 51: window.location.href='#verse3'; break; // key '3'
            case 52: window.location.href='#verse4'; break; // key '4'
            case 53: window.location.href='#verse5'; break; // key '5'
            case 53: window.location.href='#verse6'; break; // key '6'
            case 53: window.location.href='#verse6'; break; // key '6'
            case 53: window.location.href='#verse7'; break; // key '7'
            case 67: window.location.href='#chorus'; break; // key 'c'
            case 27: window.location.href='#bridge'; break; // key 'b'
            default: break;
        }
    });

    /**
     * handle swiping on smartphones
    DISABLED for now as it fails on iPhones and doesn't work well otherwise
    Use the forward/backward buttons instead!

    $('#app-layout').on("swipeleft",function(){
        navigateTo('next-item');
    });
    $('#app-layout').on("swiperight",function(){
        navigateTo('previous-item'); 
    });

     */
    

    /**
     * re-design the showing of lyrics interspersed with guitar chords
     */
    if ( $('#chords').text() != '' ) {
        // only do this for PRE tags, not on input fields etc...
        if ($('#chords')[0].nodeName == 'PRE') {
            reDisplayChords();
        }
        $('.edit-show-buttons').show();
    }
    // if sheetmusic is displayed, show button to swap between sheetmusic and chords
    if ( window.location.href.indexOf('sheetmusic')>0 || window.location.href.indexOf('swap')>0 ) {
        $('#show-chords-or-music').show();
    }

});



/* 
    List filtering: Reload page with alternate filtering
*/
function toogleAllorFuturePlans()
{
    $('#show-spinner').modal({keyboard: false})
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
    $('#show-spinner').modal({keyboard: false})
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
            $('#show-spinner').modal({keyboard: false})
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
            $('#show-spinner').modal({keyboard: false})

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



/**
 * Increase or decrease font size of a given element
 *
 * stores the value in LocalStorage for later reference
 *
 * param  selectorList string or array of valid CSS selectors
 * return void
 */
function incFontSize(selectorList) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    selectorList.forEach( function(selector) {
        element = $(selector);
        if (element.length>0) {
            fontSize = parseInt($(element).css('font-size')) * 1.1;
            $(element).css('font-size', fontSize);
            localStorage.setItem(selector+'_font-size', fontSize);
            console.log('LocalStorage for '+selector+' was set to '+localStorage.getItem(selector+'_font-size'));
        }
    });
}

function decFontSize(selectorList) {
    if ( typeof selectorList === 'string') {
        selectorList = [selectorList];
    }
    selectorList.forEach( function(selector) {
        element = $(selector);
        if (element.length>0) {
            fontSize = parseInt($(element).css('font-size')) * 0.9;
            if (fontSize>12)
                $(element).css('font-size', fontSize);
            localStorage.setItem(selector+'_font-size', fontSize);
            console.log('LocalStorage for '+selector+' was set to '+localStorage.getItem(selector+'_font-size'));
        }
    });
}

function getLocalStorValue(name) {
    value = localStorage.getItem(name);
    console.log('LocalStorage for '+name+' was at '+value);
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

