
/*\
|  \
|   \__________________________________
|
|        main javascript for c-SPOT
|
|      (C) 2016 Matthias Kuhs, Ireland
|    __________________________________
|   /
|  /
\*/




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
 * Array to keep a list of future plan dates for highlighing in the calendar widget
 */
var SelectedDates = {};
SelectedDates[new Date().toLocaleDateString()] = 'Today';




/*
    Cause UI elements (e.g. buttons) to flash in order to get attention....
*/
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


/*
    turn an URL string into a DOM object

    @param string url (default: current url)
    @returns object

    This DOM object provides the following values:
        url.protocol; //(http:)
        url.hostname ; //(www.example.com)
        url.pathname ; //(/some/path)
        url.search ; // (?name=value)
        url.hash; //(#anchor)
*/
function parseURLstring(urlstring)
{
    urlstring = urlstring || window.location.href;
    var url = document.createElement('a');
    url.href = urlstring;
    return url;
}


/*
    Automatically close the info modals after a timeout
    (called from layouts\flasing.modal.php)
*/
var timeoutID;
function delayedCloseFlashingModals(selector) {
    timeoutID = window.setTimeout( closeMyModal, 3000, selector);
}
function closeMyModal(selector) {
    $(selector).modal('hide');
    // set focus again on main input field
    $('.main-input').focus();    
}



/*
    changes the class of the selected html element so that
    it either shows a checked tickbox or an unchecked tickbox
*/
function changeCheckboxIcon(selector, onOrOff)
{
    var unchecked = "fa-square-o";
    var checked = "fa-check-square-o";

    if (onOrOff) {
        $(selector).removeClass( unchecked );
        $(selector).addClass( checked );
    } else {
        $(selector).removeClass( checked );
        $(selector).addClass( unchecked );
    }
}


/*
    Gets the value from the Local Storage for a given key
    or returns a default value if the key doesn't exist
*/
function getLocalStorageItem(key, defaultValue)
{
    return localStorage.getItem(key) ? localStorage.getItem(key) : defaultValue;
}



/*\
|*|
|*|
|*#======================================================================================= VARIOUS  HELPERS  FOR  SPECIFIC  PAGES
|*|
|*|
\*/





