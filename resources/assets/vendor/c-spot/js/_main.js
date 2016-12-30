
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


/* for eslint */
if (typeof($)===undefined) {
    var $, cSpot;
}



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


/* Textarea input field height calculations
*/
function calculateTextAreaHeight(that)
{
    $(that).attr( 'rows', 1+Math.max($(that).val().split('\n').length, 2) );
    positionZoomButtons($(that).attr('name'));
}

// position the zoom buttons always above the textarea on the right corner
function positionZoomButtons(what) {
    $('#zoom-'+what+'-textarea').show();
    if ($('#zoom-'+what+'-textarea').is(':visible'))
        $('#zoom-'+what+'-textarea').position({my: 'right bottom', at: 'right top', of: 'textarea[name="'+what+'"]'});
}

// called from the zoom buttons - increase or decrease height of textarea
function resizeTextArea(what, name) {
    var cursize = $('textarea[name='+name+']').attr('rows');
    var diff = 0;
    if ( what=='plus' ) diff  = 1.5*cursize;
    else diff = cursize>5 ?  0.7*cursize  :  4;
    $('textarea[name='+name+']').attr('rows', diff);
    positionZoomButtons(name);
}





/* Toggle tabs on Training Videos page
*/
function toggleVideoTabs(on, off)
{
    $('#tab-'+on ).show();
    $('#tab-'+off).hide();
    $('#pill-'+on ).addClass('active');
    $('#pill-'+off).removeClass('active');
}



function selectServiceType(that)
{
    if (that!='submit') {
        $('#multi-filter-dropdown').addClass('open');
        return;
    }
    // collect all selected options
    var options = [];
    $('input.form-check-input').each( function( index ) {
        if ($(this).prop('checked'))
            options.push( $(this).val().split('-')[1] );
    })
    var url = $('#multi-filter-dropdown').data('url');
    showSpinner();
    if (options.length)
        location.href = url + JSON.stringify(options);
    else location.href = location.pathname;
}


/* 
    provide certain (locally cached) data accross all cSpot  views 
*/
function loadFromLocalCache() 
{
    if (window.location.pathname.indexOf('cspot/')>0) 
    {
        /*  check if songList exists in local cache and if it is still up-to-date,
            otherwise grab an update from the server
        */

        // check local storage
        //  (provide empty array just in case when localStorage doesn't contain this item)
        cSpot.songList = JSON.parse(localStorage.getItem('songList')) || [];
        var songListDate = localStorage.getItem('songList.updated_at');
        if ( !songListDate || (songListDate && ( songListDate == "[object Object]"  || songListDate.substr(0,1)!='{' )) )
            cSpot.songList = null;
        else 
            cSpot.songList.updated_at = JSON.parse( songListDate );

        // not found in local storage, or not up-to-date
        // so get it from the server
        if ( !songListDate || !cSpot.songList || cSpot.songList.updated_at.date != cSpot.lastSongUpdated_at.date) {
            
            ;;;console.log("Song list must be reloaded from server!");

            $.get(cSpot.routes.apiGetSongList, function(data, status) {

                if ( status == 'success') {
                    cSpot.songList = JSON.parse(data);
                    cSpot.songList.updated_at = cSpot.lastSongUpdated_at;
                    localStorage.setItem( 'songList', JSON.stringify(cSpot.songList) );
                    localStorage.setItem( 'songList.updated_at', JSON.stringify(cSpot.lastSongUpdated_at) );
                    ;;;console.log('Saving Song Titles List to LocalStorage');
                    addOptionsToMPsongSelect();
                }
            });
        } 
        else {
            addOptionsToMPsongSelect();
        }
        


        /***
         * Get array with all bible books with all chapters and number of verses in each chapter
         */

        // first check if data is alerady cached locally
        cSpot.bibleBooks = JSON.parse(localStorage.getItem('bibleBooks'));

        if (cSpot.bibleBooks==null) {
            $.get( cSpot.routes.apiBibleBooksAllVerses, function(data, status) {

                if ( status == 'success') {
                    cSpot.bibleBooks = data;
                    localStorage.setItem( 'bibleBooks', JSON.stringify(cSpot.bibleBooks) );
                    ;;;console.log('Saving verses structure to LocalStorage');
                    addOptionsToBookSelect();
                }
            });
        }
        else {
            addOptionsToBookSelect();
        }
    }


    /**
     * check sync setting for chords or sheetmusic presentation
     */
    if ( window.location.pathname.indexOf('/chords')>10 || window.location.pathname.indexOf('/sheetmusic')>10 ) 
    {
        // check if we want to syncronise our own presentation with the Main Presenter
        var configSyncPresentationSetting = localStorage.getItem('configSyncPresentation');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (configSyncPresentationSetting=='true') {
            $('#configSyncPresentation').prop( "checked", true );
            // save in global namespace
            cSpot.presentation.sync = true;
        }
        // use the offline mode (Local Storage) - Default is: Yes
        cSpot.presentation.useOfflineMode = getLocalStorageItem('config-OfflineMode', 'true') == 'true';
        
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        changeCheckboxIcon('#config-OfflineModeItem', cSpot.presentation.useOfflineMode);
    }



    /**
     * Check some user-defined settings in the Local Storage of the browser
     */
    if ( window.location.pathname.indexOf('/leader')>10 ) {
        getLocalConfiguration()
    }


    /**
     * prepare lyrics or bible texts or image slides for presentation
     */
    if ( window.location.pathname.indexOf('/present')>10 ) {
        preparePresentation();
    }
}



