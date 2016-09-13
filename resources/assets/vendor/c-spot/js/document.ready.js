
/*\
|  \
|   \__________________________________
|
|            document.ready.js
|
|      (C) 2016 Matthias Kuhs, Ireland
|    __________________________________
|   /
|  /
\*/


$(document).ready(function() {




    /**
     * Make certain content editable
     *
     * (see http://www.appelsiini.net/projects/jeditable)
     */
    $('.editable').editable(__app_url + '/cspot/api/items/update', {
        onblur      : 'cancel',
        cssclass    : 'editable-input-field',
        style       : 'display: inline',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
        data        : function(value, settings) {
            // check if text is a simple string or a html element
            // Issue: when comment contains a string AND a bible ref...
            if (value.substr(0,2)=='<a')
                return $(value).text();
            return value;
        }
    });

    // lyrics and chords textareas on the item details page
    $('.edit_area').editable(__app_url + '/cspot/api/songs/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        submit      : 'Update',
        onblur      : 'ignore',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
    });

    // comment field in the resources list of a plan
    $('.editable-resource').editable(__app_url + '/cspot/api/plans/resource/update', {
        style       : 'display: inline',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
        event       : 'mouseover',
        onblur      : 'cancel',
    });

    // comment field or private notes on the Item Detail page
    $('.editable-item-field').editable(__app_url + '/cspot/api/items/update', {
        type        : 'textarea',
        event       : 'mouseover',
        event       : 'mouseover',
        width       : '100%',
        rows        : '3',
        cancel      : 'Cancel',
        submit      : 'Save',
        onblur      : 'ignore',
        indicator   : '<span class="fa fa-refresh fa-spin"> </span> saving...',
        placeholder : '<span class="fa fa-edit">&nbsp;</span>',
    });

    $('.editable-item-field-present').editable(__app_url + '/cspot/api/items/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        submit      : 'Save',
        onblur      : 'cancel',
        indicator   : '<span class="fa fa-refresh fa-spin"> </span> saving...',
        placeholder : '<span class="fa fa-edit">&nbsp;</span>',
    });




    /**
     * Show WAIT spinner for all navbar anchor items
     */
    $('a, input:submit, input.form-submit').click( function() {
        // do not use for anchors with their own click handling
        if ( $(this).attr('onclick')!= undefined
          || $(this).attr('target') != undefined    // or for links opening in new tabs
          || ($(this).attr('href')  != undefined && $(this).attr('href').substr(0,1) == '#') )
            return;
        $('#show-spinner').modal({keyboard: false});
    })


    /*
        formatting of pagination buttons (links)
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

        // activate popovers
        $('[data-toggle="popover"]').popover();
        $('.popover-dismiss').popover({
            trigger: 'focus'
        });

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
     * On list pages, when a new item was inserted and highlighted,
     *      slowly fade out the highlighting
     */
    if ($('.newest-item').length) {
        $('.newest-item').removeClass('bg-khaki', 19999);
    }


    /*  Start SPA utility once the modal popup is being launched
    */
    $('#searchSongForm').on('show.bs.modal', function (event) {
        insertNewOrUpdateExistingItems( event);
    })




    /*
         in Presentation mode, modify the modal's position and outlook
    */
    if ( window.location.pathname.indexOf('/present') > 10 ) {

        $('#searchSongModal').on('show.bs.modal', function (event) {

            // move to the bottom
            $('#searchSongModal').css('top','inherit');

            // no animation
            $('#searchSongModal').removeClass('fade');

            $('.modal-title').hide();   /* title not needed */

            // darker background
            $('.modal-content').css('background-color', '#c2c2d6');

        });

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

        // not when a popup is open...
        if ($('#searchSongModal').is(':visible')) return;

        // do this only once ...
        if ($('.submit-button').is(':visible')) return;

        // show submit or save buttons
        $('.submit-button').show();
        blink('.submit-button');
    });




    /* 
        provide certain (locally cached) data accross all cSpot  views 
    */
    if (window.location.pathname.indexOf('cspot/')>0) {


        /*  check if songList exists in local cache,
            otherwise grab an update from the server

            TODO: make sure we always get an update when the songs table was changed!
        */

        // check local storage
        cSpot.songList = JSON.parse(localStorage.getItem('songList'));

        // not found in local storage, so get it from the server
        if (cSpot.songList==null) {
            
            ;;;console.log("Song list must be reloaded from server!");

            $.get(__app_url+'/cspot/api/songs/getsonglist', function(data, status) {

                if ( status == 'success') {
                    cSpot.songList = JSON.parse(data);
                    localStorage.setItem( 'songList', JSON.stringify(cSpot.songList) );
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
            $.get(__app_url+'/bible/books/all/verses', function(data, status) {

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
     * Allow items on Plan page to be moved into new positions
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
                if (sib.classList.contains('trashed')) {
                    // ignore trashed items....
                    continue;
                }
                // is this the moved item?
                if ( sib.dataset.itemId == movedItem.id ) {
                    changed = sib;
                    break;
                } 
                else {
                    should_seq_no = 0.0 + sib.id.split('-')[2];
                    if (changed) { 
                        break; 
                    }
                }
            }
            if (changed) {
                should_seq_no = 1 * should_seq_no;
                window.location.href = __app_url + '/cspot/items/' + changed.dataset.itemId + '/seq_no/'+ (0.5 + should_seq_no);
                return;
            } 
        },
        }).disableSelection();
    }

    
    /**
     * On the Songs List page, allow some key codes
     */
    if (window.location.pathname.indexOf('cspot/songs')>1) {

        $(document).keydown(function( event ) {
            ;;;console.log('pressed key code: '+event.keyCode);
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
    if ( window.location.pathname.indexOf('/present' ) > 10
      || window.location.pathname.indexOf('/chords'   ) > 10
      || window.location.pathname.indexOf('/sheetmusic') > 10 ) {

        // handle keyboard events
        $(document).keydown(function( event ) {

            // do nothing while a modal is open
            if ($('.modal-content').is(':visible')) return;

            // key codes: 37=left arrow, 39=right, 38=up, 40=down, 34=PgDown, 33=pgUp, 
            //            36=home, 35=End, 32=space, 27=Esc, 66=e
            //
            ;;;console.log('pressed key code: '+event.keyCode);
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
                case 76: $('.lyrics-parts').toggle();break; // key 'l', (letter l) show all lyrics
                case 96: showBlankScreen();          break; // key '0'
                case 109: $('#decr-font').click();   break; // key '-'
                case 107: $('#incr-font').click();   break; // key '+'
                default: break;
            }
        });
    }
    



    /**
     * check sync setting for chords or sheetmusic presentation
     */
    if ( window.location.pathname.indexOf('/chords')>10 || window.location.pathname.indexOf('/sheetmusic')>10 ) {

        // check if we want to syncronise our own presentation with the Main Presenter
        configSyncPresentationSetting = localStorage.getItem('configSyncPresentation');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (configSyncPresentationSetting=='true') {
            $('#configSyncPresentation').prop( "checked", true );
            // save in global namespace
            cSpot.presentation.sync = true;
        }

    }



    /**
     * prepare lyrics or bible texts or image slides for presentation
     */
    if ( window.location.pathname.indexOf('/present')>10 ) {

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


        /**
         * Check some user-defined settings in the Local Storage of the browser
         */
        getLocalConfiguration()


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



        /**
         * Save the new content into the local storage for offline presentations!
         */
        if (cSpot.presentation.useOfflineMode) {
            saveMainContentToLocalStorage();
        } 

    }

    /**
     * re-design the showing of lyrics interspersed with guitar chords
     */
    if ( $('#chords').text() != '' ) {
        // only do this for PRE tags, not on input fields etc...
        if ( $('#chords')[0].nodeName == 'PRE' ) {
            reDisplayChords();
        }
        $('.edit-show-buttons').css('display', 'inline');
    }
    // remove dropup button and menu on info screens
    else if ( $('#bibletext').text()!='' || $('#comment').text()!='' ) {
        $('#jumplist').remove();
    }

    // if sheetmusic is displayed, show button to swap between sheetmusic and chords
    if ( window.location.pathname.indexOf('sheetmusic')>0 || window.location.pathname.indexOf('swap')>0 ) {
        $('#show-chords-or-music').css('display', 'inline');
    }


    ;;;console.log('document fully loaded');
});





/*\
|*|
|*|
|*+------------------------------------------ END of    document.ready.js   ------------------------------------
|*|
\*/

