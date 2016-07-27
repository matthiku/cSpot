


/*\
|*|
|*|
|*+------------------------------------------ Triggered when HTML Document is fully loaded
|*|
|*|
\*/

$(document).ready(function() {




    /**
     * Make certain content editable
     *
     * (see http://www.appelsiini.net/projects/jeditable)
     */
    $('.editable').editable(__app_url + '/cspot/api/items/update', {
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
    $('.edit_area').editable(__app_url + '/cspot/api/songs/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        submit      : 'Update',
        onblur      : 'ignore',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
    });




    /**
     * Show WAIT spinner for all navbar anchor items
     */
    $('a, input:submit, input.form-submit').click( function() {
        // do not use for anchors with their own click handling
        if ( $(this).attr('href').substr(0,1) == '#' 
          || $(this).attr('target') != undefined    // or for links opening in new tabs
          || $(this).attr('onclick')!= undefined )
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
     * On list pages, when a new item was inserted and highlighted,
     *      slowly fade out the highlighting
     */
    if ($('.newest-item').length) {
        $('.newest-item').removeClass('bg-khaki', 19999);
    }

                
    /*\
    |*|----------------------------------------------------------------------
    |*|    Insert NEW or update EXISTING ITEMS on the Plan Overview page
    |*|----------------------------------------------------------------------
    |*|
    |*| The corresponding modal is declared in plan.blade.php
    |*|
    |*| The method below is called when the modal popup is activated (shown) by clicking on the respective buttons or links.
    |*| It populates the modal popup with the data provided by the launching button ....
    |*|
    |*| For insertion of new items, a selection is given between 'song', 'scripture' or 'comment'
    |*| in order to show the appropriate input and selection elements
    |*|
    |*| This same modal is also being used to update an existing song item (ie. to change the song)
    |*|
    |*| The new data is processed via the 'searchForSongs' js helper function above
    \*/
    $('#searchSongForm').on('shown.bs.modal', function (event) {

        // first make sure the form is back in its initial state
        resetSearchForSongs();

        // get item-specific data from the triggering element
        var button = $(event.relatedTarget);        // Button that triggered the modal
        var plan_id  = button.data('plan-id');      // Extract info from data-* attributes
        var item_id  = button.data('item-id');
        var seq_no   = button.data('seq-no' );
        var actionUrl= button.data('action-url' );

        // was modal opened from existing item?
        if (plan_id=="update-song") {
            // directly activate the song selection
            showModalSelectionItems('song');
            $('#searchSongForm'      ).attr('data-action', actionUrl);
            $('#searchSongModalLabel').text('Select song');
            $('#modal-show-item-id').text('for item No '+seq_no+':');
        }
        else if (plan_id=="update-scripture") {
            // directly activate the song selection
            showModalSelectionItems('scripture');
            // use current comment text as initial value
            var curCom = button.parent().children().first().text().trim();
            $('#comment').val( curCom=='Click to edit' ? '' : curCom );
            // URL needed to update the comment as derived from the calling element
            $('#searchSongForm'      ).attr('data-action', actionUrl);
            $('#searchSongModalLabel').text('Select a scripture');
            $('#modal-show-item-id').text('for item No '+seq_no+':');
        } 
        else {
            $('#modal-show-item-id').text('before item No '+seq_no+':');
        }
        // Update the modal's content
        $('#plan_id'      ).val(plan_id);
        $('#beforeItem_id').val(item_id);
        $('#seq-no'       ).val(seq_no);
        // reset the form
        $('#search-string').focus(); // make sure the search string input field has focus

        // prevent the Song Search Form from being submitted when 
        //      the ENTER key is used; instead, perform the actual search
        $("#searchSongForm").submit(function(event){
            if (! $('#searchForSongsButton').is(':visible') ||  $('#song_id').val()=='')
                event.preventDefault();
        });
        // intervene cancel button - reset form and close the popup
        $("#searchSongForm").on('cancel', function(event){
            event.preventDefault();
            resetSearchForSongs();
            $('#searchSongModal').modal('hide');
        });
    })





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


        // check if we want to be Main Presenter
        configMainPresenterSetting = getLocalStorValue('configMainPresenter');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (configMainPresenterSetting=='true') {
            // Check if there already is a presenter
            if ( cSpot.presentation.mainPresenter && cSpot.presentation.mainPresenter.id != cSpot.user.id ) {
                // someone else is already ....
                $('#configMainPresenter').parent().parent().parent().hide();
                localStorage.setItem('configMainPresenter', 'false');
            } 
            else {
                // make sure the Server knows we want to be presenter (if we are allowed to...)
                setMainPresenter();
                // activate the checkbox in the UI
                $('#configMainPresenter').prop( "checked", true );
                // if we are Main Presenter, we can't sync to another ....
                localStorage.setItem('configSyncPresentation', 'false');
                // hide the form the contains this checkbox
                $('#configSyncPresentation').parent().parent().parent().hide();
            }
        } else {
            // Make sure the server knows we don't want to be Main Presenter
            setMainPresenter('false');
        }

        // check if we want to syncronise our own presentation with the Main Presenter
        configSyncPresentationSetting = getLocalStorValue('configSyncPresentation');
        // if the value in LocalStorage was set to 'true', then we activate the checkbox:
        if (configSyncPresentationSetting=='true') {
            $('#configSyncPresentation').prop( "checked", true );
            // if we sync our presentation, we can't be Main Presenter
            localStorage.setItem('configMainPresenter', 'false');
            // hide the form the contains this checkbox
            $('#configMainPresenter').parent().parent().parent().hide();
            // save in global namespace
            cSpot.presentation.sync = true;
        }



        // check if we want a blank slide between items
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

        // check if we have changed the default font size and text alignment for the presentation
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
