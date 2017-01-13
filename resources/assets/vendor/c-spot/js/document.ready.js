
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


/* for eslint */
if (typeof($)===undefined) {
    var $, cSpot;
}
 


$(document).ready(function() {



    /* Get Config data from backend
    */
    if (location.pathname.search('cspot')>=0)
        $.get( cSpot.getConfigRoute+'?item_id='+$("#item_id").val() )

        .done( function(data) {
            for ( var item in data) {
                cSpot[item] = data[item];
            }

            cSpot.config = {};
            // some common html elements
            cSpot.const = {};
            cSpot.const.waitspinner = '<i class="fa fa-spinner fa-spin fa-fw"></i>';
            cSpot.const.editIcon   = '<i class="fa fa-edit"></i>';
            cSpot.const.deleteIcon = '<i class="fa fa-trash"></i>';
            
            // presentation type might have been set in the view
            if (cSpot.presentationType) {
                cSpot.presentation.type = cSpot.presentationType;
                // load cached presentation data
                if ( cSpot.presentationType=='lyrics' && cSpot.presentation.plan )
                    loadCachedPresentation(cSpot.presentation.plan.id);
                if ( cSpot.presentationType=='chords' )
                    // for chords presentation
                    prepareChordsPresentation(cSpot.presentationType);
            }

            // we need this config data to run these functions:
            loadFromLocalCache();

            if ( cSpot.env.presentationEnableSync )
                prepareSyncPresentation();

            // watch blank slide status in presentation
            cSpot.presentation.screenBlank = true;
        })

        .fail( function(data) {
            alert('c-SPOT might not work properly as it could not load config data from backend server!');
        });



    /**
     * Make certain content editable
     *
     * (see http://www.appelsiini.net/projects/jeditable)
     */
    $('.editable').editable(cSpot.appURL + '/cspot/api/items/update', {
        onblur      : 'cancel',
        cssclass    : 'editable-input-field',
        style       : 'display: inline',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
        data        : function(value, settings) {
            // strip any html code off the field value
            var tmp = document.createElement("DIV");
            tmp.innerHTML = value;
            return tmp.textContent || tmp.innerText || "";
        }
    });

    // song sequence field on the item details page
    $('.editable-song-field').editable(cSpot.appURL + '/cspot/api/songs/update', {
        style       : 'display: inline',
        cancel      : 'Cancel',
        submit      : 'Update',
        onblur      : 'ignore',
        indicator   : '<span class="fa fa-refresh fa-spin"> </span> saving...',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
    });

    // lyrics and chords textareas on the item details page
    $('.edit_area').editable(cSpot.appURL + '/cspot/api/songs/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        submit      : 'Update',
        onblur      : 'ignore',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
    });

    // comment field in the resources list of a plan
    $('.editable-resource').editable(cSpot.appURL + '/cspot/api/plans/resource/update', {
        style       : 'display: inline',
        placeholder : '<span class="fa fa-pencil text-muted">&nbsp;</span>',
        event       : 'mouseover',
        onblur      : 'cancel',
    });

    // comment field or private notes on the Item Detail page
    $('.editable-item-field').editable(cSpot.appURL + '/cspot/api/items/update', {
        type        : 'textarea',
        event       : 'mouseover',
        width       : '100%',
        rows        : '3',
        cancel      : 'Cancel',
        submit      : 'Save',
        onblur      : 'ignore',
        indicator   : '<span class="fa fa-refresh fa-spin"> </span> saving...',
        placeholder : '<span class="fa fa-edit">&nbsp;</span>',
    });

    $('.editable-item-field-present').editable(cSpot.appURL + '/cspot/api/items/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        submit      : 'Save',
        onblur      : 'cancel',
        indicator   : '<span class="fa fa-refresh fa-spin"> </span> saving...',
        placeholder : '<span class="fa fa-edit">&nbsp;</span>',
    });

    // Plan Detail page - update Plan Note
    $('.editable-plan-info').editable(cSpot.appURL + '/cspot/api/plan/update', {
        type        : 'textarea',
        cancel      : 'Cancel',
        width       : '90%',
        rows        : '3',
        submit      : 'Save',
        onblur      : 'ignore',
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
     * enabling certain Bootstrap UI features (Tooltips, Popovers)
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
    if ( window.location.href == cSpot.appURL + '/home' ) {
        $.getJSON( cSpot.appURL + '/cspot/plans?filterby=future&api=api',
            function(result){
                $.each(result, function(i, field) {
                    // map each resulting event to the appropriate DAY element of the datepicker
                    var hint = field.type.name+' led by '+field.leader.first_name; 
                    if ( field.teacher.first_name != "n/a" ) {
                        hint +='(teacher: ' + field.teacher.first_name +')'; }
                    var dt = new Date(field.date.split(' ')[0]).toLocaleDateString();
                    if (SelectedDates[dt]==undefined)
                        SelectedDates[dt] = hint;
                    else if (SelectedDates[dt]=="Today")
                        SelectedDates[dt] = "Today: " + hint;
                    else 
                        SelectedDates[dt] = SelectedDates[dt]+'; '+hint;
                });
                // get the current browser window dimension (width)
                var browserWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                var numberOfMonths = 3;
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


    /*  Start SPA utility once the modal popup is being launched
    */
    $('#addPlanNoteModal').on('shown.bs.modal', function (event) {
        addNoteToPlan( event );
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
        enableSubmitButton();
    });
    $("input, textarea, input:radio, input:file").click(function() {
        // change background color of those fields
        $(this).css("background-color", "#D6D6FF");

        // not when a popup is open...
        if ($('#searchSongModal').is(':visible')) return;

        // show submit or save buttons
        enableSubmitButton();
    });





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
            var should_seq_no = 0;
            var movedItem = [];
            movedItem.id = ui.item.data('itemId');
            movedItem.seq_no = ui.item.attr('id').split('-')[2];

            // get all siblings of the just moved item
            var siblings = $(ui.item).parent().children();
            // check each sibling's sequence
            for (var i = 1; i <= siblings.length; i++) {
                // 'i' is one ahead of the actual index...
                var sib = siblings[-1+i];

                // ignore trashed items....
                if (sib.classList.contains('trashed')) continue;

                // is this the moved item?
                if ( sib.dataset.itemId == movedItem.id ) {
                    changed = sib;
                    break;
                } 
                else {
                    should_seq_no = 0.0 + sib.id.split('-')[2];
                    if (changed)
                        break; 
                }
            }
            if (changed) {
                should_seq_no = 1 * should_seq_no;
                window.location.href = cSpot.appURL + '/cspot/items/' + changed.dataset.itemId + '/seq_no/'+ (0.5 + should_seq_no);
                return;
            } 
        },
        }).disableSelection();
    }

    
    /**
     * On the Songs List page, allow some key codes
     */
    if (window.location.pathname.indexOf('cspot/songs')>0) {

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

        // allow right-mouse-click to move one slide or item back
        $('#main-content').on('mouseup', function(event){
            event.preventDefault();
            if (event.which == 1) {
                advancePresentation(); }
            if (event.which == 3) {
                advancePresentation('back'); }
        });        
    }


    /**
     * Handle Keyboard events 
     */
    $(document).keydown(function( event ) {

        // Handle Keyboard events in Presentation Views
        if ( window.location.pathname.indexOf('/present' ) > 10
          || window.location.pathname.indexOf('/chords'   ) > 10
          || window.location.pathname.indexOf('/leaser'    ) > 10
          || window.location.pathname.indexOf('/sheetmusic' ) > 10 ) {
            // do nothing while a modal is open
            if ($('.modal-content').is(':visible')) return;

            // key codes: 37=left arrow, 39=right, 38=up, 40=down, 34=PgDown, 33=pgUp, 
            //            36=home, 35=End, 32=space, 27=Esc, 66=e
            //
            ;;;console.log('pressed key code: '+event.keyCode);
            switch (event.keyCode) {
                case 37: advancePresentation('back'); break; // left arrow
                case 33: navigateTo('previous-item'); break; // left PgUp
                case 36: navigateTo('first-item');   break; // 'home'
                case 39: advancePresentation();     break; // right arrow
                case 32: advancePresentation();    break; // spacebar
                case 34: navigateTo('next-item'); break; // 'PgDown'
                case 35: navigateTo('last-item'); break; // 'end'
                case 27: navigateTo('back');     break; // 'Esc'
                case 68: navigateTo('edit');    break; // 'd'
                case 83: jumpTo('start-lyrics');break; // 's'
                case 80: jumpTo('prechorus'); break; // 'p'
                case 49: jumpTo('verse1'); break; // '1'
                case 50: jumpTo('verse2'); break; // '2'
                case 51: jumpTo('verse3'); break; // '3'
                case 52: jumpTo('verse4'); break; // '4'
                case 53: jumpTo('verse5'); break; // '5'
                case 53: jumpTo('verse6'); break; // '6'
                case 53: jumpTo('verse6'); break; // '6'
                case 53: jumpTo('verse7'); break; // '7'
                case 67: jumpTo('chorus1'); break; // 'c'
                case 75: jumpTo('chorus2');  break; // 'k'
                case 66: jumpTo('bridge');     break; // 'b'
                case 69: jumpTo('ending');       break; // 'e'
                case 76: $('.lyrics-parts').toggle();break; // 'l', (letter l) show all lyrics
                case 96: showBlankScreen();          break; // '0'
                case 109: $('#decr-font').click();   break; // '-'
                case 107: $('#incr-font').click();   break; // '+'
                case 188: recallPrevBibleverse();   break; // '.'  - jumpt to next bible verse
                case 190: recallNextBibleverse();  break; // '.'  - jumpt to next bible verse
                default: break;
            }
        }

        else {
            //console.log(event.keyCode);
            switch (event.keyCode) {
                case 27: cancelForm(); break; // escape key
                default: break;
            }
        }
    });


    function KeyPress(e) {
        var evtobj = window.event? event : e
        if (evtobj.keyCode == 13 && evtobj.ctrlKey) {
            $('#inputForm').submit();
        }
    }

    document.onkeydown = KeyPress;



    /* reformat OnSong texts into sepeare lyrics and chords
    */
    if ($('.show-onsong-text').length) {
        reFormatOnsongLyrics();
        // show zoom buttons on onsong chords presentation
        $('.edit-show-buttons').css('display', 'inline');
        applyLocallyDefinedTextFormatting();
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


    // now we can allow the Modal to work fully
    $('.item-modal-header').toggle();
    $('.modal-footer').show();


    // set focus to main content of presentations
    $('#main-content').focus();


    // on a form, put focus on first input field, if there is any
    if ($('.first-focus').length)
        $('.first-focus')[0].focus();


    ;;;console.log('document fully loaded');
});





/*\
|*|
|*|
|*+------------------------------------------ END of    document.ready.js   ------------------------------------
|*|
\*/