// simple function to determine if the current user is the MP
function isPresenter() {
    if (cSpot.user.id == cSpot.presentation.mainPresenter.id)
        return true;
    return false;
}


function prepareSyncPresentation()
{

    // only on presentation pages
    if ( window.location.pathname.indexOf('/present')   > 10 
      || window.location.pathname.indexOf('/chords')    > 10 
      || window.location.pathname.indexOf('/sheetmusic')> 10 
      || window.location.pathname.indexOf('/leader')    > 10 ) {

        // prepare Server-Sent Events
        var es = new EventSource( cSpot.presentation.eventSource );
        // handle generic messages
        es.onmessage = function(e) {
              console.log(e);
        };

        // handle advetisements of new Show Positions
        es.addEventListener("syncPresentation", function(e) {
            cSpot.presentation.syncData = JSON.parse(e.data);
            ;;;console.log('New sync request received: ' + e.data);
            // has user requested a syncchronisation?
            if (cSpot.presentation.sync) {
                // call function to sync 
                syncPresentation(cSpot.presentation.syncData);
            }
        });

        // handle advertisements of new MPs
        es.addEventListener("newMainPresenter", function(e) {
            cSpot.presentation.mainPresenter = JSON.parse(e.data);
            // are we not longer MP?
            if (!isPresenter()) {
                // make sure the MP checkbox is no longer checked!
                $('#configMainPresenter').prop( "checked", false);
                // make sure the Sync checkbox is visible!
                $('#configSyncPresentation').parent().parent().parent().show();
            }
            // write the new MP name into checkbox label
            $('.showPresenterName').text(' ('+cSpot.presentation.mainPresenter.name+')')
        });

    }
}

// Function to inform server of current position
function sendShowPosition(slideName) {
    if (!cSpot.presentation.sync)
        return;
    cSpot.presentation.slide = slideName;
    if (isPresenter()) {
        var data = {
                plan_id : cSpot.presentation.plan_id,
                item_id : cSpot.presentation.item_id,
                slide   : slideName,
            }
        ;;;console.log('sending show position: '+JSON.stringify(data));
        $.ajax({
            url: cSpot.presentation.setPositionURL,
            type: 'PUT',
            data: data,
        });
    }
}