/*\
|*|
|*+------------------------------------------ Build a Bible Reference string
|*|
|*| (called from scripture_input.blade.php)
\*/
function showNextSelect(fromOrTo, what) 
{
    book    = $('#from-book').val();
    chapter = $('#from-chapter').val();

    // make sure all fields are visible now
    $('.select-reference').show();

    // remove old options from select box
    emptyRefSelect(fromOrTo, what);
    var x = document.getElementById(fromOrTo+'-'+what);

    // API call to get the books/chapter/verses data
    if (typeof(cSpot.bibleBooks)=='object') {
        // make the element visible
        $('#'+fromOrTo+'-'+what).show();

        // minimum value for the 'to' verse is the 'from' verse
        minNumber = 1
        if (fromOrTo=='to' && what=='verse') {
            minNumber = $('#from-verse').val();
        }

        // are wee looking at chapters of a book or verses of a chapter?
        if (what=='chapter') {
            maxNumber = Object.keys(cSpot.bibleBooks[book]).length;
        } else {
            maxNumber = cSpot.bibleBooks[book][chapter];
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




/*
    In the Song Search modal popup, show list of max 5 songs that 
    correspond to the entered search string (needle)
*/
function showSongHints(that, needle, limit)
{
    // get list of songs from global variable
    var haystackMP = cSpot.songList;

    if (needle.length == 0) {
        $(that).html('');
        return;
    }
    var count=0;
    var found = 'no match';
    needle = needle.toLowerCase();
    for (var i=0; i<haystackMP.length; i++) {
        if ( haystackMP[i].title.toLowerCase().indexOf(needle)>=0 
          || haystackMP[i].title_2.toLowerCase().indexOf(needle)>=0 
          || haystackMP[i].book_ref.toLowerCase().indexOf(needle)>=0 ) {

            // are we limited to only show videoclips or infoscreen items?
            if (limit=='clips') {
                if ( ! (haystackMP[i].title_2.toLowerCase() == 'video' || haystackMP[i].title_2.toLowerCase() == 'infoscreen') )
                    continue;
            }
            if (count==0) found='';
            
            found+='<div class="radio"><label class="text-muted link"><input type="radio" onclick="$(\'#searchForSongsButton\').click();" name="haystack" id="needle-';
            found+=haystackMP[i].id + '" value="'+ haystackMP[i].id;
            found+='">' + haystackMP[i].book_ref + ' ' + haystackMP[i].title + (haystackMP[i].title_2 ? ' ('+haystackMP[i].title_2+')' : '') + '</label></div>';
            count++;
            ;;;console.log('found song. Index: '+i+', id: '+haystackMP[i].id+', title:'+haystackMP[i].title);
        }
        if (count>5) break;
    };
    $(that).html(found);
}

/*
    from the (locally cached) songlist, get all MP songs
    and add them as options to the dropdown-select box
    in the Song Search modal popup

    Same for the list of video clips or infoscreens
*/
function addOptionsToMPsongSelect()
{
    // get handle on current html element
    var mps = document.getElementById('MPselect');
    var clips = document.getElementById('ClipSelect');

    // ignore the rest if the element wasn't found
    if (! mps) return;

    var songs = cSpot.songList;

    // create new nodes with the data from each song and add it to the list of options
    for (var i in songs) {
        // create a new HTML 'option' element
        var opt = document.createElement('option');
        // for the list of MP songs....
        if ( songs[i].book_ref && songs[i].book_ref.substr(0,2) == "MP" ) {
            opt.value = songs[i].id;
            opt.text = songs[i].number + '-' + songs[i].title;
            mps.appendChild(opt);
        }
        // for the list of Clips....
        if ( songs[i].title_2 == "video" || songs[i].title_2 == "infoscreen" ) {
            opt.value = songs[i].id;
            opt.text = songs[i].title + (songs[i].title_2 ? '('+songs[i].title_2+')' : '');
            clips.appendChild(opt);
        }
    }

}


/*
    from the (locally cached) list of Bible books,
    add each as options to the dropdown-select box
    in the Scripture input dropdown selection
*/
function addOptionsToBookSelect()
{
    // get handle on current html element
    var mps = document.getElementById('from-book');

    // ignore the rest if the element wasn't found
    if (! mps) return;

    var books = cSpot.bibleBooks;

    // create new nodes with the data from each song and add it to the list of options
    for (var book in books) {
        var opt = document.createElement('option');
        opt.value = book;
        opt.text  = book;
        mps.appendChild(opt);
    }

}



/*
    Inserts default service start- and end-times and other default values
    when user selects a service type while creating a new service plan
    (plan.blade.php)
*/
function fillPlanDefaultValues(that)
{
    // get selected service type
    var selSerType = $(that).val();

    for (var i in cSpot.serviceTypes) {
        if (cSpot.serviceTypes[i].id == selSerType) {
            selSerType = cSpot.serviceTypes[i];
            break;
        }
    }

    // read default times from global var
    var start = selSerType.start;
    var   end = selSerType.end;

    // assign to times input fields
    $('#start').val(start);
    $('#end'  ).val(end);

    // fill default leader name
    if (selSerType.leader_id != null) {

        $('#leader_id').val(selSerType.leader_id);
        
    }
}


/* 
    List filtering: Reload page with alternate filtering
    (plans.blade.php)
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
    $('#show-spinner').modal({keyboard: false});
    window.location.href = __app_url + '/cspot/plans/by_date/' + date.value;
}


/**
    Open modal popup to show linked YT video
*/
function showYTvideoInModal(ytid, that)
{
    // get title from data attribute of the link (to avoid problems with special characters!)
    var title = $(that).data('songTitle');

    // write the modal title
    $('#snippet-modal-title').text(title);

    // replace the modal content with the video iframe
    $('#snippet-modal-content')
        .html('<iframe width="560" height="315" src="https://www.youtube.com/embed/'+ytid+'" frameborder="0" allowfullscreen></iframe>');

    // open the modal
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
        var currUrl  = parseURLstring(window.location.href);
        // check if there is a query string in the URL
        if (currUrl.search.length > 1) { 
            // check that it doesn't contain a plan_id!
            if (currUrl.search.search('plan_id') >= 0) {
                return;
            }
            // clear existing filter and reload page without a filter
            showSpinner();
            // remove filter elements from URL query string
            var queryStr = currUrl.search.split('?')[1].split('&');
            var newUrl = currUrl.pathname;
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
    // hide the triggering item
    $(that).hide();
    // show the desired element 
    $(selector).show();
    $("input[name='search']").focus();

    // make sure the form can be submitted without a mandatory 'file_category_id' field
    $('#file_category_id').removeAttr('required')
}


/**
 * On the Team page, show the role select element once the user was selected
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



/*
    On the ITEM DETAIL page, show or hide the trashed items ?
*/
function toggleTrashed() {
    $('.trashed').toggle();
    if ($('#toggleBtn').text() == 'Show') {
        $('#toggleBtn').text('Hide');
    } else {
        $('#toggleBtn').text('Show');
    }
}