/*
    In the Song Search modal popup, show list of max 5 songs that 
    correspond to the entered search string (needle)
*/
function showSongHints(that, needle, limit)
{
    // get list of songs from global variable
    var haystackMP = cSpot.songList;

    if (needle.length === 0) {
        $(that).html('');
        return;
    }
    var count=0;
    var found = 'no match';
    needle = needle.toLowerCase();
    for (var i=0; i<haystackMP.length; i++) {
        if ( haystackMP[i].title.toLowerCase().indexOf(needle) >= 0 
          || haystackMP[i].title_2.toLowerCase().indexOf(needle) >= 0 
          || haystackMP[i].book_ref.toLowerCase().indexOf(needle) >= 0 ) {

            // are we limited to only show videoclips or slide items?
            if (limit=='clips') {
                if ( ! (haystackMP[i].title_2.toLowerCase() == 'video' || haystackMP[i].title_2.toLowerCase() == 'slide') )
                    continue;
            }
            if (count===0) found='';
            
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

    Same for the list of video clips or slides
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
        if ( songs[i].title_2 == "video" || songs[i].title_2 == "slides" ) {
            opt.value = songs[i].id;
            opt.text = songs[i].title + (songs[i].title_2 ? ' ('+songs[i].title_2+')' : '');
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
    // make sure the Submit button is enabled
    enableSaveButton(that);

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

    // propose a date for this event based on the weekday property of the default values
    var n = moment();
    // first check if the plan still has the default date value of today!
    var p = moment($('input[name="date"]').val());     
    if (selSerType.weekday !== null && n.dayOfYear()==p.dayOfYear()) {
        var newDate = moment();
        var diff = selSerType.weekday - newDate.weekday();
        if (diff < 0) diff += 7;
        newDate = newDate.add(diff, 'day');
        $('input[name="date"]').val(newDate.format("YYYY-MM-DD"));
    }

    // fill default leader name
    if (selSerType.leader_id !== null) {

        $('#leader_id').val(selSerType.leader_id);
        
    }
}


/*  On Plan Details page, enable the save button when user changes a plan detail
*/
function enableSaveButton(that) {
    $('.text-help.plan-details').toggle( "bounce", { times: 3 }, "slow" );
    $('.text-help.plan-details').removeAttr('disabled');
    $('.text-help.plan-details').removeClass('disabled');
    $('.text-help.plan-details').removeClass('btn-secondary');
    $('.text-help.plan-details').addClass('btn-primary');
    $('.text-help.plan-details').toggle( "bounce", { times: 3 }, "slow" );
    $(that).parent().addClass('has-warning');
}

/* On all pages with a Submit Button
*/
function enableSubmitButton()
{
    if ( $('.submit-button').hasClass('disabled') ) {
        $('.submit-button').removeClass('disabled');
        $('.submit-button').removeClass('btn-outline-success');
        $('.submit-button').addClass('btn-success');
    }
}

/*  Called from an keyboard event (usually the Esc key)
    click on the "Cancel" button in a form in order to navigate to the location defined with that button
*/
function cancelForm()
{
    // if a modal is open, simply close it and do nothing else
    if ($('#myErrorModal').is(':visible')) {
        $('#myErrorModal').modal('hide');
        return;
    }
    if ($('#searchSongModal').is(':visible')) {
        $('#searchSongModal').modal('hide');
        return;
    }
    if ($('#addPlanNoteModal').is(':visible')) {
        $('#addPlanNoteModal').modal('hide');
        return;
    }
    if ($('.newrow-cancel-button').is(':visible')) {
        $('.newrow-cancel-button').click();
        return;
    }

    showSpinner(); 
    var newLoc = $('.cancel-button').attr('href');
    if (newLoc === undefined)
        newLoc = $('.cancel-button').parent().attr('href');
    if (newLoc == undefined) {
        ;;;console.log('going back on history! History count: '+ window.history.length)
        if (window.history.length) {
            history.go(-1);
            return false;
        }
        $('#show-spinner').modal('hide');
        return false;
    }
    location.href = newLoc;
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
    // does the URL contain a query string?
    if (currUrl.length > 1) 
    {
        // modify existing query string
        var show=false;
        var queryStr = currUrl[1].split('&');
        if (queryStr.length > 1) {
            newUrl += '?';
            for (var i = queryStr.length - 1; i >= 0; i--) {
                parms = queryStr[i].split('=');
                if (parms[0]=='show') {
                    show=true;
                    parms[1]=='all'  ?  parms[1]='future'  :  parms[1]='all';
                    queryStr[i] = 'show='+parms[1];
                }                
                newUrl += queryStr[i];
                if (i > 0) newUrl += '&';
            }
            if (!show) {
                newUrl += '&show=all';
            }
        }
    } 
    else
    {
        // add new query string 
        newUrl += '?show=all';
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
    var orderbyFound;
    if (currUrl.length > 1) 
    {
        var queryStr = currUrl[1].split('&');
        orderbyFound = false;
        if (queryStr.length > 1) {
            for (var i = queryStr.length - 1; i >= 0; i--) {
                parms = queryStr[i].split('=');
                if (parms[0]=='orderby') {
                    queryStr[i] = 'orderby='+field;
                    orderbyFound = true;
                }
                if (parms[0]=='order') {
                    parms[1]=='asc'  ?  parms[1]='desc'  :  parms[1]='asc';
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
function openPlanByDate(date) 
{
    $('#show-spinner').modal({keyboard: false});
    window.location.href = cSpot.appURL + '/cspot/plans/by_date/' + date.value;
}


/**
    Open modal popup to show linked YT video
*/
function showYTvideoInModal(ytid, that)
{
    // get title from data attribute of the link (to avoid problems with special characters!)
    var title = $(that).data('songTitle');
    if (title===undefined)
        title = $(that).parent().data('songTitle');

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
        for (var j in instruments) {
            var text = instruments[j].name;
            if (j < instruments.length-1) {
                text += ', '; } 
            else {
                text += ')'; }
            $('#show-instruments').append(text);
        }
    }
    if (role_id===undefined) {
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





/*\
|*|
|*+------------------------------------------ Convert Chords to OnSong and vice versa
|*|
\*/


/* take one DOM element, get it's text content
 *    and write it back as individual lines of chords and lyrics 
 */
function rewriteOnsong(element)
{
    var newText = '';
    var textblocks = $(element).text().split("\n");

    $.each(textblocks, function(i) {

        var tx = splitOnSong(textblocks[i]);

        if ( tx.chords.trim()!='' ) // don't add an empty line
            newText += '<pre class="chords">' + tx.chords + '</pre>';

        if ( tx.lyrics.substr(0,1)=='('  
          && tx.lyrics.substr(-1,1)==')' )
            newText += '<pre class="mb-0 text-primary lh-1h">' + tx.lyrics + "</pre>";

        else if (tx.lyrics 
              && tx.lyrics.substr(0,1)!='#')
            newText += '<pre class="lyrics">' + tx.lyrics + "</pre>";
    });

    $(element).html(newText);

    ;;;console.log('onsong chords re-formatted for '+element.nodeName+'.'+element.className);
}


/* Split OnSong code into chords and lyrics
 *
 * @param onsong string line with lyrics and interspersed chords
 *
 * returns object with lyrics and chords, properly aligned
 */
function splitOnSong(onsong)
{
    var result = {}, lyrics='', chords='', spl, maxl=0, padd;
    
    var parts = onsong.split('[');
    for (var i = 0; i < parts.length; i++) {

        // divide this into chord and lyrics
        spl = parts[i].split(']');
        var chord = spl[0].trim();
        var lyric;
        if (spl[1]) lyric = spl[1];
        // if there are leading blanks, make sure we have max one!
        if (lyric && lyric.trim() && lyric.substr(0,2)=='  ') 
            lyric = ' '+lyric.trimLeft();

        // does this part contain both chord and lyrics?
        if (spl.length>1) {
            maxl = Math.max(chord.length, lyric.length);
            if (chord.length >= lyric.length) 
                maxl+=1; // add an extra blank if chords are longer than lyrics
            padd = ' '.repeat(maxl);
            lyrics += (lyric+padd).substr(0,maxl);
            chords += (chord+padd).substr(0,maxl);
        } 
        // no chords in this section, just lyrics
        else {
            lyrics += spl[0];
            chords += ' '.repeat(spl[0].length);
        }
    }
    result.lyrics = lyrics.trim();
    result.chords = chords;

    return result;
}


/*** 
 * Join separate lines of chords and lyrics into an OnSong formatted line
 * (this assumes that the first line always contains only chords!)
 * Also, we must have a even number of lines for this to work properly
 */
function joinLyricsAndChordsToOnSong(chords)
{
    var lines = chords.split("\n");
    if (lines < 2) return chords;

    // at least one line must be chords only
    var chordsFound = false;
    for (var i = 0; i < lines.length; i++) {
        chordsFound = identifyChords(lines[i]);
        if (chordsFound) break;
    }
    if (!chordsFound) return chords;
    
    var result = '';

    // iterate through each line, TWO at a time
    for (var i = 0; i < lines.length; i+=2) {

        var online = '', start = 0;
        var chline = lines[i];
        var lyline = lines[1+i];

        // ignore empty lines
        if (! chline.trim().length  ||  chline == ' ') {
            if (chline.trim().length)
                result += chline + "\n";
            i -= 1; 
            continue;
        }
        // ignore parts headers or other instructions
        if (identifyHeadings(chline).length  ||  !identifyChords(chline)) {
            result += chline + "\n";            
            i -= 1;
            continue;
        }
        
        // find locations of chords
        var chordsLocations = findChords(chline);
        // array of actual chords
        var lnchrds = chline.trim().split(/\s+/);

        // insert the chords into the lyrics text at the right location
        if (lyline===undefined)
            lyline=' ';
        for (var j = 0; j < lyline.length; j++) {
            if (j==chordsLocations[start]) {
                online += '['+ lnchrds[start] + ']';
                start++;
            }
            online += lyline[j];
        }
        result += online + "\n";
    }
    // return the result and make sure we have no trailing newline chars
    return result.trimRight();
}
/* create array of the textual postion of chords in a string 
*/
function findChords(text)
{
    var loc=[], start=false;
    for (var i = 0; i < text.length; i++) {
        if (text[i]!==' ') {
            if (!start) {
                loc.push(i);
                start=true;
            }
        } else start = false;

    }
    return loc;
}


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
        minNumber = 1;
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
    if ($('#from-book').val()===null || $('#from-book').val()==' ') { 
        return; }

    // check existing comment
    oldComment = $('#comment').val();
    if (oldComment.length>0) {
        oldComment += '; ';
    }

    // set default and minimum value identical with 'from' value
    $('#comment').val( oldComment +
        $('#from-book').val()+' ' +
        $('#from-chapter').val()+':' +
        $('#from-verse').val() + 
        ($('#to-verse').val() != $('#from-verse').val() ? '-'+$('#to-verse').val() : '') + ' (' +
        $('#version').val() + ')'
    );

    $('#waiting').show();
    // now get the bible text via API and display it on the page
    showScriptureText($('#version').val(), $('#from-book').val(), $('#from-chapter').val(), $('#from-verse').val(), $('#to-verse').val());

    $('#from-book').val('');
    emptyRefSelect('from', 'chapter');
    emptyRefSelect('from', 'verse');
    emptyRefSelect('to', 'verse');
    $('#version').val('');
    $('.select-reference').hide();
    $('.select-version').hide();
    $('#col-2-song-search').hide();
    $('#comment-label').text('Bible Reading');
    $('#searchForSongsSubmit').focus()
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

    $.get(cSpot.appURL+'/bible/passage/'+version+'/'+book+'/'+chapter+'/'+fromVerse+'/'+toVerse , 
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




/* ---------------------------------- END of main.js ------------------------------------------------##############################*/
